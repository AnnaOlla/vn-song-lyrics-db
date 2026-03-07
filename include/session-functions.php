<?php

function buildInternalLink(string ...$linkParts): string
{
	for ($i = 0; $i < count($linkParts); $i++)
		$linkParts[$i] = rawurlencode($linkParts[$i]);
	
	return '/'.implode('/', $linkParts);
}

function attachGetParameters(string $link, array $parameters): string
{
	$keyToValue = [];
	
	foreach ($parameters as $key => $value)
		$keyToValue[] = $key.'='.$value;
	
	return $link.'?'.implode("&", $keyToValue);
}

function detectUserLanguages(): array|null
{
	// My example:
	// en-GB,en;q=0.9,ru;q=0.8,fi;q=0.7,ja;q=0.6
	$httpAcceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
	
	if (isNullOrEmpty($httpAcceptLanguage))
		return null;
	
	// First, divide by commas: thus, we keep 'language;value' to divide further
	$preferences = explode(',', $httpAcceptLanguage);
	$languages = [];
	
	foreach ($preferences as $preference)
	{
		$parts = explode(';', $preference);
		
		if (count($parts) !== 2)
			continue;
		
		// en-GB,en => en
		$language = mb_substr($parts[0], 0, 2);
		
		// q=0.9 => 0.9 as float
		$weight = (float)mb_substr($parts[1], 2);
		
		$languages[$language] = $weight;
	}
	
	return $languages;
}

function getSuitableLanguage(array|null $languages): string
{
	if (is_null($languages))
		return 'en';
	
	foreach ($languages as $language => $weight)
	{
		if (in_array($language, ['ru', 'en', 'ja'], true))
			return $language;
	}
	
	return 'en';
}

function isCurrentUserModerator(): bool
{
	//return in_array($_SESSION['user']['role'], ['moderator', 'supermoderator', 'administrator'], true);
	return $_SESSION['user']['role'] === 'administrator';
}

function isCurrentUser(int|null $id): bool
{
	if (isset($_SESSION['user']['id']))
		return $_SESSION['user']['id'] === $id;
	
	return false;
}

function isCurrentUserVisitor(): bool
{
	return $_SESSION['user']['role'] === 'visitor';
}

function isCurrentUserViolator(): bool
{
	return $_SESSION['user']['role'] === 'violator';
}

function canCurrentUserPost(): bool
{
	return !in_array($_SESSION['user']['role'], ['visitor', 'violator'], true);
}

function canCurrentUserAddEntity(): array
{
	if (isCurrentUserVisitor())
	{
		$canUserDoIt = false;
		$reason = \Localization\Tooltip\UserVisitor;
	}
	else if (isCurrentUserViolator())
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

function canCurrentUserEditEntity(int|null $entityContributorId, string $entityStatus): array
{
	if ($entityStatus === 'hidden' && !isCurrentUserModerator())
	{
		$canUserDoIt = false;
		$reason = \Localization\Tooltip\InfoHidden;
	}
	else if (!isCurrentUser($entityContributorId) && !isCurrentUserModerator())
	{
		$canUserDoIt = false;
		$reason = \Localization\Tooltip\UserNotAuthor;
	}
	else if ($entityStatus === 'checked' && !isCurrentUserModerator())
	{
		$canUserDoIt = false;
		$reason = \Localization\Tooltip\InfoChecked;
	}
	else if (isCurrentUserViolator())
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

function canCurrentUserDeleteEntity(int|null $entityContributorId, string $entityStatus): array
{
	return canCurrentUserEditEntity($entityContributorId, $entityStatus);
}

function canCurrentUserReportEntity(string $entityStatus)
{
	if ($entityStatus === 'hidden' && !isCurrentUserModerator())
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

function canCurrentUserAddLyrics(string $entityStatus): array
{
	if ($entityStatus === 'hidden' && !isCurrentUserModerator())
	{
		$canUserDoIt = false;
		$reason = \Localization\Tooltip\InfoHidden;
	}
	else if (isCurrentUserVisitor())
	{
		$canUserDoIt = false;
		$reason = \Localization\Tooltip\UserVisitor;
	}
	else if (isCurrentUserViolator())
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

function canCurrentUserEditLyrics(int|null $entityContributorId, string $entityStatus, bool $songHasTranslations): array
{
	if ($entityStatus === 'hidden' && !isCurrentUserModerator())
	{
		$canUserDoIt = false;
		$reason = \Localization\Tooltip\InfoHidden;
	}
	else if (!isCurrentUser($entityContributorId) && !isCurrentUserModerator())
	{
		$canUserDoIt = false;
		$reason = \Localization\Tooltip\UserNotAuthor;
	}
	else if ($entityStatus === 'checked' && !isCurrentUserModerator())
	{
		$canUserDoIt = false;
		$reason = \Localization\Tooltip\InfoChecked;
	}
	else if ($songHasTranslations && !isCurrentUserModerator())
	{
		$canUserDoIt = false;
		$reason = \Localization\Tooltip\SongHasTranslations;
	}
	else if (isCurrentUserVisitor())
	{
		$canUserDoIt = false;
		$reason = \Localization\Tooltip\UserVisitor;
	}
	else if (isCurrentUserViolator())
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

function canCurrentUserEditTranslation(int|null $entityContributorId, string $entityStatus, bool $isSongOriginal): array
{
	if ($entityStatus === 'hidden' && !isCurrentUserModerator())
	{
		$canUserDoIt = false;
		$reason = \Localization\Tooltip\InfoHidden;
	}
	else if (!isCurrentUser($entityContributorId) && !isCurrentUserModerator())
	{
		$canUserDoIt = false;
		$reason = \Localization\Tooltip\UserNotAuthor;
	}
	else if (!$isSongOriginal)
	{
		$canUserDoIt = false;
		$reason = \Localization\Tooltip\NotOriginalSong;
	}
	else if (isCurrentUserViolator())
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

function canCurrentUserDeleteTranslation(int|null $entityContributorId, string $entityStatus, bool $isSongOriginal): array
{
	return canCurrentUserEditTranslation($entityContributorId, $entityStatus, $isSongOriginal);
}
