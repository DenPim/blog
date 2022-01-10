<?php 

if (!$_SESSION['login'] || $_SESSION['user_group'] !== 'admin') {
	require '404.php';
}

$users_per_page = 10;

$users_count = Users::count();

if ($_GET['block-user'] && $_GET['user-id']) {
	if ($_GET['block-user'] === 'true') {
		$user_block = Users::update($_GET['user-id'], [ "blocked" => 1 ]);

		if ($user_block === 'ok') {
			header('Location: /admin/users?page='.$_GET['page']); exit;
		}
	} else {
		$user_block = Users::update($_GET['user-id'], [ "blocked" => 0 ]);
		
		if ($user_block === 'ok') {
			header('Location: /admin/users?blocked=1&page='.$_GET['page']); exit;
		}
	}
}
if ($_GET['set-group'] && !empty($_GET['user-id'])) {
	if ($_GET['set-group'] === 'admin') {
		$user_block = Users::update($_GET['user-id'], [ "user_group" => 'admin' ]);
	} else {
		$user_block = Users::update($_GET['user-id'], [ "user_group" => 'user' ]);
	}

	if ($user_block === 'ok') {
		header('Location: /admin/users?page='.$_GET['page']); exit;
	}
}

if ($_GET['page']) {
	$page = $_GET['page'];
} else {
	$page = 1;
}

if ($page * $users_per_page >= $users_count + $users_per_page) {
	require '404.php';
	exit;
}

if ($users_count > $users_per_page * $page) {
	$need_next_page = true;
}

if ($_GET['blocked'] === '1') {
	$users_all = Users::getAll($users_per_page, $page, true, true);
} else {
	$users_all = Users::getAll($users_per_page, $page, true, false);
}

require 'html/admin-users.html';