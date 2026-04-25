<?php

require_once 'models/user-model.php';

class AdministratorModel extends UserModel
{
	public function __construct()
	{
		$this->pdo = getPdo('administrator');
	}
	
	final public function updateGameStatus(string $gameUri, string $status, int $userReviewedId): bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				games
			SET
				user_reviewed_id   = :user_reviewed_id,
				timestamp_reviewed = NOW(),
				status             = :status
			WHERE
				uri = :game_uri
			'
		);
		
		$stmt->bindParam(':user_reviewed_id', $userReviewedId, PDO::PARAM_INT);
		$stmt->bindParam(':status',           $status,         PDO::PARAM_STR);
		$stmt->bindParam(':game_uri',         $gameUri,        PDO::PARAM_STR);
		
		$stmt->execute();
		
		return ($stmt->rowCount() !== 0);
	}
	
	final public function updateAlbumStatus(string $albumUri, string $status, int $userReviewedId): bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				albums
			SET
				user_reviewed_id   = :user_reviewed_id,
				timestamp_reviewed = NOW(),
				status             = :status
			WHERE
				uri = :album_uri
			'
		);
		
		$stmt->bindParam(':user_reviewed_id', $userReviewedId, PDO::PARAM_INT);
		$stmt->bindParam(':status',           $status,         PDO::PARAM_STR);
		$stmt->bindParam(':album_uri',        $albumUri,       PDO::PARAM_STR);
		
		$stmt->execute();
		
		return ($stmt->rowCount() !== 0);
	}
	
	final public function updateArtistStatus(string $artistUri, string $status, int $userReviewedId): bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				artists
			SET
				user_reviewed_id   = :user_reviewed_id,
				timestamp_reviewed = NOW(),
				status             = :status
			WHERE
				uri = :artist_uri
			'
		);
		
		$stmt->bindParam(':user_reviewed_id', $userReviewedId, PDO::PARAM_INT);
		$stmt->bindParam(':status',           $status,         PDO::PARAM_STR);
		$stmt->bindParam(':artist_uri',       $artistUri,      PDO::PARAM_STR);
		
		$stmt->execute();
		
		return ($stmt->rowCount() !== 0);
	}
	
	final public function updateCharacterStatus(string $characterUri, string $status, int $userReviewedId): bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				characters
			SET
				user_reviewed_id   = :user_reviewed_id,
				timestamp_reviewed = NOW(),
				status             = :status
			WHERE
				uri = :character_uri
			'
		);
		
		$stmt->bindParam(':user_reviewed_id', $userReviewedId, PDO::PARAM_INT);
		$stmt->bindParam(':status',           $status,         PDO::PARAM_STR);
		$stmt->bindParam(':character_uri',    $characterUri,   PDO::PARAM_STR);
		
		$stmt->execute();
		
		return ($stmt->rowCount() !== 0);
	}
	
	final public function updateSongStatus
	(
		string $albumUri,
		string $songUri,
		string $status,
		int    $userReviewedId
	): bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				songs
			SET
				user_reviewed_id   = :user_reviewed_id,
				timestamp_reviewed = NOW(),
				status             = :status
			WHERE
				album_id = (SELECT id FROM albums WHERE uri = :album_uri)
			AND
				uri = :song_uri
			'
		);
		
		$stmt->bindParam(':user_reviewed_id', $userReviewedId, PDO::PARAM_INT);
		$stmt->bindParam(':status',           $status,         PDO::PARAM_STR);
		$stmt->bindParam(':album_uri',        $albumUri,       PDO::PARAM_STR);
		$stmt->bindParam(':song_uri',         $songUri,        PDO::PARAM_STR);
		
		$stmt->execute();
		
		return ($stmt->rowCount() !== 0);
	}
	
	final public function updateTranslationStatus
	(
		string $albumUri,
		string $songUri,
		string $translationUri,
		string $status,
		int    $userReviewedId
	): bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				translations
			SET
				user_reviewed_id   = :user_reviewed_id,
				timestamp_reviewed = NOW(),
				status             = :status
			WHERE
				song_id =
				(
					SELECT
						id
					FROM
						songs
					WHERE
						album_id = (SELECT id FROM albums WHERE uri = :album_uri)
					AND
						uri = :song_uri
				)
			AND
				uri = :translation_uri
			'
		);
		
		$stmt->bindParam(':user_reviewed_id', $userReviewedId, PDO::PARAM_INT);
		$stmt->bindParam(':status',           $status,         PDO::PARAM_STR);
		$stmt->bindParam(':album_uri',        $albumUri,       PDO::PARAM_STR);
		$stmt->bindParam(':song_uri',         $songUri,        PDO::PARAM_STR);
		$stmt->bindParam(':translation_uri',  $translationUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		return ($stmt->rowCount() !== 0);
	}
	
	final public function updateGameAlbumRelationStatus(string $gameUri, string $albumUri, string $status): bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				game_album_relations
			SET
				status = :status
			WHERE
				game_id = (SELECT id FROM games WHERE uri = :game_uri)
			AND
				album_id = (SELECT id FROM albums WHERE uri = :album_uri)
			'
		);
		
		$stmt->bindParam(':status',    $status,   PDO::PARAM_STR);
		$stmt->bindParam(':game_uri',  $gameUri,  PDO::PARAM_STR);
		$stmt->bindParam(':album_uri', $albumUri, PDO::PARAM_STR);
		
		$stmt->execute();
		
		return ($stmt->rowCount() !== 0);
	}
	
	final public function updateCharacterGameRelationStatus(string $characterUri, string $gameUri, string $status): bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				character_game_relations
			SET
				status = :status
			WHERE
				character_id = (SELECT id FROM characters WHERE uri = :character_uri)
			AND
				game_id = (SELECT id FROM games WHERE uri = :game_uri)
			'
		);
		
		$stmt->bindParam(':status',        $status,       PDO::PARAM_STR);
		$stmt->bindParam(':character_uri', $characterUri, PDO::PARAM_STR);
		$stmt->bindParam(':game_uri',      $gameUri,      PDO::PARAM_STR);
		
		$stmt->execute();
		
		return ($stmt->rowCount() !== 0);
	}
	
	final public function updateSongArtistCharacterRelationStatus
	(
		string $albumUri,
		string $songUri,
		string $status
	): bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				song_artist_character_relations
			SET
				status = :status
			WHERE
				song_id =
				(
					SELECT
						id
					FROM
						songs
					WHERE
						album_id = (SELECT id FROM albums WHERE uri = :album_uri)
					AND
						uri = :song_uri
				)
			'
		);
		
		$stmt->bindParam(':status',    $status,   PDO::PARAM_STR);
		$stmt->bindParam(':album_uri', $albumUri, PDO::PARAM_STR);
		$stmt->bindParam(':song_uri',  $songUri,  PDO::PARAM_STR);
		
		$stmt->execute();
		
		return ($stmt->rowCount() !== 0);
	}
	
	final public function updateNonVocalSongStatus
	(
		string $albumUri,
		string $status,
		int    $userReviewedId
	): bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				songs
			SET
				status             = :status,
				user_reviewed_id   = :user_id,
				timestamp_reviewed = NOW()
			WHERE
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
				has_vocal = FALSE
			'
		);
		
		$stmt->bindParam(':status',    $status,         PDO::PARAM_STR);
		$stmt->bindParam(':album_uri', $albumUri,       PDO::PARAM_STR);
		$stmt->bindParam(':user_id',   $userReviewedId, PDO::PARAM_INT);
		
		$stmt->execute();
		
		return ($stmt->rowCount() !== 0);
	}
	
	final public function addFeedbackReply(int $feedbackId, string $replyText, int $moderatorId): bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				feedbacks
			SET
				moderator_id    = :moderator_id,
				reply           = :reply_text,
				reply_timestamp = NOW()
			WHERE
				id = :feedback_id
			'
		);
		
		$stmt->bindParam(':feedback_id',  $feedbackId,  PDO::PARAM_INT);
		$stmt->bindParam(':moderator_id', $moderatorId, PDO::PARAM_INT);
		$stmt->bindParam(':reply_text',   $replyText,   PDO::PARAM_STR);
		
		$stmt->execute();
		
		return ($stmt->rowCount() !== 0);
	}
	
	final public function deleteFeedback(int $feedbackId): bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			DELETE FROM
				feedbacks
			WHERE
				id = :feedback_id
			'
		);
		
		$stmt->bindParam(':feedback_id',  $feedbackId,  PDO::PARAM_INT);
		
		$stmt->execute();
		
		return ($stmt->rowCount() !== 0);
	}
	
	final public function getReportList(): array
	{
		$stmt = $this->pdo->query
		(
			'
			SELECT 
				r.id,
				r.sender_id,
				r.ip_address,
				u.username,
				r.message,
				r.request_uri,
				r.user_agent,
				r.timestamp_sent,
				r.status
			FROM
				reports AS r
			LEFT JOIN
				users AS u
			ON
				r.sender_id = u.id
			'
		);
		
		$reportList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $reportList;
	}
	
	final public function updateReportStatus(int $id, string $status): bool
	{
		$stmt = $this->pdo->prepare
		(
			'
			UPDATE
				reports
			SET
				status = :status
			WHERE
				id = :id
			'
		);
		
		$stmt->bindParam(':id',     $id,     PDO::PARAM_INT);
		$stmt->bindParam(':status', $status, PDO::PARAM_STR);
		
		$stmt->execute();
		
		return ($stmt->rowCount() !== 0);
	}
	
	final public function addLanguage
	(
		string $ownName,
		string $ruName,
		string $enName,
		string $jaName
	): int
	{
		$stmt = $this->pdo->prepare
		(
			'
			INSERT INTO languages
			(
				own_name,
				ru_name,
				en_name,
				ja_name
			)
			VALUES
			(
				:own_name,
				:ru_name,
				:en_name,
				:ja_name
			)
			'
		);
		
		$stmt->bindParam(':own_name', $ownName, PDO::PARAM_STR);
		$stmt->bindParam(':ru_name',  $ruName,  PDO::PARAM_STR);
		$stmt->bindParam(':en_name',  $enName,  PDO::PARAM_STR);
		$stmt->bindParam(':ja_name',  $jaName,  PDO::PARAM_STR);
		
		$stmt->execute();
		
		return $this->pdo->lastInsertId();
	}
	
	final public function getUserList(): array
	{
		$stmt = $this->pdo->query
		(
			'
			SELECT 
				u.id,
				r.en_name,
				u.username,
				u.email,
				u.timestamp_created,
				u.timestamp_last_log_in,
				GROUP_CONCAT(f.ip_address SEPARATOR 0xFFFFFFFFFFFFFFFF) AS "fingerprints"
			FROM
				users AS u
			JOIN
				roles AS r
			ON
				u.role_id = r.id
			JOIN
				fingerprints AS f
			ON
				u.id = f.user_id
			GROUP BY
				u.id,
				r.en_name,
				u.username,
				u.email,
				u.timestamp_created,
				u.timestamp_last_log_in
			ORDER BY
				u.id
			'
		);
		
		$userList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $userList;
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
		
		if (!$album)
			return false;
		
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
				song_count = :song_count
			WHERE
				uri = :uri
			AND
				LAST_INSERT_ID(id)
			'
		);
		
		// all arrays have the same size, so it doesn't matter which to count
		$songCount = count($discNumbers);
		
		$stmt->bindParam(':song_count', $songCount, PDO::PARAM_INT);
		$stmt->bindParam(':uri',        $albumUri,  PDO::PARAM_STR);
		$stmt->execute();
		
		if ($stmt->rowCount() === 0)
			throw new HttpInternalServerError500(__METHOD__.': album update failed for '.$albumUri);
		
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
			throw new HttpInternalServerError500(__METHOD__.': song insert failed for '.$albumUri);
		
		$this->pdo->commit();
	}
}
