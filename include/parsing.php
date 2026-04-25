<?php

final class Parsing
{
	public static function convertToInteger(string $value, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): int|null
	{
		$options =
		[
			'options' =>
			[
				'min_range' => $min,
				'max_range' => $max
			],
			'flags' => FILTER_NULL_ON_FAILURE
		];
		
		return filter_var($value, FILTER_VALIDATE_INT, $options);
	}
	
	public static function getNullableFile(array|null $file): array|null
	{
		if (is_null($file))
			return null;
		
		if ($file['error'] === UPLOAD_ERR_NO_FILE)
			return null;
		
		return $file;
	}

	public static function trimNullableString(string|null $value): string|null
	{
		if (is_null($value))
			return null;
		
		$value = mb_trim($value);
		
		if ($value === '')
			return null;
		
		return $value;
	}

	public static function trimNullableStringArray(array $values): array
	{
		for ($i = 0; $i < count($values); $i++)
			$values[$i] = Parsing::trimNullableString($values[$i]);
		
		return $values;
	}

	public static function trimNullableText(string|null $multilineString): string|null
	{
		if (is_null($multilineString))
			return null;
		
		$lines = explode("\n", $multilineString);
		
		// Important: it is allowed that the text may have empty lines
		for ($i = 0; $i < count($lines); $i++)
			$lines[$i] = mb_trim($lines[$i]);
		
		$multilineString = implode("\n", $lines);
		
		return $multilineString;
	}

	public static function parseNullableVndbId(string|null $link, string $entityFirstLetter): int|null
	{
		if (is_null($link))
			return null;
		
		// A valid id has no leading zeros => starts with [1-9]
		// Other numbers may be any => [0-9]
		// Parentheses () capture the group of numbers
		
		$patterns =
		[
			'/^https:\/\/vndb.org\/'.$entityFirstLetter.'([1-9][0-9]*)$/u',
			'/^http:\/\/vndb.org\/'.$entityFirstLetter.'([1-9][0-9]*)$/u',
			'/^vndb.org\/'.$entityFirstLetter.'([1-9][0-9]*)$/u',
			'/^'.$entityFirstLetter.'([1-9][0-9]*)$/u',
			'/^([1-9][0-9]*)$/u'
		];
		
		foreach ($patterns as $pattern)
		{
			if (preg_match($pattern, $link, $matches, PREG_UNMATCHED_AS_NULL))
				return Parsing::convertToInteger($matches[1], 1);
		}
		
		return null;
	}

	public static function parseNullableVgmdbId(string|null $link, string $entityName): int|null
	{
		if (is_null($link))
			return null;
		
		// A valid id has no leading zeros => starts with [1-9]
		// Other numbers may be any => [0-9]
		// Parentheses () capture the group of numbers
		
		$patterns =
		[
			'/^https:\/\/vgmdb.net\/'.$entityName.'\/([1-9][0-9]*)$/u',
			'/^http:\/\/vgmdb.net\/'.$entityName.'\/([1-9][0-9]*)$/u',
			'/^vgmdb.net\/'.$entityName.'\/([1-9][0-9]*)$/u',
			'/^'.$entityName.'\/([1-9][0-9]*)$/u',
			'/^([1-9][0-9]*)$/u'
		];
		
		foreach ($patterns as $pattern)
		{
			if (preg_match($pattern, $link, $matches, PREG_UNMATCHED_AS_NULL))
				return Parsing::convertToInteger($matches[1], 1);
		}
		
		return null;
	}

	public static function parseNullableInteger(string|null $value, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): int|null
	{
		if (is_null($value))
			return null;
		
		return Parsing::convertToInteger($value, $min, $max);
	}

	public static function parseNullableIntegerArray(array $values, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): array
	{
		for ($i = 0; $i < count($values); $i++)
			$values[$i] = Parsing::convertToInteger($values[$i], $min, $max);
		
		return $values;
	}

	public static function removeNullValues(array $values): array
	{
		return array_diff($values, [null]);
	}

	public static function cleanSearchString(string|null $string): string|null
	{
		if (is_null($string))
			return null;
		
		// The user may enter a name that was transliterated
		// with a different system from what is used here
		// The difference is usually only spaces
		
		$string = preg_replace('/\s+/u', '', $string);
		return $string;
	}
}
