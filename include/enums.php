<?php

enum AssetFolder: string
{
	case Base       = 'assets';
	case Games      = 'game-logos';
	case Albums     = 'album-covers';
	case Artists    = 'artist-photos';
	case Characters = 'character-images';
	case Static     = 'static-images';
}

enum StaticAsset: string
{
	case NoGame      = 'no-game';
	case NoAlbum     = 'no-album';
	case NoArtist    = 'no-artist';
	case NoCharacter = 'no-character';
	
	/*
	case BadRequest400          = 'bad-request-400';
	case Unauthorized401        = 'unauthorized-401';
	case PaymentRequired402     = 'payment-required-402';
	case Forbidden403           = 'forbidden-403';
	case NotFound404            = 'not-found-404';
	case InternalServerError500 = 'internal-server-error-500';
	*/
}

enum AuthorizationError
{
	case None;
	
	case CaptchaInvalid;
	
	case EmptyEmail;
	case EmptyPassword;
	case EmailNotFound;
	case EmailNotExists;
	case IncorrectPassword;
	
	case UsernameTrimmable;
	case UsernameForbiddenSymbols;
	case UsernameLengthIncorrect;
	case UsernameTaken;
	
	case EmailTaken;
	case EmailInvalid;
	
	case PasswordTrimmable;
	case PasswordForbiddenSymbols;
	case PasswordLengthIncorrect;
	
	case AccountNotVerified;
	case MailSendFailed;
}

enum EntityStatus
{
	case Hidden;
	case Unchecked;
	case Checked;
}
