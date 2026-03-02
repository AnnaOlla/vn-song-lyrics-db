<?php

abstract class Controller
{
    protected $model;
    protected $view;
	protected $language;
	
	public function __construct(string $language)
	{
		$this->language = $language;
	}
	
	final public function isEmailValid(string $email): bool
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	final protected function isLatinAlphabetAndNumbers(string $value): bool
	{
		// Must contain at least one character
		// utf8 on
		return (bool)preg_match('/^[a-zA-Z0-9]+$/u', $value);
	}
	
	final protected function isPrintableAscii(string $value): bool
	{
		// Printable characters are from space to tilda
		// Must contain at least one character
		// utf8 on
		return (bool)preg_match('/^[ -~]+$/u', $value);
	}
	
	final protected function getNullableFile(array|null $file): array|null
	{
		if (is_null($file))
			return null;
		
		if ($file['error'] === UPLOAD_ERR_NO_FILE)
			return null;
		
		return $file;
	}
	
	final protected function trimNullableString(string|null $value): string|null
	{
		if (is_null($value))
			return null;
		
		$value = mb_trim($value);
		
		if ($value === '')
			return null;
		
		return $value;
	}
	
	final protected function trimNullableStringArray(array $values): array
	{
		for ($i = 0; $i < count($values); $i++)
			$values[$i] = $this->trimNullableString($values[$i]);
		
		return $values;
	}
	
	final protected function trimNullableText(string|null $multilineString): string|null
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
	
	final protected function parseNullableVndbId(string|null $link, string $entityFirstLetter): int|null
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
				return convertToInteger($matches[1], 1);
		}
		
		return null;
	}
	
	final protected function parseNullableVgmdbId(string|null $link, string $entityName): int|null
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
				return convertToInteger($matches[1], 1);
		}
		
		return null;
	}
	
	final protected function parseNullableInteger(string|null $value, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): int|null
	{
		if (is_null($value))
			return null;
		
		return convertToInteger($value, $min, $max);
	}
	
	final protected function parseNullableIntegerArray(array $values, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): array
	{
		for ($i = 0; $i < count($values); $i++)
			$values[$i] = convertToInteger($values[$i], $min, $max);
		
		return $values;
	}
	
	final protected function removeNullValues(array $values): array
	{
		return array_diff($values, [null]);
	}
	
	final protected function haveArraysSameLength(array ...$arrays): bool|null
	{
		// Idk what I am supposed to return in this case
		if (count($arrays) === 0)
			return null;
		
		$length = count($arrays[0]);
		
		for ($i = 1; $i < count($arrays); $i++)
		{
			if (count($arrays[$i]) !== $length)
				return false;
		}
		
		return true;
	}
}
