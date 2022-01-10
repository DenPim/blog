<?php 

if (!$_SESSION['login'] || $_SESSION['user_group'] !== 'admin') {
	require '404.php';
}

$posts_per_page = 10;

$posts_count = Posts::count();

if ($_GET['delete-post']) {
	$post_delete = Posts::delete($_GET['delete-post']);
	if ($post_delete === 'ok') {
		header('Location: /admin/posts?page='.$_GET['page']); exit;
	}
}

if ($_GET['archive-post'] && !empty($_GET['post-id'])) {
	if ($_GET['archive-post'] === 'true') {
		$post_archive = Posts::update($_GET['post-id'], [ "archive" => 1 ]);

		if ($post_archive === 'ok') {
			header('Location: /admin/posts?page='.$_GET['page']); exit;
		}

	} else {
		$post_archive = Posts::update($_GET['post-id'], [ "archive" => 0 ]);

		if ($post_archive === 'ok') {
			header('Location: /admin/posts?archive=1&page='.$_GET['page']); exit;
		}

	}
}

if ($_GET['page']) {
	$page = $_GET['page'];
} else {
	$page = 1;
}

if ($page * $posts_per_page >= $posts_count + $posts_per_page) {
	require '404.php';
	exit;
}

if ($posts_count > $posts_per_page * $page) {
	$need_next_page = true;
}

if ($_GET['archive'] === '1') {
	$posts_all = Posts::getAll($posts_per_page, $page, true, true);
} else {
	$posts_all = Posts::getAll($posts_per_page, $page, true, false);
}

require 'html/admin-posts.html';