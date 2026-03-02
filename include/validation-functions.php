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
