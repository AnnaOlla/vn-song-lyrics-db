<?php

return
[
	'header' =>
	[
		'home_page'        => 'vn-song-lyrics-db',
		'game_list'        => 'ゲーム',
		'album_list'       => 'アルバム',
		'artist_list'      => 'カシュ',
		'character_list'   => 'キャラ',
		'song_list'        => 'ウタ',
		'translation_list' => 'ホンヤク',
		'feedback'         => 'コメント',
		'log_in'           => 'ログイン',
		'log_out'          => 'ログアウト',
		'sign_up'          => 'サインアップ',
		'language_en'      => 'EN',
		'language_ru'      => 'RU',
		'language_ja'      => 'JA'
	],
	
	'footer' =>
	[
		'author' => 'AnnaOlla, 2026',
		'about'  => 'サイトについて',
		'policy' => 'サイトのポリシー',
		'rules'  => 'サイトのルール',
		'guide'  => '文章作法',
		'email'  => 'support@vn-song-lyrics-db.ru'
	],
	
	'image_alt_text' =>
	[
		'no_game'      => 'ゲームのロゴはありません',
		'no_album'     => 'アルバムのカバーはありません',
		'no_artist'    => '歌手の写真はありません',
		'no_character' => 'キャラのイメージはありません',
		
		'game'      => '「$game」ゲームのロゴ',
		'album'     => '「$album」アルバムのカバー',
		'artist'    => '「$artist」歌手の写真',
		'character' => '「$character」キャラのイメージ',
	],
	
	'timestamp' =>
	[
		'added'    => '加え',
		'updated'  => '修正',
		'reviewed' => '確認',
		'string'   => '$action：$timestamp、$username。',
		
		'deleted_user' => '［削除したユーザー］'
	],
	
	'moderation_status' =>
	[
		'relation_status' => '関係のステータス：$status。',
		'status'          => 'ステータス：$status。',
		'unchecked'       => 'まだ確認されなかった',
		'checked'         => '確認された',
		'hidden'          => '隠された'
	],
	
	'input_error' =>
	[
		'none'                       => '',
		'captcha_invalid'            => 'コードは正しくなかったです。',
		'email_not_found'            => 'メールは見つかっていません。',
		'email_invalid'              => 'メールの書き方は正しくなかったです。',
		'email_taken'                => 'メールは使ってはいけません。',
		'username_forbidden_symbols' => 'ユーザーネームは許さない文字を持っています。',
		'username_forbidden_length'  => 'ユーザーネームの長さは正しくなかったです。',
		'username_taken'             => 'ユーザーネームはもう使われています。',
		'password_incorrect'         => 'パスワードは正しくなかったです。',
		'password_forbidden_symbols' => 'パスワードは許さない文字を持っています。',
		'password_forbidden_length'  => 'パスワードの長さは正しくなかったです。'
	],
	
	'access_state' =>
	[
		'ok'                       => '',
		'user_is_visitor'          => '貢献するのようにログインしなければなりません。',
		'user_is_violator'         => 'あなたのアカウントは限られています。',
		'user_is_not_author'       => 'この情報を出した者はあなたではありません。',
		'entity_is_hidden'         => 'サイトは公式の手紙もらって、アクセスは限られています。',
		'entity_is_checked'        => '情報はスタッフに確認されました。',
		'song_has_translations'    => '歌は翻訳があります。',
		'lyrics_not_original'      => '原作の歌のページに訪れてください。',
		'lyrics_use_same_language' => 'これは歌の言語です。',
		'user_used_same_language'  => 'あなたはこの言語に歌をもう翻訳しました。'
	],
	
	'controls' =>
	[
		'search' =>
		[
			'hint' => '入力して…'
		],
		
		'textarea' =>
		[
			'hint' => '入力して…'
		],
		
		'fileupload' =>
		[
			'hint_idle'          => 'ファイルを選ぶ（ｍａｘ：２ＭｉＢ）…',
			'hint_size_exceeded' => 'ファイルは大きすぎています…'
		],
		
		'select' =>
		[
			'hint_idle'   => '選ぶ…',
			'hint_filter' => '探す…'
		],
		
		'button' =>
		[
			'return'  => '戻る',
			'confirm' => '確認',
			'submit'  => '出す',
			'search'  => '探す'
		]
	]
];
