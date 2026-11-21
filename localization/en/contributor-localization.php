<?php

return
[
	'edit' =>
	[
		'shared' =>
		[
			'hint'     => 'Hint',
			'controls' => 'Controls',
			
			'tooltip' =>
			[
				'hint'     => 'Here you can find useful information about elements on the page.'.
							  '</br></br>'.
							  'Hover over an item to find out details.',
				'controls' => 'Click the return button to go back.'.
							  '<br/><br/>'.
							  'Click the submit button to send the entry.'
			]
		],
		
		'game' =>
		[
			'heading' =>
			[
				'add'  => 'Add Game',
				'edit' => 'Edit Game: $name'
			],
			
			'original_name'       => 'Original Title',
			'transliterated_name' => 'Romanized Title',
			'localized_name'      => 'Localized Title',
			'old_image'           => 'Current Logo',
			'new_image'           => 'New Logo',
			'image'               => 'Logo',
			'vndb_link'           => 'Link to Game at vndb.org',
			'related_albums'      => 'Related Albums',
			'related_characters'  => 'Related Characters',
			
			'tooltip' =>
			[
				'original_name'       => 'The name of the game in its original language.'.
										 '<br/><br/>'.
										 'Please, check if the game is already present in the database before adding a new entry.'.
										 '<br/><br/>'.
										 '* = mandatory',
				'transliterated_name' => 'The original name of the game written in romaji.'.
										 '<br/><br/>'.
										 'Please, check the <a href="/en/writing-guide#romanization" target="_blank">Writing Guide</a> on how to romanize names.'.
										 '<br/><br/>'.
										 'Remember that the romanized name is not allowed to have symbols other than printable ASCII.'.
										 '<br/><br/>'.
										 '* = mandatory',
				'localized_name'      => 'The name used in international stores (usually English one).'.
										 '<br/><br/>'.
										 'If the game was never released for international community, leave this field empty.',
				'new_image'           => 'If you want to change the logo, upload a new image.'.
										 '<br/><br/>'.
										 'Maximum size of the file: 2 megabytes.'.
										 '<br/><br/>'.
										 'It is recommended to use an image of 384x384 pixels.',
				'old_image'           => 'The current logo of the game.',
				'image'               => 'The logo of the game.'.
										 '<br/><br/>'.
										 'Maximum size of the file: 2 megabytes.'.
										 '<br/><br/>'.
										 'It is recommended to use an image of 384x384 pixels.',
				'vndb_link'           => 'The link to the game in the database at vndb.org.'.
										 '<br/><br/>'.
										 'vndb.org focuses on collecting various data on visual novels. It is the largest visual novel database in the internet.'.
										 '<br/><br/>'.
										 'Please, find the game there and paste the link in this field.',
				'related_albums'      => 'If some album has songs related to the game, then select it here.'.
										 '<br/><br/>'.
										 'Use the plus button to add a new relation.'.
										 '<br/><br/>'.
										 'Use the minus button to remove the relation.'.
										 '<br/><br/>'.
										 'If there are no albums related to it yet, then select nothing. You can add albums and relations later.'.
										 '<br/><br/>'.
										 'If the field is not allowed to edit, it means that the staff has approved the relation.',
				'related_characters'  => 'If some singing characters are related to the game, then select them here.'.
										 '<br/><br/>'.
										 'Click the plus button to add a new relation.'.
										 '<br/><br/>'.
										 'Click the minus button to remove the relation.'.
										 '<br/><br/>'.
										 'If there are no characters related to it yet, then select nothing. You can add characters and relations later.'.
										 '<br/><br/>'.
										 'If the field is not allowed to edit, it means that the staff has approved the relation.'
			]
		],
		
		'album' =>
		[
			'heading' =>
			[
				'add'  => 'Add Album',
				'edit' => 'Edit Album: $name'
			],
			
			'original_name'       => 'Original Title',
			'transliterated_name' => 'Romanized Title',
			'localized_name'      => 'Localized Title',
			'old_image'           => 'Current Cover',
			'new_image'           => 'New Cover',
			'image'               => 'Cover',
			'vgmdb_link'          => 'Link to Album at vgmdb.net',
			'related_games'       => 'Related Games',
			'song_count'          => 'Song Count',
			
			'tooltip' =>
			[
				'original_name'       => 'The name of the album in its original language'.
										 '<br/><br/>'.
										 'Please, check if the album is already present in the database before adding a new entry.'.
										 '<br/><br/>'.
										 '* = mandatory',
				'transliterated_name' => 'The original name of the album written in romaji.'.
										 '<br/><br/>'.
										 'Please, check the <a href="/en/writing-guide#romanization" target="_blank">Writing Guide</a> on how to romanize names.'.
										 '<br/><br/>'.
										 'Remember that the romanized name is not allowed to have symbols other than printable ASCII.'.
										 '<br/><br/>'.
										 '* = mandatory',
				'localized_name'      => 'The name used in international stores (usually English one).'.
										 '<br/><br/>'.
										 'If the album was never released for international community, leave this field empty.',
				'new_image'           => 'If you want to change the cover, upload a new image.'.
										 '<br/><br/>'.
										 'Maximum size of the file: 2 megabytes.'.
										 '<br/><br/>'.
										 'It is recommended to use an image of 384x384 pixels.',
				'old_image'           => 'The current cover of the album.',
				'image'               => 'The cover of the album.'.
										 '<br/><br/>'.
										 'Maximum size of the file: 2 megabytes.'.
										 '<br/><br/>'.
										 'It is recommended to use an image of 384x384 pixels.',
				'vgmdb_link'          => 'The link to the album in the database at vgmdb.net.'.
										 '<br/><br/>'.
										 'vgmdb.net focuses on soundtracks of various types of media and has a big community supporting the website.'.
										 '<br/><br/>'.
										 'Please, find the album there and paste the link in this field.',
				'related_games'       => 'If this album has songs related to some game that is already added, then select it here.'.
										 '<br/><br/>'.
										 'Click the plus button to add a new relation.'.
										 '<br/><br/>'.
										 'Use the minus button to remove the relation.'.
										 '<br/><br/>'.
										 'If there are no games related to it yet, then select nothing. You can add games and relations later.'.
										 '<br/><br/>'.
										 'If the field is not allowed to edit, it means that the staff has approved the relation.',
				'song_count'          => 'Enter the total track count on all disks of the album.'.
										 '<br/><br/>'.
										 '* = mandatory'
			]
		],
		
		'artist' =>
		[
			'heading' =>
			[
				'add'  => 'Add Artist',
				'edit' => 'Edit Artist: $name'
			],
			
			'original_name'       => 'Original Name',
			'transliterated_name' => 'Romanized Name',
			'localized_name'      => 'Localized Name',
			'old_image'           => 'Current Photo',
			'new_image'           => 'New Photo',
			'image'               => 'Photo',
			'vgmdb_link'          => 'Link to Artist at vgmdb.net',
			'alias_of'            => 'Alias of',
			
			'tooltip' =>
			[
				'original_name'       => 'The name of the artist in their native language.'.
										 '<br/><br/>'.
										 'Please, check if the artist is already present in the database before adding a new entry.'.
										 '<br/><br/>'.
										 '* = mandatory',
				'transliterated_name' => 'The name of the artist written in romaji.'.
										 '<br/><br/>'.
										 'Please, check the <a href="/en/writing-guide#romanization" target="_blank">Writing Guide</a> on how to romanize names.'.
										 '<br/><br/>'.
										 'Remember that the romanized name is not allowed to have symbols other than printable ASCII.'.
										 '<br/><br/>'.
										 '* = mandatory',
				'localized_name'      => 'The name of the artist used in international media and stores.'.
										 '<br/><br/>'.
										 'If the artist was never advertized for the international community, leave this field empty.',
				'new_image'           => 'If you want to change the photo, upload a new image.'.
										 '<br/><br/>'.
										 'Maximum size of the file: 2 megabytes.'.
										 '<br/><br/>'.
										 'It is recommended to use an image of 384x384 pixels.',
				'old_image'           => 'The current photo of the artist.',
				'image'               => 'The photo of the artist.'.
										 '<br/><br/>'.
										 'Maximum size of the file: 2 megabytes.'.
										 '<br/><br/>'.
										 'It is recommended to use an image of 384x384 pixels.',
				'vgmdb_link'          => 'The link to the artist in the database at vgmdb.net.'.
										 '<br/><br/>'.
										 'vgmdb.net focuses on soundtracks of various types of media and has a big community supporting the website.'.
										 '<br/><br/>'.
										 'Please, find the artist there and paste the link in this field.',
				'alias_of'            => 'If the current artist is an alias of another one, fill this field, else leave it empty.'
			]
		],
		
		'character' =>
		[
			'heading' =>
			[
				'add'  => 'Add Character',
				'edit' => 'Edit Character: $name'
			],
			
			'original_name'       => 'Original Name',
			'transliterated_name' => 'Romanized Name',
			'localized_name'      => 'Localized Name',
			'old_image'           => 'Current Image',
			'new_image'           => 'New Image',
			'image'               => 'Image',
			'vndb_link'           => 'Link to Character at vndb.org',
			'related_games'       => 'Related Games',
			
			'tooltip' =>
			[
				'original_name'       => 'The name of the character in the original language of the game.'.
										 '<br/><br/>'.
										 'Please, check if the character is already present in the database before adding a new entry.'.
										 '<br/><br/>'.
										 '* = mandatory',
				'transliterated_name' => 'The name of the character written in romaji.'.
										 '<br/><br/>'.
										 'Please, check the <a href="/en/writing-guide#romanization" target="_blank">Writing Guide</a> on how to romanize names.'.
										 '<br/><br/>'.
										 'Remember that the romanized name is not allowed to have symbols other than printable ASCII.'.
										 '<br/><br/>'.
										 '* = mandatory',
				'localized_name'      => 'The name used in a localized version of the game.'.
										 '<br/><br/>'.
										 'If the game was never released for the international community, leave this field empty.',
				'new_image'           => 'If you want to change the image, upload a new one.'.
										 '<br/><br/>'.
										 'Maximum size of the file: 2 megabytes.'.
										 '<br/><br/>'.
										 'It is recommended to use an image of 384x384 pixels.',
				'old_image'           => 'The current image of the character.',
				'image'               => 'The photo of the character.'.
										 '<br/><br/>'.
										 'Maximum size of the file: 2 megabytes.'.
										 '<br/><br/>'.
										 'It is recommended to use an image of 384x384 pixels.',
				'vndb_link'           => 'The link to the game character in the database at vndb.org.'.
										 '<br/><br/>'.
										 'vndb.org focuses on collecting various data on visual novels. It is the largest visual novel database in the internet.'.
										 '<br/><br/>'.
										 'Please, find the character there and paste the link in this field.',
				'related_games'       => 'If this character is related to some game that is already added, then select it here.'.
										 '<br/><br/>'.
										 'Click the plus button to add a new relation.'.
										 '<br/><br/>'.
										 'Use the minus button to remove the relation.'.
										 '<br/><br/>'.
										 'If there are no games related to it yet, then select nothing. You can add games and relations later.'.
										 '<br/><br/>'.
										 'If the field is not allowed to edit, it means that the staff has approved the relation.'
			]
		],
		
		'song' =>
		[
			'heading' =>
			[
				'add'  => 'Add Song',
				'edit' => 'Edit Song: $name'
			],
			
			'disc_and_track'      => 'Disc & Track',
			'original_name'       => 'Original Title',
			'transliterated_name' => 'Romanized Title',
			'localized_name'      => 'Localized Title',
			'has_lyrics'          => 'Has Lyrics?',
			
			'next_disc'           => 'Disc: +1',
			'previous_disc'       => 'Disc: −1',
			'has_lyrics_false'    => 'No',
			'has_lyrics_true'     => 'Yes',
			'submit_last_song'    => 'Submit Last Song',
			'submit_nonlast_song' => 'Submit and Start Next Song',
			
			'tooltip' =>
			[
				'disc_and_track'      => 'The number of the disc and number of the track on it.'.
										 '<br/><br/>'.
										 'Click the button to set a new value for the disc.'.
										 '<br/><br/>'.
										 'The number of the disc is calculated automatically.'.
										 '<br/><br/>'.
										 '* = mandatory',
				'original_name'       => 'The title of the song in its original language.'.
										 '<br/><br/>'.
										 '* = mandatory',
				'transliterated_name' => 'The original title of the song written in romaji.'.
										 '<br/><br/>'.
										 'Please, check the <a href="/en/writing-guide#romanization" target="_blank">Writing Guide</a> on how to romanize names.'.
										 '<br/><br/>'.
										 'Remember that the romanized name is not allowed to have symbols other than printable ASCII.'.
										 '<br/><br/>'.
										 '* = mandatory',
				'localized_name'      => 'The title of the song used in a localized version of the album.'.
										 '<br/><br/>'.
										 'If the album was never released for the international community, then leave this field empty.',
				'has_lyrics'          => 'If the song is instrumental or has chorus only, then select “No”. If the song has lyrics, then select “Yes”.'.
										 '<br/><br/>'.
										 'Selecting “Yes” allows you to add its lyrics later. The song becomes clickable in the track list of the album.'.
										 '<br/><br/>'.
										 '* = mandatory'
			]
		],
		
		'lyrics' =>
		[
			'heading' =>
			[
				'add'  => 'Add Lyrics: $name',
				'edit' => 'Edit Lyrics: $name'
			],
			
			'performers'    => 'Artist & Character',
			'original_song' => 'Original Song',
			'language'      => 'Language',
			'lyrics'        => 'Lyrics',
			'notes'         => 'Notes',
			
			'performs_as'   => 'as',
			
			'tooltip' =>
			[
				'performers'    => 'Select performers of the song.'.
								   '<br/><br/>'.
								   'If the song is performed by a game character, then select the character too.'.
								   '<br/><br/>'.
								   'Click the plus button to add an artist.'.
								   '<br/><br/>'.
								   'Click the minus button to remove an artist.'.
								   '* = mandatory to select artists.',
				'original_song' => 'If the song is a rearrangement, remix or a short version of another song, then use this option.'.
								   '<br/><br/>'.
								   'Lyrics and translations of the original song will be automatically assigned to this song.',
				'language'      => 'Select the main language of the song.'.
								   '<br/><br/>'.
								   'Some songs may have phrases in a different language in their lyrics. Select the prevailing language.'.
								   '<br/><br/>'.
								   '* = mandatory',
				'lyrics'        => 'Write the lyrics of the song. Make sure you have read rules in the <a href="/en/writing-guide#lyrics" target="_blank">Writing Guide</a>.'.
								   '<br/><br/>'.
								   'Here you can use special syntax to make the text more meaningful.'.
								   '<br/><br/>'.
								   '{kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby>'.
								   '<br/><br/>'.
								   '{cl #FF0000}色づいた{/cl}言葉　→　<span style="color: #FF0000">色づいた</span>言葉'.
								   '<br/><br/>'.
								   '{nt}1{/nt}　→　If used both in notes and lyrics, creates a clickable link between them.'.
								   '<br/><br/>'.
								   'More details on how to use these features are in the <a href="/en/writing-guide#formatting" target="_blank">Writing Guide</a>.'.
								   '<br/><br/>'.
								   '* = mandatory',
				'notes'         => 'Write any comments on the song if necessary.'.
								   '<br/><br/>'.
								   'Here you can use special syntax to make the text more meaningful.'.
								   '<br/><br/>'.
								   '{kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby>'.
								   '<br/><br/>'.
								   '{cl #FF0000}色づいた{/cl}言葉　→　<span style="color: #FF0000">色づいた</span>言葉'.
								   '<br/><br/>'.
								   '{nt}1{/nt}　→　If used both in notes and lyrics, creates a clickable link between them.'.
								   '<br/><br/>'.
								   'More details on how to use these features are in the <a href="/en/writing-guide#formatting" target="_blank">Writing Guide</a>.'
			]
		],
		
		'translation' =>
		[
			'heading' =>
			[
				'add'  => 'Add Translation',
				'edit' => 'Edit Translation'
			],
			
			'target_language' => 'Language of Translation',
			'target_name'     => 'Title of Translation',
			'target_lyrics'   => 'Lyrics of Translation',
			'target_notes'    => 'Translator’s Notes',
			'source_language' => 'Language of Song',
			'source_name'     => 'Title of Song',
			'source_lyrics'   => 'Lyrics of Song',
			'source_notes'    => 'Notes on Song',
			
			'tooltip' =>
			[
				'target_language' => 'Select the language you want to translate the song to.'.
									 '<br/><br/>'.
									 'A user may not translate a song to the same language several times.'.
									 '<br/><br/>'.
									 '* = mandatory',
				'target_name'     => 'Translate the title of the song.'.
									 '<br/><br/>'.
									 '* = mandatory',
				'target_lyrics'   => 'Translate the lyrics of the song.'.
									 '<br/><br/>'.
									 'Make sure you have read recommendations in the <a href="/en/writing-guide#translation" target="_blank">Writing Guide</a>.'.
									 '<br/><br/>'.
									 'Here you can use special syntax to make lyrics more meaningful.'.
									 '{kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby>'.
									 '<br/><br/>'.
									 '{cl #FF0000}色づいた{/cl}言葉　→　<span style="color: #FF0000">色づいた</span>言葉'.
									 '<br/><br/>'.
									 '{nt}1{/nt}　→　If used both in notes and lyrics, creates a clickable link between them.'.
									 '<br/><br/>'.
									 'More details on how to use these features are in the <a href="/en/writing-guide#formatting" target="_blank">Writing Guide</a>.'.
									 '<br/><br/>'.
									 '* = mandatory',
				'target_notes'    => 'If you think that you should explain some phrases or make a comment in general, use this section.',
									 '<br/><br/>'.
									 'Make sure you have read recommendations in the <a href="/en/writing-guide#translation" target="_blank">Writing Guide</a>.'.
									 '<br/><br/>'.
									 'Here you can use special syntax to make lyrics more meaningful.'.
									 '{kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby>'.
									 '<br/><br/>'.
									 '{cl #FF0000}色づいた{/cl}言葉　→　<span style="color: #FF0000">色づいた</span>言葉'.
									 '<br/><br/>'.
									 '{nt}1{/nt}　→　If used both in notes and lyrics, creates a clickable link between them.'.
									 '<br/><br/>'.
									 'More details on how to use these features are in the <a href="/en/writing-guide#formatting" target="_blank">Writing Guide</a>.'
			]
		]
	],
	
	'delete' =>
	[
		'game'        => 'Delete Game: $name',
		'album'       => 'Delete Album: $name',
		'artist'      => 'Delete Artist: $name',
		'character'   => 'Delete Character: $name',
		'song'        => 'Delete Song: $name',
		'lyrics'      => 'Delete Lyrics: $name',
		'translation' => 'Delete Translation: $name',
		
		'description' =>
		[
			'one' => 'Are you sure you want to delete this data?',
			'two' => 'This action can not be undone.'
		]
	],
	
	'account' =>
	[
		'change_email' =>
		[
			'heading'      => 'Change Email: $name',
			'new_email'    => 'New Email',
			'old_password' => 'Current Password'
		],
		
		'change_username' =>
		[
			'heading'      => 'Change Username: $name',
			'new_username' => 'New Username',
			'old_password' => 'Current Password'
		],
		
		'change_password' =>
		[
			'heading'      => 'Change Password: $name',
			'new_password' => 'New Password',
			'old_password' => 'Current Password'
		],
		
		'change_about_me' =>
		[
			'heading'  => 'Change “About Me”: $name',
			'about_me' => 'Self-Introduction (up to 500 symbols)'
		],
		
		'delete_account' =>
		[
			'heading'     => 'Delete Account: $name',
			
			'description' =>
			[
				'one'   => 'You are going to delete your account.',
				'two'   => 'The website will forget your account data such as email.',
				'three' => 'All your contributions will be kept and will be marked with “Deleted User”.',
				'four'  => 'This action is impossible to undo and comes into force immediately.',
				'five'  => 'Your password is required to confirm the action.'
			],
			
			'old_password' => 'Password'
		]
	]
];
