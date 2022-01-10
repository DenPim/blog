<?php

if ($_GET['page']) {
	$page = $_GET['page'];
} else {
	$page = 1;
}

$posts_per_page = 10;

$posts_count = Posts::count();

if ($page * $posts_per_page >= $posts_count + $posts_per_page) {
	require '404.php';
	exit;
}

if ($posts_count > $posts_per_page * $page) {
	$need_next_page = true;
}

$posts_all = Posts::getAll($posts_per_page, $page);

require 'html/index.html';