CREATE DATABASE vn_songs_lyrics_db;

USE vn_songs_lyrics_db;

CREATE TABLE roles
(
	id             INT UNSIGNED PRIMARY KEY,
    technical_name VARCHAR(16)  NOT NULL,
    ru_name        VARCHAR(16)  NOT NULL,
    en_name        VARCHAR(16)  NOT NULL,
    ja_name        VARCHAR(16)  NOT NULL
);

CREATE TABLE users
(
	id                    INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    role_id               INT UNSIGNED   NOT NULL,
    username              VARCHAR(32)    NOT NULL       UNIQUE,
    password_hash         VARBINARY(255) NOT NULL,
    email                 VARCHAR(32)    NOT NULL       UNIQUE,
	ip_address            VARBINARY(45)  NOT NULL,
	
	timestamp_created     TIMESTAMP      NOT NULL,
	timestamp_last_log_in TIMESTAMP      NOT NULL,
	
	verification_token    VARBINARY(32),
	is_verified           BOOL           NOT NULL,
	
    FOREIGN KEY (role_id)                REFERENCES roles(id) ON DELETE RESTRICT
);

CREATE TABLE languages
(
	id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	own_name VARCHAR(32)  NOT NULL       UNIQUE,
    ru_name  VARCHAR(32)  NOT NULL       UNIQUE,
    en_name  VARCHAR(32)  NOT NULL       UNIQUE,
    ja_name  VARCHAR(32)  NOT NULL       UNIQUE
);

CREATE TABLE games
(
	id                  INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    original_name       VARCHAR(128)  NOT NULL       UNIQUE,
	transliterated_name VARCHAR(128)  NOT NULL       UNIQUE,
	localized_name      VARCHAR(128),
	uri                 VARCHAR(128)  NOT NULL       UNIQUE,
	vndb_id             INT UNSIGNED,
	is_image_uploaded   BOOLEAN       NOT NULL,
	
	user_added_id       INT UNSIGNED,
	timestamp_added     TIMESTAMP,
	user_updated_id     INT UNSIGNED,
    timestamp_updated   TIMESTAMP,
	user_reviewed_id    INT UNSIGNED,
	timestamp_reviewed  TIMESTAMP,
	
	status              ENUM('unchecked', 'checked', 'hidden') NOT NULL,
	
	FOREIGN KEY (user_added_id)    REFERENCES users(id) ON DELETE SET NULL,
	FOREIGN KEY (user_updated_id)  REFERENCES users(id) ON DELETE SET NULL,
	FOREIGN KEY (user_reviewed_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE albums
(
	id                  INT UNSIGNED      AUTO_INCREMENT PRIMARY KEY,
    original_name       VARCHAR(128)      NOT NULL       UNIQUE,
	transliterated_name VARCHAR(128)      NOT NULL       UNIQUE,
	localized_name      VARCHAR(128),
	uri                 VARCHAR(128)      NOT NULL       UNIQUE,
	vgmdb_id            INT UNSIGNED,
	is_image_uploaded   BOOLEAN           NOT NULL,
	
	song_count          SMALLINT UNSIGNED NOT NULL,
	
	user_added_id       INT UNSIGNED,
	timestamp_added     TIMESTAMP,
	user_updated_id     INT UNSIGNED,
    timestamp_updated   TIMESTAMP,
	user_reviewed_id    INT UNSIGNED,
	timestamp_reviewed  TIMESTAMP,
	
	status              ENUM('unchecked', 'checked', 'hidden') NOT NULL,
	
	FOREIGN KEY (user_added_id)   REFERENCES users(id) ON DELETE SET NULL,
	FOREIGN KEY (user_updated_id) REFERENCES users(id) ON DELETE SET NULL,
	FOREIGN KEY (user_reviewed_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE artists
(
	id                  INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    original_name       VARCHAR(64)   NOT NULL       UNIQUE,
	transliterated_name VARCHAR(64)   NOT NULL       UNIQUE,
	localized_name      VARCHAR(64),
	uri                 VARCHAR(64)   NOT NULL       UNIQUE,
	vgmdb_id            INT UNSIGNED,
	is_image_uploaded   BOOLEAN       NOT NULL,
	alias_of_artist_id  INT UNSIGNED,
	
	user_added_id       INT UNSIGNED,
	timestamp_added     TIMESTAMP,
	user_updated_id     INT UNSIGNED,
    timestamp_updated   TIMESTAMP,
	user_reviewed_id    INT UNSIGNED,
	timestamp_reviewed  TIMESTAMP,
	
	status              ENUM('unchecked', 'checked', 'hidden') NOT NULL,
	
	FOREIGN KEY (alias_of_artist_id) REFERENCES artists(id) ON DELETE SET NULL,
	
	FOREIGN KEY (user_added_id)      REFERENCES users(id)   ON DELETE SET NULL,
	FOREIGN KEY (user_updated_id)    REFERENCES users(id)   ON DELETE SET NULL,
	FOREIGN KEY (user_reviewed_id)   REFERENCES users(id)   ON DELETE SET NULL
);

CREATE TABLE characters
(
	id                  INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    original_name       VARCHAR(64)   NOT NULL,
	transliterated_name VARCHAR(64)   NOT NULL,
	localized_name      VARCHAR(64),
	uri                 VARCHAR(64)   NOT NULL       UNIQUE,
	vndb_id             INT UNSIGNED,
	is_image_uploaded   BOOLEAN       NOT NULL,
	
	user_added_id       INT UNSIGNED,
	timestamp_added     TIMESTAMP,
	user_updated_id     INT UNSIGNED,
    timestamp_updated   TIMESTAMP,
	user_reviewed_id    INT UNSIGNED,
	timestamp_reviewed  TIMESTAMP,
	
	status              ENUM('unchecked', 'checked', 'hidden') NOT NULL,
	
	FOREIGN KEY (user_added_id)    REFERENCES users(id) ON DELETE SET NULL,
	FOREIGN KEY (user_updated_id)  REFERENCES users(id) ON DELETE SET NULL,
	FOREIGN KEY (user_reviewed_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE songs
(
	id                  INT UNSIGNED      AUTO_INCREMENT PRIMARY KEY,
    original_name       VARCHAR(132)      NOT NULL,
	transliterated_name VARCHAR(132)      NOT NULL,
	localized_name      VARCHAR(132),
	uri                 VARCHAR(132)      NOT NULL,
	
    album_id            INT UNSIGNED,
    disc_number         TINYINT UNSIGNED  NOT NULL,
    track_number        SMALLINT UNSIGNED NOT NULL,
	
	has_vocal           BOOLEAN           NOT NULL,
    original_song_id    INT UNSIGNED,
	language_id         INT UNSIGNED,
    lyrics              TEXT,
	notes               TEXT,
    
	user_added_id       INT UNSIGNED,
	timestamp_added     TIMESTAMP,
	user_updated_id     INT UNSIGNED,
    timestamp_updated   TIMESTAMP,
	user_reviewed_id    INT UNSIGNED,
	timestamp_reviewed  TIMESTAMP,
	
	status              ENUM('unchecked', 'checked', 'hidden') NOT NULL,
	
	FOREIGN KEY (original_song_id) REFERENCES songs(id)     ON DELETE SET NULL,
	FOREIGN KEY (language_id)      REFERENCES languages(id) ON DELETE RESTRICT,
	FOREIGN KEY (album_id)         REFERENCES albums(id)    ON DELETE SET NULL,
	
	UNIQUE (album_id, disc_number, track_number),
	
	FOREIGN KEY (user_added_id)    REFERENCES users(id)     ON DELETE SET NULL,
	FOREIGN KEY (user_updated_id)  REFERENCES users(id)     ON DELETE SET NULL,
	FOREIGN KEY (user_reviewed_id) REFERENCES users(id)     ON DELETE SET NULL
);

CREATE TABLE translations
(
	id                 INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
	song_id            INT UNSIGNED,
	uri                VARCHAR(48)   NOT NULL,
	
	language_id        INT UNSIGNED  NOT NULL,
    name               VARCHAR(132)  NOT NULL,
    lyrics             TEXT          NOT NULL,
	notes              TEXT,
	
	user_added_id      INT UNSIGNED,
	timestamp_added    TIMESTAMP,
	user_updated_id    INT UNSIGNED,
    timestamp_updated  TIMESTAMP,
	user_reviewed_id   INT UNSIGNED,
	timestamp_reviewed TIMESTAMP,
	
	status             ENUM('unchecked', 'checked', 'hidden') NOT NULL,
	
    FOREIGN KEY (song_id)         REFERENCES songs(id)     ON DELETE SET NULL,
    FOREIGN KEY (language_id)     REFERENCES languages(id) ON DELETE RESTRICT,
	
	UNIQUE (song_id, language_id, user_added_id),
	
	FOREIGN KEY (user_added_id)    REFERENCES users(id)     ON DELETE SET NULL,
	FOREIGN KEY (user_updated_id)  REFERENCES users(id)     ON DELETE SET NULL,
	FOREIGN KEY (user_reviewed_id) REFERENCES users(id)     ON DELETE SET NULL
);

CREATE TABLE game_album_relations
(
    game_id  INT UNSIGNED NOT NULL,
    album_id INT UNSIGNED NOT NULL,
	
	status   ENUM('unchecked', 'checked', 'hidden') NOT NULL,
	
    FOREIGN KEY (game_id)  REFERENCES games(id)  ON DELETE CASCADE,
    FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE CASCADE,
	
	UNIQUE (game_id, album_id)
);

CREATE TABLE song_artist_character_relations
(
    song_id      INT UNSIGNED  NOT NULL,
    artist_id    INT UNSIGNED  NOT NULL,
    character_id INT UNSIGNED  NULL,
	
	status       ENUM('unchecked', 'checked', 'hidden') NOT NULL,
	
    FOREIGN KEY (song_id)      REFERENCES songs(id)      ON DELETE CASCADE,
    FOREIGN KEY (artist_id)    REFERENCES artists(id)    ON DELETE CASCADE,
    FOREIGN KEY (character_id) REFERENCES characters(id) ON DELETE CASCADE,
	
	UNIQUE (song_id, artist_id, character_id)
);

CREATE TABLE character_game_relations
(
    character_id INT UNSIGNED NOT NULL,
	game_id      INT UNSIGNED NOT NULL,
	
	status       ENUM('unchecked', 'checked', 'hidden') NOT NULL,
	
    FOREIGN KEY (character_id) REFERENCES characters(id) ON DELETE CASCADE,
    FOREIGN KEY (game_id)      REFERENCES games(id)      ON DELETE CASCADE,
	
	UNIQUE (character_id, game_id)
);

CREATE TABLE feedbacks
(
	id                INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
	sender_id         INT UNSIGNED,
	sender_ip         VARBINARY(45) NOT NULL,
    message           VARCHAR(250)  NOT NULL,
    message_timestamp TIMESTAMP     NOT NULL,
	moderator_id      INT UNSIGNED,
	reply             VARCHAR(250),
	reply_timestamp   TIMESTAMP,
	
    FOREIGN KEY (sender_id)    REFERENCES users(id) ON DELETE SET NULL,
	FOREIGN KEY (moderator_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE reports
(
	id             INT UNSIGNED  PRIMARY KEY AUTO_INCREMENT,
	sender_id      INT UNSIGNED,
	message        VARCHAR(500)  NOT NULL,
	request_uri    VARCHAR(256)  NOT NULL,
	user_agent     VARCHAR(256)  NOT NULL,
	timestamp_sent TIMESTAMP     NOT NULL,
	
	status         ENUM('unchecked', 'checked', 'hidden') NOT NULL,
	
	FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX index_uri ON games(uri);
CREATE INDEX index_uri ON albums(uri);
CREATE INDEX index_uri ON characters(uri);
CREATE INDEX index_uri ON artists(uri);
CREATE INDEX index_uri ON songs(uri);
CREATE INDEX index_uri ON translations(uri);

INSERT INTO
	roles(id, technical_name, ru_name, en_name, ja_name)
VALUES
	(0,    'visitor',         'Посетитель',      'Visitor',         'キャクジン'),
	(100,  'violator',        'Нарушитель',      'Violator',        'イハンシャ'),
    (200,  'user',            'Пользователь',    'User',            'ジュウニン'),
-- (300,  'contributor',     'Помощник',        'Contributor',     'エンジョシャ'),
-- (400,  'translator',      'Переводчик',      'Translator',      'ホンヤクシャ'),
-- (800,  'moderator',       'Модератор',       'Moderator',       'モデレーター'),
-- (900,  'super_moderator', 'Супер-модератор', 'Super Moderator', 'スーパーモデレーター'),
    (1000, 'administrator',   'Администратор',   'Administrator',   'アドミニストレーター');

INSERT INTO
	languages(own_name, ru_name, en_name, ja_name)
VALUES
	('English', 'Английский', 'English', '英語'),
	('Русский', 'Русский', 'Russian', 'ロシア語'),
	('日本語', 'Японский', 'Japanese', '日本語'),
	('Romanization', 'Транслитерация', 'Romanization', 'ローマ字');

-- Changes

CREATE TABLE fingerprints
(
	user_id    INT UNSIGNED   NOT NULL,
	ip_address VARBINARY(190) NOT NULL,
	
	UNIQUE (user_id, ip_address),
	FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE NO ACTION
);

ALTER TABLE `users` DROP `verification_token`;
ALTER TABLE `users` DROP `is_verified`;
ALTER TABLE `users` DROP `ip_address`;
ALTER TABLE `users` CHANGE `email` `email` VARBINARY(128) NOT NULL;
ALTER TABLE `users` CHANGE `password_hash` `password` VARBINARY(255) NOT NULL;
ALTER TABLE `reports` ADD `ip_address` VARBINARY(190) NOT NULL ;
