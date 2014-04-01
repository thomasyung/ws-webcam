<?php

$data = file_get_contents("php://input");

if (json_decode($data) != Null) {
	if (file_put_contents('config.json', $data) > 0 ) {
		echo 'Configuration saved.';
	}
} else {
	echo 'Not valid JSON data.';
}

?>