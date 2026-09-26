<?php

final class Logger
{
	private const ERROR_LOG_DIRNAME   = 'logs/error-logs';
	private const ERROR_LOG_FILENAME  = '-error.log';
	
	private const ACCESS_LOG_DIRNAME  = 'logs/access-logs';
	private const ACCESS_LOG_FILENAME = '-access.log';
	
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
				$args = PHP_EOL.'('.var_export($trace[$i]['args'], true).')';
			else
				$args = '';
			
			$stackTrace[] = $index.' '.$place.': '.$function.$args;
		}
		
		$log['datetime']   = date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
		$log['agentIp']    = $_SERVER['REMOTE_ADDR'];
		$log['class']      = get_class($exception);
		$log['message']    = $exception->getMessage();
		$log['emptyLine']  = '';
		$log['stackTrace'] = implode(PHP_EOL, $stackTrace);
		$log['separator']  = '----------------------------------------------------------';
		
		foreach ($log as $part => $line)
			error_log($line.PHP_EOL, 3, $logFilename);
		
		// Use the default logger too [just in case]
		error_log($exception);
	}
	
	public static function logRequest(): void
	{
		$currentDate = date("Y-m-d", $_SERVER['REQUEST_TIME']);
		$logFilename = self::ACCESS_LOG_DIRNAME.'/.'.$currentDate.self::ACCESS_LOG_FILENAME;
		
		$log['datetime']   = date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
		$log['agentIp']    = $_SERVER['REMOTE_ADDR'];
		$log['agentInfo']  = $_SERVER['HTTP_USER_AGENT'];
		$log['request']    = $_SERVER['REQUEST_URI'];
		$log['method']     = $_SERVER['REQUEST_METHOD'];
		$log['httpCode']   = http_response_code();
		$log['emptyLine1'] = '';
		$log['get']        = '$_GET = ('.var_export($_GET, true).')';
		$log['emptyLine2'] = '';
		$log['post']       = '$_POST = ('.var_export($_POST, true).')';
		$log['emptyLine3'] = '';
		$log['files']      = '$_FILES = ('.var_export($_FILES, true).')';
		$log['separator']  = '----------------------------------------------------------';
		
		foreach ($log as $part => $line)
			error_log($line.PHP_EOL, 3, $logFilename);
	}
}
