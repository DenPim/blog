<?php

if (!$_SESSION['login'] || $_SESSION['user_group'] !== 'admin') {
	require '404.php';
}

require 'html/admin-index.html';