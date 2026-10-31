<?php

require_once 'core/authorizer.php';
require_once 'core/configuration.php';
require_once 'core/localizer.php';
require_once 'core/logger.php';
require_once 'core/router.php';

require_once 'include/entity-manager.php';
require_once 'include/enums.php';
require_once 'include/exceptions.php';
require_once 'include/http.php';
require_once 'include/image-procession.php';
require_once 'include/juliamo-captcha.php';
require_once 'include/parsing.php';
require_once 'include/role-manager.php';
require_once 'include/validation.php';

if (file_exists('include/.cryptography-production.php'))
	require_once 'include/.cryptography-production.php';
else
	require_once 'include/cryptography-development.php';
