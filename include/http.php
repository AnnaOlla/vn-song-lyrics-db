<?php

final class Http
{
	public static function buildUri(string ...$uriParts): string
	{
		for ($i = 0; $i < count($uriParts); $i++)
			$uriParts[$i] = rawurlencode($uriParts[$i]);
		
		return '/'.implode('/', $uriParts);
	}
	
	public static function buildQueryParameters(array|null $keysToValues, bool $dropNullValues = true): string
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
		
		$host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
		
		if ($host === null || $host !== $_SERVER['HTTP_HOST'])
			return $fallback;
		
		$path = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
		
		if ($path === null)
			return $fallback;
		
		$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		
		if ($path === $currentPath)
			return $fallback;
		
		$query = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
		
		if ($query === null)
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
		
		if ($limit !== null)
			$params[] = 'limit='.$limit;
		if ($page !== null)
			$params[] = 'page='.$page;
		if ($search !== null)
			$params[] = 'search='.rawurlencode($search);
		
		if (count($params) === 0)
			return '';
		
		return '?'.implode('&', $params);
	}
}
