<?php

return
[
	'header' =>
	[
		'home_page'        => 'vn-song-lyrics-db',
		'game_list'        => 'Games',
		'album_list'       => 'Albums',
		'artist_list'      => 'Artists',
		'character_list'   => 'Characters',
		'song_list'        => 'Songs',
		'translation_list' => 'Translations',
		'feedback'         => 'Feedback',
		'log_in'           => 'Log In',
		'log_out'          => 'Log Out',
		'sign_up'          => 'Sign Up',
		'language_en'      => 'EN',
		'language_ru'      => 'RU',
		'language_ja'      => 'JA'
	],
	
	'footer' =>
	[
		'author' => 'AnnaOlla, 2026',
		'about'  => 'About Website',
		'policy' => 'Website Policy',
		'rules'  => 'Website Rules',
		'guide'  => 'Writing Guide',
		'email'  => 'support@vn-song-lyrics-db.ru'
	],
	
	'image_alt_text' =>
	[
		'no_game'      => 'No Game Logo',
		'no_album'     => 'No Album Cover',
		'no_artist'    => 'No Artist Photo',
		'no_character' => 'No Character Image',
		
		'game'      => 'Logo of Game $game',
		'album'     => 'Cover of Album $album',
		'artist'    => 'Photo of Artist $artist',
		'character' => 'Image of Character $character',
	],
	
	'timestamp' =>
	[
		'added'    => 'Added',
		'updated'  => 'Updated',
		'reviewed' => 'Reviewed',
		'string'   => '$action $timestamp by $username.',
		
		'deleted_user' => '[Deleted User]'
	],
	
	'moderation_status' =>
	[
		'relation_status' => 'Relation Status: $status.',
		'status'          => 'Status: $status.',
		'unchecked'       => 'Awaiting Staff Approval',
		'checked'         => 'Approved',
		'hidden'          => 'Hidden'
	],
	
	'input_error' =>
	[
		'none'                       => '',
		'captcha_invalid'            => 'The code was incorrect.',
		'email_not_found'            => 'The email was not found.',
		'email_invalid'              => 'The email was not written in the correct form.',
		'email_taken'                => 'The email can not be used.',
		'username_forbidden_symbols' => 'The username had forbidden symbols.',
		'username_forbidden_length'  => 'The username had incorrect length.',
		'username_taken'             => 'The username is taken.',
		'password_incorrect'         => 'The password was incorrect.',
		'password_forbidden_symbols' => 'The password had forbidden symbols.',
		'password_forbidden_length'  => 'The password had incorrect length.'
	],
	
	'access_state' =>
	[
		'ok'                       => '',
		'user_is_visitor'          => 'Log in or sign up to contribute.',
		'user_is_violator'         => 'You have been restricted from making any changes.',
		'user_is_not_author'       => 'You are not the submitter of this information.',
		'entity_is_hidden'         => 'The information is not available due to formal reasons.',
		'entity_is_checked'        => 'The information has been approved by the staff.',
		'song_has_translations'    => 'The song has translations.',
		'lyrics_not_original'      => 'These lyrics are copied from another song.',
		'lyrics_use_same_language' => 'It is the language of the song.',
		'user_used_same_language'  => 'You have already translated this song to this language.'
	],
	
	'controls' =>
	[
		'search' =>
		[
			'hint' => 'Enter text …'
		],
		
		'textarea' =>
		[
			'hint' => 'Enter text …'
		],
		
		'fileupload' =>
		[
			'hint_idle'          => 'Choose file (max. size: 2 MiB) …',
			'hint_size_exceeded' => 'File is too big. Try another file …'
		],
		
		'select' =>
		[
			'hint_idle'   => 'Select …',
			'hint_filter' => 'Filter out …'
		],
		
		'button' =>
		[
			'return'  => 'Return',
			'confirm' => 'I confirm',
			'submit'  => 'Submit',
			'search'  => 'Search'
		]
	]
];
