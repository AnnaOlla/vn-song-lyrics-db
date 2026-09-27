<?php

final class Localizer
{
	public const ACCEPTED_LANGUAGES = ['en', 'ru', 'ja'];
	public const DEFAULT_LANGUAGE   = 'en';
	
	public static function detectUserLanguages(): array
	{
		// My example: en-GB,en;q=0.9,ru;q=0.8,fi;q=0.7,ja;q=0.6
		// en-GB must be deduced to q=1.0
		// en;q=0.9 must be dropped
		
		$preferences = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
		$languages = [];
		
		foreach ($preferences as $preference)
		{
			// Split code and value
			$parts    = explode(';', $preference);
			
			// Strip country code if exists
			$language = explode('-', $parts[0])[0];
			
			// Deduce q=1.0 if parts[1] does not exist
			$weight   = explode('=', $parts[1] ?? 'q=1.0')[1];
			
			// Assign and avoid collision (same language, different countries)
			$languages[$language] = $languages[$language] ?? (float)$weight;
		}
		
		return $languages;
	}
	
	public static function isAcceptedLanguage(string $language): string
	{
		return in_array($language, self::ACCEPTED_LANGUAGES, true);
	}
	
	public static function getSuitableLanguage(array $languages): string
	{
		foreach ($languages as $language => $weight)
		{
			if (self::isAcceptedLanguage($language))
				return $language;
		}
		
		return self::DEFAULT_LANGUAGE;
	}
}
