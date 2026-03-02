<?php

require_once 'models/violator-model.php';

class UserModel extends ViolatorModel
{	
	public function __construct()
	{
		$this->pdo = getPdo('user');
	}
	
	//------------------------------------//
	//      Methods Building Strings      //
	//------------------------------------//
	
	/*
	// These functions were required when images could have different extensions.
	// Now, all uploaded images are converted to the WEBP format
	// because it takes less space (at the cost of quality)
	
	final protected function buildExtension(array $file): string
	{
		$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
		$extension = mb_strtolower($extension);
		
		return $extension;
	}
	
	final protected function buildFilename(string $name, string $extension): string
	{
		return $name.'.'.$extension;
	}
	
	private function moveUploadedFile(array $file, string $fullPath): bool
	{
		return move_uploaded_file($file['tmp_name'], $fullPath);
	}
	*/
	
	final protected function buildFilename(string $filename): string
	{
		return $filename.'.webp';
	}
	
	final protected function buildFullPath(string ...$values): string
	{
		return implode('/', $values);
	}
	
	/* Now status is always set to "unchecked", even for the admin
	final protected function buildStatus(bool $needsCheck): string
	{
		return $needsCheck ? 'unchecked' : 'checked';
	}
	*/
	
	final protected function buildUri(string $value): string
	{
		$uri = mb_strtolower($value);
		$uri = preg_replace('/\s+/u', '-', $uri);
		$uri = preg_replace('/[^a-z0-9\-]*/u', '', $uri);
		return $uri;
	}
	
	//----------------------------------//
	//      Methods Handling Files      //
	//----------------------------------//
	
	private function isFileUploaded(array $file): bool
	{
		return $file['error'] === UPLOAD_ERR_OK;
	}
	
	private function isFileExtensionAllowed(array $file): bool
	{
		$allowedExtensions = ['jpg', 'jpeg' ,'png', 'webp'];
		
		$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
		$extension = mb_strtolower($extension);
		
		return in_array($extension, $allowedExtensions, true);
	}
	
	private function isFileFormatAllowed(array $file): bool
	{
		$allowedFormats = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP];
		
		$format = exif_imagetype($file['tmp_name']);
		
		return in_array($format, $allowedFormats, true);
	}
	
	private function moveUploadedFile(array $file, string $fullPath): bool
	{
		// This function automatically detects the file format
		$uploadedImage = imagecreatefromstring(file_get_contents($file['tmp_name']));
		
		if (!$uploadedImage)
			return false;
		
		return imagewebp($uploadedImage, $fullPath, 100);
	}
	
	final protected function saveUploadedFile(array $file, string $fullPath): void
	{
		if (!$this->isFileUploaded($file))
			throw new Exception('File upload failed: '.$filename.'.');
		
		if (!$this->isFileExtensionAllowed($file))
			throw new Exception('File extension not allowed: '.$filename.'.');
		
		if (!$this->isFileFormatAllowed($file))
			throw new Exception('File format not allowed: '.$filename.'.');
		
		if (!$this->moveUploadedFile($file, $fullPath))
			throw new Exception('File move failed: '.$fullPath.'.');
	}
	
	final protected function deleteUploadedFile(string $fullPath): void
	{
		if (!file_exists($fullPath))
			return;
		
		if (!unlink($fullPath))
			throw new Exception('File delete failed: '.$fullPath.'.');
	}
	
	final protected function renameUploadedFile(string $oldFullPath, string $newFullPath)
	{
		if (!file_exists($oldFullPath))
			return;
		
		if (!rename($oldFullPath, $newFullPath))
			throw new Exception('File rename failed: from '.$oldFullPath.' to '.$newFullPath);
	}
	
	//----------------------------------------//
	//      Methods Fetching Identifiers      //
	//----------------------------------------//
	
	final public function getGameIdList
	(
		string|null $albumUri     = null,
		string|null $characterUri = null
	): array
	{
		$selectAlbums      = '';
		$selectCharacters  = '';
		
		$joinAlbums        = '';
		$joinCharacters    = '';
		
		$whereAlbumUri     = '';
		$whereCharacterUri = '';
		
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
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT'.
				$selectAlbums.
				$selectCharacters.'
				g.id,
				g.transliterated_name
			FROM
				games AS g'.
			$joinAlbums.
			$joinCharacters.'
			WHERE
				TRUE = TRUE'.
			$whereAlbumUri.
			$whereCharacterUri.'
			ORDER BY
				g.transliterated_name ASC
			'
		);
		
		if (!is_null($albumUri))
			$stmt->bindParam(':album_uri',     $albumUri,     PDO::PARAM_STR);
		if (!is_null($characterUri))
			$stmt->bindParam(':character_uri', $characterUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$gameList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $gameList;
	}
	
	final public function getAlbumIdList
	(
		string|null $gameUri = null
	): array
	{
		$selectGames  = '';
		$joinGames    = '';
		$whereGameUri = '';
		
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
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT'.
				$selectGames.'
				a.id,
				a.transliterated_name
			FROM
				albums AS a
			'.
			$joinGames.
			'
			WHERE
				TRUE = TRUE
			'.
			$whereGameUri.
			'
			ORDER BY
				a.transliterated_name ASC
			'
		);
		
		if (!is_null($gameUri))
			$stmt->bindParam(':game_uri', $gameUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$albumList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $albumList;
	}
	
	final public function getArtistIdList(): array
	{
		$stmt = $this->pdo->query
		(
			'
			SELECT
				id,
				transliterated_name
			FROM
				artists
			'
		);
		
		$artists = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $artists;
	}
	
	final public function getCharacterIdList
	(
		string|null $gameUri = null
	): array
	{
		$selectGames  = '';
		$joinGames    = '';
		$whereGameUri = '';
		
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
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT'.
				$selectGames.'
				c.id,
				c.transliterated_name
			FROM
				characters AS c'.
			$joinGames.'
			WHERE
				TRUE = TRUE'.
			$whereGameUri.'
			ORDER BY
				c.transliterated_name ASC
			'
		);
		
		if (!is_null($gameUri))
			$stmt->bindParam(':game_uri', $gameUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$characterList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $characterList;
	}
	
	final public function getPerformerIdList
	(
		string|null $albumUri = null,
		string|null $songUri  = null
	): array
	{
		$whereAlbum = '';
		$whereSong  = '';
		
		if (!is_null($whereAlbum))
		{
			$whereAlbum =
			'
			AND
				al.uri = :album_uri
			';
		}
		
		if (!is_null($songUri))
		{
			$whereSong =
			'
			AND
				sn.uri = :song_uri
			';
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				ar.id                  AS artist_id,
				ar.transliterated_name AS artist_transliterated_name,
				ch.id                  AS character_id,
				ch.transliterated_name AS character_transliterated_name,
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
				TRUE = true'.
			$whereAlbum.
			$whereSong.'
			'
		);
		
		if (!is_null($albumUri))
			$stmt->bindParam(':album_uri', $albumUri, PDO::PARAM_STR);
		if (!is_null($songUri))
			$stmt->bindParam(':song_uri',  $songUri,  PDO::PARAM_STR);
		
		$stmt->execute();
		
		$performerList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $performerList;
	}
	
	final public function getSongIdList
	(
		string|null $albumUri   = null,
		bool  |null $isOriginal = null,
		bool  |null $hasVocal   = null,
		int   |null $excludeId  = null
	): array
	{
		$joinAlbums      = '';
		
		$whereAlbumUri   = '';
		$whereIsOriginal = '';
		$whereHasVocal   = '';
		$whereExcludeId  = '';
		
		if (!is_null($albumUri))
		{
			$joinAlbums =
			'
			JOIN
				albums AS al
			ON
				al.id = sn.album_id
			';
			
			$whereAlbumUri =
			'
			AND
				al.uri = :album_uri
			';
		}
		
		if (!is_null($isOriginal))
		{
			$value = $isOriginal ? 'IS NULL' : 'IS NOT NULL';
			
			$whereIsOriginal =
			'
			AND
				sn.original_song_id '.$value.'
			AND
				sn.lyrics IS NOT NULL
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
		
		if (!is_null($excludeId))
		{
			$whereExcludeId =
			'
			AND
				id <> :exclude_id
			';
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				sn.id,
				sn.transliterated_name
			FROM
				songs AS sn
			'.
			$joinAlbums.
			'
			WHERE
				TRUE = TRUE
			'.
			$whereAlbumUri.
			$whereIsOriginal.
			$whereHasVocal.
			$whereExcludeId.'
			'
		);
		
		if (!is_null($albumUri))
			$stmt->bindParam(':album_uri',  $albumUri,  PDO::PARAM_STR);
		if (!is_null($hasVocal))
			$stmt->bindParam(':has_vocal',  $hasVocal,  PDO::PARAM_BOOL);
		if (!is_null($excludeId))
			$stmt->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $songs;
	}
	
	final public function getTranslationIdList
	(
		string|null $albumUri    = null,
		string|null $songUri     = null,
		int   |null $userAddedId = null
	): array
	{
		$whereAlbumUri    = '';
		$whereSongUri     = '';
		$whereUserAddedId = '';
		
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
		
		if (!is_null($userAddedId))
		{
			$whereUserAddedId =
			'
			AND
				sn.user_added_id = :user_added_id
			';
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				tr.id,
				tr.language_id
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
			WHERE
				TRUE = TRUE
			'.
			$whereAlbumUri.
			$whereSongUri.
			$whereUserAddedId
		);
		
		if (!is_null($albumUri))
			$stmt->bindParam(':album_uri',     $albumUri,    PDO::PARAM_STR);
		if (!is_null($songUri))
			$stmt->bindParam(':song_uri',      $songUri,     PDO::PARAM_STR);
		if (!is_null($userAddedId))
			$stmt->bindParam(':user_added_id', $userAddedId, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$translationList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $translationList;
	}
	
	final public function getLanguageList(): array
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
			'
		);
		
		$languageList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $languageList;
	}
	
	//-----------------------------------------------------//
	//      Methods Fetching Basic Info Of One Entity      //
	//-----------------------------------------------------//
	
	final public function getGameId(string $gameUri): array|bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				g.id,
				g.transliterated_name,
				g.uri,
				g.status,
				g.user_added_id
			FROM
				games AS g
			WHERE
				g.uri = :game_uri
			'
		);
		
		$stmt->bindParam(':game_uri', $gameUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$game = $stmt->fetch(PDO::FETCH_ASSOC);
		return $game;
	}
	
	final public function getAlbumId(string $albumUri): array|bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				a.id,
				a.transliterated_name,
				a.uri,
				a.status,
				a.user_added_id,
				a.song_count
			FROM
				albums AS a
			WHERE
				a.uri = :album_uri
			'
		);
		
		$stmt->bindParam(':album_uri', $albumUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$album = $stmt->fetch(PDO::FETCH_ASSOC);
		return $album;
	}
	
	final public function getArtistId(string $artistUri): array|bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				a.id,
				a.transliterated_name,
				a.uri,
				a.status,
				a.user_added_id
			FROM
				artists AS a
			WHERE
				a.uri = :artist_uri
			'
		);
		
		$stmt->bindParam(':artist_uri', $artistUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$artist = $stmt->fetch(PDO::FETCH_ASSOC);
		return $artist;
	}
	
	final public function getCharacterId(string $characterUri): array|bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				c.id,
				c.transliterated_name,
				c.uri,
				c.status,
				c.user_added_id
			FROM
				characters AS c
			WHERE
				c.uri = :character_uri
			'
		);
		
		$stmt->bindParam(':character_uri', $characterUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$character = $stmt->fetch(PDO::FETCH_ASSOC);
		return $character;
	}
	
	final public function getSongId
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
				a.uri = :album_uri
			';
		}
		
		if (!is_null($songUri))
		{
			$whereSongUri =
			'
			AND
				s.uri = :song_uri
			';
		}
		
		if (!is_null($songId))
		{
			$whereSongId =
			'
			AND
				s.id = :song_id
			';
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				s.id,
				s.transliterated_name,
				s.uri,
				s.has_vocal,
				(1 - ISNULL(s.lyrics))  AS has_lyrics,
				s.original_song_id,
				s.language_id,
				s.status,
				s.user_added_id,
				a.uri                   AS album_uri
			FROM
				songs AS s
			JOIN
				albums AS a
			ON
				a.id = s.album_id
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
	
	final public function getTranslationId
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
				t.id,
				t.song_id,
				t.language_id,
				t.name,
				t.uri,
				t.status,
				t.user_added_id,
				l.en_name        AS language_en_name,
				l.ru_name        AS language_ru_name,
				l.ja_name        AS language_ja_name
			FROM
				translations AS t
			JOIN
				languages AS l
			ON
				t.language_id = l.id
			JOIN
				songs AS s
			ON
				s.id = t.song_id
			JOIN
				albums AS a
			ON
				a.id = s.album_id
			WHERE
				t.uri = :translation_uri
			AND
				s.uri = :song_uri
			AND
				a.uri = :album_uri
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
	
	//------------------------------------------//
	//      Methods Fetching Specific Info      //
	//------------------------------------------//
	
	final public function getSongCurrentCount(string $albumUri): int
	{
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				COUNT(*) AS result
			FROM
				songs AS s
			JOIN
				albums AS a
			ON
				a.id = s.album_id
			WHERE
				a.uri = :album_uri
			'
		);
		
		$stmt->bindParam(':album_uri', $albumUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$count = $stmt->fetch(PDO::FETCH_ASSOC);
		return $count['result'];
	}
	
	final public function getLastDiscAndTrack(string $albumUri): array|false
	{
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				s.disc_number,
				s.track_number
			FROM
				songs AS s
			JOIN
				albums AS a
			ON
				a.id = s.album_id
			WHERE
				a.uri = :album_uri
			ORDER BY
				s.disc_number DESC,
				s.track_number DESC
			LIMIT 1
			'
		);
		
		$stmt->bindParam(':album_uri', $albumUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$song = $stmt->fetch(PDO::FETCH_ASSOC);
		return $song;
	}
	
	//-------------------------------------------//
	//      Methods Adding Info to Database      //
	//-------------------------------------------//
	
	final public function addGame
	(
		string      $originalName,
		string      $transliteratedName,
		string|null $localizedName,
		array |null $logo,
		int   |null $vndbId,
		int         $userAddedId
	): array
	{
		$isImageUploaded = $logo ? true : false;
		$uri             = $this->buildUri($transliteratedName);
		
		$this->pdo->beginTransaction();
		
		$stmt = $this->pdo->prepare
		(
			'
			INSERT INTO games
			(
				original_name,
				transliterated_name,
				localized_name,
				uri,
				is_image_uploaded,
				vndb_id,
				user_added_id,
				timestamp_added,
				status
			)
			VALUES
			(
				:original_name,
				:transliterated_name,
				:localized_name,
				:uri,
				:is_image_uploaded,
				:vndb_id,
				:user_added_id,
				NOW(),
				"unchecked"
			)
			'
		);
		
		$stmt->bindParam(':original_name',       $originalName,       PDO::PARAM_STR);
		$stmt->bindParam(':transliterated_name', $transliteratedName, PDO::PARAM_STR);
		$stmt->bindParam(':localized_name',      $localizedName,      PDO::PARAM_STR);
		$stmt->bindParam(':uri',                 $uri,                PDO::PARAM_STR);
		$stmt->bindParam(':is_image_uploaded',   $isImageUploaded,    PDO::PARAM_BOOL);
		$stmt->bindParam(':vndb_id',             $vndbId,             PDO::PARAM_STR);
		$stmt->bindParam(':user_added_id',       $userAddedId,        PDO::PARAM_INT);
		
		$stmt->execute();
		
		if ($isImageUploaded)
		{
			$filename = $this->buildFilename($uri);
			$fullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Games->value, $filename);
			$this->saveUploadedFile($logo, $fullPath);
		}
		
		$id = $this->pdo->lastInsertId();
		
		$this->pdo->commit();
		
		return [$id, $uri];
	}
	
	final public function addAlbum
	(
		string      $originalName,
		string      $transliteratedName,
		string|null $localizedName,
		array |null $cover,
		int   |null $vgmdbId,
		int         $songCount,
		int         $userAddedId
	): array
	{
		$isImageUploaded = $cover ? true : false;
		$uri             = $this->buildUri($transliteratedName);
		
		$this->pdo->beginTransaction();
		
		$stmt = $this->pdo->prepare
		(
			'
			INSERT INTO albums
			(
				original_name,
				transliterated_name,
				localized_name,
				uri,
				is_image_uploaded,
				vgmdb_id,
				song_count,
				user_added_id,
				timestamp_added,
				status
			)
			VALUES
			(
				:original_name,
				:transliterated_name,
				:localized_name,
				:uri,
				:is_image_uploaded,
				:vgmdb_id,
				:song_count,
				:user_added_id,
				NOW(),
				"unchecked"
			)
			'
		);
		
		$stmt->bindParam(':original_name',       $originalName,       PDO::PARAM_STR);
		$stmt->bindParam(':transliterated_name', $transliteratedName, PDO::PARAM_STR);
		$stmt->bindParam(':localized_name',      $localizedName,      PDO::PARAM_STR);
		$stmt->bindParam(':uri',                 $uri,                PDO::PARAM_STR);
		$stmt->bindParam(':is_image_uploaded',   $isImageUploaded,    PDO::PARAM_BOOL);
		$stmt->bindParam(':vgmdb_id',            $vgmdbId,            PDO::PARAM_STR);
		$stmt->bindParam(':song_count',          $songCount,          PDO::PARAM_INT);
		$stmt->bindParam(':user_added_id',       $userAddedId,        PDO::PARAM_INT);
		
		$stmt->execute();
		
		if ($isImageUploaded)
		{
			$filename = $this->buildFilename($uri);
			$fullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Albums->value, $filename);
			$this->saveUploadedFile($cover, $fullPath);
		}
		
		$id = $this->pdo->lastInsertId();
		
		$this->pdo->commit();
		
		return [$id, $uri];
	}
	
	final public function addArtist
	(
		string      $originalName,
		string      $transliteratedName,
		string|null $localizedName,
		array |null $photo,
		int   |null $vgmdbId,
		int         $userAddedId
	): array
	{
		$isImageUploaded = $photo ? true : false;
		$uri             = $this->buildUri($transliteratedName);
		
		$this->pdo->beginTransaction();
		
		$stmt = $this->pdo->prepare
		(
			'
			INSERT INTO artists
			(
				original_name,
				transliterated_name,
				localized_name,
				uri,
				is_image_uploaded,
				vgmdb_id,
				user_added_id,
				timestamp_added,
				status
			)
			VALUES
			(
				:original_name,
				:transliterated_name,
				:localized_name,
				:uri,
				:is_image_uploaded,
				:vgmdb_id,
				:user_added_id,
				NOW(),
				"unchecked"
			)
			'
		);
		
		$stmt->bindParam(':original_name',       $originalName,       PDO::PARAM_STR);
		$stmt->bindParam(':transliterated_name', $transliteratedName, PDO::PARAM_STR);
		$stmt->bindParam(':localized_name',      $localizedName,      PDO::PARAM_STR);
		$stmt->bindParam(':uri',                 $uri,                PDO::PARAM_STR);
		$stmt->bindParam(':is_image_uploaded',   $isImageUploaded,    PDO::PARAM_BOOL);
		$stmt->bindParam(':vgmdb_id',            $vgmdbId,            PDO::PARAM_STR);
		$stmt->bindParam(':user_added_id',       $userAddedId,        PDO::PARAM_INT);
		
		$stmt->execute();
		
		if ($isImageUploaded)
		{
			$filename = $this->buildFilename($uri);
			$fullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Artists->value, $filename);
			$this->saveUploadedFile($photo, $fullPath);
		}
		
		$id = $this->pdo->lastInsertId();
		
		$this->pdo->commit();
		
		return [$id, $uri];
	}
	
	final public function addCharacter
	(
		string      $originalName,
		string      $transliteratedName,
		string|null $localizedName,
		array|null  $image,
		int|null    $vndbId,
		int         $userAddedId
	): array
	{
		$isImageUploaded = $image ? true : false;
		$uri             = $this->buildUri($transliteratedName);
		
		$this->pdo->beginTransaction();
		
		$stmt = $this->pdo->prepare
		(
			'
			INSERT INTO characters
			(
				original_name,
				transliterated_name,
				localized_name,
				uri,
				is_image_uploaded,
				vndb_id,
				user_added_id,
				timestamp_added,
				status
			)
			VALUES
			(
				:original_name,
				:transliterated_name,
				:localized_name,
				:uri,
				:is_image_uploaded,
				:vndb_id,
				:user_added_id,
				NOW(),
				"unchecked"
			)
			'
		);
		
		$stmt->bindParam(':original_name',       $originalName,       PDO::PARAM_STR);
		$stmt->bindParam(':transliterated_name', $transliteratedName, PDO::PARAM_STR);
		$stmt->bindParam(':localized_name',      $localizedName,      PDO::PARAM_STR);
		$stmt->bindParam(':uri',                 $uri,                PDO::PARAM_STR);
		$stmt->bindParam(':is_image_uploaded',   $isImageUploaded,    PDO::PARAM_BOOL);
		$stmt->bindParam(':vndb_id',             $vndbId,             PDO::PARAM_STR);
		$stmt->bindParam(':user_added_id',       $userAddedId,        PDO::PARAM_INT);
		
		$stmt->execute();
		
		if ($isImageUploaded)
		{
			$filename = $this->buildFilename($uri);
			$fullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Characters->value, $filename);
			$this->saveUploadedFile($image, $fullPath);
		}
		
		$id = $this->pdo->lastInsertId();
		
		$this->pdo->commit();
		
		return [$id, $uri];
	}
	
	final public function addSong
	(
		string      $albumUri,
		string      $originalName,
		string      $transliteratedName,
		string|null $localizedName,
		int         $discNumber,
		int         $trackNumber,
		bool        $hasLyrics,
		int         $userAddedId
	): array
	{
		$uri  = $this->buildUri($transliteratedName);
		
		$stmt = $this->pdo->prepare
		(
			'
			INSERT INTO songs
			(
				original_name,
				transliterated_name,
				localized_name,
				uri,
				has_vocal,
				album_id,
				disc_number,
				track_number,
				user_added_id,
				timestamp_added,
				status
			)
			VALUES
			(
				:original_name,
				:transliterated_name,
				:localized_name,
				:uri,
				:has_vocal,
				(SELECT id FROM albums WHERE uri = :album_uri),
				:disc_number,
				:track_number,
				:user_added_id,
				NOW(),
				"unchecked"
			)
			'
		);
		
		$stmt->bindParam(':original_name',       $originalName,       PDO::PARAM_STR);
		$stmt->bindParam(':transliterated_name', $transliteratedName, PDO::PARAM_STR);
		$stmt->bindParam(':localized_name',      $localizedName,      PDO::PARAM_STR);
		$stmt->bindParam(':uri',                 $uri,                PDO::PARAM_STR);
		$stmt->bindParam(':has_vocal',           $hasLyrics,          PDO::PARAM_BOOL);
		$stmt->bindParam(':album_uri',           $albumUri,           PDO::PARAM_STR);
		$stmt->bindParam(':disc_number',         $discNumber,         PDO::PARAM_INT);
		$stmt->bindParam(':track_number',        $trackNumber,        PDO::PARAM_INT);
		$stmt->bindParam(':user_added_id',       $userAddedId,        PDO::PARAM_INT);
		
		$stmt->execute();
		
		$id = $this->pdo->lastInsertId();
		
		return [$id, $uri];
	}
	
	final public function addLyrics
	(
		string      $albumUri,
		string      $songUri,
		int|null    $originalSongId,
		int|null    $languageId,
		string|null $lyrics,
		string|null $notes,
		int         $userAddedId
	): void
	{
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				songs
			SET
				original_song_id = :original_song_id,
				lyrics           = :lyrics,
				notes            = :notes,
				language_id      = :language_id,
				user_added_id    = :user_added_id,
				timestamp_added  = NOW(),
				status           = "unchecked"
			WHERE
				uri = :song_uri
			AND
				album_id =
				(
					SELECT
						id
					FROM
						albums
					WHERE
						uri = :album_uri
				)
			'
		);
		
		$stmt->bindParam(':original_song_id', $originalSongId, PDO::PARAM_INT);
		$stmt->bindParam(':lyrics',           $lyrics,         PDO::PARAM_STR);
		$stmt->bindParam(':notes',            $notes,          PDO::PARAM_STR);
		$stmt->bindParam(':language_id',      $languageId,     PDO::PARAM_INT);
		$stmt->bindParam(':user_added_id',    $userAddedId,    PDO::PARAM_INT);
		$stmt->bindParam(':album_uri',        $albumUri,       PDO::PARAM_STR);
		$stmt->bindParam(':song_uri',         $songUri,        PDO::PARAM_STR);
		
		$stmt->execute();
		
		if ($stmt->rowCount() === 0)
			throw new Exception('Lyrics add by user failed: /album/'.$albumUri.'/'.$songUri);
	}
	
	final public function addTranslation
	(
		string      $albumUri,
		string      $songUri,
		string      $name,
		int         $languageId,
		string      $lyrics,
		string|null $notes,
		int         $userAddedId
	): array
	{
		$language = $this->getLanguage($languageId);
		
		$uri = $this->buildUri($language['language_en_name'].'-'.$userAddedId);
		
		$stmt = $this->pdo->prepare
		(
			'
			INSERT INTO translations
			(
				song_id,
				language_id,
				uri,
				name,
				lyrics,
				notes,
				user_added_id,
				timestamp_added,
				status
			)
			VALUES
			(
				(
					SELECT
						s.id
					FROM
						songs AS s
					JOIN
						albums AS a
					ON
						s.album_id = a.id
					WHERE
						s.uri = :song_uri
					AND
						a.uri = :album_uri
				),
				:language_id,
				:uri,
				:name,
				:lyrics,
				:notes,
				:user_added_id,
				NOW(),
				"unchecked"
			)
			'
		);
		
		$stmt->bindParam(':album_uri',     $albumUri,    PDO::PARAM_STR);
		$stmt->bindParam(':song_uri',      $songUri,     PDO::PARAM_STR);
		$stmt->bindParam(':language_id',   $languageId,  PDO::PARAM_INT);
		$stmt->bindParam(':name',          $name,        PDO::PARAM_STR);
		$stmt->bindParam(':uri',           $uri,         PDO::PARAM_STR);
		$stmt->bindParam(':lyrics',        $lyrics,      PDO::PARAM_STR);
		$stmt->bindParam(':notes',         $notes,       PDO::PARAM_STR);
		$stmt->bindParam(':user_added_id', $userAddedId, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$id = $this->pdo->lastInsertId();
		
		return [$id, $uri];
	}
	
	final public function addGameAlbumRelation(int $gameId, int $albumId): void
	{
		$stmt = $this->pdo->prepare
		(
			'
			INSERT IGNORE INTO game_album_relations
			(
				game_id,
				album_id,
				status
			)
			VALUES
			(
				:game_id,
				:album_id,
				"unchecked"
			)
			'
		);
		
		$stmt->bindParam(':game_id',  $gameId,  PDO::PARAM_INT);
		$stmt->bindParam(':album_id', $albumId, PDO::PARAM_INT);
		
		$stmt->execute();
	}
	
	final public function addSongArtistCharacterRelation
	(
		int      $songId,
		int      $artistId,
		int|null $characterId
	): void
	{
		$stmt = $this->pdo->prepare
		(
			'
			INSERT IGNORE INTO song_artist_character_relations
			(
				song_id,
				artist_id,
				character_id,
				status
			)
			VALUES
			(
				:song_id,
				:artist_id,
				:character_id,
				"unchecked"
			)
			'
		);
		
		$stmt->bindParam(':song_id',      $songId,      PDO::PARAM_INT);
		$stmt->bindParam(':artist_id',    $artistId,    PDO::PARAM_INT);
		$stmt->bindParam(':character_id', $characterId, PDO::PARAM_INT);
		
		$stmt->execute();
	}
	
	final public function addCharacterGameRelation(int $characterId, int $gameId): void
	{
		$stmt = $this->pdo->prepare
		(
			'
			INSERT IGNORE INTO character_game_relations
			(
				character_id,
				game_id,
				status
			)
			VALUES
			(
				:character_id,
				:game_id,
				"unchecked"
			)
			'
		);
		
		$stmt->bindParam(':character_id', $characterId, PDO::PARAM_INT);
		$stmt->bindParam(':game_id',      $gameId,      PDO::PARAM_INT);
		
		$stmt->execute();
	}
	
	final public function fetchDataFromVgmdbPage(string $albumUri): array|false
	{
		$stmt = $this->pdo->prepare
		(
			'
			SELECT
				vgmdb_id
			FROM
				albums
			WHERE
				uri = :uri
			'
		);
		$stmt->bindParam(':uri', $albumUri, PDO::PARAM_STR);
		$stmt->execute();
		
		$album = $stmt->fetch(PDO::FETCH_ASSOC);
		
		// The albumUri was wrong, so nothing was found
		if ($album === [])
			return false;
		
		// albumUri was correct but the vgmdb was not set
		if (is_null($album['vgmdb_id']))
			return false;
		
		/* The feature has been disabled for users, though stays available through link if known
		
		// Reason: vgmdb.net started to use the Cloudflare protection
		//         It blocks all automated requests, so I have no opportunity to bypass it now
		
		$url = 'https://vgmdb.net/album/'.$album['vgmdb_id'];
		$fakeHeaders =
		[
			'https' =>
			[
				'method' => "GET",
				'header' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36"
			]
		];
		
		$context = stream_context_create($fakeHeaders);
		$html = file_get_contents($url, false, $context);
		*/
		
		$html = file_get_contents('vgmdb-album-local-page.html');
		
		// Now it is the server error: server failed. Throw exception, show 500
		if (!$html)
			throw new Exception(__METHOD__.': failed to fetch the vgmdb page for '.$album);
		
		// DOMDocument is outdated: https://www.php.net/manual/en/domdocument.loadhtml.php
		// Errors must be turned off
		$dom = Dom\HTMLDocument::createFromString($html, LIBXML_NOERROR);
		
		// Tracklist consists of:
		// - at least 1 localization
		// - at least 1 disc in a localization
		// - at least 1 track in a disc
		$trackList = $dom->getElementById('tracklist');
		
		if (!$trackList)
			throw new Exception('Tracklist for '.$albumUri.' was not found');
		
		$localizations = [];
		
		// The page has a lot of plain text like whitespaces
		// Plain text is also a node and must be filtered out
		foreach ($trackList->childNodes as $node)
		{
			if (mb_strtolower($node->nodeName) !== 'span')
				continue;
			
			$localizations[] = $node;
		}
		
		$discography = [];
		$localizationIndex = 0;
		
		foreach ($localizations as $localization)
		{
			$discography[$localizationIndex] = [];
			$discIndex = 0;
			$discs = $localization->getElementsByTagName('table');
			
			foreach ($discs as $disc)
			{
				$discography[$localizationIndex][$discIndex] = [];
				$trackIndex = 0;
				$trackRows = $disc->getElementsByTagName('tr');
				
				foreach ($trackRows as $trackRow)
				{
					// $trackCells[0] = track number
					// $trackCells[1] = track name
					// $trackCells[2] = track duration
					// P.S. It seems that values have whitespaces around them?
					
					$trackCells = $trackRow->getElementsByTagName('td');
					$discography[$localizationIndex][$discIndex][$trackIndex] =
						mb_trim($trackCells[1]->textContent);
					
					$trackIndex++;
				}
				
				$discIndex++;
			}
			
			$localizationIndex++;
		}
		
		// The tracklist was empty, like here: https://vgmdb.net/album/153150 (13.01.2026)
		// The id='tracklist' has only plain text inside: "No tracklist found."
		// This case works correctly, $discography is empty, no warnings/errors are raised
		if (!$discography)
			return false;
		
		/*
		$i = 1;
		
		foreach ($discography as $localization)
		{
			echo 'Localization: '.$i.'<br/>';
			$j = 1;
			
			foreach ($localization as $disc)
			{
				echo 'Disc: '.$j.'<br/>';
				$k = 1;
				
				foreach ($disc as $track)
				{
					echo $k.': '.$track.'<br/>';
					$k++;
				}
				$j++;
			}
			$i++;
		}
		*/
		
		// The problem now is that I need to rearrange the array
		//
		// Current index order:  localization -> disc -> track
		// Required index order: disc -> track -> localization
		//
		// (it is how it is supposed to be shown on the page)
		
		$rearrangedDiscography = [];
		
		for ($i = 0; $i < count($discography); $i++)
		{
			for ($j = 0; $j < count($discography[$i]); $j++)
			{
				for ($k = 0; $k < count($discography[$i][$j]); $k++)
					$rearrangedDiscography[$j][$k][$i] = mb_trim($discography[$i][$j][$k]);
			}
		}
		
		$i = 1;
		
		/*
		foreach ($rearrangedDiscography as $disc)
		{
			echo 'Disc: '.$i.'<br/>';
			$j = 1;
			
			foreach ($disc as $track)
			{
				echo 'Track: '.$j.'<br/>';
				$k = 1;
				
				foreach ($track as $localization)
				{
					echo 'Localization '.$k.': '.$localization.'<br/>';
					$k++;
				}
				$j++;
			}
			$i++;
		}
		*/
		
		return $rearrangedDiscography;
	}
	
	final public function fillAlbum
	(
		string $albumUri,
		array  $discNumbers,
		array  $trackNumbers,
		array  $originalNames,
		array  $transliteratedNames,
		array  $localizedNames,
		array  $haveVocal,
		int    $userAddedId
	): void
	{
		$this->pdo->beginTransaction();
		
		// Updating the song count in the album
		
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				albums
			SET
				song_count        = :song_count,
				user_updated_id   = :user_updated_id,
				timestamp_updated = NOW()
			WHERE
				uri = :uri
			AND
				LAST_INSERT_ID(id)
			'
		);
		
		// all arrays have the same size, so it doesn't matter which to count
		$songCount = count($discNumbers);
		
		$stmt->bindParam(':song_count',      $songCount,   PDO::PARAM_INT);
		$stmt->bindParam(':user_updated_id', $userAddedId, PDO::PARAM_INT);
		$stmt->bindParam(':uri',             $albumUri,    PDO::PARAM_STR);
		$stmt->execute();
		
		if ($stmt->rowCount() === 0)
			throw new Exception(__METHOD__.': album update failed for '.$albumUri);
		
		$albumId = $this->pdo->lastInsertId();
		
		// Adding songs into the album
		
		// A lot of INSERTs must be avoided
		// It is better to run the query once
		// But using bindParam() won't work here
		// Using placeholders '?' with execute() may be the right solution
		// But what about types of variables?
		
		$sql =
		'
			INSERT INTO songs
			(
				original_name,
				transliterated_name,
				localized_name,
				uri,
				has_vocal,
				album_id,
				disc_number,
				track_number,
				user_added_id,
				timestamp_added,
				status
			)
			VALUES
		';
		
		$values = [];
		$sqlParts = [];
		
		for ($i = 0; $i < count($discNumbers); $i++)
		{
			$sqlParts[] = 
			'
			(
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				NOW(),
				"unchecked"
			)
			';
			
			$values[] = $originalNames[$i];
			$values[] = $transliteratedNames[$i];
			$values[] = $localizedNames[$i];
			$values[] = $this->buildUri($transliteratedNames[$i]);
			$values[] = $haveVocal[$i];
			$values[] = $albumId;
			$values[] = $discNumbers[$i];
			$values[] = $trackNumbers[$i];
			$values[] = $userAddedId;
		}
		$sql .= implode(',', $sqlParts);
		
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($values);
		
		if ($stmt->rowCount() === 0)
			throw new Exception(__METHOD__.': song insert failed for '.$albumUri);
		
		$this->pdo->commit();
	}
	
	//---------------------------------------------//
	//      Methods Updating Info in Database      //
	//---------------------------------------------//
	
	final public function updateGame
	(
		string      $oldUri,
		string      $originalName,
		string      $transliteratedName,
		string|null $localizedName,
		array |null $logo,
		int   |null $vndbId,
		int         $userUpdatedId
	): array
	{
		if ($logo)
		{
			$isImageUploaded = true;
			$setIsImageUploaded = 'is_image_uploaded   = :is_image_uploaded,';
		}
		else
		{
			$isImageUploaded = null;
			$setIsImageUploaded = '';
		}
		
		$newUri = $this->buildUri($transliteratedName);
		
		$this->pdo->beginTransaction();
		
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				games
			SET
				original_name       = :original_name,
				transliterated_name = :transliterated_name,
				localized_name      = :localized_name,
				uri                 = :new_uri,
				'.$setIsImageUploaded.'
				vndb_id             = :vndb_id,
				user_updated_id     = :user_updated_id,
				timestamp_updated   = NOW(),
				status              = "unchecked"
			WHERE
				uri = :old_uri
			AND
				LAST_INSERT_ID(id)
			'
		);
		
		$stmt->bindParam(':original_name',       $originalName,       PDO::PARAM_STR);
		$stmt->bindParam(':transliterated_name', $transliteratedName, PDO::PARAM_STR);
		$stmt->bindParam(':localized_name',      $localizedName,      PDO::PARAM_STR);
		$stmt->bindParam(':new_uri',             $newUri,             PDO::PARAM_STR);
		
		if ($isImageUploaded)
			$stmt->bindParam(':is_image_uploaded',   $isImageUploaded,    PDO::PARAM_BOOL);
		
		$stmt->bindParam(':vndb_id',             $vndbId,             PDO::PARAM_STR);
		$stmt->bindParam(':user_updated_id',     $userUpdatedId,      PDO::PARAM_INT);
		$stmt->bindParam(':old_uri',             $oldUri,             PDO::PARAM_STR);
		
		$stmt->execute();
		
		if ($stmt->rowCount() === 0)
			throw new Exception(__METHOD__.' failed: '.$oldUri);
		
		if ($isImageUploaded)
		{
			$oldFilename = $this->buildFilename($oldUri);
			$oldFullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Games->value, $oldFilename);
			$this->deleteUploadedFile($oldFullPath);
			
			$newFilename = $this->buildFilename($newUri);
			$newFullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Games->value, $newFilename);
			$this->saveUploadedFile($logo, $newFullPath);
		}
		else
		{
			$oldFilename = $this->buildFilename($oldUri);
			$oldFullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Games->value, $oldFilename);
			
			$newFilename = $this->buildFilename($newUri);
			$newFullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Games->value, $newFilename);
			
			$this->renameUploadedFile($oldFullPath, $newFullPath);
		}
		
		$id = $this->pdo->lastInsertId();
		
		$this->pdo->commit();
		
		return [$id, $newUri];
	}
	
	final public function updateAlbum
	(
		string      $oldUri,
		string      $originalName,
		string      $transliteratedName,
		string|null $localizedName,
		array |null $cover,
		int   |null $vgmdbId,
		int         $songCount,
		int         $userUpdatedId
	): array
	{
		if ($cover)
		{
			$isImageUploaded = true;
			$setIsImageUploaded = 'is_image_uploaded   = :is_image_uploaded,';
		}
		else
		{
			$isImageUploaded = null;
			$setIsImageUploaded = '';
		}
		
		$newUri = $this->buildUri($transliteratedName);
		
		$this->pdo->beginTransaction();
		
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				albums
			SET
				original_name       = :original_name,
				transliterated_name = :transliterated_name,
				localized_name      = :localized_name,
				uri                 = :new_uri,
				'.$setIsImageUploaded.'
				vgmdb_id            = :vgmdb_id,
				song_count          = :song_count,
				user_updated_id     = :user_updated_id,
				timestamp_updated   = NOW(),
				status              = "unchecked"
			WHERE
				uri = :old_uri
			AND
				LAST_INSERT_ID(id)
			'
		);
		
		$stmt->bindParam(':original_name',       $originalName,       PDO::PARAM_STR);
		$stmt->bindParam(':transliterated_name', $transliteratedName, PDO::PARAM_STR);
		$stmt->bindParam(':localized_name',      $localizedName,      PDO::PARAM_STR);
		$stmt->bindParam(':new_uri',             $newUri,             PDO::PARAM_STR);
		
		if ($isImageUploaded)
			$stmt->bindParam(':is_image_uploaded',   $isImageUploaded,    PDO::PARAM_BOOL);
		
		$stmt->bindParam(':vgmdb_id',            $vgmdbId,            PDO::PARAM_STR);
		$stmt->bindParam(':song_count',          $songCount,          PDO::PARAM_INT);
		$stmt->bindParam(':user_updated_id',     $userUpdatedId,      PDO::PARAM_INT);
		$stmt->bindParam(':old_uri',             $oldUri,             PDO::PARAM_STR);
		
		$stmt->execute();
		
		if ($stmt->rowCount() === 0)
			throw new Exception(__METHOD__.' failed: '.$oldUri);
		
		if ($isImageUploaded)
		{
			$oldFilename = $this->buildFilename($oldUri);
			$oldFullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Albums->value, $oldFilename);
			$this->deleteUploadedFile($oldFullPath);
			
			$newFilename = $this->buildFilename($newUri);
			$newFullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Albums->value, $newFilename);
			$this->saveUploadedFile($cover, $newFullPath);
		}
		else
		{
			$oldFilename = $this->buildFilename($oldUri);
			$oldFullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Albums->value, $oldFilename);
			
			$newFilename = $this->buildFilename($newUri);
			$newFullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Albums->value, $newFilename);
			
			$this->renameUploadedFile($oldFullPath, $newFullPath);
		}
		
		$id = $this->pdo->lastInsertId();
		
		$this->pdo->commit();
		
		return [$id, $newUri];
	}
	
	final public function updateArtist
	(
		string      $oldUri,
		string      $originalName,
		string      $transliteratedName,
		string|null $localizedName,
		array |null $photo,
		int   |null $vgmdbId,
		int         $userUpdatedId
	): array
	{
		if ($photo)
		{
			$isImageUploaded = true;
			$setIsImageUploaded = 'is_image_uploaded   = :is_image_uploaded,';
		}
		else
		{
			$isImageUploaded = null;
			$setIsImageUploaded = '';
		}
		
		$newUri = $this->buildUri($transliteratedName);
		
		$this->pdo->beginTransaction();
		
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				artists
			SET
				original_name       = :original_name,
				transliterated_name = :transliterated_name,
				localized_name      = :localized_name,
				uri                 = :new_uri,
				'.$setIsImageUploaded.'
				vgmdb_id            = :vgmdb_id,
				user_updated_id     = :user_updated_id,
				timestamp_updated   = NOW(),
				status              = "unchecked"
			WHERE
				uri = :old_uri
			AND
				LAST_INSERT_ID(id)
			'
		);
		
		$stmt->bindParam(':original_name',       $originalName,       PDO::PARAM_STR);
		$stmt->bindParam(':transliterated_name', $transliteratedName, PDO::PARAM_STR);
		$stmt->bindParam(':localized_name',      $localizedName,      PDO::PARAM_STR);
		$stmt->bindParam(':new_uri',             $newUri,             PDO::PARAM_STR);
		
		if ($isImageUploaded)
			$stmt->bindParam(':is_image_uploaded',   $isImageUploaded,    PDO::PARAM_BOOL);
		
		$stmt->bindParam(':vgmdb_id',            $vgmdbId,            PDO::PARAM_STR);
		$stmt->bindParam(':user_updated_id',     $userUpdatedId,      PDO::PARAM_INT);
		$stmt->bindParam(':old_uri',             $oldUri,             PDO::PARAM_STR);
		
		$stmt->execute();
		
		if ($stmt->rowCount() === 0)
			throw new Exception(__METHOD__.' failed: '.$oldUri);
		
		if ($isImageUploaded)
		{
			$oldFilename = $this->buildFilename($oldUri);
			$oldFullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Artists->value, $oldFilename);
			$this->deleteUploadedFile($oldFullPath);
			
			$newFilename = $this->buildFilename($newUri);
			$newFullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Artists->value, $newFilename);
			$this->saveUploadedFile($photo, $newFullPath);
		}
		else
		{
			$oldFilename = $this->buildFilename($oldUri);
			$oldFullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Artists->value, $oldFilename);
			
			$newFilename = $this->buildFilename($newUri);
			$newFullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Artists->value, $newFilename);
			
			$this->renameUploadedFile($oldFullPath, $newFullPath);
		}
		
		$id = $this->pdo->lastInsertId();
		
		$this->pdo->commit();
		
		return [$id, $newUri];
	}
	
	final public function updateCharacter
	(
		string      $oldUri,
		string      $originalName,
		string      $transliteratedName,
		string|null $localizedName,
		array|null  $image,
		int|null    $vndbId,
		int         $userUpdatedId
	): array
	{
		if ($image)
		{
			$isImageUploaded = true;
			$setIsImageUploaded = 'is_image_uploaded   = :is_image_uploaded,';
		}
		else
		{
			$isImageUploaded = null;
			$setIsImageUploaded = '';
		}
		
		$newUri = $this->buildUri($transliteratedName);
		
		$this->pdo->beginTransaction();
		
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				characters
			SET
				original_name       = :original_name,
				transliterated_name = :transliterated_name,
				localized_name      = :localized_name,
				uri                 = :new_uri,
				'.$setIsImageUploaded.'
				vndb_id             = :vndb_id,
				user_updated_id     = :user_updated_id,
				timestamp_updated   = NOW(),
				status              = "unchecked"
			WHERE
				uri = :old_uri
			AND
				LAST_INSERT_ID(id)
			'
		);
		
		$stmt->bindParam(':original_name',       $originalName,       PDO::PARAM_STR);
		$stmt->bindParam(':transliterated_name', $transliteratedName, PDO::PARAM_STR);
		$stmt->bindParam(':localized_name',      $localizedName,      PDO::PARAM_STR);
		$stmt->bindParam(':new_uri',             $newUri,             PDO::PARAM_STR);
		
		if ($isImageUploaded)
			$stmt->bindParam(':is_image_uploaded',   $isImageUploaded,    PDO::PARAM_BOOL);
		
		$stmt->bindParam(':vndb_id',             $vndbId,             PDO::PARAM_STR);
		$stmt->bindParam(':user_updated_id',     $userUpdatedId,      PDO::PARAM_INT);
		$stmt->bindParam(':old_uri',             $oldUri,             PDO::PARAM_STR);
		
		$stmt->execute();
		
		if ($stmt->rowCount() === 0)
			throw new Exception(__METHOD__.' failed: '.$oldUri);
		
		if ($isImageUploaded)
		{
			$oldFilename = $this->buildFilename($oldUri);
			$oldFullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Characters->value, $oldFilename);
			$this->deleteUploadedFile($oldFullPath);
			
			$newFilename = $this->buildFilename($newUri);
			$newFullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Characters->value, $newFilename);
			$this->saveUploadedFile($image, $newFullPath);
		}
		else
		{
			$oldFilename = $this->buildFilename($oldUri);
			$oldFullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Characters->value, $oldFilename);
			
			$newFilename = $this->buildFilename($newUri);
			$newFullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Characters->value, $newFilename);
			
			$this->renameUploadedFile($oldFullPath, $newFullPath);
		}
		
		$id = $this->pdo->lastInsertId();
		
		$this->pdo->commit();
		
		return [$id, $newUri];
	}
	
	final public function updateSong
	(
		string      $albumUri,
		string      $oldUri,
		string      $originalName,
		string      $transliteratedName,
		string|null $localizedName,
		bool        $hasLyrics,
		int         $userUpdatedId
	): array
	{
		$newUri = $this->buildUri($transliteratedName);
		
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				songs
			SET
				original_name       = :original_name,
				transliterated_name = :transliterated_name,
				localized_name      = :localized_name,
				uri                 = :new_uri,
				has_vocal           = :has_vocal,
				user_updated_id     = :user_updated_id,
				timestamp_updated   = NOW(),
				status              = "unchecked"
			WHERE
				album_id = (SELECT id FROM albums WHERE uri = :album_uri)
			AND
				uri = :old_uri
			AND
				LAST_INSERT_ID(id)
			'
		);
		
		$stmt->bindParam(':original_name',       $originalName,       PDO::PARAM_STR);
		$stmt->bindParam(':transliterated_name', $transliteratedName, PDO::PARAM_STR);
		$stmt->bindParam(':localized_name',      $localizedName,      PDO::PARAM_STR);
		$stmt->bindParam(':new_uri',             $newUri,             PDO::PARAM_STR);
		$stmt->bindParam(':has_vocal',           $hasLyrics,          PDO::PARAM_BOOL);
		$stmt->bindParam(':user_updated_id',     $userUpdatedId,      PDO::PARAM_INT);
		$stmt->bindParam(':album_uri',           $albumUri,           PDO::PARAM_STR);
		$stmt->bindParam(':old_uri',             $oldUri,             PDO::PARAM_STR);
		
		$stmt->execute();
		
		if ($stmt->rowCount() === 0)
			throw new Exception(__METHOD__.' failed: '.$albumUri.', '.$oldUri);
		
		$id = $this->pdo->lastInsertId();
		
		return [$id, $newUri];
	}
	
	final public function updateLyrics
	(
		string      $albumUri,
		string      $songUri,
		int   |null $originalSongId,
		int   |null $languageId,
		string|null $lyrics,
		string|null $notes,
		int   |null $userUpdatedId
	): void
	{
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				songs
			SET
				original_song_id  = :original_song_id,
				lyrics            = :lyrics,
				notes             = :notes,
				language_id       = :language_id,
				user_updated_id   = :user_updated_id,
				timestamp_updated = NOW(),
				status            = "unchecked"
			WHERE
				uri = :song_uri
			AND
				album_id =
				(
					SELECT
						id
					FROM
						albums
					WHERE
						uri = :album_uri
				)
			AND
				LAST_INSERT_ID(id)
			'
		);
		
		$stmt->bindParam(':original_song_id', $originalSongId, PDO::PARAM_INT);
		$stmt->bindParam(':lyrics',           $lyrics,         PDO::PARAM_STR);
		$stmt->bindParam(':notes',            $notes,          PDO::PARAM_STR);
		$stmt->bindParam(':language_id',      $languageId,     PDO::PARAM_INT);
		$stmt->bindParam(':user_updated_id',  $userUpdatedId,  PDO::PARAM_INT);
		$stmt->bindParam(':album_uri',        $albumUri,       PDO::PARAM_STR);
		$stmt->bindParam(':song_uri',         $songUri,        PDO::PARAM_STR);
		
		$stmt->execute();
		
		if ($stmt->rowCount() === 0)
			throw new Exception(__METHOD__.' failed: '.$albumUri.', '.$songUri);
	}
	
	final public function updateTranslation
	(
		string      $albumUri,
		string      $songUri,
		string      $translationUri,
		string      $name,
		string      $lyrics,
		string|null $notes,
		int         $userUpdatedId
	): void
	{
		// It is not allowed to select when deleting
		// But the trick here is to create a temporary copy of the table
		
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				translations
			SET
				name              = :name,
				lyrics            = :lyrics,
				notes             = :notes,
				user_updated_id   = :user_updated_id,
				timestamp_updated = NOW(),
				status            = "unchecked"
			WHERE
				id =
				(
					SELECT
						t.id
					FROM
						(SELECT id, song_id, uri FROM translations) AS t
					JOIN
						songs AS s
					ON
						t.song_id = s.id
					JOIN
						albums AS a
					ON
						s.album_id = a.id
					WHERE
						t.uri = :translation_uri
					AND
						s.uri = :song_uri
					AND
						a.uri = :album_uri
				)
			AND
				LAST_INSERT_ID(id)
			'
		);
		
		$stmt->bindParam(':album_uri',       $albumUri,       PDO::PARAM_STR);
		$stmt->bindParam(':song_uri',        $songUri,        PDO::PARAM_STR);
		$stmt->bindParam(':translation_uri', $translationUri, PDO::PARAM_STR);
		$stmt->bindParam(':name',            $name,           PDO::PARAM_STR);
		$stmt->bindParam(':lyrics',          $lyrics,         PDO::PARAM_STR);
		$stmt->bindParam(':notes',           $notes,          PDO::PARAM_STR);
		$stmt->bindParam(':user_updated_id', $userUpdatedId,  PDO::PARAM_INT);
		
		$stmt->execute();
		
		if ($stmt->rowCount() === 0)
			throw new Exception(__METHOD__.' failed: '.$albumUri.', '.$songUri.', '.$translationUri);
	}
	
	//-----------------------------------------------//
	//      Methods Deleting Info from Database      //
	//-----------------------------------------------//
	
	public function deleteGame(array $game): void
	{
		$this->pdo->beginTransaction();
		
		$stmt = $this->pdo->prepare
		(
			'
			DELETE FROM
				games
			WHERE
				id = :id
			'
		);
		$stmt->bindParam(':id', $game['id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if ($stmt->rowCount() === 0)
			throw new Exception(__METHOD__.' failed: '.$game['id']);
		
		if ($game['is_image_uploaded'])
		{
			$filename = $this->buildFilename($game['uri']);
			$fullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Games->value, $filename);
			$this->deleteUploadedFile($fullPath);
		}
		
		$this->pdo->commit();
	}
	
	public function deleteAlbum(array $album): void
	{
		$this->pdo->beginTransaction();
		
		$stmt = $this->pdo->prepare
		(
			'
			DELETE FROM
				albums
			WHERE
				id = :id
			'
		);
		$stmt->bindParam(':id', $album['id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if ($stmt->rowCount() === 0)
			throw new Exception(__METHOD__.' failed: '.$album['id']);
		
		if ($album['is_image_uploaded'])
		{
			$filename = $this->buildFilename($album['uri']);
			$fullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Albums->value, $filename);
			$this->deleteUploadedFile($fullPath);
		}
		
		$this->pdo->commit();
	}
	
	public function deleteArtist(array $artist): void
	{
		$this->pdo->beginTransaction();
		
		$stmt = $this->pdo->prepare
		(
			'
			DELETE FROM
				artists
			WHERE
				id = :id
			'
		);
		$stmt->bindParam(':id', $artist['id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if ($stmt->rowCount() === 0)
			throw new Exception(__METHOD__.' failed: '.$artist['id']);
		
		if ($artist['is_image_uploaded'])
		{
			$filename = $this->buildFilename($artist['uri']);
			$fullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Artists->value, $filename);
			$this->deleteUploadedFile($fullPath);
		}
		
		$this->pdo->commit();
	}
	
	public function deleteCharacter(array $character): void
	{
		$this->pdo->beginTransaction();
		
		$stmt = $this->pdo->prepare
		(
			'
			DELETE FROM
				characters
			WHERE
				id = :id
			'
		);
		$stmt->bindParam(':id', $character['id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if ($stmt->rowCount() === 0)
			throw new Exception(__METHOD__.' failed: '.$character['id']);
		
		if ($character['is_image_uploaded'])
		{
			$filename = $this->buildFilename($character['uri']);
			$fullPath = $this->buildFullPath(AssetFolder::Base->value, AssetFolder::Characters->value, $filename);
			$this->deleteUploadedFile($fullPath);
		}
		
		$this->pdo->commit();
	}
	
	public function deleteLyrics(array $song): void
	{
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				songs
			SET
				original_song_id   = NULL,
				language_id        = NULL,
				lyrics             = NULL,
				notes              = NULL,
				user_updated_id    = NULL,
				timestamp_updated  = NULL,
				user_reviewed_id   = NULL,
				timestamp_reviewed = NULL
			WHERE
				id = :id
			'
		);
		$stmt->bindParam(':id', $song['id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if ($stmt->rowCount() === 0)
			throw new Exception(__METHOD__.' failed: '.$id);
	}
	
	public function deleteTranslation(array $translation): void
	{
		$stmt = $this->pdo->prepare
		(
			'
			DELETE FROM
				translations
			WHERE
				id = :id
			'
		);
		$stmt->bindParam(':id', $translation['id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if ($stmt->rowCount() === 0)
			throw new Exception(__METHOD__.' failed: '.$id);
	}
	
	final public function deleteGameAlbumRelation
	(
		int|null $gameId  = null,
		int|null $albumId = null
	): void
	{
		if (is_null($gameId) && is_null($albumId))
			throw new Exception(__METHOD__.' was called without conditions');
		
		$whereGameId    = '';
		$whereAlbumId   = '';
		
		if (!is_null($gameId))
		{
			$whereGameId =
			'
			AND
				game_id = :game_id
			';
		}
		
		if (!is_null($albumId))
		{
			$whereAlbumId =
			'
			AND
				album_id = :album_id
			';
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			DELETE FROM
				game_album_relations
			WHERE
				status = "unchecked"
			'.
			$whereGameId.
			$whereAlbumId
		);
		
		if (!is_null($gameId))
			$stmt->bindParam(':game_id',  $gameId,  PDO::PARAM_INT);
		if (!is_null($albumId))
			$stmt->bindParam(':album_id', $albumId, PDO::PARAM_INT);
		
		$stmt->execute();
	}
	
	final public function deleteSongArtistCharacterRelation
	(
		int|null $songId      = null,
		int|null $artistId    = null,
		int|null $characterId = null
	): void
	{
		if (is_null($songId) && is_null($artistId) && is_null($characterId))
			throw new Exception(__METHOD__.' was called without conditions');
		
		$whereSongId      = '';
		$whereArtistId    = '';
		$whereCharacterId = '';
		
		if (!is_null($songId))
		{
			$whereSongId =
			'
			AND
				song_id = :song_id
			';
		}
		
		if (!is_null($artistId))
		{
			$whereArtistId =
			'
			AND
				artist_id = :artist_id
			';
		}
		
		if (!is_null($characterId))
		{
			$whereCharacterId =
			'
			AND
				character_id = :character_id
			';
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			DELETE FROM
				song_artist_character_relations
			WHERE
				status = "unchecked"
			'.
			$whereSongId.
			$whereArtistId.
			$whereCharacterId
		);
		
		if (!is_null($songId))
			$stmt->bindParam(':song_id',      $songId,      PDO::PARAM_INT);
		if (!is_null($artistId))
			$stmt->bindParam(':artist_id',    $artistId,    PDO::PARAM_INT);
		if (!is_null($characterId))
			$stmt->bindParam(':character_id', $characterId, PDO::PARAM_INT);
		
		$stmt->execute();
	}
	
	final public function deleteCharacterGameRelation
	(
		int|null $characterId = null,
		int|null $gameId      = null
	): void
	{
		if (is_null($characterId) && is_null($gameId))
			throw new Exception(__METHOD__.' was called without conditions');
		
		$whereCharacterId = '';
		$whereGameId      = '';
		
		if (!is_null($characterId))
		{
			$whereCharacterId =
			'
			AND
				character_id = :character_id
			';
		}
		
		if (!is_null($gameId))
		{
			$whereGameId =
			'
			AND
				game_id = :game_id
			';
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			DELETE FROM
				character_game_relations
			WHERE
				status = "unchecked"
			'.
			$whereCharacterId.
			$whereGameId
		);
		
		if (!is_null($characterId))
			$stmt->bindParam(':character_id', $characterId, PDO::PARAM_INT);
		if (!is_null($gameId))
			$stmt->bindParam(':game_id',      $gameId,      PDO::PARAM_INT);
		
		$stmt->execute();
	}
	
	final public function updateUserData
	(
		int         $userId,
		string      $newUsername,
		string      $newEmail,
		string|null $newPassword
	)
	{
		$setPassword = '';
		
		if ($newPassword)
			$setPassword = ', password_hash = "'.password_hash($newPassword, PASSWORD_DEFAULT).'"';
		
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				users
			SET
				username = :username,
				email    = :email'.
				$setPassword.'
			WHERE
				id = :id
			'
		);
		
		$stmt->bindParam(':id',       $userId,      PDO::PARAM_INT);
		$stmt->bindParam(':username', $newUsername, PDO::PARAM_STR);
		$stmt->bindParam(':email',    $newEmail,    PDO::PARAM_STR);
		
		$stmt->execute();
	}
	
	final public function deleteUser(array $user): void
	{
		$stmt = $this->pdo->prepare
		(
			'
			DELETE FROM
				users
			WHERE
				id = :id
			'
		);
		
		$stmt->bindParam(':id', $user['user_id'], PDO::PARAM_INT);
		$stmt->execute();
	}
}
