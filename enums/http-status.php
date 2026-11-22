<?php

enum HttpStatus: int
{
	case Ok                         = 200;
	
	case BadRequest                 = 400;
	case Unauthorized               = 401;
	case PaymentRequired            = 402;
	case Forbidden                  = 403;
	case NotFound                   = 404;
	case MethodNotAllowed           = 405;
	case NotAcceptable              = 406;
	case Conflict                   = 409;
	case Gone                       = 410;
	case ContentTooLarge            = 413;
	case UnsupportedMediaType       = 415;
	case PermanentlyBlocked         = 418;
	case UnprocessableEntity        = 422;
	case TooManyRequests            = 429;
	case UnavailableForLegalReasons = 451;
	
	case InternalServerError        = 500;
	case NotImplemented             = 501;
	case BadGateway                 = 502;
	case ServiceUnavailable         = 503;
	
	public function exception
	(
		string         $message   = '[no message]',
		array          $variables = [],
		Throwable|null $previous  = null
	): HttpException
	{
		return new HttpException($this, $message, $variables, $previous);
	}
}
