<?php

try
{
	require_once 'core/boot.php';
	
	Config::initialize();
	Router::run();
}
catch (Throwable $e)
{
	error_log($e);
	http_response_code(500);
}
