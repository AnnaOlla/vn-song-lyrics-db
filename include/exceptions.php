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

abstract class HttpException extends CustomException
{
	// Nothing here
}

final class HttpBadRequest400 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 400, $previous);
	}
}

final class HttpUnauthorized401 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 401, $previous);
	}
}

final class HttpPaymentRequired402 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 402, $previous);
	}
}

final class HttpForbidden403 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 403, $previous);
	}
}

final class HttpNotFound404 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 404, $previous);
	}
}

final class HttpMethodNotAllowed405 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 405, $previous);
	}
}

final class HttpNotAcceptable406 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 406, $previous);
	}
}

final class HttpConflict409 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 409, $previous);
	}
}

final class HttpContentTooLarge413 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 413, $previous);
	}
}

final class HttpUnsupportedMediaType415 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 415, $previous);
	}
}

final class HttpUnprocessableEntity422 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 415, $previous);
	}
}

final class HttpUnavailableForLegalReasons451 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 451, $previous);
	}
}

final class HttpInternalServerError500 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 500, $previous);
	}
}

final class HttpNotImplemented501 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 501, $previous);
	}
}

final class HttpBadGateway502 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 502, $previous);
	}
}

final class HttpServiceUnavailable503 extends HttpException
{
	public function __construct(string $message = '', array $parameters = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $parameters, 503, $previous);
	}
}
