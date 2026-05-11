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
	
	public static function getLastVisitedPage(string|null $fallback = null): string
	{
		if (is_null($fallback))
			$fallback = '';
		
		if (!isset($_SERVER['HTTP_REFERER']))
			return $fallback;
		
		$host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
		
		if (is_null($host) || $host !== $_SERVER['HTTP_HOST'])
			return $fallback;
		
		$path = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
		
		if (is_null($path))
			return $fallback;
		
		$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		
		if ($path === $currentPath)
			return $fallback;
		
		$query = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
		
		if (is_null($query))
			return $path;
		
		return $path.'?'.$query;
	}
	
	public static function buildPaginationParameters
	(
		int|null    $limit,
		int|null    $page,
		string|null $search
	): string
	{
		$params = [];
		
		if (!is_null($limit))
			$params[] = 'limit='.$limit;
		if (!is_null($page))
			$params[] = 'page='.$page;
		if (!is_null($search))
			$params[] = 'search='.rawurlencode($search);
		
		if (count($params) === 0)
			return '';
		
		return '?'.implode('&', $params);
	}
}
