<?php

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
