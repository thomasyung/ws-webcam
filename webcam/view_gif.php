<?php
$imgsrc = Null;
$imgsrc = $_GET['img'];
if (!isset($imgsrc)) {
	$imgsrc = 'apple-touch-icon.png';
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta charset="utf-8">
	<title>Webcam - View Image</title>
	<link rel="shortcut icon" sizes="120x120" href="apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="120x120" href="apple-touch-icon.png">
	<link rel="stylesheet" type="text/css" href="styles.css?<?php echo time(); ?>">
	<link rel="stylesheet" type="text/css" href="menu.css?<?php echo time(); ?>">
	<script src="homescreen_app_links.js"></script>
	<script src="menu.js?<?php echo time(); ?>"></script>
	<script>
	function init() {
		homescreen_app_links();
		menu_init();
	}
	window.onload = init;
	</script>
</head>
<body>
	<div id="container">
		<div id="menu" class="menu_toggle_close">
			<ul>
				<li id="menu_toggle">Menu <div class="arrow-down"></div></li>
				<li class="menu_item"><a href="record.html">Start Webcam</a></li>
				<li class="menu_item"><a href=".">View Webcam</a></li>
				<li class="menu_item"><a href="snapshots.php">Snapshots</a></li>
				<li class="menu_item"><a href="recordings.php">Recordings</a></li>
				<li class="menu_item"><a href="admin.php">Admin</a></li>
			</ul>
		</div>
		<div id="header">
			<h1>View Image</h1>
		</div>
		<img id="img" src="<?php echo $imgsrc; ?>">
	</div>
</body>
</html>