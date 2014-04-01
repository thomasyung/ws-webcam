<?php
session_start(); 
$pass = '1qazxsw2';
$admin = false;
$message = '';
if (isset($_POST['token'])) {
	$_SESSION['token'] = $_POST['token'];
}
if (isset($_POST['logout'])) {
	if ($_POST['logout']=='true'){
		unset($_SESSION['token']);
		$admin = false;
	}
}
if (isset($_SESSION['token'])){
	if ($_SESSION['token'] == $pass) {
		$message = 'You are logged in.';
		$admin = true;
	} else {
		$message = 'Passphrase is incorrect.';
		$admin = false;
	}
}
?>