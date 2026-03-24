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
		
		$where = ['TRUE'];
		$binds = [];
		
		if ($email)
		{
			$where[] = 'u.email = :email';
			$binds[] = [':email', $email, PDO::PARAM_STR];
		}
		
		if ($username)
		{
			$where[] = 'u.username = :username';
			$binds[] = [':username', $username, PDO::PARAM_STR];
		}
		
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
				'.implode(' AND ', $where).'
			'
		);
		
		foreach ($binds as $bind)
			$stmt->bindParam($bind[0], $bind[1], $bind[2]);
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
		bool        $fetchMinInfo = false,
		string|null $albumUri     = null,
		string|null $characterUri = null,
		string|null $userAddedUri = null,
		array       $orderBy      = ['g.transliterated_name ASC']
	): array
	{
		$select = ['g.id', 'g.transliterated_name'];
		$from   = ['games AS g'];
		$join   = [];
		$where  = ['TRUE'];
		$binds  = [];
		
		if (!$fetchMinInfo)
		{
			$select[] = 'g.original_name';
			$select[] = 'g.localized_name';
			$select[] = 'g.is_image_uploaded';
			$select[] = 'g.uri';
		}
		
		if (!is_null($albumUri))
		{
			$select[] = 'gar.status AS game_album_relation_status';
			$join[]   =
			'
			JOIN
				game_album_relations AS gar
			ON
				g.id = gar.game_id
			JOIN
				albums AS a
			ON
				a.id = gar.album_id
			';
			$where[]  = 'a.uri = :album_uri';
			$binds[]  = [':album_uri', $albumUri, PDO::PARAM_STR];
		}
		
		if (!is_null($characterUri))
		{
			$select[] = 'cgr.status AS character_game_relation_status';
			$join[]   =
			'
			JOIN
				character_game_relations AS cgr
			ON
				g.id = cgr.game_id
			JOIN
				characters AS c
			ON
				c.id = cgr.character_id
			';
			$where[]  = 'c.uri = :character_uri';
			$binds[]  = [':character_uri', $characterUri, PDO::PARAM_STR];
		}
		
		if (!is_null($userAddedUri))
		{
			$join[]   =
			'
			JOIN
				users AS u
			ON
				g.user_added_id = u.id
			';
			$where[]  = 'u.username = :user_added_uri';
			$binds[]  = [':user_added_uri', $userAddedUri, PDO::PARAM_STR];
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				'.implode(', ', $select).'
			FROM
				'.implode(', ', $from).'
			
			'.implode("\n", $join).'
			
			WHERE
				'.implode(' AND ', $where).'
			ORDER BY
				'.implode(', ', $orderBy).'
			'
		);
		
		foreach ($binds as $bind)
			$stmt->bindParam($bind[0], $bind[1], $bind[2]);
		$stmt->execute();
		
		$gameList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $gameList;
	}
	
	final public function getAlbumList
	(
		bool        $fetchMinInfo = false,
		string|null $gameUri      = null,
		string|null $userAddedUri = null,
		array       $orderBy      = ['a.transliterated_name ASC']
	): array
	{
		$select = ['a.id', 'a.transliterated_name'];
		$from   = ['albums AS a'];
		$join   = [];
		$where  = ['TRUE'];
		$binds  = [];
		
		if (!$fetchMinInfo)
		{
			$select[] = 'a.original_name';
			$select[] = 'a.localized_name';
			$select[] = 'a.is_image_uploaded';
			$select[] = 'a.uri';
			$select[] = 'a.song_count';
		}
		
		if (!is_null($gameUri))
		{
			$select[] = 'gar.status AS game_album_relation_status';
			$join[]   =
			'
			JOIN
				game_album_relations AS gar
			ON
				a.id = gar.album_id
			JOIN
				games AS g
			ON
				g.id = gar.game_id
			';
			$where[]  = 'g.uri = :game_uri';
			$binds[]  = [':game_uri', $gameUri, PDO::PARAM_STR];
		}
		
		if (!is_null($userAddedUri))
		{
			$join[]   =
			'
			JOIN
				users AS u
			ON
				a.user_added_id = u.id
			';
			$where[]  = 'u.username = :user_added_uri';
			$binds[]  = [':user_added_uri', $userAddedUri, PDO::PARAM_STR];
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				'.implode(', ', $select).'
			FROM
				'.implode(', ', $from).'
			
			'.implode("\n", $join).'
			
			WHERE
				'.implode(' AND ', $where).'
			ORDER BY
				'.implode(', ', $orderBy).'
			'
		);
		
		foreach ($binds as $bind)
			$stmt->bindParam($bind[0], $bind[1], $bind[2]);
		$stmt->execute();
		
		$albumList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $albumList;
	}
	
	final public function getArtistList
	(
		bool        $fetchMinInfo = false,
		string|null $userAddedUri = null,
		int   |null $aliasesOfId  = null,
		bool  |null $mayBeAlias   = null,
		array       $orderBy      = ['a.transliterated_name ASC']
	): array
	{
		$select = ['a.id', 'a.transliterated_name'];
		$from   = ['artists AS a'];
		$join   = [];
		$where  = ['TRUE'];
		$binds  = [];
		
		if (!$fetchMinInfo)
		{
			$select[] = 'a.original_name';
			$select[] = 'a.localized_name';
			$select[] = 'a.is_image_uploaded';
			$select[] = 'a.uri';
		}
		
		if (!is_null($userAddedUri))
		{
			$join[]   =
			'
			JOIN
				users AS u
			ON
				a.user_added_id = u.id
			';
			$where[]  = 'u.username = :user_added_uri';
			$binds[]  = [':user_added_uri', $userAddedUri, PDO::PARAM_STR];
		}
		
		if (!is_null($aliasesOfId))
		{
			$where[]  = 'a.alias_of_artist_id = :aliases_of_id';
			$binds[]  = [':aliases_of_id', $aliasesOfId, PDO::PARAM_STR];
		}
		
		if ($mayBeAlias === false)
		{
			$where[]  = 'a.alias_of_artist_id IS NULL';
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				'.implode(', ', $select).'
			FROM
				'.implode(', ', $from).'
			
			'.implode("\n", $join).'
			
			WHERE
				'.implode(' AND ', $where).'
			ORDER BY
				'.implode(', ', $orderBy).'
			'
		);
		
		foreach ($binds as $bind)
			$stmt->bindParam($bind[0], $bind[1], $bind[2]);
		$stmt->execute();
		
		$artistList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $artistList;
	}
	
	final public function getCharacterList
	(
		bool        $fetchMinInfo = false,
		string|null $gameUri      = null,
		string|null $userAddedUri = null,
		array       $orderBy      = ['c.transliterated_name ASC']
	): array
	{
		$select = ['c.id', 'c.transliterated_name'];
		$from   = ['characters AS c'];
		$join   = [];
		$where  = ['TRUE'];
		$binds  = [];
		
		if (!$fetchMinInfo)
		{
			$select[] = 'c.original_name';
			$select[] = 'c.localized_name';
			$select[] = 'c.is_image_uploaded';
			$select[] = 'c.uri';
		}
		
		if (!is_null($gameUri))
		{
			$select[] = 'cgr.status AS character_game_relation_status';
			$join[]   =
			'
			JOIN
				character_game_relations AS cgr
			ON
				c.id = cgr.character_id
			JOIN
				games AS g
			ON
				g.id = cgr.game_id
			';
			$where[]  = 'g.uri = :game_uri';
			$binds[]  = [':game_uri', $gameUri, PDO::PARAM_STR];
		}
		
		if (!is_null($userAddedUri))
		{
			$join[]   =
			'
			JOIN
				users AS u
			ON
				c.user_added_id = u.id
			';
			$where[]  = 'u.username = :user_added_uri';
			$binds[]  = [':user_added_uri', $userAddedUri, PDO::PARAM_STR];
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				'.implode(', ', $select).'
			FROM
				'.implode(', ', $from).'
			
			'.implode("\n", $join).'
			
			WHERE
				'.implode(' AND ', $where).'
			ORDER BY
				'.implode(', ', $orderBy).'
			'
		);
		
		foreach ($binds as $bind)
			$stmt->bindParam($bind[0], $bind[1], $bind[2]);
		$stmt->execute();
		
		$characterList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $characterList;
	}
	
	final public function getPerformerList
	(
		bool        $fetchMinInfo = false,
		string|null $albumUri     = null,
		string|null $songUri      = null,
		array       $orderBy      = ['ch.transliterated_name ASC', 'ar.transliterated_name ASC']
	): array
	{
		$select =
		[
			'ar.id                  AS artist_id',
			'ar.transliterated_name AS artist_transliterated_name',
			'ch.id                  AS character_id',
			'ch.transliterated_name AS character_transliterated_name',
			'sacr.status            AS song_artist_character_relation_status'
		];
		$where  = ['TRUE'];
		$binds  = [];
		
		if (!$fetchMinInfo)
		{
			$select[] = 'ar.original_name       AS artist_original_name';
			$select[] = 'ar.localized_name      AS artist_localized_name';
			$select[] = 'ar.uri                 AS artist_uri';
			$select[] = 'ch.original_name       AS character_original_name';
			$select[] = 'ch.localized_name      AS character_localized_name';
			$select[] = 'ch.uri                 AS character_uri';
		}
		
		if (!is_null($albumUri))
		{
			$where[] = 'al.uri = :album_uri';
			$binds[] = [':album_uri', $albumUri, PDO::PARAM_STR];
		}
		
		if (!is_null($songUri))
		{
			$where[] = 'sn.uri = :song_uri';
			$binds[] = [':song_uri', $songUri, PDO::PARAM_STR];
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				'.implode(', ', $select).'
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
				'.implode(' AND ', $where).'
			ORDER BY
				'.implode(', ', $orderBy).'
			'
		);
		
		foreach ($binds as $bind)
			$stmt->bindParam($bind[0], $bind[1], $bind[2]);
		$stmt->execute();
		
		$performerList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $performerList;
	}
	
	final public function getSongList
	(
		bool        $fetchMinInfo = false,
		string|null $albumUri     = null,
		string|null $artistUri    = null,
		string|null $characterUri = null,
		bool  |null $hasVocal     = null,
		bool  |null $isOriginal   = null,
		int   |null $excludeId    = null,
		string|null $userAddedUri = null,
		array       $orderBy      = ['sn.transliterated_name ASC']
	): array
	{
		$select = ['sn.id', 'sn.transliterated_name'];
		$from   = ['songs AS sn'];
		$join   = [];
		$where  = ['TRUE'];
		$binds  = [];
		
		if (!$fetchMinInfo)
		{
			$select[] = 'sn.original_name';
			$select[] = 'sn.localized_name';
			$select[] = 'sn.uri';
			$select[] = 'sn.has_vocal';
			$select[] = 'sn.disc_number';
			$select[] = 'sn.track_number';
			$select[] = 'sn.user_added_id';
			$select[] = 'sn.status';
			$select[] = 'al.original_name       AS album_original_name';
			$select[] = 'al.transliterated_name AS album_transliterated_name';
			$select[] = 'al.localized_name      AS album_localized_name';
			$select[] = 'al.uri                 AS album_uri';
			$select[] = 'al.is_image_uploaded';
		}
		
		if (!is_null($albumUri))
		{
			$where[]  = 'al.id = sn.album_id';
			$where[]  = 'al.uri = :album_uri';
			$binds[]  = [':album_uri', $albumUri, PDO::PARAM_STR];
		}
		
		if (!is_null($artistUri) || !is_null($characterUri))
		{
			$select[] = 'sacr.status AS song_artist_character_relation_status';
			$join[]   = 
			'
			JOIN
				song_artist_character_relations AS sacr
			ON
				sacr.song_id = sn.id
			';
		}
		
		if (!is_null($artistUri))
		{
			$join[]   = 
			'
			JOIN
				artists AS ar
			ON
				ar.id = sacr.artist_id
			';
			$where[]  = 'ar.uri = :artist_uri';
			$binds[]  = [':artist_uri', $artistUri, PDO::PARAM_STR];
		}
		
		if (!is_null($characterUri))
		{
			$join[]   = 
			'
			JOIN
				characters AS ch
			ON
				ch.id = sacr.character_id
			';
			$where[]  = 'ch.uri = :character_uri';
			$binds[]  = [':character_uri', $characterUri, PDO::PARAM_STR];
		}
		
		if (!is_null($hasVocal))
		{
			$where[]  = 'sn.has_vocal = :has_vocal';
			$binds[]  = [':has_vocal', $hasVocal, PDO::PARAM_BOOL];
		}
		
		if ($isOriginal === true)
		{
			$where[]  = 'sn.original_song_id IS NULL';
			$where[]  = 'sn.lyrics IS NOT NULL';
		}
		
		if ($isOriginal === false)
		{
			$where[]  = 'sn.original_song_id IS NOT NULL';
		}
		
		if (!is_null($hasVocal))
		{
			$where[]  = 'sn.has_vocal = :has_vocal';
			$binds[]  = [':has_vocal', $hasVocal, PDO::PARAM_BOOL];
		}
		
		if (!is_null($excludeId))
		{
			$where[]  = 'sn.id <> :exclude_id';
			$binds[]  = [':exclude_id', $excludeId, PDO::PARAM_INT];
		}
		
		if (!is_null($userAddedUri))
		{
			$join[]   = 
			'
			JOIN
				users as us
			ON
				sn.user_added_id = us.id
			';
			$where[]  = 'us.username = :user_added_uri';
			$binds[]  = [':user_added_uri', $userAddedUri, PDO::PARAM_STR];
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				'.implode(', ', $select).'
			FROM
				'.implode(', ', $from).'
			JOIN
				albums AS al
			ON
				sn.album_id = al.id
			
			'.implode("\n", $join).'
			
			WHERE
				'.implode(' AND ', $where).'
			ORDER BY
				'.implode(', ', $orderBy).'
			'
		);
		
		foreach ($binds as $bind)
			$stmt->bindParam($bind[0], $bind[1], $bind[2]);
		$stmt->execute();
		
		$songList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $songList;
	}
	
	final public function getTranslationList
	(
		bool        $fetchMinInfo = false,
		string|null $albumUri     = null,
		string|null $songUri      = null,
		string|null $userAddedUri = null,
		array       $orderBy      = ['tr.id']
	): array
	{
		$select = ['tr.id', 'tr.name', 'tr.language_id'];
		$from   = ['translations AS tr'];
		$join   = [];
		$where  = ['TRUE'];
		$binds  = [];
		
		if (!$fetchMinInfo)
		{
			$select[] = 'al.transliterated_name AS album_transliterated_name';
			$select[] = 'al.uri                 AS album_uri';
			$select[] = 'al.is_image_uploaded   AS is_image_uploaded';
			$select[] = 'sn.transliterated_name AS song_transliterated_name';
			$select[] = 'sn.uri                 AS song_uri';
			$select[] = 'tr.uri';
			$select[] = 'lg.ru_name             AS language_ru_name';
			$select[] = 'lg.en_name             AS language_en_name';
			$select[] = 'lg.ja_name             AS language_ja_name';
			
			$join[]   =
			'
			JOIN
				songs AS sn
			ON
				sn.id = tr.song_id
			';
			$join[]   =
			'
			JOIN
				albums AS al
			ON
				al.id = sn.album_id
			';
			$join[]   =
			'
			JOIN
				languages AS lg
			ON
				lg.id = tr.language_id
			';
		}
		
		if (!is_null($albumUri))
		{
			$join[]   =
			'
			JOIN
				songs AS sn
			ON
				sn.id = tr.song_id
			';
			$join[]  =
			'
			JOIN
				albums AS al
			ON
				al.id = sn.album_id
			';
			$where[] = 'al.uri = :album_uri';
			$binds[] = [':album_uri', $albumUri, PDO::PARAM_STR];
		}
		
		if (!is_null($songUri))
		{
			$join[]  =
			'
			JOIN
				songs AS sn
			ON
				sn.id = tr.song_id
			';
			$where[] = 'sn.uri = :song_uri';
			$binds[] = [':song_uri', $songUri, PDO::PARAM_STR];
		}
		
		if (!is_null($userAddedUri))
		{
			$join[]  =
			'
			JOIN
				users as us
			ON
				tr.user_added_id = us.id
			';
			$where[] = 'us.username = :user_added_uri';
			$binds[] = [':user_added_uri', $userAddedUri, PDO::PARAM_STR];
		}
		
		$join = array_unique($join);
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				'.implode(', ', $select).'
			FROM
				'.implode(', ', $from).'
			
			'.implode("\n", $join).'
			
			WHERE
				'.implode(' AND ', $where).'
			ORDER BY
				'.implode(', ', $orderBy).'
			'
		);
		
		foreach ($binds as $bind)
			$stmt->bindParam($bind[0], $bind[1], $bind[2]);
		$stmt->execute();
		
		$translationList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $translationList;
	}
	
	final public function getLanguageList(array $orderBy = ['id ASC']): array
	{
		$stmt = $this->pdo->query
		(
			'
			SELECT
				id,
				own_name AS language_own_name,
				ru_name  AS language_ru_name,
				en_name  AS language_en_name,
				ja_name  AS language_ja_name
			FROM
				languages
			ORDER BY
				'.implode(', ', $orderBy).'
			'
		);
		
		$languageList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $languageList;
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
		$where  = ['TRUE'];
		$binds  = [];
		
		if (!is_null($albumUri))
		{
			$where[] = 'al.id = sn.album_id';
			$where[] = 'al.uri = :album_uri';
			$binds[] = [':album_uri', $albumUri, PDO::PARAM_STR];
		}
		
		if (!is_null($songUri))
		{
			$where[] = 'sn.uri = :uri';
			$binds[] = [':uri', $songUri, PDO::PARAM_STR];
		}
		
		if (!is_null($songId))
		{
			$where[] = 'sn.id = :id';
			$binds[] = [':id', $songId, PDO::PARAM_INT];
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
				(1 - ISNULL(sn.lyrics))  AS has_lyrics,
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
				'.implode(' AND ', $where).'
			'
		);
		
		foreach ($binds as $bind)
			$stmt->bindParam($bind[0], $bind[1], $bind[2]);
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
	
	final public function getLanguage(int $languageId): array
	{
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				id,
				own_name AS language_own_name,
				ru_name  AS language_ru_name,
				en_name  AS language_en_name,
				ja_name  AS language_ja_name
			FROM
				languages
			WHERE
				id = :language_id
			'
		);
		
		$stmt->bindParam(':language_id', $languageId, PDO::PARAM_INT);
		$stmt->execute();
		
		$language = $stmt->fetch(PDO::FETCH_ASSOC);
		return $language;
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
