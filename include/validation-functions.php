<?php

function isNullOrEmpty(mixed $value): bool
{
	return ($value === null || $value === '' || $value === []);
}

function haveNullOrEmpty(mixed ...$values): bool
{
	foreach ($values as $value)
	{
		if (isNullOrEmpty($value))
			return true;
	}
	return false;
}

function convertToInteger(string $value, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): int|null
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

function isEmailValid(string $email): bool
{
	return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isLatinAlphabetAndNumbers(string $value): bool
{
	// Must contain at least one character
	// utf8 on
	return (bool)preg_match('/^[a-zA-Z0-9]+$/u', $value);
}

function isPrintableAscii(string $value): bool
{
	// Printable characters are from space to tilda
	// Must contain at least one character
	// utf8 on
	return (bool)preg_match('/^[ -~]+$/u', $value);
}

function haveArraysSameLength(array ...$arrays): bool|null
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
