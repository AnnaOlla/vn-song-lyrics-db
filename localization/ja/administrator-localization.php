<?php

return
[
	'control_panel' =>
	[
		'heading' => 'Control Panel',
		
		'maintenance' =>
		[
			'heading'    => 'Maintenance',
			'switch_off' => 'Deactivate Maintenance Mode',
			'switch_on'  => 'Activate Maintenance Mode'
		],
		
		'add' =>
		[
			'heading'  => 'Add Data',
			'language' => 'Add Language'
		],
		
		'view' =>
		[
			'heading'     => 'View Data',
			'report_list' => 'Report List',
			'user_list'   => 'User List'
		],
		
		'check' =>
		[
			'heading'          => 'Check Data',
			'game_list'        => 'Unchecked Game List',
			'album_list'       => 'Unchecked Album List',
			'artist_list'      => 'Unchecked Artist List',
			'character_list'   => 'Unchecked Character List',
			'song_list'        => 'Unchecked Song List',
			'translation_list' => 'Unchecked Translation List'
		],
		
		'update_sitemap' =>
		[
			'heading'          => 'Update Sitemap',
			'static_list'      => 'Static Pages',
			'game_list'        => 'Games',
			'album_list'       => 'Albums',
			'artist_list'      => 'Artists',
			'character_list'   => 'Characters',
			'song_list'        => 'Songs',
			'translation_list' => 'Translations'
		]
	],
	
	'edit' =>
	[
		'language' =>
		[
			'heading' =>
			[
				'add'  => 'Add Language',
				'edit' => 'Edit Language'
			],
			
			'original_name' => 'Own Name',
			'russian_name'  => 'Name in Russian',
			'english_name'  => 'Name in English',
			'japanese_name' => 'Name in Japanese'
		]
	],
	
	'report_list' =>
	[
		'heading'     => 'Report List',
		'user_id'     => 'Id',
		'user_ip'     => 'Sender Ip',
		'username'    => 'Sender Username',
		'report_text' => 'Message',
		'entity_uri'  => 'Request Uri',
		'user_agent'  => 'User Agent',
		'timestamp'   => 'Timestamp',
		'status'      => 'Status',
		'anonymous'   => '[Anonymous]'
	],
	
	'user_list' =>
	[
		'heading'      => 'User List',
		'id'           => 'Id',
		'role'         => 'Role',
		'username'     => 'Username',
		'email'        => 'Email',
		'signed_up'    => 'Created',
		'logged_in'    => 'Last Log-In',
		'fingerprints' => 'Fingerprints'
	],
	
	'fill_album' =>
	[
		'heading'             => 'Fill Album: $name',
		'disc_number'         => 'Disc',
		'track_number'        => 'Track',
		'original_name'       => 'Original Title',
		'transliterated_name' => 'Romanized Title',
		'localized_title'     => 'Localized Title',
		'has_lyrics'          => 'Has Lyrics?',
		'has_lyrics_true'     => 'Yes',
		'has_lyrics_false'    => 'No'
	]
];
