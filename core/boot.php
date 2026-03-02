<?php

require_once 'core/router.php';

require_once 'include/enums.php';
require_once 'include/session-functions.php';
require_once 'include/validation-functions.php';

session_start();

if (!isset($_SESSION['user']))
	$_SESSION['user']['role'] = 'visitor';

/*
if (!isset($_SESSION['language']))
{
	if (isset($_COOKIE['language']) && in_array($_COOKIE['language'], ['en', 'ru', 'ja'], true))
	{
		$_SESSION['language'] = $_COOKIE['language'];
	}
	else
	{
		$languages = detectUserLanguages();
		$_SESSION['language'] = getSuitableLanguage($languages);
	}
}

setcookie('language', $_SESSION['language'], time() + 60 * 60 * 24 * 30);
*/