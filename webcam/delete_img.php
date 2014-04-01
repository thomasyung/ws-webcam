<?php

$data = file_get_contents("php://input");

header('Content-type: application/json');

if ($data != false) {
	$images = json_decode($data);
	if (isset($images)) {
		if (sizeof($images) > 0) {
			foreach($images as $image){
				if (unlink($image)) { } else {
					$images = array_merge(array_diff($images, array($image)));
				}
			}
			echo json_encode($images);
		}
	}
} else {
	echo '[]';
}