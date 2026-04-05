<?php

abstract class CustomException extends Exception
{
	public function __construct(string $message = '', array $parameters = [], int $code = 0, ?Throwable $previous = null)
	{
		$message = 'User "'.($_SESSION['user']['username'] ?? '[null]').'"'.
		           ' of role "'.($_SESSION['user']['role'] ?? '[null]').'"'.
		           ' encountered error '.$code.
		           ' when requested '.$_SERVER['REQUEST_URI'].
		           ' with '.$_SERVER['REQUEST_METHOD'].
		           ' and parameters ('.$this->stringifyParameters($parameters).'): '.
				   $message;
		
		parent::__construct($message, $code, $previous);
	}
	
	private function stringifyParameters(mixed ...$parameters): string
	{
		return print_r($parameters, true);
	}
}

class UploadedFileException extends CustomException
{
	// Nothing here
};

class DatabaseLogicException extends CustomException
{
	// Nothing here
}

abstract class HttpException extends CustomException
{
	// Nothing here
}

class HttpBadRequest400 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 400, $previous);
	}
}

class HttpUnauthorized401 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 401, $previous);
	}
}

class HttpPaymentRequired402 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 402, $previous);
	}
}

class HttpForbidden403 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 403, $previous);
	}
}

class HttpNotFound404 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 404, $previous);
	}
}

class HttpMethodNotAllowed405 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 405, $previous);
	}
}

class HttpNotAcceptable406 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 406, $previous);
	}
}

class HttpUnavailableForLegalReasons451 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 451, $previous);
	}
}

class HttpInternalServerError500 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 500, $previous);
	}
}
