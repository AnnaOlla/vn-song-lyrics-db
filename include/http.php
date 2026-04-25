<?php

final class Http
{
	public static function buildInternalPath(string ...$uriParts): string
	{
		for ($i = 0; $i < count($uriParts); $i++)
			$uriParts[$i] = rawurlencode($uriParts[$i]);
		
		return '/'.implode('/', $uriParts);
	}
	
	public static function buildQueryParameters(array|null $keysToValues): string
	{
		if (!$keysToValues)
			return '';
		
		return '?'.http_build_query($keysToValues);
	}
	
	public static function getLastVisitedPath(string|null $pathIfNull = null): string
	{
		if (!$_SERVER['HTTP_REFERER'])
			return $pathIfNull;
		
		$host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
		
		if (is_null($host) || $host !== $_SERVER['HTTP_HOST'])
			return $pathIfNull;
		
		$path = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
		
		if (is_null($path))
			return $pathIfNull;
		
		return $path;
	}
	
	public static function getLastVisitedQuery(string|null $queryIfNull = null): string
	{
		if (!$_SERVER['HTTP_REFERER'])
			return $queryIfNull;
		
		$host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
		
		if (is_null($host) || $host !== $_SERVER['HTTP_HOST'])
			return $queryIfNull;
		
		$query = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
		
		if (is_null($query))
			return $queryIfNull;
		
		return $query;
	}
}
