<?php

$url = parse_url(htmlspecialchars($_SERVER['REQUEST_URI']), PHP_URL_PATH);
$url_part = explode('/', $url);

switch ($url) {

	case '/':
		$page_title = 'Главная';
		require 'pages/index.php';
		break;

	case '/login':
		$page_title = 'Вход';
		require 'pages/login.php';
		break;

	case '/register':
		$page_title = 'Регистрация';
		require 'pages/register.php';
		break;

	case '/contacts':
		$page_title = 'Контакты';
		require 'pages/html/contacts.html';
		break;

	case '/post/'.$url_part[2]:
		require 'pages/post.php';
		break;

	case '/logout':
		unset($_SESSION['login']);
		unset($_SESSION['nickname']);
		unset($_SESSION['user_group']);
		header('Location: /');
		break;

	case '/admin/'.$url_part[2]:
		switch ($url_part[2]) {
			case '':
				$page_title = 'Админка';
				require 'pages/admin-index.php';
				break;

			case 'new-post':
				$page_title = 'Новый пост';
				require 'pages/admin-new-post.php';
				break;

			case 'edit-post':
				$page_title = 'Редактирование поста';
				require 'pages/admin-edit-post.php';
				break;

			case 'posts':
				$page_title = 'Все посты';
				require 'pages/admin-posts.php';
				break;

			case 'users':
				$page_title = 'Все пользователи';
				require 'pages/admin-users.php';
				break;

			case 'comments':
				$page_title = 'Все комментарии';
				require 'pages/admin-comments.php';
				break;
			
			default:
				require 'pages/404.php';
				break;
		}
		break;
	
	default:
		require 'pages/404.php';
		break;
}