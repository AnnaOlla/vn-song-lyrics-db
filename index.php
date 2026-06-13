<?php

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
