<?php

$img = file_get_contents("php://input");

if ($img != false) {
	$data = base64_decode(str_replace('data:image/png;base64,','',$img));
	if(function_exists('imagejpeg')) {
		$png = imagecreatefromstring($data);
		$imgjpg = imagejpeg($png,'img.jpg');
		$imgjpgcopy = imagejpeg($png,'snapshots/'.time().'.jpg');
		imagedestroy($png); 
	}
}

?>