<?php
require_once('auth.inc');
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta charset="utf-8">
	<title>Webcam - Snapshots</title>
	<link rel="shortcut icon" sizes="120x120" href="apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="120x120" href="apple-touch-icon.png">
	<link rel="stylesheet" type="text/css" href="styles.css?<?php echo time(); ?>">
	<link rel="stylesheet" type="text/css" href="menu.css?<?php echo time(); ?>">
	<style>
		/* style overrides */
	</style>
	<script src="homescreen_app_links.js"></script>
	<script src="menu.js"></script>
	<script>
	var selected = [];
	var selected_elems = {};
	function init() {
		homescreen_app_links();
		menu_init();
		var snapshots_display = document.querySelector('#snapshots_display');
		var message_area = document.querySelector('#message_area');
		var list_req = (window.XMLHttpRequest) ? (new XMLHttpRequest()) : (new ActiveXObject("Microsoft.XMLHTTP"));
		list_req.onreadystatechange = function() {
			if (list_req.readyState==4 && list_req.status==200) {
	    		var snapshot_list = JSON.parse(list_req.responseText);
	    		for(var i=0; i<snapshot_list.length; i++) {
	    			var li = document.createElement('li');
	    			var img = document.createElement('img');
	    			var br = document.createElement('br');
	    			var timestamp = document.createTextNode(snapshot_list[i].timestamp);
	    			img.id = 'img_'+i;
	    			img.src = snapshot_list[i].name;
	    			img.width = '212';
	    			img.rel = snapshot_list[i].name;
	    			img.addEventListener('click', select_image);
	    			li.id = 'li_'+i;
	    			li.appendChild(img);
	    			li.appendChild(br);
	    			li.appendChild(timestamp);
	    			snapshots_display.appendChild(li);
	    		}
	    		message_area.innerHTML = snapshot_list.length + ' snapshots loaded.';
		    }
		};
		list_req.open("GET", "list_snapshots.php", true);
		list_req.send();
		<?php if ($admin == true) { ?>
			var create_recording = document.querySelector('#create_recording');
			create_recording.addEventListener('click', function(e){
				if (selected.length > 0) {
					message_area.innerHTML = 'Creating...';
					var anim_req = (window.XMLHttpRequest) ? (new XMLHttpRequest()) : (new ActiveXObject("Microsoft.XMLHTTP"));
					anim_req.onreadystatechange = function() {
						if (anim_req.readyState==4 && anim_req.status==200) {
							var anim_res = JSON.parse(anim_req.responseText);
							if (anim_res.length > 0) {
								message_area.innerHTML = anim_res[0];
							}
						}
					};
					anim_req.open("POST", "create_gif.php", true);
					anim_req.setRequestHeader("Content-type","application/json");
					anim_req.send(JSON.stringify(selected));
				} else {
					message_area.innerHTML = 'Select snapshots first.';
				}
			});
			var delete_snapshots = document.querySelector('#delete_snapshots');
			delete_snapshots.addEventListener('click', function(e){
				if (selected.length > 0) {
					var delete_req = (window.XMLHttpRequest) ? (new XMLHttpRequest()) : (new ActiveXObject("Microsoft.XMLHTTP"));
					delete_req.onreadystatechange = function() {
						if (delete_req.readyState==4 && delete_req.status==200) {
							var delete_res = JSON.parse(delete_req.responseText);
							if (delete_res.length > 0) {
								for(var key in selected_elems) {
									selected_elems[key].parentNode.removeChild(selected_elems[key]);
									delete selected_elems[key];
								}
								selected = [];
								message_area.innerHTML = delete_res.length + ' snapshots deleted.';
							}
						}
					};
					delete_req.open("POST", "delete_img.php", true);
					delete_req.setRequestHeader("Content-type","application/json");
					delete_req.send(JSON.stringify(selected));
				} else {
					message_area.innerHTML = 'Select snapshots first.';
				}
			});
		<?php } ?>
	}
	function select_image(e) {
		var el = e.target;
		var position = selected.indexOf(el.rel);
		el.className = (el.className=='img_selected')?'':'img_selected';
		if (position==-1) {
			selected.push(el.rel);
			selected_elems[el.rel] = el.parentNode;
		} else {
			selected.splice(position, 1);
			delete selected_elems[el.rel];
		}
		selected.sort();
		message_area.innerHTML = selected.length + ' snapshots selected';
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
			<h1>Snapshots</h1>
		</div>
		<div id="content">
			<div id="snapshots_display"></div>
		</div>
		<br><br><br>
	</div>
	<div id="bottom"></div>
	<div id="scroller">
		<div class="arrow-up"></div><br>
		<a href="#container">Top</a>
		<br><br>
		<a href="#bottom">Bottom</a><br>
		<div class="arrow-down"></div>
	</div>
	<div id="actionbar">
		<?php if ($admin == true) { ?>
			<button id="create_recording" class="button-link">Animate</button>
			<button id="delete_snapshots" class="button-link">Delete</button>
		<?php } ?>	
		<span id="message_area"></span>
	</div>
</body>
</html>