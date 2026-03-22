<?php

require_once 'models/model.php';

class VisitorModel extends Model
{
	public function __construct()
	{
		$this->pdo = getPdo('visitor');
	}
	
	final public function isUserRegistered(string $username): bool
	{
		$stmt = $this->pdo->prepare('SELECT id FROM users WHERE username = :username');
		$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		$stmt->execute();
		
		return ($stmt->rowCount() !== 0);
	}
	
	final public function isPasswordCorrect(string $email, string $password): bool
	{
		$stmt = $this->pdo->prepare('SELECT password_hash FROM users WHERE email = :email');
		$stmt->bindParam(':email', $email, PDO::PARAM_STR);
		$stmt->execute();
		
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		return password_verify($password, $user['password_hash']);
	}
	
	final public function isEmailRegistered(string $email): bool
	{
		$stmt = $this->pdo->prepare('SELECT id FROM users WHERE email = :email');
		$stmt->bindParam(':email', $email, PDO::PARAM_STR);
		$stmt->execute();
		
		return ($stmt->rowCount() !== 0);
	}
	
	final public function isAccountVerified(string $email): bool
	{
		$stmt = $this->pdo->prepare('SELECT is_verified FROM users WHERE email = :email');
		$stmt->bindParam(':email', $email, PDO::PARAM_STR);
		$stmt->execute();
		
		$value = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return $value['is_verified'] ?? false;
	}
	
	final public function getRandomCaptcha(int $length, int $strength): array
	{
		return JuliamoCaptcha::generateBase64Captcha($length, $strength);
	}
	
	final public function createUser
	(
		string      $username,
		string      $password,
		string      $email,
		string      $ipAddress
	): int
	{
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
		
		$stmt = $this->pdo->prepare
		(
			'
			INSERT INTO users
			(
				username,
				password_hash,
				email,
				role_id,
				timestamp_created,
				timestamp_last_log_in,
				ip_address,
				verification_token,
				is_verified
			)
			VALUES
			(
				:username,
				:password_hash,
				:email,
				(SELECT id FROM roles WHERE technical_name = "user"),
				NOW(),
				NOW(),
				:ip_address,
				NULL,
				TRUE
			)
			'
		);
		
		$stmt->bindParam(':username',      $username,          PDO::PARAM_STR);
		$stmt->bindParam(':password_hash', $passwordHash,      PDO::PARAM_STR);
		$stmt->bindParam(':email',         $email,             PDO::PARAM_STR);
		$stmt->bindParam(':ip_address',    $ipAddress,         PDO::PARAM_STR);
		//$stmt->bindParam(':token',         $verificationToken, PDO::PARAM_STR);
		$stmt->execute();
		
		return $stmt->rowCount();
	}
	
	final public function updateLastLogInTimestamp(string $email): void
	{
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				users
			SET
				timestamp_last_log_in = NOW()
			WHERE
				email = :email
			'
		);
		
		$stmt->bindParam(':email', $email, PDO::PARAM_STR);
		$stmt->execute();
	}
	
	final public function getUserData(string|null $email = null, string|null $username = null): array|false
	{
		if (!$email && !$username)
			throw new Exception(__METHOD__.' was called without arguments');
		
		$whereEmail    = $email    ? ' AND u.email = :email'       : '';
		$whereUsername = $username ? ' AND u.username = :username' : '';
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				u.username       AS user_username,
				u.id             AS user_id,
				u.email          AS user_email,
				r.id             AS role_id,
				r.technical_name AS role_technical_name,
				r.ru_name        AS language_ru_name,
				r.en_name        AS language_en_name,
				r.ja_name        AS language_ja_name
			FROM
				users AS u
			JOIN
				roles AS r
			ON
				r.id = u.role_id
			WHERE
				TRUE = TRUE'.
			$whereEmail.
			$whereUsername.'
			'
		);
		
		if ($email)
			$stmt->bindParam(':email',    $email,    PDO::PARAM_STR);
		if ($username)
			$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$userData = $stmt->fetch(PDO::FETCH_ASSOC);
		return $userData;
	}
	
	final public function addFeedback(int|null $senderId, string $senderIp, string $message): int
	{
		$stmt = $this->pdo->prepare
		(
			'
			INSERT INTO feedbacks
			(
				sender_id,
				sender_ip,
				message,
				message_timestamp
			)
			VALUES
			(
				:sender_id,
				:sender_ip,
				:message,
				NOW()
			)
			'
		);
		
		$stmt->bindParam(':sender_id', $senderId, PDO::PARAM_INT);
		$stmt->bindParam(':sender_ip', $senderIp, PDO::PARAM_STR);
		$stmt->bindParam(':message',   $message,  PDO::PARAM_STR);
		$stmt->execute();
		
		return $this->pdo->lastInsertId();
	}
	
	final public function addReport
	(
		int|null $senderId,
		string   $message,
		string   $entityUri,
		string   $userAgent
	): int
	{
		$stmt = $this->pdo->prepare
		(
			'
			INSERT INTO reports
			(
				sender_id,
				message,
				request_uri,
				user_agent,
				timestamp_sent
			)
			VALUES
			(
				:sender_id,
				:message,
				:entity_uri,
				:user_agent,
				NOW()
			)
			'
		);
		
		$stmt->bindParam(':sender_id',  $senderId,  PDO::PARAM_INT);
		$stmt->bindParam(':message',    $message,   PDO::PARAM_STR);
		$stmt->bindParam(':entity_uri', $entityUri, PDO::PARAM_STR);
		$stmt->bindParam(':user_agent', $userAgent, PDO::PARAM_STR);
		
		$stmt->execute();
		
		return $this->pdo->lastInsertId();
	}
	
	final public function getGameList
	(
		string|null $albumUri     = null,
		string|null $characterUri = null,
		string|null $userUri      = null
	): array
	{
		$selectAlbums      = '';
		$selectCharacters  = '';
		
		$joinAlbums        = '';
		$joinCharacters    = '';
		$joinUsers         = '';
		
		$whereAlbumUri     = '';
		$whereCharacterUri = '';
		$whereUserUri      = '';
		
		if (!is_null($albumUri))
		{
			$selectAlbums =
			'
				gar.status AS game_album_relation_status,
			';
			
			$joinAlbums =
			'
			JOIN
				game_album_relations AS gar
			ON
				g.id = gar.game_id
			JOIN
				albums AS a
			ON
				gar.album_id = a.id
			';
			
			$whereAlbumUri =
			'
			AND
				a.uri = :album_uri
			';
		}
		
		if (!is_null($characterUri))
		{
			$selectCharacters =
			'
				cgr.status AS character_game_relation_status,
			';
			
			$joinCharacters .=
			'
			JOIN
				character_game_relations AS cgr
			ON
				cgr.game_id = g.id
			JOIN
				characters AS c
			ON
				c.id = cgr.character_id
			';
			
			$whereCharacterUri =
			'
			AND
				c.uri = :character_uri
			';
		}
		
		if (!is_null($userUri))
		{
			$joinUsers =
			'
			JOIN
				users as u
			ON
				g.user_added_id = u.id
			';
			
			$whereUserUri =
			'
			AND
				u.username = :user_uri
			';
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT'.
				$selectAlbums.
				$selectCharacters.'
				g.id,
				g.original_name,
				g.transliterated_name,
				g.localized_name,
				g.is_image_uploaded,
				g.uri
			FROM
				games AS g'.
			$joinAlbums.
			$joinCharacters.
			$joinUsers.'
			WHERE
				TRUE = TRUE'.
			$whereAlbumUri.
			$whereCharacterUri.
			$whereUserUri.'
			ORDER BY
				g.transliterated_name ASC
			'
		);
		
		if (!is_null($albumUri))
			$stmt->bindParam(':album_uri',     $albumUri,     PDO::PARAM_STR);
		if (!is_null($characterUri))
			$stmt->bindParam(':character_uri', $characterUri, PDO::PARAM_STR);
		if (!is_null($userUri))
			$stmt->bindParam(':user_uri',      $userUri,      PDO::PARAM_STR);
		
		$stmt->execute();
		
		$gameList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $gameList;
	}
	
	final public function getAlbumList
	(
		string|null $gameUri = null,
		string|null $userUri = null
	): array
	{
		$selectGames  = '';
		
		$joinGames    = '';
		$joinUsers    = '';
		
		$whereGameUri = '';
		$whereUserUri = '';
		
		if (!is_null($gameUri))
		{
			$selectGames =
			'
				gar.status AS game_album_relation_status,
			';
			
			$joinGames =
			'
			JOIN
				game_album_relations AS gar
			ON
				gar.album_id = a.id
			JOIN
				games AS g
			ON
				gar.game_id = g.id
			';
			
			$whereGameUri =
			'
			AND
				g.uri = :game_uri
			';
		};
		
		if (!is_null($userUri))
		{
			$joinUsers =
			'
				JOIN
					users as u
				ON
					a.user_added_id = u.id
			';
			
			$whereUserUri =
			'
				AND
					u.username = :user_uri
			';
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT'.
				$selectGames.'
				a.id,
				a.original_name,
				a.transliterated_name,
				a.localized_name,
				a.is_image_uploaded,
				a.uri,
				a.song_count
			FROM
				albums AS a
			'.
			$joinGames.
			$joinUsers.'
			WHERE
				TRUE = TRUE
			'.
			$whereGameUri.
			$whereUserUri.'
			ORDER BY
				a.transliterated_name ASC
			'
		);
		
		if (!is_null($gameUri))
			$stmt->bindParam(':game_uri', $gameUri, PDO::PARAM_STR);
		if (!is_null($userUri))
			$stmt->bindParam(':user_uri', $userUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$albumList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $albumList;
	}
	
	final public function getArtistList
	(
		string|null $userUri     = null,
		int   |null $aliasesOfId = null
	): array
	{
		$joinUsers    = '';
		$whereUserUri = '';
		
		$joinArtists    = '';
		$whereAliasesOfId = '';
		
		if (!is_null($userUri))
		{
			$joinUsers =
			'
			JOIN
				users as u
			ON
				a.user_added_id = u.id
			';
			
			$whereUserUri =
			'
			AND
				u.username = :user_uri
			';
		}
		
		if (!is_null($aliasesOfId))
		{
			$whereAliasesOfId =
			'
			AND
				a.alias_of_artist_id = :aliases_of_id
			';
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				a.id,
				a.original_name,
				a.transliterated_name,
				a.localized_name,
				a.is_image_uploaded,
				a.uri
			FROM
				artists AS a'.
			$joinUsers.
			$joinArtists.'
			WHERE
				TRUE = TRUE
			'.
			$whereUserUri.
			$whereAliasesOfId.'
			ORDER BY
				a.transliterated_name ASC
			'
		);
		
		if (!is_null($userUri))
			$stmt->bindParam(':user_uri', $userUri, PDO::PARAM_STR);
		if (!is_null($aliasesOfId))
			$stmt->bindParam(':aliases_of_id', $aliasesOfId, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$artistList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $artistList;
	}
	
	final public function getCharacterList
	(
		string|null $gameUri = null,
		string|null $userUri = null
	): array
	{
		$selectGames  = '';
		$joinGames    = '';
		$whereGameUri = '';
		
		$joinUsers    = '';
		$whereUserUri = '';
		
		if (!is_null($gameUri))
		{
			$selectGames =
			'
				cgr.status AS character_game_relation_status,
			';
			
			$joinGames =
			'
			JOIN
				character_game_relations as cgr
			ON
				c.id = cgr.character_id
			JOIN
				games AS g
			ON
				cgr.game_id = g.id
			';
			
			$whereGameUri =
			'
			AND
				g.uri = :game_uri
			';
		}
		
		if (!is_null($userUri))
		{
			$joinUsers =
			'
				JOIN
					users as u
				ON
					c.user_added_id = u.id
			';
			
			$whereUserUri =
			'
				AND
					u.username = :user_uri
			';
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT'.
				$selectGames.'
				c.id,
				c.original_name,
				c.transliterated_name,
				c.localized_name,
				c.is_image_uploaded,
				c.uri
			FROM
				characters AS c'.
			$joinGames.
			$joinUsers.'
			WHERE
				TRUE = TRUE'.
			$whereGameUri.
			$whereUserUri.'
			ORDER BY
				c.transliterated_name ASC
			'
		);
		
		if (!is_null($gameUri))
			$stmt->bindParam(':game_uri', $gameUri, PDO::PARAM_STR);
		
		if (!is_null($userUri))
			$stmt->bindParam(':user_uri', $userUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$characterList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $characterList;
	}
	
	final public function getPerformerList(string $albumUri, string $songUri): array
	{
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				ar.id                  AS artist_id,
				ar.original_name       AS artist_original_name,
				ar.transliterated_name AS artist_transliterated_name,
				ar.localized_name      AS artist_localized_name,
				ar.uri                 AS artist_uri,
				ch.id                  AS character_id,
				ch.original_name       AS character_original_name,
				ch.transliterated_name AS character_transliterated_name,
				ch.localized_name      AS character_localized_name,
				ch.uri                 AS character_uri,
				sacr.status            AS song_artist_character_relation_status
			FROM
				artists AS ar
			JOIN
				song_artist_character_relations AS sacr
			ON
				ar.id = sacr.artist_id
			LEFT JOIN
				characters AS ch
			ON
				sacr.character_id = ch.id
			JOIN
				songs AS sn
			ON
				sacr.song_id = sn.id
			JOIN
				albums AS al
			ON
				al.id = sn.album_id
			WHERE
				sn.uri = :song_uri
			AND
				al.uri = :album_uri
			'
			//ORDER BY
			//	character_transliterated_name ASC,
			//	artist_transliterated_name ASC
		);
		
		$stmt->bindParam(':song_uri',  $songUri,  PDO::PARAM_STR);
		$stmt->bindParam(':album_uri', $albumUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$artistList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $artistList;
	}
	
	final public function getSongList
	(
		string|null $albumUri     = null,
		string|null $artistUri    = null,
		string|null $characterUri = null,
		bool  |null $hasVocal     = null,
		string|null $userUri      = null
	): array
	{
		$selectRelations   = '';
		
		$joinRelations     = '';
		$joinArtists       = '';
		$joinCharacters    = '';
		$joinUsers         = '';
		
		$whereAlbumUri     = '';
		$whereArtistUri    = '';
		$whereCharacterUri = '';
		$whereHasVocal     = '';
		$whereHasOriginal  = '';
		$whereUserUri      = '';
		
		$orderBy =
		'
				sn.transliterated_name
		';
		
		if (!is_null($albumUri))
		{
			$whereAlbumUri =
			'
			AND
				al.uri = :album_uri
			';
			
			$orderBy =
			'
				sn.disc_number ASC,
				sn.track_number ASC
			';
		}
		
		if (!is_null($artistUri))
		{
			$selectRelations =
			'
				sacr.status AS song_artist_character_relation_status,
			';
			
			$joinRelations =
			'
			JOIN
				song_artist_character_relations AS sacr
			ON
				sacr.song_id = sn.id
			';
			
			$joinArtists =
			'
			JOIN
				artists AS ar
			ON
				ar.id = sacr.artist_id
			';
			
			$whereArtistUri =
			'
			AND
				ar.uri = :artist_uri
			';
		}
		
		if (!is_null($characterUri))
		{
			$selectRelations =
			'
				sacr.status AS song_artist_character_relation_status,
			';
			
			$joinRelations =
			'
			JOIN
				song_artist_character_relations AS sacr
			ON
				sacr.song_id = sn.id
			';
			
			$joinCharacters =
			'
			JOIN
				characters AS ch
			ON
				ch.id = sacr.character_id
			';
			
			$whereCharacterUri =
			'
			AND
				ch.uri = :character_uri
			';
		}
		
		if (!is_null($hasVocal))
		{
			$whereHasVocal =
			'
			AND
				sn.has_vocal = :has_vocal
			';
		}
		
		if (!is_null($userUri))
		{
			$joinUsers =
			'
				JOIN
					users as us
				ON
					sn.user_added_id = us.id
			';
			
			$whereUserUri =
			'
				AND
					us.username = :user_uri
				AND
					sn.lyrics IS NOT NULL
			';
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT'.
				$selectRelations.'
				sn.id,
				sn.original_name,
				sn.transliterated_name,
				sn.localized_name,
				sn.uri,
				sn.has_vocal,
				sn.disc_number,
				sn.track_number,
				sn.user_added_id,
				sn.status,
				al.original_name         AS album_original_name,
				al.transliterated_name   AS album_transliterated_name,
				al.localized_name        AS album_localized_name,
				al.uri                   AS album_uri,
				al.is_image_uploaded
			FROM
				songs AS sn
			JOIN
				albums AS al
			ON
				al.id = sn.album_id
			'.
			$joinRelations.
			$joinArtists.
			$joinCharacters.
			$joinUsers.'
			WHERE
				TRUE = TRUE
			'.
			$whereAlbumUri.
			$whereArtistUri.
			$whereCharacterUri.
			$whereHasVocal.
			$whereUserUri.'
			ORDER BY
			'.
			$orderBy
		);
		
		if (!is_null($albumUri))
			$stmt->bindParam(':album_uri',     $albumUri,     PDO::PARAM_STR);
		if (!is_null($artistUri))
			$stmt->bindParam(':artist_uri',    $artistUri,    PDO::PARAM_STR);
		if (!is_null($characterUri))
			$stmt->bindParam(':character_uri', $characterUri, PDO::PARAM_STR);
		if (!is_null($hasVocal))
			$stmt->bindParam(':has_vocal',     $hasVocal,     PDO::PARAM_BOOL);
		if (!is_null($userUri))
			$stmt->bindParam(':user_uri',      $userUri,      PDO::PARAM_STR);
		
		$stmt->execute();
		
		$songList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $songList;
	}
	
	final public function getTranslationList
	(
		string|null $albumUri = null,
		string|null $songUri  = null,
		string|null $songId   = null,
		string|null $userUri  = null
	): array
	{
		$where = '';
		$orderBy = '';
		
		$joinUsers    = '';
		$whereUserUri = '';
		
		if (!is_null($albumUri) && !is_null($songUri))
		{
			$where =
			'
			AND
				al.uri = :album_uri
			AND
				sn.uri = :song_uri
			';
			
			$orderBy =
			'
			ORDER BY
				lg.en_name ASC,
				tr.user_added_id ASC
			';
		}
		else if (!is_null($songId))
		{
			$where =
			'
			AND
				sn.id = :song_id
			';
			
			$orderBy =
			'
			ORDER BY
				lg.en_name ASC,
				tr.user_added_id ASC
			';
		}
		else
		{
			$orderBy =
			'
			ORDER BY
				tr.name ASC
			';
		}
		
		if (!is_null($userUri))
		{
			$joinUsers =
			'
				JOIN
					users as us
				ON
					tr.user_added_id = us.id
			';
			
			$whereUserUri =
			'
				AND
					us.username = :user_uri
			';
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				al.transliterated_name AS album_transliterated_name,
				al.uri                 AS album_uri,
				al.is_image_uploaded   AS is_image_uploaded,
				sn.transliterated_name AS song_transliterated_name,
				sn.uri                 AS song_uri,
				tr.id,
				tr.name,
				tr.uri,
				lg.ru_name             AS language_ru_name,
				lg.en_name             AS language_en_name,
				lg.ja_name             AS language_ja_name
			FROM
				translations AS tr
			JOIN
				languages AS lg
			ON
				lg.id = tr.language_id
			JOIN
				songs AS sn
			ON
				sn.id = tr.song_id
			JOIN
				albums AS al
			ON
				al.id = sn.album_id
			'.
			$joinUsers.'
			WHERE
				TRUE = TRUE
			'.
			$where.
			$whereUserUri.
			$orderBy
		);
		
		if (!is_null($albumUri) && !is_null($songUri))
		{
			$stmt->bindParam(':album_uri', $albumUri, PDO::PARAM_STR);
			$stmt->bindParam(':song_uri',  $songUri,  PDO::PARAM_STR);
		}
		if (!is_null($songId))
			$stmt->bindParam(':song_id',   $songId,    PDO::PARAM_INT);
		if (!is_null($userUri))
			$stmt->bindParam(':user_uri',  $userUri,   PDO::PARAM_STR);
		
		$stmt->execute();
		
		$translationList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $translationList;
	}
	
	final public function getFeedbackList(): array
	{
		$stmt = $this->pdo->query
		(
			'
			SELECT
				fdb.id,
				usr.username            AS sender_username,
				fdb.message,
				fdb.message_timestamp,
				fdb.sender_ip,
				mdr.username            AS moderator_username,
				fdb.reply,
				fdb.reply_timestamp
			FROM
				feedbacks AS fdb
			LEFT JOIN
				users AS usr
			ON
				usr.id = fdb.sender_id
			LEFT JOIN
				users AS mdr
			ON
				mdr.id = fdb.moderator_id
			ORDER BY
				fdb.id DESC
			'
		);
		
		$feedbackList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $feedbackList;
	}
	
	final public function getGame(string $gameUri): array|bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				gm.id,
				gm.original_name,
				gm.transliterated_name,
				gm.localized_name,
				gm.uri,
				gm.is_image_uploaded,
				gm.vndb_id,
				gm.timestamp_added,
				gm.timestamp_updated,
				gm.timestamp_reviewed,
				gm.status,
				gm.user_added_id,
				gm.user_updated_id,
				gm.user_reviewed_id,
				u1.username              AS user_added,
				u2.username              AS user_updated,
				u3.username              AS user_reviewed
			FROM
				games AS gm
			LEFT JOIN
				users AS u1
			ON
				u1.id = gm.user_added_id
			LEFT JOIN
				users AS u2
			ON
				u2.id = gm.user_updated_id
			LEFT JOIN
				users AS u3
			ON
				u3.id = gm.user_reviewed_id
			WHERE
				gm.uri = :game_uri
			'
		);
		
		$stmt->bindParam(':game_uri', $gameUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$game = $stmt->fetch(PDO::FETCH_ASSOC);
		return $game;
	}
	
	final public function getAlbum(string $albumUri): array|bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				al.id,
				al.original_name,
				al.transliterated_name,
				al.localized_name,
				al.uri,
				al.vgmdb_id,
				al.is_image_uploaded,
				al.song_count,
				al.status,
				al.timestamp_added,
				al.timestamp_updated,
				al.timestamp_reviewed,
				al.user_added_id,
				al.user_updated_id,
				al.user_reviewed_id,
				u1.username              AS user_added,
				u2.username              AS user_updated,
				u3.username              AS user_reviewed
			FROM
				albums AS al
			LEFT JOIN
				game_album_relations AS gar
			ON
				gar.album_id = al.id
			LEFT JOIN
				games AS gm
			ON
				gm.id = gar.game_id
			LEFT JOIN
				users AS u1
			ON
				u1.id = al.user_added_id
			LEFT JOIN
				users AS u2
			ON
				u2.id = al.user_updated_id
			LEFT JOIN
				users AS u3
			ON
				u3.id = al.user_reviewed_id
			WHERE
				al.uri = :album_uri
			'
		);
		
		$stmt->bindParam(':album_uri', $albumUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$album = $stmt->fetch(PDO::FETCH_ASSOC);
		return $album;
	}
	
	final public function getArtist(string $artistUri): array|bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				ar.id,
				ar.original_name,
				ar.transliterated_name,
				ar.localized_name,
				ar.uri,
				ar.vgmdb_id,
				ar.is_image_uploaded,
				ar.alias_of_artist_id,
				ar.status,
				ar.timestamp_added,
				ar.timestamp_updated,
				ar.timestamp_reviewed,
				ar.user_added_id,
				ar.user_updated_id,
				ar.user_reviewed_id,
				u1.username              AS user_added,
				u2.username              AS user_updated,
				u3.username              AS user_reviewed,
				al.transliterated_name   AS alias_of_transliterated_name,
				al.uri                   AS alias_of_uri
			FROM
				artists AS ar
			LEFT JOIN
				users AS u1
			ON
				u1.id = ar.user_added_id
			LEFT JOIN
				users AS u2
			ON
				u2.id = ar.user_updated_id
			LEFT JOIN
				users AS u3
			ON
				u3.id = ar.user_reviewed_id
			LEFT JOIN
				artists AS al
			ON
				ar.alias_of_artist_id = al.id
			WHERE
				ar.uri = :artist_uri
			'
		);
		
		$stmt->bindParam(':artist_uri', $artistUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$artist = $stmt->fetch(PDO::FETCH_ASSOC);
		return $artist;
	}
	
	final public function getCharacter(string $characterUri): array|bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				ch.id,
				ch.original_name,
				ch.transliterated_name,
				ch.localized_name,
				ch.uri,
				ch.vndb_id,
				ch.is_image_uploaded,
				ch.status,
				ch.timestamp_added,
				ch.timestamp_updated,
				ch.timestamp_reviewed,
				ch.user_added_id,
				ch.user_updated_id,
				ch.user_reviewed_id,
				u1.username              AS user_added,
				u2.username              AS user_updated,
				u3.username              AS user_reviewed
			FROM
				characters AS ch
			LEFT JOIN
				users AS u1
			ON
				u1.id = ch.user_added_id
			LEFT JOIN
				users AS u2
			ON
				u2.id = ch.user_updated_id
			LEFT JOIN
				users AS u3
			ON
				u3.id = ch.user_reviewed_id
			WHERE
				ch.uri = :character_uri
			'
		);
		
		$stmt->bindParam(':character_uri', $characterUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$character = $stmt->fetch(PDO::FETCH_ASSOC);
		return $character;
	}
	
	final public function getSong
	(
		string|null $albumUri = null,
		string|null $songUri  = null,
		int   |null $songId   = null
	): array|bool
	{
		$whereAlbumUri = '';
		$whereSongUri  = '';
		$whereSongId   = '';
		
		if (!is_null($albumUri))
		{
			$whereAlbumUri =
			'
			AND
				al.uri = :album_uri
			';
		}
		
		if (!is_null($songUri))
		{
			$whereSongUri =
			'
			AND
				sn.uri = :song_uri
			';
		}
		
		if (!is_null($songId))
		{
			$whereSongId =
			'
			AND
				sn.id = :song_id
			';
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				sn.id,
				sn.disc_number,
				sn.track_number,
				sn.original_name,
				sn.transliterated_name,
				sn.localized_name,
				sn.uri,
				sn.has_vocal,
				sn.original_song_id,
				sn.language_id,
				sn.lyrics,
				sn.notes,
				sn.status,
				sn.timestamp_added,
				sn.timestamp_updated,
				sn.timestamp_reviewed,
				sn.user_added_id,
				sn.user_updated_id,
				sn.user_reviewed_id,
				lg.ru_name               AS language_ru_name,
				lg.en_name               AS language_en_name,
				lg.ja_name               AS language_ja_name,
				u1.username              AS user_added,
				u2.username              AS user_updated,
				u3.username              AS user_reviewed,
				al.uri                   AS album_uri,
				al.transliterated_name   AS album_transliterated_name
			FROM
				songs AS sn
			LEFT JOIN
				languages AS lg
			ON
				lg.id = sn.language_id
			LEFT JOIN
				users AS u1
			ON
				u1.id = sn.user_added_id
			LEFT JOIN
				users AS u2
			ON
				u2.id = sn.user_updated_id
			LEFT JOIN
				users AS u3
			ON
				u3.id = sn.user_reviewed_id
			JOIN
				albums AS al
			ON
				al.id = sn.album_id
			WHERE
				TRUE = TRUE
			'.
			$whereAlbumUri.
			$whereSongUri.
			$whereSongId
		);
		
		if (!is_null($albumUri))
			$stmt->bindParam(':album_uri', $albumUri, PDO::PARAM_STR);
		if (!is_null($songUri))
			$stmt->bindParam(':song_uri',  $songUri,  PDO::PARAM_STR);
		if (!is_null($songId))
			$stmt->bindParam(':song_id',   $songId,   PDO::PARAM_INT);
		
		$stmt->execute();
		
		$song = $stmt->fetch(PDO::FETCH_ASSOC);
		return $song;
	}
	
	final public function getTranslation
	(
		string $albumUri,
		string $songUri,
		string $translationUri
	): array|bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				tr.id,
				tr.song_id,
				tr.language_id,
				tr.name,
				tr.uri,
				tr.lyrics,
				tr.notes,
				tr.status,
				tr.timestamp_added,
				tr.timestamp_updated,
				tr.timestamp_reviewed,
				tr.user_added_id,
				tr.user_updated_id,
				tr.user_reviewed_id,
				u1.username            AS user_added,
				u2.username            AS user_updated,
				u3.username            AS user_reviewed,
				lg.ru_name             AS language_ru_name,
				lg.en_name             AS language_en_name,
				lg.ja_name             AS language_ja_name,
				al.uri                 AS album_uri,
				al.transliterated_name AS album_transliterated_name,
				sn.uri                 AS song_uri,
				sn.transliterated_name AS song_transliterated_name
			FROM
				translations AS tr
			JOIN
				languages AS lg
			ON
				lg.id = tr.language_id
			JOIN
				songs AS sn
			ON
				sn.id = tr.song_id
			JOIN
				albums AS al
			ON
				al.id = sn.album_id
			LEFT JOIN
				users AS u1
			ON
				u1.id = tr.user_added_id
			LEFT JOIN
				users AS u2
			ON
				u2.id = tr.user_updated_id
			LEFT JOIN
				users AS u3
			ON
				u3.id = tr.user_reviewed_id
			WHERE
				tr.uri = :translation_uri
			AND
				sn.uri = :song_uri
			AND
				al.uri = :album_uri
			'
		);
		
		$stmt->bindParam(':translation_uri', $translationUri, PDO::PARAM_STR);
		$stmt->bindParam(':song_uri',        $songUri,        PDO::PARAM_STR);
		$stmt->bindParam(':album_uri',       $albumUri,       PDO::PARAM_STR);
		
		$stmt->execute();
		
		$translation = $stmt->fetch(PDO::FETCH_ASSOC);
		return $translation;
	}
	
	final public function getLastAddedAlbums(int $count): array
	{
		$stmt = $this->pdo->query
		(
			'
			SELECT
				a.transliterated_name,
				a.is_image_uploaded,
				a.uri
			FROM
				albums AS a
			ORDER BY
				a.timestamp_added DESC
			LIMIT '.$count.'
			'
		);
		
		$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $albums;
	}
	
	final public function getLastAddedLyrics(int $count): array
	{
		$stmt = $this->pdo->query
		(
			'
			SELECT
				s.transliterated_name,
				s.uri,
				a.transliterated_name AS album_transliterated_name,
				a.is_image_uploaded,
				a.uri                 AS album_uri
			FROM
				songs AS s
			JOIN
				albums AS a
			ON
				s.album_id = a.id
			WHERE
				s.has_vocal = TRUE
			AND
				s.lyrics IS NOT NULL
			ORDER BY
				s.timestamp_added DESC
			LIMIT '.$count.'
			'
		);
		
		$lyrics = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $lyrics;
	}
	
	final public function getLastAddedTranslations(int $count): array
	{
		$stmt = $this->pdo->query
		(
			'
			SELECT
				t.name,
				t.uri,
				s.transliterated_name AS song_transliterated_name,
				s.uri                 AS song_uri,
				a.transliterated_name AS album_transliterated_name,
				a.is_image_uploaded,
				a.uri                 AS album_uri
			FROM
				translations AS t
			JOIN
				songs AS s
			ON
				t.song_id = s.id
			JOIN
				albums AS a
			ON
				s.album_id = a.id
			ORDER BY
				t.timestamp_added DESC
			LIMIT '.$count.'
			'
		);
		
		$translations = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $translations;
	}
}
