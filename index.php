<?php

try
{
	require_once 'core/boot.php';
	require_once 'core/config.php';
	require_once 'core/custom-exceptions.php';
	require_once 'core/router.php';
	
	Router::run();
}
catch (Throwable $e)
{
	error_log($e);
	http_response_code(500);
}
