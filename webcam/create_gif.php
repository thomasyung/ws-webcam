<?php
require_once('GifCreator.class');
$configuration = json_decode(file_get_contents('config.json'));
$recordings_dir = 'recordings';
$durations = array();
if(function_exists('imagegif')) {
	$data = file_get_contents("php://input");
	header('Content-type: application/json');
	if ($data != false) {
		$images = json_decode($data);
		if (isset($images)) {
			if (sizeof($images) > 0) {
				$filename = basename($images[0],'.jpg');
				foreach($images as $image){
					array_push($durations, $configuration->animgif_frame_duration);
				}
				$gc = new GifCreator();
				$gc->create($images, $durations, $configuration->animgif_loop_count);
				$gifBinary = $gc->getGif();
				$gif_filename = $recordings_dir.'/'.$filename.'.gif';
				if (file_put_contents($gif_filename, $gifBinary) > 0 ) {
					echo '["Successfuly created. <a href=\"view_gif.php?img='.$gif_filename.'\">View</a>"]';
				}
			}
		} else {
			echo '["Bad data"]';
		}
	} else {
		echo '["No data"]';
	}	
}
else {
	echo '["Sorry. Your installation does not support GIF creation."]';
}
?>