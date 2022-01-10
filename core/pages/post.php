<?php

$post_url = $url_part[2];

if (!$post_url) {
	require '404.php';
}

$new_comment_alert = NULL;
$comments_per_page = 10;

$post_exist = Posts::count($post_url);

if ($post_exist !== 1) {
	require '404.php';
}

$post = Posts::get($post_url);
$page_title = $post['title'];

if ($_POST['new-comment-content'] && $_SESSION['login']) {
	$comment_add = Comments::new($post['id'], $_POST['new-comment-content'], $_SESSION['nickname']);

	if ($comment_add === 'ok') {
		header('Location: /post/'.$post['url'].'#comments'); exit;
	} else {
		$new_comment_alert = $comment_add;
	}
}

if ($_GET['delete-comment'] && ctype_digit($_GET['delete-comment']) && $_SESSION['login']) {
	$comment_delete = Comments::delete($_GET['delete-comment']);
	if ($comment_delete === 'ok') {
		header('Location: /post/'.$post['url'].'#comments'); exit;
	}
}

if ($_GET['comment-page']) {
	$comment_page = $_GET['comment-page'];
} else {
	$comment_page = 1;
}

$comments_count = Comments::count($post['id']);

if ($comment_page * $comments_per_page >= $comments_count + $comments_per_page) {
	$comments_count = 0;
} else {
	$comments = Comments::getForPost($comments_per_page, $comment_page, $post['id']);
}

if ($comments_count > $comments_per_page * $comment_page) {
	$need_next_page = true;
}

require 'html/post.html';