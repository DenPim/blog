<?php 

if (!$_SESSION['login'] || $_SESSION['user_group'] !== 'admin') {
	require '404.php';
}

$alert = NULL;

if ($_POST['title'] && $_POST['content'] && $_POST['url']) {
	$post_add = Posts::newPost($_POST['title'], $_POST['content'], $_POST['url'], $_FILES);

	if ($post_add === 'ok') {
		$alert = 'Пост был успешно опубликован';
	} else {
		$alert = $post_add;
	}
}

require 'html/admin-new-post.html';