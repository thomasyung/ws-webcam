<!DOCTYPE html>
<html>
<title>Webcam - Start webcam</title>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta charset="utf-8">
<link rel="shortcut icon" sizes="120x120" href="apple-touch-icon.png">
<link rel="apple-touch-icon" sizes="120x120" href="apple-touch-icon.png">
<link rel="stylesheet" type="text/css" href="styles.css?<?php echo time(); ?>">
<link rel="stylesheet" type="text/css" href="menu.css?<?php echo time(); ?>">
<style>
	video {
		width: 100%;
	}
	canvas {
		display: none;
	}
</style>
<script src="homescreen_app_links.js"></script>
<script src="menu.js"></script>
<script>
var bufidx = 0, buffers = [];
var motion_sensitivity=30, motiondetecttoggle, motiondetectdisplay, motiondetected, motionsendstatus, motionless, motionpause;
var log, webcamstatus, webcamstatusdiv;
var ws, ws_error = false, websocket_server = 'ws://localhost:5000';
var localMediaStream = null;
function init() {
	homescreen_app_links();
	menu_init();
	// Grab configuration settings
	var configuration = {};
	var get_cfg_req = ((window.XMLHttpRequest)?(new XMLHttpRequest()):(new ActiveXObject("Microsoft.XMLHTTP")));
	get_cfg_req.onreadystatechange = function() {
		if (get_cfg_req.readyState==4 && get_cfg_req.status==200) {
	    	configuration = JSON.parse(get_cfg_req.responseText);
	    	motion_sensitivity = parseInt(configuration.motion_sensitivity);
	    	websocket_server = 'ws://' + configuration.websocket_server;
	    	websocket();
	    }
	}
	get_cfg_req.open("GET", "config.json", true);
	get_cfg_req.send();
	// Prepare buffers to store lightness data.
    for (var i = 0; i < 2; i++) {
		buffers.push(new Uint8Array(320 * 240));
    }
	log = document.querySelector('#log');
	webcamstatusdiv = document.querySelector('#webcamstatus');
	motiondetectdisplay = document.querySelector('#motiondetectdisplay');
	motiondetecttoggle = document.querySelector('#motiondetecttoggle');
	video = document.querySelector('video');
	canvas = document.querySelector('canvas');
	ctx = canvas.getContext('2d');
	motionpause = true; // default is to NOT record when motion is detected
	navigator.getMedia = ( navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia );
	if (navigator.getMedia) {
		navigator.getMedia({video: true}, function(stream) {
			video.src = window.URL.createObjectURL(stream);
			localMediaStream = stream;
			requestAnimationFrame(draw);
			video.addEventListener('click', snapshot, false);
			video.addEventListener('play', function() { 
				webcamstatus = 'ONLINE';
				webcamstatusdiv.innerHTML = webcamstatus;
				try {
					ws.send(JSON.stringify({evt:'webcam_started',message:'Webcam started.',motiondetectstatus:((motionpause)?'OFF':'ON')}));
				} catch (e) {
					if ("window" in console) {
						console.log(e);
					}
				}
			}, false);
			video.addEventListener('pause', function() {
				webcamstatus = 'OFFLINE';
				webcamstatusdiv.innerHTML = webcamstatus;
				try {
					ws.send(JSON.stringify({evt:'webcam_stopped',message:'Webcam stopped.',motiondetectstatus:((motionpause)?'OFF':'ON')}));
				} catch (e) {
					if ("window" in console) {
						console.log(e);
					}
				}
			}, false);
			motiondetecttoggle.addEventListener('click', function(){
				if (motionpause == true) {
					doMotionDetectResume();
				} else {
					doMotionDetectPause();
				}
			}, false);
		}, onFailSoHard);
	} else {
		log.innerHTML = 'getUserMedia API not supported.';
	}
	window.addEventListener('online', function(){
		log.innerHTML = "Network online";
		websocket();
	});
	window.addEventListener('offline', function(){
		log.innerHTML = "Network offline";
	});
}
function draw() {
	var frame = readFrame();
    if (frame) {
    	motiondetected = false;
    	markLightnessChanges(frame.data);
    	if (motionpause == false) {
	    	if (motiondetected == true && motionsendstatus == 'ready') {
	    		motionsendstatus = 'sending';
	    		ws.send(JSON.stringify({evt:'motiondetected',message:'Motion detected.'}));
	    	}
	    	if (motiondetected == false && motionsendstatus == 'ready' && motionless == false) {
	    		motionsendstatus = 'sending';
	    		ws.send(JSON.stringify({evt:'motionless',message:'Motion not detected.'}));
	    	}
    	}
    	ctx.putImageData(frame, 0, 0);
    }
    requestAnimationFrame(draw);
}
function readFrame() {
	try {
		ctx.drawImage(video, 0, 0, 320, 240);
	} catch (e) {
		return null;
	}
	return ctx.getImageData(0, 0, 320, 240);
}
function markLightnessChanges(data) {
	var buffer = buffers[bufidx++ % buffers.length];
	for (var i = 0, j = 0; i < buffer.length; i++, j += 4) {
		var current = lightnessValue(data[j], data[j + 1], data[j + 2]);
		if (lightnessHasChanged(i, current)) {
			motiondetected = true;
		}
		buffer[i] = current;		
	}
}
function lightnessHasChanged(index, value) {
	return buffers.some(function (buffer) {
	  	return Math.abs(value - buffer[index]) >= motion_sensitivity;
	});
}
function lightnessValue(r, g, b) {
	return (Math.min(r, g, b) + Math.max(r, g, b)) / 255 * 50;
}
function websocket() {
	ws = new WebSocket(websocket_server);
	ws.onopen = function(event) {
		log.innerHTML = "WebSocket opened.";
		ws.send(JSON.stringify({evt:'webcam_started',message:'Webcam started.',motiondetectstatus:((motionpause)?'OFF':'ON')}));
		motiondetectdisplay.innerHTML = ((motionpause)?'OFF':'ON');
		motiondetecttoggle.innerHTML = ((motionpause)?'ON':'OFF');
		motionsendstatus = 'ready';
		motionless = true;
	};
	ws.onclose = function(event) {
		log.innerHTML = "WebSocket closed.";
		if (navigator.onLine && ws_error==false) {
			websocket();
		}
		if (ws_error==true) {
			log.innerHTML = 'Error connecting to ' + websocket_server;
		}
	};
	ws.onerror = function(event) {
		ws_error = true;
	};
	ws.onmessage = function (event) {
		req = JSON.parse(event.data);
		log.innerHTML = req.message;
		if (req.evt == 'snapshot') {
			snapshot();
		}
		if (req.evt == 'snapshot_ready') {
			motionsendstatus = 'ready';
		}
		if (req.evt == 'webcam_status') {
			if (webcamstatus == 'ONLINE') { 
				ws.send(JSON.stringify({evt:'webcam_started',message:'Webcam started.',motiondetectstatus:((motionpause)?'OFF':'ON')}));
			} else {
				ws.send(JSON.stringify({evt:'webcam_stopped',message:'Webcam stopped.',motiondetectstatus:((motionpause)?'OFF':'ON')}));
			}
		}
		if (req.evt == 'motiondetected') {
			snapshot();
			motionless = false;
		}
		if (req.evt == 'motionless') {
			motionsendstatus = 'ready';
			motionless = true;
		}
		if (req.evt == 'motionpause') {
			doMotionDetectPause();
		}
		if (req.evt == 'motionresume') {
			doMotionDetectResume();
		}
	};
}
function onFailSoHard() {
	log.innerHTML = 'Did not accept camera connection.';
}
function snapshot() {
	if (localMediaStream) {
		ctx.drawImage(video, 0, 0, 320, 240);
		var shot = canvas.toDataURL('image/png');
		var snap_req = ((window.XMLHttpRequest)?(new XMLHttpRequest()):(new ActiveXObject("Microsoft.XMLHTTP")));
		snap_req.onreadystatechange = function() {
			if (snap_req.readyState==4 && snap_req.status==200) {
		    	ws.send(JSON.stringify({evt:'snapshot_ready',message:'Snapshot ready.'}));
		    }
		}
		snap_req.open("POST", "post_img.php", true);
		snap_req.setRequestHeader("Content-type","application/json");
		snap_req.send(shot);
	}
}
function doMotionDetectPause() {
	motionpause = true;
	motiondetectdisplay.innerHTML = ((motionpause)?'OFF':'ON');
	motiondetecttoggle.innerHTML = ((motionpause)?'ON':'OFF');
	ws.send(JSON.stringify({evt:'motiondetectstatus',message:'Motion detection turned OFF.',motiondetectstatus:((motionpause)?'OFF':'ON')}));
}
function doMotionDetectResume() {
	motionpause = false;
	motiondetectdisplay.innerHTML = ((motionpause)?'OFF':'ON');
	motiondetecttoggle.innerHTML = ((motionpause)?'ON':'OFF');
	ws.send(JSON.stringify({evt:'motiondetectstatus',message:'Motion detection turned ON.',motiondetectstatus:((motionpause)?'OFF':'ON')}));
}
window.onload = init;
</script>
</head>
<body>
	<div id="container">
		<div id="menu" class="menu_toggle_close">
			<ul>
				<li id="menu_toggle">Menu <div class="arrow-down"></div></li>
				<li class="menu_item"><a href="record.php">Start Webcam</a></li>
				<li class="menu_item"><a href=".">View Webcam</a></li>
				<li class="menu_item"><a href="snapshots.php">Snapshots</a></li>
				<li class="menu_item"><a href="recordings.php">Recordings</a></li>
				<li class="menu_item"><a href="admin.php">Admin</a></li>
			</ul>
		</div>
		<div id="header">
			<h1>Start webcam</h1>
		</div>
		<div class="col_one">
			<video width="320" height="240" controls autoplay></video>
			<canvas width="320" height="240"></canvas>
		</div>
		<div class="col_two">
			<p>Tap/click image to take a snapshot.</p>
		    <p><b>Webcam status:</b> <span id="webcamstatus">UNKNOWN</span></p>
		    <p><b>Motion Detection:</b> <span id="motiondetectdisplay">UNKNOWN</span> / <button id="motiondetecttoggle" class="button-link">TOGGLE</button></p>
			<p><b>Request status:</b> <span id="log"></span></p>
		</div>
	</div>
</body>
</html>