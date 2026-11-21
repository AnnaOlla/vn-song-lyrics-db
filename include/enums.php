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

enum AccessState: string
{
	case Ok                         = 'ok';
	case AgentIsVisitorError        = 'user_is_visitor';
	case AgentIsViolatorError       = 'user_is_violator';
	case AgentIsNotAuthorError      = 'user_is_not_author';
	case EntityIsHiddenError        = 'entity_is_hidden';
	case EntityIsCheckedError       = 'entity_is_checked';
	case SongHasTranslationsError   = 'song_has_translations';
	case LyricsAreNotOriginalError  = 'lyrics_not_original';
	case LyricsUseSameLanguageError = 'lyrics_use_same_language';
	case AgentUsedSameLanguageError = 'user_used_same_language';
}
