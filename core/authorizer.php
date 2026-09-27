<?php

final class Authorizer
{
	private const BLOCKED_IPS_FILENAME          = 'blockings/.blocked-ips.txt';
	private const BLOCKED_USER_AGENTS_FILENAME  = 'blockings/.blocked-user-agents.txt';
	private const BLOCKED_REQUESTS_FILENAME     = 'blockings/.blocked-requests.txt';
	
	private const RATE_LIMIT_WINDOW         = 10;
	private const RATE_LIMIT_COUNT          = 20;
	private const RATE_LIMIT_COUNT_TO_BLOCK = 40;
	
	public static function startSession(): void
	{
		session_start();

		if (!isset($_SESSION['user']))
			$_SESSION['user']['role'] = 'visitor';

		if (!isset($_SESSION['rateLimit']))
			$_SESSION['rateLimit'] = new SplDoublyLinkedList();
	}
	
	public static function endSession(): void
	{
		session_unset();
		setcookie(session_name(), session_id(), time() - 60 * 60 * 60 * 24);
		session_destroy();
	}
	
	public static function isMaintenanceModeActive(): bool
	{
		$fileName = Configuration::getMaintenanceModeFileName();
		return file_exists($fileName);
	}
	
	public static function isCurrentIpBlocked(): bool
	{
		$blockedIps = new SplFileObject(self::BLOCKED_IPS_FILENAME);
		$blockedIps->setFlags(SplFileObject::DROP_NEW_LINE);
		
		foreach ($blockedIps as $blockedIp)
		{
			if ($_SERVER['REMOTE_ADDR'] === $blockedIp)
				return true;
		}
		
		return false;
	}
	
	public static function isRequestForbidden(): bool
	{
		$blockedRequests = new SplFileObject(self::BLOCKED_REQUESTS_FILENAME);
		$blockedRequests->setFlags(SplFileObject::DROP_NEW_LINE);
		
		foreach ($blockedRequests as $blockedRequest)
		{
			if (str_contains($_SERVER['REQUEST_URI'], $blockedRequest))
				return true;
		}
		
		return false;
	}
	
	public static function isCurrentUserAgentBlocked(): bool
	{
		$blockedUserAgents = new SplFileObject(self::BLOCKED_USER_AGENTS_FILENAME);
		$blockedUserAgents->setFlags(SplFileObject::DROP_NEW_LINE);
		
		foreach ($blockedUserAgents as $blockedUserAgent)
		{
			if (str_contains($_SERVER['HTTP_USER_AGENT'], $blockedUserAgent))
				return true;
		}
		
		return false;
	}
	
	public static function blockCurrentIp(): void
	{
		$blockedIps = new SplFileObject(self::BLOCKED_IPS_FILENAME, 'a');
		$blockedIps->fwrite($_SERVER['REMOTE_ADDR'].PHP_EOL);
	}
	
	public static function updateRateLimit(): void
	{
		$now = time();
		
		while (!$_SESSION['rateLimit']->isEmpty() && $now - $_SESSION['rateLimit']->bottom() >= self::RATE_LIMIT_WINDOW)
			$_SESSION['rateLimit']->shift();
		
		if (!self::isCurrentUserAdministrator())
			$_SESSION['rateLimit']->push($now);
	}
	
	public static function isRateLimitExceeded(): bool
	{
		return $_SESSION['rateLimit']->count() > self::RATE_LIMIT_COUNT;
	}
	
	public static function isRateLimitExceededToBlock(): bool
	{
		return $_SESSION['rateLimit']->count() > self::RATE_LIMIT_COUNT_TO_BLOCK;
	}
	
	public static function isCurrentUser(int|null $id): bool
	{
		if (isset($_SESSION['user']['id']) && isset($id))
			return $_SESSION['user']['id'] === $id;
		
		return false;
	}
	
	public static function isCurrentUserVisitor(): bool
	{
		return $_SESSION['user']['role'] === 'visitor';
	}
	
	public static function isCurrentUserViolator(): bool
	{
		return $_SESSION['user']['role'] === 'violator';
	}
	
	public static function isCurrentUserContributor(): bool
	{
		return $_SESSION['user']['role'] === 'user';
	}
	
	public static function isCurrentUserAdministrator(): bool
	{
		return $_SESSION['user']['role'] === 'administrator';
	}
}
