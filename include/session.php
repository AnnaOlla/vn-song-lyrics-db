<?php

final class Session
{
	public static function isCurrentUserModerator(): bool
	{
		return $_SESSION['user']['role'] === 'administrator';
	}
	
	public static function isCurrentUser(int|null $id): bool
	{
		if (isset($_SESSION['user']['id']))
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
	
	public static function canCurrentUserPost(): bool
	{
		return !in_array($_SESSION['user']['role'], ['visitor', 'violator'], true);
	}
	
	public static function canCurrentUserAddEntity(): array
	{
		if (Session::isCurrentUserVisitor())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\UserVisitor;
		}
		else if (Session::isCurrentUserViolator())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\UserViolator;
		}
		else
		{
			$canUserDoIt = true;
			$reason = '';
		}
		
		return [$canUserDoIt, $reason];
	}
	
	public static function canCurrentUserEditEntity(int|null $contributorId, string $entityStatus): array
	{
		if ($entityStatus === 'hidden' && !Session::isCurrentUserModerator())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\InfoHidden;
		}
		else if (!Session::isCurrentUser($contributorId) && !Session::isCurrentUserModerator())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\UserNotAuthor;
		}
		else if ($entityStatus === 'checked' && !Session::isCurrentUserModerator())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\InfoChecked;
		}
		else if (Session::isCurrentUserViolator())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\UserViolator;
		}
		else
		{
			$canUserDoIt = true;
			$reason = '';
		}
		
		return [$canUserDoIt, $reason];
	}
	
	public static function canCurrentUserDeleteEntity(int|null $contributorId, string $entityStatus): array
	{
		return Session::canCurrentUserEditEntity($contributorId, $entityStatus);
	}
	
	public static function canCurrentUserReportEntity(string $entityStatus)
	{
		if ($entityStatus === 'hidden' && !Session::isCurrentUserModerator())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\InfoHidden;
		}
		else
		{
			$canUserDoIt = true;
			$reason = '';
		}
		
		return [$canUserDoIt, $reason];
	}
	
	public static function canCurrentUserAddLyrics(string $entityStatus): array
	{
		if ($entityStatus === 'hidden' && !Session::isCurrentUserModerator())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\InfoHidden;
		}
		else if (Session::isCurrentUserVisitor())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\UserVisitor;
		}
		else if (Session::isCurrentUserViolator())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\UserViolator;
		}
		else
		{
			$canUserDoIt = true;
			$reason = '';
		}
		
		return [$canUserDoIt, $reason];
	}
	
	public static function canCurrentUserEditLyrics(int|null $contributorId, string $entityStatus, bool $songHasTranslations): array
	{
		if ($entityStatus === 'hidden' && !Session::isCurrentUserModerator())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\InfoHidden;
		}
		else if (!Session::isCurrentUser($contributorId) && !Session::isCurrentUserModerator())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\UserNotAuthor;
		}
		else if ($entityStatus === 'checked' && !Session::isCurrentUserModerator())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\InfoChecked;
		}
		else if ($songHasTranslations && !Session::isCurrentUserModerator())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\SongHasTranslations;
		}
		else if (Session::isCurrentUserVisitor())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\UserVisitor;
		}
		else if (Session::isCurrentUserViolator())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\UserViolator;
		}
		else
		{
			$canUserDoIt = true;
			$reason = '';
		}
		
		return [$canUserDoIt, $reason];
	}
	
	public static function canCurrentUserEditTranslation(int|null $contributorId, string $entityStatus, bool $isSongOriginal): array
	{
		if ($entityStatus === 'hidden' && !Session::isCurrentUserModerator())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\InfoHidden;
		}
		else if (!Session::isCurrentUser($contributorId) && !Session::isCurrentUserModerator())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\UserNotAuthor;
		}
		else if (!$isSongOriginal)
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\NotOriginalSong;
		}
		else if (Session::isCurrentUserViolator())
		{
			$canUserDoIt = false;
			$reason = \Localization\Tooltip\UserViolator;
		}
		else
		{
			$canUserDoIt = true;
			$reason = '';
		}
		
		return [$canUserDoIt, $reason];
	}
	
	public static function canCurrentUserDeleteTranslation(int|null $contributorId, string $entityStatus, bool $isSongOriginal): array
	{
		return Session::canCurrentUserEditTranslation($contributorId, $entityStatus, $isSongOriginal);
	}
}
