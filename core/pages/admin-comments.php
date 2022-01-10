<?php 

if (!$_SESSION['login'] || $_SESSION['user_group'] !== 'admin') {
	require '404.php';
}

$comments_per_page = 10;

$comments_count = Comments::count();

if ($_GET['delete-comment']) {
	$delete_comment = Comments::delete($_GET['delete-comment']);

	if ($delete_comment === 'ok') {
		header('Location: /admin/comments?page='.$_GET['page']); exit;
	}
}

if ($_GET['page']) {
	$page = $_GET['page'];
} else {
	$page = 1;
}

if ($page * $comments_per_page >= $comments_count + $comments_per_page) {
	require '404.php';
	exit;
}

if ($comments_count > $comments_per_page * $page) {
	$need_next_page = true;
}

$comments_all = Comments::getAll($comments_per_page, $page);

require 'html/admin-comments.html';