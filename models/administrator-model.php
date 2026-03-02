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
		// Never use *
		
		$stmt = $this->pdo->query
		(
			'
			SELECT 
				r.id,
				r.sender_id,
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
		// Never use *
		
		$stmt = $this->pdo->query
		(
			'
			SELECT 
				u1.id,
				rl.en_name,
				u1.username,
				u1.email,
				u1.ip_address,
				u1.timestamp_created,
				u1.timestamp_last_log_in
			FROM
				users AS u1
			JOIN
				roles AS rl
			ON
				u1.role_id = rl.id
			'
		);
		
		$userList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $userList;
	}
}
