<?php

function getPdo(string $userRole): PDO
{
	$settings =
	[
		'dbName' => '',
		'dbHost' => '',
		'dbUser' => $userRole,
		'dbPswd' => null,
		'dbChar' => 'utf8mb4'
	];
	
	switch ($userRole)
	{
		case 'visitor':
			$settings['dbPswd'] = '';
			break;
			
		case 'violator':
			$settings['dbPswd'] = '';
			break;
			
		case 'user':
			$settings['dbPswd'] = '';
			break;
			
		case 'administrator':
			$settings['dbPswd'] = '';
			break;
			
		default:
			throw DatabaseLogicException('Database connection problem', get_defined_vars());
	}
	
	$dsn = 'mysql:dbname='.$settings['dbName'].';host='.$settings['dbHost'].';charset='.$settings['dbChar'];
	
	$pdo = new PDO($dsn, $settings['dbUser'], $settings['dbPswd']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	return $pdo;
}
