<?php

require_once 'core/router.php';

require_once 'include/enums.php';
require_once 'include/juliamo-captcha.php';
require_once 'include/session-functions.php';
require_once 'include/validation-functions.php';

session_start();

if (!isset($_SESSION['user']))
	$_SESSION['user']['role'] = 'visitor';
