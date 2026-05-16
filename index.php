<?php

session_start();

if (!isset($_SESSION['user']))
	$_SESSION['user']['role'] = 'visitor';

if (!isset($_SESSION['rateLimit']))
	$_SESSION['rateLimit'] = new SplDoublyLinkedList();

try
{
	require_once 'core/boot.php';
	Router::run();
}
catch (Throwable $e)
{
	error_log($e);
	http_response_code(500);
}
