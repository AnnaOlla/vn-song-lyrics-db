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
}

enum InputError
{
	case None;
	
	case CaptchaInvalid;
	
	case EmailNotFound;
	case EmailInvalid;
	case EmailTaken;
	
	case UsernameForbiddenSymbols;
	case UsernameLengthIncorrect;
	case UsernameTaken;
	
	case IncorrectPassword;
	case PasswordForbiddenSymbols;
	case PasswordLengthIncorrect;
}

enum EntityStatus
{
	case Hidden;
	case Unchecked;
	case Checked;
}
