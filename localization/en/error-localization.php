<?php

return
[
	'400' =>
	[
		'reason' => 'You entered something either forbidden or invalid.',
		'hint'   => 'Please, go back and check the input one time more.'
	],
	
	'401' =>
	[
		'reason' => 'Only authorized users may have access to this page.',
		'hint'   => 'Make sure you are logged in.'
	],
	
	'402' =>
	[
		'reason' => 'Only those who pay for the server are allowed to visit this page.',
		'hint'   => '~♪~ Have a good day ~♪~'
	],
	
	'403' =>
	[
		'reason' => 'You are not allowed to visit this page.',
		'hint'   => 'There is nothing you can do to get access.'
	],
	
	'404' =>
	[
		'reason' => 'The requested page was not found.',
		'hint'   => 'Perhaps, it was renamed?'
	],
	
	'405' =>
	[
		'reason' => 'The server understood your request, but the selected method is not allowed.',
		'hint'   => 'Please, do not misuse the service.'
	],
	
	'406' =>
	[
		'reason' => 'The server does not support the language you selected.',
		'hint'   => 'Select one of the available languages in the header of the page.'
	],
	
	'409' =>
	[
		'reason' => 'The content you tried to add already exists in the database.',
		'hint'   => 'Please, check the existence of the content before you add it.'
	],
	
	'413' =>
	[
		'reason' => 'The file you tried to upload was too large.',
		'hint'   => 'Please, go back and choose another file.'
	],
	
	'415' =>
	[
		'reason' => 'The file you tried to upload has an incorrect type.',
		'hint'   => 'Please, go back and choose another file.'
	],
	
	'418' =>
	[
		'reason' => '',
		'hint'   => ''
	],
	
	'422' =>
	[
		'reason' => 'Your input failed to pass validation rules on the server.',
		'hint'   => 'Please, send a mail to support@vn-song-lyrics-db.ru with details.'
	],
	
	'429' =>
	[
		'reason' => 'Your rate limit has been exceeded.',
		'hint'   => 'Please, try again in 10 seconds. Do not open new pages so often.'
	],
	
	'451' =>
	[
		'reason' => 'The website got a legal note to restrict access to this page.',
		'hint'   => 'There is nothing you can do to get access.'
	],
	
	'500' =>
	[
		'reason' => 'The server has encountered an error while processing your request.',
		'hint'   => 'Try reloading the page. It would help if a momentary glitch occured.'
	],
	
	'501' =>
	[
		'reason' => 'The method to complete your request is unknown.',
		'hint'   => 'Please, do not misuse the service.'
	],
	
	'502' =>
	[
		'reason' => 'The server received an error from another one.',
		'hint'   => 'Please, try again later.'
	],
	
	'503' =>
	[
		'reason' => 'The website is under maintenance.',
		'hint'   => 'Please, visit us later.'
	]
];
