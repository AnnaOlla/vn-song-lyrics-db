<?php

return
[
	'header' =>
	[
		'home_page'        => 'vn-song-lyrics-db',
		'game_list'        => 'Игры',
		'album_list'       => 'Альбомы',
		'artist_list'      => 'Исполнители',
		'character_list'   => 'Персонажи',
		'song_list'        => 'Песни',
		'translation_list' => 'Переводы',
		'feedback'         => 'Отзывы',
		'log_in'           => 'Вход',
		'log_out'          => 'Выход из аккаунта',
		'sign_up'          => 'Регистрация',
		'language_en'      => 'EN',
		'language_ru'      => 'RU',
		'language_ja'      => 'JA'
	],
	
	'footer' =>
	[
		'author' => 'AnnaOlla, 2026',
		'about'  => 'О сайте',
		'policy' => 'Политика сайта',
		'rules'  => 'Правила сайта',
		'guide'  => 'Руководство',
		'email'  => 'support@vn-song-lyrics-db.ru'
	],
	
	'image_alt_text' =>
	[
		'no_game'      => 'Нет логотипа игры',
		'no_album'     => 'Нет обложки альбома',
		'no_artist'    => 'Нет фотографии исполнителя',
		'no_character' => 'Нет изображения персонажа',
		
		'game'      => 'Логотип игры $game',
		'album'     => 'Обложка альбома $album',
		'artist'    => 'Фотография исполнителя $artist',
		'character' => 'Изображение персонажа $character',
	],
	
	'timestamp' =>
	[
		'added'    => 'Добавлено',
		'updated'  => 'Обновлено',
		'reviewed' => 'Рассмотрено',
		'string'   => '$action $timestamp пользователем $username.',
		
		'deleted_user' => '«Удалённый пользователь»'
	],
	
	'moderation_status' =>
	[
		'relation_status' => 'Статус связи: $status.',
		'status'          => 'Статус: $status.',
		'unchecked'       => 'Ожидает подтверждения администрацией',
		'checked'         => 'Подтверждено',
		'hidden'          => 'Скрыто'
	],
	
	'input_error' =>
	[
		'none'                       => '',
		'captcha_invalid'            => 'Код был введён неверно.',
		'email_not_found'            => 'Почта не была найдена.',
		'email_invalid'              => 'Формат адреса почты не был соблюдён.',
		'email_taken'                => 'Данная почта не может быть использована.',
		'username_forbidden_symbols' => 'Имя пользователя содержало запрещённые символы.',
		'username_forbidden_length'  => 'Имя пользователя имело неверную длину.',
		'username_taken'             => 'Имя пользователя занято.',
		'password_incorrect'         => 'Пароль был введён неверно.',
		'password_forbidden_symbols' => 'Пароль содержал запрещённые символы.',
		'password_forbidden_length'  => 'Пароль имел неверную длину.'
	],
	
	'access_state' =>
	[
		'ok'                       => '',
		'user_is_visitor'          => 'Войдите в свой аккаунт или зарегиструйтесь.',
		'user_is_violator'         => 'Ваш аккаунт ограничен в праве изменения данных.',
		'user_is_not_author'       => 'Вы не являетесь пользователем, добавившим эту информацию.',
		'entity_is_hidden'         => 'Информация скрыта администрацией по формальным причинам.',
		'entity_is_checked'        => 'Эта информация была проверена администрацией.',
		'song_has_translations'    => 'У песни есть переводы.',
		'lyrics_not_original'      => 'Данная песня копирует слова из другой.',
		'lyrics_use_same_language' => 'Нельзя сделать перевод песни на язык её исполнения.',
		'user_used_same_language'  => 'Вы уже переводили данную песню на этот язык.'
	],
	
	'controls' =>
	[
		'search' =>
		[
			'hint' => 'Введите текст …'
		],
		
		'textarea' =>
		[
			'hint' => 'Введите текст …'
		],
		
		'fileupload' =>
		[
			'hint_idle'          => 'Выбрать файл (максимальный размер: 512 MiB) …',
			'hint_size_exceeded' => 'Файл слишком большой. Попробуйте другой файл …'
		],
		
		'select' =>
		[
			'hint_idle'   => 'Выбрать …',
			'hint_filter' => 'Фильтровать …'
		],
		
		'button' =>
		[
			'return'  => 'Назад',
			'confirm' => 'Подтверждаю',
			'submit'  => 'Отправить',
			'search'  => 'Искать'
		]
	]
];
