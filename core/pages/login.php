<?php 

if ($_SESSION['login']) {
	header('Location: /'); exit;
}

if ($_POST['login'] && $_POST['password']) {

	$alert = NULL;

	$check_login = Users::login($_POST['login'], $_POST['password']);

	if ($check_login === 'ok') {
		header('Location: /'); exit;
	} else {
		$alert = $check_login;
	}
}

require 'html/login.html';