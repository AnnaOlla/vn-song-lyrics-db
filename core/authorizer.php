<?php

final class AccessManager
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
		
		if (!self::isUserAdministrator())
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
	
	// ---------------- //
	// Function-Helpers //
	// ---------------- //
	
	public static function isEntityUnchecked(array $entity): bool
	{
		return $entity['status'] === 'unchecked';
	}
	
	public static function isEntityChecked(array $entity): bool
	{
		return $entity['status'] === 'checked';
	}
	
	public static function isEntityHidden(array $entity): bool
	{
		return $entity['status'] === 'hidden';
	}
	
	public static function isUser(int|null $id): bool
	{
		if (isset($_SESSION['user']['id']) && isset($id))
			return $_SESSION['user']['id'] === $id;
		
		return false;
	}
	
	public static function isUserVisitor(): bool
	{
		return $_SESSION['user']['role'] === 'visitor';
	}
	
	public static function isUserViolator(): bool
	{
		return $_SESSION['user']['role'] === 'violator';
	}
	
	public static function isUserContributor(): bool
	{
		return $_SESSION['user']['role'] === 'user';
	}
	
	public static function isUserAdministrator(): bool
	{
		return $_SESSION['user']['role'] === 'administrator';
	}
	
	// -------------------------------------------------------------- //
	// Viewing: if the entry is hidden, block its button or throw 451 //
	// -------------------------------------------------------------- //
	
	private static function getStateForUserToViewEntity(array $entity): AccessState
	{
		if (self::isUserAdministrator())
			return AccessState::Ok;
		
		if (self::isEntityHidden($entity))
			return AccessState::EntityIsHiddenError;
		
		return AccessState::Ok;
	}
	
	public static function getStateForUserToViewGame(array $game): AccessState
	{
		return self::getStateForUserToViewEntity($game);
	}
	
	public static function getStateForUserToViewAlbum(array $album): AccessState
	{
		return self::getStateForUserToViewEntity($album);
	}
	
	public static function getStateForUserToViewArtist(array $artist): AccessState
	{
		return self::getStateForUserToViewEntity($artist);
	}
	
	public static function getStateForUserToViewCharacter(array $character): AccessState
	{
		return self::getStateForUserToViewEntity($character);
	}
	
	public static function getStateForUserToViewSong(array $song): AccessState
	{
		return self::getStateForUserToViewEntity($song);
	}
	
	public static function getStateForUserToViewLyrics(array $song): AccessState
	{
		return self::getStateForUserToViewEntity($song);
	}
	
	public static function getStateForUserToViewTranslation(array $translation): AccessState
	{
		return self::getStateForUserToViewEntity($translation);
	}
	
	// ----------------------------------------------------------------- //
	// Reporting: if the entry is hidden, block its button or throw 451  //
	// ----------------------------------------------------------------- //
	
	private static function getStateForUserToReportEntity(array $entity): AccessState
	{
		return self::getStateForUserToViewEntity($entity);
	}
	
	public static function getStateForUserToReportGame(array $game)
	{
		return self::getStateForUserToReportEntity($game);
	}
	
	public static function getStateForUserToReportAlbum(array $album)
	{
		return self::getStateForUserToReportEntity($album);
	}
	
	public static function getStateForUserToReportArtist(array $artist)
	{
		return self::getStateForUserToReportEntity($artist);
	}
	
	public static function getStateForUserToReportCharacter(array $character)
	{
		return self::getStateForUserToReportEntity($character);
	}
	
	public static function getStateForUserToReportSong(array $song)
	{
		return self::getStateForUserToReportEntity($song);
	}
	
	public static function getStateForUserToReportLyrics(array $song)
	{
		return self::getStateForUserToReportEntity($song);
	}
	
	public static function getStateForUserToReportTranslation(array $translation)
	{
		return self::getStateForUserToReportEntity($translation);
	}
	
	// ---------------------------------------------------------------------- //
	// Adding: if the user is not allowed, then block the button or throw 403 //
	// ---------------------------------------------------------------------- //
	
	private static function getStateForUserToAddEntity(): AccessState
	{
		if (self::isUserAdministrator())
			return AccessState::Ok;
		
		if (self::isUserVisitor())
			return AccessState::AgentIsVisitorError;
		
		if (self::isUserViolator())
			return AccessState::AgentIsViolatorError;
		
		return AccessState::Ok;
	}
	
	public static function getStateForUserToAddGame(): AccessState
	{
		return self::getStateForUserToAddEntity();
	}
	
	public static function getStateForUserToAddAlbum(): AccessState
	{
		return self::getStateForUserToAddEntity();
	}
	
	public static function getStateForUserToAddArtist(): AccessState
	{
		return self::getStateForUserToAddEntity();
	}
	
	public static function getStateForUserToAddCharacter(): AccessState
	{
		return self::getStateForUserToAddEntity();
	}
	
	public static function getStateForUserToAddSong(): AccessState
	{
		return self::getStateForUserToAddEntity();
	}
	
	public static function getStateForUserToAddLyrics(): AccessState
	{
		return self::getStateForUserToAddEntity();
	}
	
	public static function getStateForUserToAddTranslation(): AccessState
	{
		return self::getStateForUserToAddEntity();
	}
	
	// -------------------------------------------------------------------------- //
	// Editing: if the user is not the author, then block the button or throw 403 //
	// -------------------------------------------------------------------------- //
	
	private static function getStateForUserToEditEntity(array $entity): AccessState
	{
		if (self::isUserAdministrator())
			return AccessState::Ok;
		
		if (self::isEntityChecked($entity))
			return AccessState::EntityIsCheckedError;
		
		if (!self::isUser($entity['user_added_id']))
			return AccessState::AgentIsNotAuthorError;
		
		if (self::isUser($entity['user_added_id']) && self::isUserViolator())
			return AccessState::AgentIsViolatorError;
		
		return AccessState::Ok;
	}
	
	public static function getStateForUserToEditGame(array $game): AccessState
	{
		return self::getStateForUserToEditEntity($game);
	}
	
	public static function getStateForUserToEditAlbum(array $album): AccessState
	{
		return self::getStateForUserToEditEntity($album);
	}
	
	public static function getStateForUserToEditArtist(array $artist): AccessState
	{
		return self::getStateForUserToEditEntity($artist);
	}
	
	public static function getStateForUserToEditCharacter(array $character): AccessState
	{
		return self::getStateForUserToEditEntity($character);
	}
	
	public static function getStateForUserToEditSong(array $song): AccessState
	{
		return self::getStateForUserToEditEntity($song);
	}
	
	public static function getStateForUserToEditLyrics(array $song): AccessState
	{
		return self::getStateForUserToEditEntity($song);
	}
	
	public static function getStateForUserToEditTranslation(array $translation): AccessState
	{
		if (self::isUserAdministrator())
			return AccessState::Ok;
		
		if (!self::isUser($translation['user_added_id']))
			return AccessState::AgentIsNotAuthorError;
		
		if (self::isUser($translation['user_added_id']) && self::isUserViolator())
			return AccessState::AgentIsViolatorError;
		
		return AccessState::Ok;
	}
	
	// ---------------------------------------------------------------------------- //
	// Deleting: if the user is not the author, then block the button or throw 403  //
	// ---------------------------------------------------------------------------- //
	
	private static function getStateForUserToDeleteEntity(array $entity): AccessState
	{
		return self::getStateForUserToEditEntity($entity);
	}
	
	public static function getStateForUserToDeleteGame(array $game): AccessState
	{
		return self::getStateForUserToDeleteEntity($game);
	}
	
	public static function getStateForUserToDeleteAlbum(array $album): AccessState
	{
		return self::getStateForUserToDeleteEntity($album);
	}
	
	public static function getStateForUserToDeleteArtist(array $artist): AccessState
	{
		return self::getStateForUserToDeleteEntity($artist);
	}
	
	public static function getStateForUserToDeleteCharacter(array $character): AccessState
	{
		return self::getStateForUserToDeleteEntity($character);
	}
	
	public static function getStateForUserToDeleteSong(array $song): AccessState
	{
		return self::getStateForUserToDeleteEntity($song);
	}
	
	public static function getStateForUserToDeleteLyrics(array $song): AccessState
	{
		return self::getStateForUserToDeleteEntity($song);
	}
	
	public static function getStateForUserToDeleteTranslation(array $translation): AccessState
	{
		return self::getStateForUserToEditTranslation($translation);
	}
}
