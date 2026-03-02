<?php

// This file must not be included in git systems.

/**
 *  Creates an instance of PDO for a certain user role
 *  Must never be called outside constuctors of children of Model
 *  @userRole: role from the DB
 */
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
			/*
		case 'contributor':
			$settings['dbPswd'] = '';
			break;
			
		case 'translator':
			$settings['dbPswd'] = '';
			break;
			
		case 'moderator':
			$settings['dbPswd'] = '';
			break;
			
		case 'supermoderator':
			$settings['dbPswd'] = '';
			break;
			*/
		case 'administrator':
			$settings['dbPswd'] = '';
			break;
			
		default:
			echo 'A critical server error occured. Please, visit us later.';
			throw Exception('Database connection problem: '.$userRole);
	}
	
	$dsn = 'mysql:dbname='.$settings['dbName'].';host='.$settings['dbHost'].';charset='.$settings['dbChar'];
	$pdo = new PDO($dsn, $settings['dbUser'], $settings['dbPswd']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	return $pdo;
}

/**
 *  Encrypts a string
 *  Intended use: encrypt sender's id and keep it unique
 *  
 *  @param string $data: string to encrypt
 *
 *  @return encrypted string
 */
function encryptData(string $data): string
{
	return $data;
}

/**
 *  Creates a token for account verification
 *  
 *  @param string $data: any string for randomization
 *
 *  @return 
 */
function createToken(string $data): string
{
	return $data;
}
