<?php 

if ($_SESSION['login']) {
	header('Location: /'); exit;
}

if ($_POST['nick'] && $_POST['email'] && $_POST['password']) {

	$alert = NULL;

	$add_user = Users::newUser($_POST['nick'], $_POST['email'], $_POST['password']);

	if ($add_user === 'ok') {
		header('Location: /'); exit;
	} else {
		$alert = $add_user;
	}

}

require 'html/register.html';