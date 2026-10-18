<?php

final class Logger
{
	private const ERROR_LOG_DIRNAME   = 'logs/error-logs';
	private const ERROR_LOG_FILENAME  = '-error.log';
	
	private const ACCESS_LOG_DIRNAME  = 'logs/access-logs';
	private const ACCESS_LOG_FILENAME = '-access.log';
	
	private const SENSITIVE_PARAMETER_NAMES = ['nonce', 'email', 'password'];
	
	private static function hideSensitiveParameters(array $values): array
	{
		foreach (self::SENSITIVE_PARAMETER_NAMES as $sensitiveParameterName)
		{
			if (array_key_exists($sensitiveParameterName, $values))
				$values[$sensitiveParameterName] = '[hidden]';
		}
		
		return $values;
	}
	
	private static function calculatePageGenerationTimeInMicroseconds(): float
	{
		return microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
	}
	
	public static function logError(Throwable $exception): void
	{
		$currentDate = date("Y-m-d", $_SERVER['REQUEST_TIME']);
		$logFilename = self::ERROR_LOG_DIRNAME.'/.'.$currentDate.self::ERROR_LOG_FILENAME;
		
		$trace      = $exception->getTrace();
		$stackTrace = [];
		
		for ($i = 0; $i < count($trace); $i++)
		{
			$index = '#'.$i;
			$place = $trace[$i]['file'].'('.$trace[$i]['line'].')';
			
			if (isset($trace[$i]['class']))
				$function = $trace[$i]['class'].'->'.$trace[$i]['function'];
			else
				$function = $trace[$i]['function'];
			
			if (isset($trace[$i]['args']))
			{
				$args = self::hideSensitiveParameters($trace[$i]['args']);
				$args = print_r($args, true);
			}
			else
				$args = '';
			
			$stackTrace[] = $index.' '.$place.': '.$function.': '.$args;
		}
		
		$get   = self::hideSensitiveParameters($_GET);
		$post  = self::hideSensitiveParameters($_POST);
		$files = self::hideSensitiveParameters($_FILES);
		
		$variables = ($exception instanceof HttpException) ? $exception->getVariables() : [];
		$variables = self::hideSensitiveParameters($variables);
		
		$log['dateTime']   = date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
		$log['emptyLine1'] = '';
		$log['agentIp']    = 'IP:         '     .$_SERVER['REMOTE_ADDR'];
		$log['agentInfo']  = 'User agent: '     .$_SERVER['HTTP_USER_AGENT'];
		$log['request']    = 'URI:        '     .$_SERVER['REQUEST_URI'];
		$log['method']     = 'Method:     '     .$_SERVER['REQUEST_METHOD'];
		$log['httpCode']   = 'HTTP code:  '     .http_response_code();
		$log['class']      = 'Exception:  '     .get_class($exception);
		$log['message']    = 'Message:    '     .$exception->getMessage();
		$log['emptyLine2'] = '';
		$log['get']        = '$_GET = '         .print_r($get, true);
		$log['post']       = '$_POST = '        .print_r($post, true);
		$log['files']      = '$_FILES = '       .print_r($files, true);
		$log['variables']  = '$vars = '         .print_r($variables, true);
		$log['stackTrace'] = 'Stack trace: '    .PHP_EOL.implode(PHP_EOL, $stackTrace);
		$log['loadTime']   = 'Generation time: '.self::calculatePageGenerationTimeInMicroseconds();
		$log['separator']  = '----------------------------------------------------------';
		
		foreach ($log as $key => $line)
			error_log($line.PHP_EOL, 3, $logFilename);
		
		// Use the default logger too [just in case]
		error_log($exception);
	}
	
	public static function logAccess(): void
	{
		$currentDate = date("Y-m-d", $_SERVER['REQUEST_TIME']);
		$logFilename = self::ACCESS_LOG_DIRNAME.'/.'.$currentDate.self::ACCESS_LOG_FILENAME;
		
		$get   = self::hideSensitiveParameters($_GET);
		$post  = self::hideSensitiveParameters($_POST);
		$files = self::hideSensitiveParameters($_FILES);
		
		$log['dateTime']   = date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
		$log['emptyLine1'] = '';
		$log['agentIp']    = 'IP:         '     .$_SERVER['REMOTE_ADDR'];
		$log['agentInfo']  = 'User agent: '     .$_SERVER['HTTP_USER_AGENT'];
		$log['request']    = 'URI:        '     .$_SERVER['REQUEST_URI'];
		$log['method']     = 'Method:     '     .$_SERVER['REQUEST_METHOD'];
		$log['emptyLine2'] = '';
		$log['get']        = '$_GET = '         .print_r($get, true);
		$log['post']       = '$_POST = '        .print_r($post, true);
		$log['files']      = '$_FILES = '       .print_r($files, true);
		$log['loadTime']   = 'Generation time: '.self::calculatePageGenerationTimeInMicroseconds();
		$log['separator']  = '----------------------------------------------------------';
		
		foreach ($log as $key => $line)
			error_log($line.PHP_EOL, 3, $logFilename);
	}
}
