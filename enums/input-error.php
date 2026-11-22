<?php

enum InputError: string
{
	case None                     = 'none';
	case CaptchaInvalid           = 'captcha_invalid';
	case EmailNotFound            = 'email_not_found';
	case EmailInvalid             = 'email_invalid';
	case EmailTaken               = 'email_taken';
	case UsernameForbiddenSymbols = 'username_forbidden_symbols';
	case UsernameLengthIncorrect  = 'username_forbidden_length';
	case UsernameTaken            = 'username_taken';
	case IncorrectPassword        = 'password_incorrect';
	case PasswordForbiddenSymbols = 'password_forbidden_symbols';
	case PasswordLengthIncorrect  = 'password_forbidden_length';
}
