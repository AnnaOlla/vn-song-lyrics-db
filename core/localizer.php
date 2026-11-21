<?php

final class Localizer
{
	public const ACCEPTED_LANGUAGES = ['en', 'ru', 'ja'];
	public const DEFAULT_LANGUAGE   = 'en';
	
	private static $language     = '';
	private static $localization = [];
	
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
	
	public static function setLocale(string $language): void
	{
		if (!in_array($language, self::ACCEPTED_LANGUAGES, true))
			throw new HttpNotAcceptable406('Language '.$language.' is not supported');
		
		self::$language = $language;
	}
	
	private static function loadLocalizationFile(string $role): array
	{
		$path = 'localization/'.self::$language.'/'.$role.'-localization.php';
		
		if (!file_exists($path))
			return [];
		
		return (require_once $path);
	}
	
	public static function localize(string $complexKey, array $htmlReplacements = []): string
	{
		$keys = explode('.', $complexKey);
		
		$role = $keys[0];
		$role = str_replace('_', '-', $role);
		
		if (!array_key_exists($role, self::$localization))
			self::$localization[$role] = self::loadLocalizationFile($role);
		
		$text = self::$localization;
		
		foreach ($keys as $key)
		{
			if (is_array($text) && array_key_exists($key, $text))
				$text = $text[$key];
			else
				return $complexKey;
		}
		
		if (!is_string($text))
			return $complexKey;
		
		foreach ($htmlReplacements as $key => $value)
			$text = str_replace('$'.$key, $value, $text);
		
		return $text;
	}

	public static function localizeLanguageKey(): string
	{
		return 'language_'.self::$language.'_name';
	}
	
	public static function localizeLanguageName(array $entity): string
	{
		return $entity['language_'.self::$language.'_name'];
	}

	public static function localizeTranslationNumber(int $number): string
	{
		if (self::$language === 'ja')
			mb_convert_kana('#'.$number, 'A');
		else
			return ' #'.$number;
	}
}
