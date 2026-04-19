<?php

final class Validation
{
	public static function isNullOrEmpty(mixed $value): bool
	{
		return ($value === null || $value === '' || $value === []);
	}

	public static function haveNullOrEmpty(mixed ...$values): bool
	{
		foreach ($values as $value)
		{
			if (Validation::isNullOrEmpty($value))
				return true;
		}
		return false;
	}
	
	public static function isEmailValid(string $email): bool
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	public static function isLatinAlphabetAndNumbers(string $value): bool
	{
		// Must contain at least one character
		// utf8 on
		return (bool)preg_match('/^[a-zA-Z0-9]+$/u', $value);
	}

	public static function isPrintableAscii(string $value): bool
	{
		// Printable characters are from space to tilda
		// Must contain at least one character
		// utf8 on
		return (bool)preg_match('/^[ -~]+$/u', $value);
	}

	public static function haveArraysSameLength(array ...$arrays): bool|null
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
