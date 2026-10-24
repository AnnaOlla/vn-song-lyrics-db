<?php

final class Http
{
	public static function buildPath(string ...$pathParts): string
	{
		for ($i = 0; $i < count($pathParts); $i++)
			$pathParts[$i] = rawurlencode($pathParts[$i]);
		
		return '/'.implode('/', $pathParts);
	}
	
	public static function buildQuery(array|null $keysToValues, bool $dropNullValues = true): string
	{
		if ($dropNullValues)
			$keysToValues = array_filter($keysToValues, function ($value) { return $value !== null; });
		
		if (!$keysToValues)
			return '';
		
		return '?'.http_build_query($keysToValues);
	}
	
	public static function getLastVisitedPage(string|null $fallback = null): string
	{
		if ($fallback === null)
			$fallback = '';
		
		if (!isset($_SERVER['HTTP_REFERER']))
			return $fallback;
		
		$referrer = \Uri\Rfc3986\Uri::parse($_SERVER['HTTP_REFERER']);
		$request  = \Uri\Rfc3986\Uri::parse($_SERVER['REQUEST_URI']);
		
		if ($referrer->getHost() !== $_SERVER['HTTP_HOST'])
			return $fallback;
		
		if ($referrer->getPath() === null)
			return $fallback;
		
		if ($referrer->getPath() === $request->getPath())
			return $fallback;
		
		if ($referrer->getQuery() === null)
			return $referrer->getPath();
		
		return $referrer->getPath().'?'.$referrer->getQuery();
	}
}
