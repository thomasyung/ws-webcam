<?php
$configuration = json_decode(file_get_contents('config.json'));
if (ini_get('date.timezone')) {
	date_default_timezone_set(ini_get('date.timezone'));
} else {
	date_default_timezone_set($configuration->timezone);
}
$listing = array();
foreach (scandir('snapshots') as $filename) {
	if (strpos($filename, '.jpg')!==false) {
		array_push($listing, array('name'=>'snapshots/'.$filename, 'timestamp'=>date('Y-m-d h:iA',intval(basename($filename,'.jpg')))));
	}
}
echo json_encode($listing);
?>