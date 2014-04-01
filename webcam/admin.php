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
	<title>Webcam - Admin</title>
	<link rel="shortcut icon" sizes="120x120" href="apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="120x120" href="apple-touch-icon.png">
	<link rel="stylesheet" type="text/css" href="styles.css?<?php echo time(); ?>">
	<link rel="stylesheet" type="text/css" href="menu.css?<?php echo time(); ?>">
	<style>
		/* style overrides */
	</style>
	<script src="homescreen_app_links.js"></script>
	<script src="menu.js?<?php echo time(); ?>"></script>
	<script>
	function init() {
		homescreen_app_links();
		menu_init();

		<?php if ($admin == true) { ?>
		
		var message_area = document.querySelector('#message_area');
		var websocket_server = document.querySelector('#websocket_server');
		var motion_sensitivity = document.querySelector('#motion_sensitivity');
		var animgif_frame_duration = document.querySelector('#animgif_frame_duration');
		var animgif_loop_count = document.querySelector('#animgif_loop_count');
		var timezone = document.querySelector('#timezone');
		var configuration = {};
		var get_cfg_req = ((window.XMLHttpRequest)?(new XMLHttpRequest()):(new ActiveXObject("Microsoft.XMLHTTP")));
		get_cfg_req.onreadystatechange = function() {
			if (get_cfg_req.readyState==4 && get_cfg_req.status==200) {
		    	configuration = JSON.parse(get_cfg_req.responseText);
		    	websocket_server.value = configuration.websocket_server;
		    	motion_sensitivity.value = configuration.motion_sensitivity;
		    	animgif_frame_duration.value = configuration.animgif_frame_duration;
		    	animgif_loop_count.value = configuration.animgif_loop_count;
		    	timezone.value = configuration.timezone;
		    	message_area.innerHTML = 'Configuration loaded.';
		    }
		}
		get_cfg_req.open("GET", "config.json", true);
		get_cfg_req.send();
		var save_settings = document.querySelector('#save_settings');
		save_settings.addEventListener('click', function(e){
			e.preventDefault();
			configuration.websocket_server = websocket_server.value;
			configuration.motion_sensitivity = motion_sensitivity.value;
			configuration.animgif_frame_duration = animgif_frame_duration.value;
			configuration.animgif_loop_count = animgif_loop_count.value;
			configuration.timezone = timezone.value;
			var put_cfg_req = ((window.XMLHttpRequest)?(new XMLHttpRequest()):(new ActiveXObject("Microsoft.XMLHTTP")));
			put_cfg_req.onreadystatechange = function() {
				if (put_cfg_req.readyState==4 && put_cfg_req.status==200) {
			    	message_area.innerHTML = put_cfg_req.responseText;
			    }
			}
			put_cfg_req.open("POST", "save_config.php", true);
			put_cfg_req.setRequestHeader("Content-type","application/json");
			put_cfg_req.send(JSON.stringify(configuration));
		});

		<?php } ?>
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
			<h1>Admin</h1>
		</div>
		<div id="content">
			<form method="POST">
				<?php if ($admin == false) { ?>
					<p>Login to enable extra features.</p>
					<label for="token">Passphrase: </label>
					<input type="password" name="token">
					<input type="submit" name="submit" value="Submit" class="button-link">
					<div><b><?php echo $message; ?></b></div>
				<?php } else { ?>
					<div id="logout">
						<?php echo $message; ?>
						<input type="hidden" name="logout" value="true">
						<input type="submit" name="submit" value="Logout" class="button-link">
					</div>
					<div id="settings" style="margin-top: 1em; padding: .5em; border: 1px solid #ccc;">
						<b>Settings</b>
						<p>
							<label for="websocket_server">Websocket server: ws://</label>
							<input type="text" id="websocket_server" value="" autocomplete="off" size="30">
						</p>
						<p>
							<label for="motion_sensitivity">Motion Sensitivity (lower = more sensitive): </label>
							<input type="number" id="motion_sensitivity" min="10" max="100" value="">
						</p>
						<p>
							<label for="animgif_frame_duration">Animated GIF frame duration (ms): </label>
							<input type="number" id="animgif_frame_duration" min="1" max="1000" value="">
						</p>
						<p>
							<label for="animgif_loop_count">Animated GIF loop count (0 for infinite): </label>
							<input type="number" id="animgif_loop_count" min="0" max="20" value="">
						</p>
						<p>
							<label for="timezone">Timezone (for timestamps): </label>
							<input type="text" id="timezone" value="">
						</p>
						<a href="#" id="save_settings">Save Settings</a>
					</div>
				<?php } ?>
			</form>
		</div>
	</div>
	<br><br><br>
	<div id="actionbar">
		<span id="message_area"></span>
	</div>
</body>
</html>