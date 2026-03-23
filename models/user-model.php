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
	
	final protected function buildFilename(string $filename): string
	{
		return $filename.'.webp';
	}
	
	final protected function buildFullPath(string ...$values): string
	{
		return implode('/', $values);
	}
	
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
		$stmt->bindParam(':vndb_id',             $vndbId,             PDO::PARAM_INT);
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
		$stmt->bindParam(':vgmdb_id',            $vgmdbId,            PDO::PARAM_INT);
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
		int   |null $aliasOfId,
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
				alias_of_artist_id,
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
				:alias_of_artist_id,
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
		$stmt->bindParam(':vgmdb_id',            $vgmdbId,            PDO::PARAM_INT);
		$stmt->bindParam(':alias_of_artist_id',  $aliasOfId,          PDO::PARAM_INT);
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
		$stmt->bindParam(':vndb_id',             $vndbId,             PDO::PARAM_INT);
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
		
		$stmt->bindParam(':vndb_id',             $vndbId,             PDO::PARAM_INT);
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
		
		$stmt->bindParam(':vgmdb_id',            $vgmdbId,            PDO::PARAM_INT);
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
		int   |null $aliasOfId,
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
				alias_of_artist_id  = :alias_of_artist_id,
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
		
		$stmt->bindParam(':vgmdb_id',            $vgmdbId,            PDO::PARAM_INT);
		$stmt->bindParam(':alias_of_artist_id',  $aliasOfId,          PDO::PARAM_INT);
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
		
		$stmt->bindParam(':vndb_id',             $vndbId,             PDO::PARAM_INT);
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
		
		$where = ['status = "unchecked"'];
		$binds = [];
		
		if (!is_null($gameId))
		{
			$where[] = 'game_id = :game_id';
			$binds[] = [':game_id',  $gameId,  PDO::PARAM_INT];
		}
		
		if (!is_null($albumId))
		{
			$where[] = 'album_id = :album_id';
			$binds[] = [':album_id', $albumId, PDO::PARAM_INT];
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			DELETE FROM
				game_album_relations
			WHERE
				'.implode(' AND ', $where).'
			'
		);
		
		foreach ($binds as $bind)
			$stmt->bindParam($bind[0], $bind[1], $bind[2]);
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
		
		$where = ['status = "unchecked"'];
		$binds = [];
		
		if (!is_null($songId))
		{
			$where[] = 'song_id = :song_id';
			$binds[] = [':song_id', $songId, PDO::PARAM_INT];
		}
		
		if (!is_null($artistId))
		{
			$where[] = 'artist_id = :artist_id';
			$binds[] = [':artist_id', $artistId, PDO::PARAM_INT];
		}
		
		if (!is_null($characterId))
		{
			$where[] = 'character_id = :character_id';
			$binds[] = [':character_id', $artistId, PDO::PARAM_INT];
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			DELETE FROM
				song_artist_character_relations
			WHERE
				'.implode(' AND ', $where).'
			'
		);
		
		foreach ($binds as $bind)
			$stmt->bindParam($bind[0], $bind[1], $bind[2]);
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
		
		$where = ['status = "unchecked"'];
		$binds = [];
		
		if (!is_null($characterId))
		{
			$where[] = 'character_id = :character_id';
			$binds[] = [':character_id', $artistId, PDO::PARAM_INT];
		}
		
		if (!is_null($gameId))
		{
			$where[] = 'game_id = :game_id';
			$binds[] = [':game_id',  $gameId,  PDO::PARAM_INT];
		}
		
		$stmt = $this->pdo->prepare
		(
			'
			DELETE FROM
				character_game_relations
			WHERE
				'.implode(' AND ', $where).'
			'
		);
		
		foreach ($binds as $bind)
			$stmt->bindParam($bind[0], $bind[1], $bind[2]);
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
