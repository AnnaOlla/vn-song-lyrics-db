<?php

abstract class HttpException extends Exception
{
	private $variables;
	
	public function __construct(string $message = '', array $variables = [], int $code = 0, ?Throwable $previous = null)
	{
		$this->variables = $variables;
		$message = ($message !== '') ? $message : '[none]';
		
		parent::__construct($message, $code, $previous);
	}
	
	public function getVariables(): array
	{
		return $this->variables;
	}
}

final class HttpBadRequest400 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 400, $previous);
	}
}

final class HttpUnauthorized401 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 401, $previous);
	}
}

final class HttpPaymentRequired402 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 402, $previous);
	}
}

final class HttpForbidden403 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 403, $previous);
	}
}

final class HttpNotFound404 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 404, $previous);
	}
}

final class HttpMethodNotAllowed405 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 405, $previous);
	}
}

final class HttpNotAcceptable406 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 406, $previous);
	}
}

final class HttpConflict409 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 409, $previous);
	}
}

final class HttpContentTooLarge413 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 413, $previous);
	}
}

final class HttpUnsupportedMediaType415 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 415, $previous);
	}
}

final class HttpPermanentlyBlocked418 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 418, $previous);
	}
}

final class HttpUnprocessableEntity422 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 422, $previous);
	}
}

final class HttpTooManyRequests429 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 429, $previous);
	}
}

final class HttpUnavailableForLegalReasons451 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 451, $previous);
	}
}

final class HttpInternalServerError500 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 500, $previous);
	}
}

final class HttpNotImplemented501 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 501, $previous);
	}
}

final class HttpBadGateway502 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 502, $previous);
	}
}

final class HttpServiceUnavailable503 extends HttpException
{
	public function __construct(string $message = '', array $variables = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $variables, 503, $previous);
	}
}
