<?php

if (!$_SESSION['login'] || $_SESSION['user_group'] !== 'admin') {
	require '404.php';
}

$post_id = $_GET['id'];
$alert = NULL;

if (!$post_id) {
	require '404.php';
}

if ($_POST['title'] && $_POST['content'] && $_POST['url']) {
	$post_update = Posts::updatePost($post_id, $_POST['title'], $_POST['content'], $_POST['url'], $_FILES);

	if ($post_update === 'ok') {
		$alert = 'Пост был успешно опубликован';
	} else {
		$alert = $post_update;
	}
}

$post_exist = Posts::count($post_id);

if ($post_exist !== 1) {
	require '404.php';
}

$post = Posts::get($post_id);

require 'html/admin-edit-post.html';