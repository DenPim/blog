<?php 

session_start();

// Medoo for database and config

require 'Medoo.php';
require 'config.php';

// Database connection

use Medoo\Medoo;

$DB = new Medoo([
	'type' => 'mysql',
	'host' => DB_HOST,
	'database' => DB_NAME,
	'username' => DB_USER,
	'password' => DB_PASSWORD,
]);

// Classes

require 'class/users.php';
require 'class/posts.php';
require 'class/comments.php';

require 'routes.php';