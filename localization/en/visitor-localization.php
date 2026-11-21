<?php

return
[
	'home' =>
	[
		'heading'         => 'Visual Novel Song Lyrics Database',
		'hagana_alt_text' => 'Hagana from World End Economica',
		
		'description' =>
		[
			'one'   => 'vn-song-lyrics-db.ru is a free library for songs that sound in visual novels.',
			'two'   => 'Most of these songs are not mainstream and often it is a challenge to find lyrics for them.',
			'three' => 'Translations are unique knowledge and fine artwork that is welcome here always and forever.',
			'four'  => 'This website serves as a wiki and every registered user may contribute to it.'
		],
		
		'recently_added' =>
		[
			'albums'       => 'Recently Added Albums',
			'lyrics'       => 'Recently Added Lyrics',
			'translations' => 'Recently Added Translations',
		],
		
		'search' =>
		[
			'heading'          => 'Search',
			'all'              => 'All',
			'game_list'        => 'Games',
			'album_list'       => 'Albums',
			'artist_list'      => 'Artists',
			'character_list'   => 'Characters',
			'song_list'        => 'Songs',
			'translation_list' => 'Translations'
		]
	],
	
	'log_in' =>
	[
		'heading' => 'Log In',
		
		'email' =>
		[
			'heading' => 'Email',
			'hint'    => 'name@mailserver.domain',
		],
		
		'password' =>
		[
			'heading' => 'Password',
			'hint'    => 'If you lost it, contact us at support@vn-song-lyrics-db.ru'
		],
		
		'submit' => 'Log In'
	],
	
	'sign_up' =>
	[
		'heading' => 'Sign Up',
		
		'username' =>
		[
			'heading' => 'Username',
			'hint'    => 'Allowed Characters: A-Z, a-z, 0-9. Length: 4-32.'
		],
		
		'password' =>
		[
			'heading' => 'Password',
			'hint'    => 'Allowed Characters: A-Z, a-z, 0-9. Length: 4-32.'
		],
		
		'email' =>
		[
			'heading' => 'Email',
			'hint'    => 'Used only to verify and identify you.',
		],
		
		'confirmation' =>
		[
			'warning'   => 'By clicking “Sign Up”, you confirm that you agree with:',
			'readables' => '$policy, $rules, $guide.',
			'policy'    => 'Website Policy',
			'rules'     => 'Website Rules',
			'guide'     => 'Writing Guide',
			'advice'    => 'Make sure you spent at least one minute for each link.'
		],
		
		'submit' => 'Sign Up'
	],
	
	'game_list' =>
	[
		'heading'  => 'List of Games',
		'add_game' => 'Add New Game'
	],
	
	'album_list' =>
	[
		'heading'   => 'List of Albums',
		'add_album' => 'Add New Album'
	],
	
	'artist_list' =>
	[
		'heading'    => 'List of Artists',
		'add_artist' => 'Add New Artist'
	],
	
	'character_list' =>
	[
		'heading'       => 'List of Characters',
		'add_character' => 'Add New Character'
	],
	
	'song_list' =>
	[
		'heading' => 'List of Songs'
	],
	
	'translation_list' =>
	[
		'heading' => 'List of Translations'
	],
	
	'entity_list' =>
	[
		'search'  => 'Search over the database',
		'page'    => 'Page',
		'results' => 'Results per page'
	],
	
	'game' =>
	[
		'details'    => 'Details: ',
		'characters' => 'Related Characters',
		'albums'     => 'Related Albums',
		
		'controls' =>
		[
			'edit'   => 'Edit Game',
			'delete' => 'Delete Game',
			'report' => 'Report Game'
		]
	],
	
	'album' =>
	[
		'details'    => 'Details: ',
		'song_count' => 'Track Count: ',
		'song_list'  => 'Track List',
		'games'      => 'Related Games',
		
		'song_table' =>
		[
			'disc_number' => 'Disc',
			'song_number' => 'Track',
			'song_name'   => 'Title',
		],
		
		'controls' =>
		[
			'edit'   => 'Edit Album',
			'delete' => 'Delete Album',
			'report' => 'Report Album',
			
			'add_song'    => 'Add Track',
			'edit_song'   => 'Edit Track',
			'delete_song' => 'Delete Track',
			'fill_album'  => 'Fill Album with vgmdb.net'
		]
	],
	
	'artist' =>
	[
		'details'    => 'Details: ',
		'alias_of'   => 'Alias of: ',
		'aliases'    => 'Aliases',
		'characters' => 'Related Characters',
		'songs'      => 'Related Songs',
		
		'controls' =>
		[
			'edit'   => 'Edit Artist',
			'delete' => 'Delete Artist',
			'report' => 'Report Artist'
		]
	],
	
	'character' =>
	[
		'details' => 'Details: ',
		'games'   => 'Related Games',
		'artists' => 'Related Artists',
		'songs'   => 'Related Songs',
		
		'controls' =>
		[
			'edit'   => 'Edit Character',
			'delete' => 'Delete Character',
			'report' => 'Report Character'
		]
	],
	
	'lyrics' =>
	[
		'heading' =>
		[
			'lyrics'      => 'Lyrics of “$name”',
			'translation' => 'Translation of “$name” ($language)',
		],
		
		'no_lyrics' => 'Lyrics have not been added yet.',
		
		'description' =>
		[
			'original_song'   => 'Original song: $song',
			'album'           => 'Album: $album',
			'song'            => 'Song: $song',
			'translations'    => 'Translations: $translations',
			'performers'      => 'Performers: $performers',
			'character_voice' => '$character (CV. $artist)',
			'separator'       => ', '
		],
		
		'notes' =>
		[
			'heading'  => 'Notes',
			'no_notes' => 'No notes have been provided.'
		],
		
		'controls' =>
		[
			'lyrics' =>
			[
				'add'    => 'Add Lyrics',
				'edit'   => 'Edit Lyrics',
				'delete' => 'Delete Lyrics',
				'report' => 'Report Lyrics'
			],
			
			'translation' =>
			[
				'add'    => 'Add Translation',
				'edit'   => 'Edit Translation',
				'delete' => 'Delete Translation',
				'report' => 'Report Translation'
			],
			
			'font_size' =>
			[
				'name'  => 'Font Size',
				'size1' => 'Very Small',
				'size2' => 'Small',
				'size3' => 'Medium',
				'size4' => 'Big',
				'size5' => 'Very Big'
			],
			
			'furigana' =>
			[
				'name' => 'Furigana',
				'show' => 'On',
				'hide' => 'Off'
			],
			
			'notes' =>
			[
				'name' => 'Notes',
				'show' => 'On',
				'hide' => 'Off'
			],
			
			'colors' =>
			[
				'name' => 'Colors',
				'show' => 'On',
				'hide' => 'Off'
			]
		]
	],
	
	'feedback' =>
	[
		'heading' => 'Feedback',
		
		'description' =>
		[
			'one'   => 'Here you may say a couple of words about the website.',
			'two'   => 'All messages written here are public.',
			'three' => 'The staff read everything and may give a reply here.',
			'four'  => 'The message is limited with 500 symbols.'
		],
		
		'message' =>
		[
			'anonymous'     => '[Anonymous]',
			'heading'       => '$username [ID: #$id] ($timestamp)',
			'heading_staff' => 'Website Staff ($timestamp)'
		],
		
		'controls' =>
		[
			'hint'   => 'Text of your message',
			'delete' => 'Delete Entry',
			'reply'  => 'Send/Replace Reply'
		]
	],
	
	'report' =>
	[
		'heading' =>
		[
			'game'        => 'Report Game: $name',
			'album'       => 'Report Album: $name',
			'artist'      => 'Report Artist: $name',
			'character'   => 'Report Character: $name',
			'song'        => 'Report Song: $name',
			'lyrics'      => 'Report Lyrics: $name',
			'translation' => 'Report Translation: $name'
		],
		
		'description' =>
		[
			'one' => 'It seems that you found incomplete, incorrect or inappropriate information. Please, share the details.',
			'two' => 'If you expect a reply, please, send a mail to support@vn-song-lyrics-db.ru instead.'
		]
	],
	
	'user' =>
	[
		'heading'  => 'User: $name',
		'role'     => 'Role: $role',
		'created'  => 'Created: $timestamp',
		'about_me' => 'About Me',
		
		'controls' =>
		[
			'change_about_me' => 'Change “About Me”',
			'change_username' => 'Change Username',
			'change_email'    => 'Change Email',
			'change_password' => 'Change Password',
			'delete_account'  => 'Delete Account'
		],
		
		'contributions' =>
		[
			'heading'     => 'Contributions',
			'game'        => 'Games: $count',
			'album'       => 'Albums: $count',
			'artist'      => 'Artists: $count',
			'character'   => 'Characters: $count',
			'song'        => 'Songs: $count',
			'lyrics'      => 'Lyrics: $count',
			'translation' => 'Translations: $count'
		]
	],
	
	'about' =>
	[
		'heading' => 'About Website',
		
		'subheading1' => 'What is this website?',
		'subtext1'    => 'The website is a database for songs that you can hear in games of visual novel genre. '.
						 'These games usually have songs called “openings”, “inserts”, and “endings” like a cartoon or TV series. '.
						 'Probably, you would agree that it is not a common thing to do in games of other genres. '.
						 'That being said, you might be interested in official soundtracks of visual novels a little more because of these songs. '.
						 'However, most of the visual novels are made in Japan and it is rather difficult to find lyrics and translations for them. '.
						 'Not to mention, that the game genre is not mainstream at all, so the situation may be even more disappointing.',
		
		'subheading2' => 'Why is a website even needed?',
		'subtext2'    => 'If you like to listen to music, you might hum its lyrics and even may to be interested to know what the song is about. '.
						 'Of course, it means you probably tried to find its lyrics and, most probably, you know such websites as genius.com. '.
						 'Being a website made in countries of the Western Hemisphere, it does not really have songs came from the East. '.
						 'However, most of the visual novels are made in Japan and it is rather difficult to find lyrics and translations for them. '.
						 'And what’s more, here arises another problem: these websites are dedicated to individual artists that have no relation to games at all. ',
						 'Here comes this service trying to fill the niche: albums and songs here are related to games, artists and even game characters.',
		
		'subheading3' => 'How are lyrics transcribed?',
		'subtext3'    => 'Visual Novel soundtracks being sold officially usually have a dedicated booklet having comments of creators, lyrics, notes, and other stuff. '.
						 'It may come both in the paper form or in the digital copy. '.
						 'So, instead of transcribing by listening, it is easier to buy an edition of the soundtrack or simply look for such a book in the internet and write the lyrics using photos or screenshots.',
		
		'subheading4' => 'Who publishes lyrics here?',
		'subtext4'    => 'This website is a free wiki where each and every one may contribute. '.
						 'You too may add something to share it with everyone who loves music made specifically for visual novels.'
	],
	
	'policy' =>
	[
		'heading'     => 'Website Policy',
		'description' => 'The website strives to be a free database. '.
						 'It is a non-commercial project intended to share lyrics and translations of songs from visual novels across the world. '.
						 'The project rejects the idea of advertisement, product placement or any commercial profit. '.
						 'There is no opportunity to donate, sell or buy. Here you can only share.',
		'warning'     => 'By using the website, you agree with all policies mentioned below.',
		
		'content_policy' =>
		[
			'heading' => 'Content Policy',
			'one'     => '1. All contributions are public and available for anybody on the Internet. The user agrees that they claim no rights on any contribution.',
			'two'     => '2. Names and all translated names of games and game characters, artists and albums and all depictions of them are gathered from different online sources and may be subject to the licenses of their respective rightholders.',
			'three'   => '3. Lyrics are either transcribed with the use of materials provided in official editions of soundtracks or by listening. '.
						 'Therefore, they might look identical to the content on websites of the similar theme. '.
						 'In any case, lyrics belong only to their respective rightholders.',
			'four'    => '4. The user is not allowed to plagiarize translations regardless of whether they were published on this website or anywhere else.',
			'five'    => '5. All translations of all song lyrics belong to the public domain. '.
						 'No-one, including the website staff, is in right to claim ownership on this kind of content. '.
						 'Although it implies that anyone is in right to use them for any purpose, the website staff kindly asks you the following:',
			'six'     => '5a. When using the content, please, reference the website and creator of the translation in question,',
			'seven'   => '5b. Do not use translations for machine learning purposes.',
			'eight'   => '6. Any contribution may be changed or removed on the legal note of the rightholder of the entity in question.',
			'nine'    => '7. The website owner and staff are not liable for any damage or another kind of negative consequence that may be caused by the operation of the website or its content. '.
						 'It includes any malfunctions and malicious actions by a third party.',
			'ten'     => '8. The policy may be changed without notifying users.'
		],
		
		'content_warning' =>
		[
			'heading' => 'Content Warning',
			'one'     => '1. The website aims to be a database containing all songs regardless of the content in their lyrics and translations. It is possible that some content might be counted as offensive.',
			'two'     => '2. Some content may be illegal in some countries: visit the website at your own risk.',
			'three'   => '3. The database collects information from around the world and no content represents the opinion of the website staff.'
		],
		
		'rightholder' =>
		[
			'heading' => 'For Rightholder',
			'one'     => '1. If you are not comfortable with the fact that any data about you or your work is included in the database, please, contact us at support@vn-song-lyrics-db.ru.',
			'two'     => '2. The website is ready to find a solution to your claims.'
		],
		
		'privacy_policy' =>
		[
			'heading' => 'Privacy Policy',
			'one'     => '1. The user uses the service at their sole risk. The service is provided on basis “as is”. The website makes no warranties on anything.',
			'two'     => '2. All network traffic including information about the user (e.g. the browser they use or their IP address) may be kept for indefinite time.',
			'three'   => '3. The information about the user is collected automatically and is not shared with third parties, except for cases of legal notes from authorities or the hosting provider.',
			'four'    => '4. The website uses browser cookies. At the moment, the website uses the storage only to keep the unique session token.',
			'five'    => '5. The website provides an opportunity to delete the account immediately. In this case, all contributions remain and are marked with tag “Deleted User”.',
			'six'     => '6. The policy may be changed without notifying users.',
		],
		
		'last_updated' => 'Policies were last edited on 29 June 2026.',
		'timezone'     => 'The timezone of the website is UTC+3.'
	],
	
	'rules' =>
	[
		'heading' => 'Website Rules',
		
		'behavior' =>
		[
			'heading' => 'Behavior Rules',
			'one'     => '1. The user is expected to be over 18 years of age. The website does not have age verification, but some country may have limitations. The user visits the website at their own risk.',
			'two'     => '2. It is not allowed to have a username or self-introduction section having obscene, abusive or political meaning.',
			'three'   => '3. It is not allowed to post any materials that are not related to the website theme.',
			'four'    => '4. The website staff reserves the right to remove any controversial content.',
			'five'    => '5. The website staff reserves the right to edit or remove content that violates $writing_guide.',
			'six'     => '6. Any controversial behavior that does not fall under the abovementioned rules still may be counted as malicious.',
			'seven'   => '7. The website staff reserves the right to block the access to the website for any user or restrict their functional without a notification and explanation.',
		],
		
		'publication' =>
		[
			'heading' => 'Publication Rules',
			'one'     => '1. Under “games” are meant videogames of the “visual novel” genre. Using vndb.org to check the genre is recommended.',
			'two'     => '2. Under “albums” are meant official soundtracks published by developers or publishers and fan arrangements. So called “gamerips” are not allowed.',
			'three'   => '3. Under “artists” are meant vocalists. In rare cases, music bands may be meant too.',
			'four'    => '4. Under “characters” are meant singing characters of visual novels. Such characters are referenced as performers in game credits, album booklets and meta-tags of music files in the format similar to “CharacterName (CV. ArtistName)”.'
		],
		
		'access' =>
		[
			'heading' => 'Access Rules',
			'one'     => '1. Any user, registered or not, may file an anonymous report on any contribution.',
			'two'     => '2. Any user, registered or not, may submit feedback.',
			'three'   => '3. A registered user may add a contribution of any type.',
			'four'    => '4. A registered user may edit or delete their contribution of type “Game”, “Album”, “Artist”, and “Character” if its status is “Awaiting Staff Approval”.',
			'five'    => '5. A registered user may edit or delete their contribution of type “Lyrics” if its status is “Awaiting Staff Approval” and if the “Lyrics” in question has no “Translations”.',
			'six'     => '6. A registered user may edit or delete their contribution of type “Translation” regardless of the approval. Any edit changes the status to “Awaiting Staff Approval”.',
			'seven'   => '7. A user known for violating rules may be restricted from doing any changes on the website on any period. A violator may appeal through contacting the staff by mail.',
			'eight'   => '8. Only the website staff sets the status of the contribution.',
		],
		
		'last_updated' => 'Rules were last edited on 01 August 2026.',
		'timezone'     => 'The timezone of the website is UTC+3.'
	],
	
	'writing_guide' =>
	[
		'heading'  => 'Writing Guide',
		'contents' => 'Contents:',
		
		'lyrics' =>
		[
			'heading' => 'How To Write Lyrics',
			'one'     => 'Straight to the point:',
			'two'     => '1. Write lyrics in full. Do not use ※repeat or something alike.',
			'three'   => '2. Write only lyrics. Do not write [Verse 1], [Sings: Any Name] or anything like this.',
			'four'    => '3. Do not censor lyrics even if there are bad words. Write them as they are.',
			'five'    => '4. Do not fix the broken foreign language in phrases that the lyrics author made.',
			'six'     => '5. Correct mistakes if lyrics in the text source differ from the actual lyrics in the song.',
			'seven'   => '6. Listen to the song carefully and check whether your lyrics are correct.',
			'eight'   => '7. Read how to format text on this website.',
			'nine'    => '8. Use the “Notes” section if you want to add a comment or explanation.',
			'ten'     => 'P.S. If you are unsure whether words or opinions expressed in the song are legal, contact us at support@vn-song-lyrics-db.ru.'
		],
		
		'translation' =>
		[
			'heading'      => 'How To Translate Lyrics',
			'introduction' => 'The mistake of a beginning language learner is to find words in a dictionary and connect them directly to their translations. This is the wrong way.',
			
			'advise' =>
			[
				'one'   => 'A good translator has to keep in mind:',
				'two'   => '- cultural aspects,',
				'three' => '- original sounding,',
				'four'  => '- good phrasing,',
				'five'  => '- all kinds of tropes,',
				'six'   => '- styles of speech,',
				'seven' => '- and, of course, the original meaning.',
				'eight' => 'Maybe, there is something more to that? Good if you answered yes.'
			],
			
			'recommendations' =>
			[
				'one'   => 'Recommendations:',
				'two'   => '1. No AI or translation engine. Use your own brain instead.',
				'three' => '2. Use modern vocabularies that give definitions in the original language.',
				'four'  => '3. Use websites dedicated to help language acquisition: e.g. stackexchange.com, hinative.com and others. Explanations made by modern native speakers may become really useful.',
				'five'  => '4. Find more examples of use when in doubt: texts and posts in the Internet, songs and poems, vocabularies and databases, Youtube videos and shorts. Use everything.',
				'six'   => '5. Make a search engine your friend: learn how to use its features. For example, use straight quotation marks to search the exact word or phrase: "I love you" instead of I love you.',
				'seven' => '6. Use typographic symbols: “curly quotation marks”, «guillemets», ellipsis… and other symbols. Make your translation a fine piece of art.',
				'eight' => 'Perhaps, the language you want to translate to is still not added. Send us a mail or ask us to add a language in Feedback section.'
			]
		],
			
		'romanization' =>
		[
			'heading' => 'How To Romanize Lyrics And Names',
			
			'introduction' =>
			[
				'one'   => 'This section is dedicated to romanization of Japanese language as it is the language of the most of the songs. '.
						   'Romanization of other languages is not covered yet, so you may use any known romanization rules for them.',
				'two'   => 'So, about Japanese. Most probably, you heard about Hepburn romanization rules. On this website, another rules are used, though they look similar. '.
						   'The rules will be explained more in schemes and examples rather than in words. We hope it is more understandable.',
				'three' => 'Before we start, a little reminder: you agreed to comply with these rules when signed up. Whether you like them or not.'
			],
			
			'allowed_symbols' =>
			[
				'heading'     => 'Allowed Symbols',
				'description' => 'Romanization of Japanese symbols uses only characters visible on a regular English keyboard.'
			],
			
			'name_order' =>
			[
				'heading'     => 'Name Order',
				'description' => 'In Japan, first goes the surname and only then the given name. This order must be preserved in romanization.'
			],
			
			'capitalization' =>
			[
				'heading'     => 'Capitalizing Words',
				'description' => 'All words, except for particles, must be capitalized in names.'
			],
			
			'conversion' =>
			[
				'heading' => 'Kana Conversion Rules',
				'notes'   => 'Important Notes:'
			],
			
			'transliterating' =>
			[
				'heading' => 'Transliterating Katakana, Hiragana, Kanji, Other Languages',
				
				'description' =>
				[
					'one'   => '1. Words written in Japanese are written in lowercase, according to the table above.',
					'two'   => '2. Loanings are never replaced with words they were derived from.',
					'three' => '3. Words written not in Japanese are written in uppercase.'
				]
			],
			
			'together_or_apart' =>
			[
				'heading'  => 'Together or Apart',
				'together' => 'together',
				'apart'    => 'apart'
			],
			
			'other_rules' =>
			[
				'heading'          => 'Miscellanous Rules',
				'special_readings' => 'Special Readings',
				'character_width'  => 'Character Width',
				'full_width'       => 'full-width',
				'half_width'       => 'half-width',
				'punctuation'      => 'Punctuation',
				'asian'            => 'Asian Languages: ',
				'european'         => 'European Languages: '
			]
		],
		
		'formatting' =>
		[
			'heading' => 'How To Apply Formatting',
			
			'introduction' =>
			[
				'one'   => 'The text editor of the website supports special syntax to allow use of:',
				'two'   => '- furigana,',
				'three' => '- colored text,',
				'four'  => '- notes.'
			],
			
			'furigana' =>
			[
				'one'   => '1. Furigana is created with following syntax:',
				'two'   => ', where * is any number of any symbols.',
				'three' => 'Example:',
				'four'  => 'Use-case: irregular, special or ambiguous readings, rare words.'
			],
			
			'colors' =>
			[
				'one'   => '2. Colored text is created with following syntax:',
				'two'   => ', where XXXXXX is a hexadecimal value of RGB color and where * is any number of any symbols.',
				'three' => 'Example: ',
				'four'  => 'Use-case: several performers with different lines.'
			],
			
			'notes' =>
			[
				'one'   => '3. Notes are created with following syntax:',
				'two'   => ', where N is any natural number. Please, note that this string must be put both in notes and lyrics.',
				'three' => 'Example: ',
				'four'  => 'Use-case: connecting lyrics to comments.'
			],
			
			'example' =>
			[
				'one'     => 'If you are interested in use of these features, take look at the $example.',
				'example' => 'example'
			],
			
			'restrictions' =>
			[
				'one'   => 'Please, note that these features are available only in the following sections:',
				'two'   => '- “Lyrics”,',
				'three' => '- “Notes”,',
				'four'  => '- “Translation of Lyrics”,',
				'five'  => '- “Translator’s Notes”.',
				'six'   => 'These features are not imposed on you. Use them if you want to.'
			]
		],
		
		'useful_links' =>
		[
			'heading'     => 'Useful Links',
			'vndb'        => 'Visual novel database: ',
			'vgmdb'       => 'Video game music database: ',
			'steamgriddb' => 'Video game assets database: ',
			'jisho'       => 'English-Japanese dictionary #1: ',
			'jpdb'        => 'English-Japanese dictionary #2: ',
			'kotobank'    => 'Japanese-Japanese dictionary: '
		],
		
		'captcha' =>
		[
			'heading' => 'How To Solve Captcha'
		]
	],
	
	'lyrics_example' =>
	[
		'heading'    => 'Example of Lyrics and Formatting',
		'formatting' => 'Formatting',
		'markup'     => 'Markup',
		'result'     => 'Result',
		'japanese'   => 'Japanese',
		'notes'      => 'Notes'
	]
];
