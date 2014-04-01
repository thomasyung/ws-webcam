<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta charset="utf-8">
<title>Webcam</title>
<link rel="shortcut icon" sizes="120x120" href="apple-touch-icon.png">
<link rel="apple-touch-icon" sizes="120x120" href="apple-touch-icon.png">
<link rel="stylesheet" type="text/css" href="styles.css?<?php echo time(); ?>">
<link rel="stylesheet" type="text/css" href="menu.css?<?php echo time(); ?>">
<script src="homescreen_app_links.js"></script>
<script src="menu.js?<?php echo time(); ?>"></script>
<script>
var log, motiondetecttoggle, motiondetectstatus, webcamstatus, image, image_loading;
var ws, ws_error = false, websocket_server = 'ws://localhost:5000';
function init(){
	homescreen_app_links();
	menu_init();
	// Grab configuration settings
	var configuration = {};
	var get_cfg_req = ((window.XMLHttpRequest)?(new XMLHttpRequest()):(new ActiveXObject("Microsoft.XMLHTTP")));
	get_cfg_req.onreadystatechange = function() {
		if (get_cfg_req.readyState==4 && get_cfg_req.status==200) {
	    	configuration = JSON.parse(get_cfg_req.responseText);
	    	websocket_server = 'ws://' + configuration.websocket_server;
	    	websocket();
	    }
	}
	get_cfg_req.open("GET", "config.json", true);
	get_cfg_req.send();
	log = document.querySelector('#log');
	image = document.querySelector('#img');
	webcamstatus = document.querySelector('#webcamstatus');
	motiondetectdisplay = document.querySelector('#motiondetectdisplay');
	motiondetecttoggle = document.querySelector('#motiondetecttoggle');
	motiondetectstatus = "OFF";
	image.addEventListener('load', function(){
		image_loading = false;
	}, false);
	image.addEventListener('click', function(){
		ws.send(JSON.stringify({evt:'snapshot',message:'Snapshot requested.'}));
	}, false);
	motiondetecttoggle.addEventListener('click', function(){
		if (motiondetectstatus == 'OFF') {
			ws.send(JSON.stringify({evt:'motionresume',message:'Turning motion detection ON.'}));
		} else if (motiondetectstatus == 'ON') {
			ws.send(JSON.stringify({evt:'motionpause',message:'Turning motion detection OFF.'}));
		}
	}, false);
	window.addEventListener('online', function(){
		log.innerHTML = "Network online";
		websocket();
	});
	window.addEventListener('offline', function(){
		log.innerHTML = "Network offline";
	});
	image_loading = true;
	image.src = 'img.jpg?' + (new Date()).getTime();
}
function websocket() {
	ws = new WebSocket(websocket_server);
	ws.onopen = function(event) {
		log.innerHTML = "WebSocket opened";
		ws.send(JSON.stringify({evt:'webcam_status',message:'Webcam status requested...'}));
	};
	ws.onclose = function(event) {
		log.innerHTML = "WebSocket closed";
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
		if (req.evt == 'snapshot_ready' && image_loading == false) {
			image_loading = true;
			image.src = 'img.jpg?' + (new Date()).getTime();
		}
		else if (req.evt == 'webcam_started') {
			webcamstatus.innerHTML = "ONLINE";
			motiondetectstatus = req.motiondetectstatus;
			motiondetectdisplay.innerHTML = req.motiondetectstatus;
			motiondetecttoggle.innerHTML = (req.motiondetectstatus == 'ON')?'OFF':'ON';
		}
		else if (req.evt == 'webcam_stopped') {
			webcamstatus.innerHTML = "OFFLINE";
			motiondetectstatus = req.motiondetectstatus;
			motiondetectdisplay.innerHTML = req.motiondetectstatus;
			motiondetecttoggle.innerHTML = (req.motiondetectstatus == 'ON')?'OFF':'ON';
		}
		else if (req.evt == 'motiondetectstatus') {
			motiondetectstatus = req.motiondetectstatus;
			motiondetectdisplay.innerHTML = req.motiondetectstatus;
			motiondetecttoggle.innerHTML = (req.motiondetectstatus == 'ON')?'OFF':'ON';
		}
	};
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
			<h1>View webcam</h1>
		</div>
    	<div class="col_one">
    		<img id="img" src="">
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