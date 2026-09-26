<?php

final class Configuration
{
	private static $settings = null;
	private const USER_ROLES = ['visitor', 'violator', 'user', 'administrator'];
	
	public static function initialize(): void
	{
		self::$settings = parse_ini_file('.env', true);
	}
	
	public static function getPdo(string $userRole): PDO
	{
		if (!in_array($userRole, self::USER_ROLES, true))
			throw HttpInternalServerError500('Database connection problem', get_defined_vars());
		
		$type     = self::$settings['database']['type'];
		$name     = self::$settings['database']['name'];
		$host     = self::$settings['database']['host'];
		$login    = self::$settings['database']['login_'.$userRole];
		$password = self::$settings['database']['password_'.$userRole];
		$charset  = self::$settings['database']['character_set'];
		
		$connection = "{$type}:dbname={$name};host={$host};charset={$charset}";
		
		$pdo = new PDO($connection, $login, $password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		return $pdo;
	}
	
	public static function getPasswordSettings(): array
	{
		return self::$settings['password'];
	}
	
	public static function getHashSettings(): array
	{
		return self::$settings['hash'];
	}
}
