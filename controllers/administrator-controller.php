<?php

require_once 'controllers/user-controller.php';

class AdministratorController extends UserController
{
	public function __construct(string $language)
	{
		parent::__construct($language);
		
		require_once 'models/administrator-model.php';
		require_once 'views/administrator-view.php';

		$this->model = new AdministratorModel;
		$this->view = new AdministratorView($language);
	}
	
	const REQUEST_BODY_EMPTY = 'Request body was empty';
	const DATA_NOT_SET       = 'Data was not set';
	const DATA_INVALID       = 'Data was invalid';
	const SERVER_ERROR       = 'Something went wrong';
	
	private function isStatusValid(string $status): bool
	{
		return in_array($status, ['unchecked', 'checked', 'hidden'], true);
	}
	
	final public function handleChangeGameStatus(string $gameUri): void
	{
		if (empty($_POST))
		{
			http_response_code(400);
			echo self::REQUEST_BODY_EMPTY;
			return;
		}
		
		$status = $_POST['status'] ?? null;
		$userId = $_SESSION['user']['id'];
		
		if (!$status)
		{
			http_response_code(400);
			echo self::DATA_NOT_SET;
			return;
		}
		
		if (!$this->isStatusValid($status))
		{
			http_response_code(400);
			echo self::DATA_INVALID;
			return;
		}
		
		$isSuccess = $this->model->updateGameStatus($gameUri, $status, $userId);
		
		if (!$isSuccess)
		{
			http_response_code(500);
			echo self::SERVER_ERROR;
			return;
		}
	}
	
	final public function handleChangeAlbumStatus(string $albumUri): void
	{
		if (empty($_POST))
		{
			http_response_code(400);
			echo self::REQUEST_BODY_EMPTY;
			return;
		}
		
		$status = $_POST['status'] ?? null;
		$userId = $_SESSION['user']['id'];
		
		if (!$status)
		{
			http_response_code(400);
			echo self::DATA_NOT_SET;
			return;
		}
		
		if (!$this->isStatusValid($status))
		{
			http_response_code(400);
			echo self::DATA_INVALID;
			return;
		}
		
		$isSuccess = $this->model->updateAlbumStatus($albumUri, $status, $userId);
		
		if (!$isSuccess)
		{
			http_response_code(500);
			echo self::SERVER_ERROR;
			return;
		}
		
		$isSuccess = $this->model->updateNonVocalSongStatus($albumUri, $status, $userId);
		
		if (!$isSuccess)
		{
			http_response_code(500);
			echo self::SERVER_ERROR;
			return;
		}
	}
	
	final public function handleChangeArtistStatus(string $artistUri): void
	{
		if (empty($_POST))
		{
			http_response_code(400);
			echo self::REQUEST_BODY_EMPTY;
			return;
		}
		
		$status = $_POST['status'] ?? null;
		$userId = $_SESSION['user']['id'];
		
		if (!$status)
		{
			http_response_code(400);
			echo self::DATA_NOT_SET;
			return;
		}
		
		if (!$this->isStatusValid($status))
		{
			http_response_code(400);
			echo self::DATA_INVALID;
			return;
		}
		
		$isSuccess = $this->model->updateArtistStatus($artistUri, $status, $userId);
		
		if (!$isSuccess)
		{
			http_response_code(500);
			echo self::SERVER_ERROR;
			return;
		}
	}
	
	final public function handleChangeCharacterStatus(string $characterUri): void
	{
		if (empty($_POST))
		{
			http_response_code(400);
			echo self::REQUEST_BODY_EMPTY;
			return;
		}
		
		$status = $_POST['status'] ?? null;
		$userId = $_SESSION['user']['id'];
		
		if (!$status)
		{
			http_response_code(400);
			echo self::DATA_NOT_SET;
			return;
		}
		
		if (!$this->isStatusValid($status))
		{
			http_response_code(400);
			echo self::DATA_INVALID;
			return;
		}
		
		$isSuccess = $this->model->updateCharacterStatus($characterUri, $status, $userId);
		
		if (!$isSuccess)
		{
			http_response_code(500);
			echo self::SERVER_ERROR;
			return;
		}
	}
	
	final public function handleChangeSongStatus(string $albumUri, string $songUri): void
	{
		if (empty($_POST))
		{
			http_response_code(400);
			echo self::REQUEST_BODY_EMPTY;
			return;
		}
		
		$status = $_POST['status'] ?? null;
		$userId = $_SESSION['user']['id'];
		
		if (!$status)
		{
			http_response_code(400);
			echo self::DATA_NOT_SET;
			return;
		}
		
		if (!$this->isStatusValid($status))
		{
			http_response_code(400);
			echo self::DATA_INVALID;
			return;
		}
		
		$isSuccess = $this->model->updateSongStatus
		(
			$albumUri,
			$songUri,
			$status,
			$userId
		);
		
		if (!$isSuccess)
		{
			http_response_code(500);
			echo self::SERVER_ERROR;
			return;
		}
		
		$isSuccess = $this->model->updateSongArtistCharacterRelationStatus
		(
			$albumUri,
			$songUri,
			$status
		);
		
		if (!$isSuccess)
		{
			http_response_code(500);
			echo self::SERVER_ERROR;
			return;
		}
	}
	
	final public function handleChangeTranslationStatus
	(
		string $albumUri,
		string $songUri,
		string $translationUri
	): void
	{
		if (empty($_POST))
		{
			http_response_code(400);
			echo self::REQUEST_BODY_EMPTY;
			return;
		}
		
		$status = $_POST['status'] ?? null;
		$userId = $_SESSION['user']['id'];
		
		if (!$status)
		{
			http_response_code(400);
			echo self::DATA_NOT_SET;
			return;
		}
		
		if (!$this->isStatusValid($status))
		{
			http_response_code(400);
			echo self::DATA_INVALID;
			return;
		}
		
		$isSuccess = $this->model->updateTranslationStatus($albumUri, $songUri, $translationUri, $status, $userId);
		
		if (!$isSuccess)
		{
			http_response_code(500);
			echo self::SERVER_ERROR;
			return;
		}
	}
	
	final public function handleChangeGameAlbumRelationStatus(string $gameUri, string $albumUri): void
	{
		if (empty($_POST))
		{
			http_response_code(400);
			echo self::REQUEST_BODY_EMPTY;
			return;
		}
		
		$status = $_POST['status'] ?? null;
		
		if (!$status)
		{
			http_response_code(400);
			echo self::DATA_NOT_SET;
			return;
		}
		
		if (!$this->isStatusValid($status))
		{
			http_response_code(400);
			echo self::DATA_INVALID;
			return;
		}
		
		$isSuccess = $this->model->updateGameAlbumRelationStatus($gameUri, $albumUri, $status);
		
		if (!$isSuccess)
		{
			http_response_code(500);
			echo self::SERVER_ERROR;
			return;
		}
	}
	
	final public function handleChangeCharacterGameRelationStatus (string $characterUri, string $gameUri): void
	{
		if (empty($_POST))
		{
			http_response_code(400);
			echo self::REQUEST_BODY_EMPTY;
			return;
		}
		
		$status = $_POST['status'] ?? null;
		
		if (!$status)
		{
			http_response_code(400);
			echo self::DATA_NOT_SET;
			return;
		}
		
		if (!$this->isStatusValid($status))
		{
			http_response_code(400);
			echo self::DATA_INVALID;
			return;
		}
		
		$isSuccess = $this->model->updateCharacterGameRelationStatus($characterUri, $gameUri, $status);
		
		if (!$isSuccess)
		{
			http_response_code(500);
			echo self::SERVER_ERROR;
			return;
		}
	}
	
	final public function handleAddFeedbackReply(): void
	{
		if (empty($_POST))
		{
			http_response_code(400);
			echo self::REQUEST_BODY_EMPTY;
			return;
		}
		
		$feedbackId  = $_POST['feedback-id']   ?? null;
		$replyText   = $_POST['reply-text']    ?? null;
		$moderatorId = $_SESSION['user']['id'];
		
		if (!$feedbackId || !$replyText)
		{
			http_response_code(400);
			echo self::DATA_NOT_SET;
			return;
		}
		
		$feedbackId = $this->parseNullableInteger($feedbackId, 1);
		
		if (!$feedbackId)
		{
			http_response_code(400);
			echo self::DATA_INVALID;
			return;
		}
		
		$isSuccess = $this->model->addFeedbackReply($feedbackId, $replyText, $moderatorId);
		
		if (!$isSuccess)
		{
			http_response_code(500);
			echo self::SERVER_ERROR;
			return;
		}
	}
	
	final public function handleDeleteFeedback(): void
	{
		if (empty($_POST))
		{
			http_response_code(400);
			echo self::REQUEST_BODY_EMPTY;
			return;
		}
		
		$feedbackId = $_POST['feedback-id'] ?? null;
		
		if (!$feedbackId)
		{
			http_response_code(400);
			echo self::DATA_NOT_SET;
			return;
		}
		
		$feedbackId = $this->parseNullableInteger($feedbackId, 1);
		
		if (!$feedbackId)
		{
			http_response_code(400);
			echo self::DATA_INVALID;
			return;
		}
		
		$isSuccess = $this->model->deleteFeedback($feedbackId);
		
		if (!$isSuccess)
		{
			http_response_code(500);
			echo self::SERVER_ERROR;
			return;
		}
	}
	
	final public function handleControlPanelPage(): void
	{
		$this->view->renderControlPanelPage();
	}
	
	final public function handleReportStatus(): void
	{
		if (empty($_POST))
		{
			http_response_code(400);
			echo self::REQUEST_BODY_EMPTY;
			return;
		}
		
		$id     = $_POST['id']     ?? null;
		$status = $_POST['status'] ?? null;
		
		if (!$id)
		{
			http_response_code(400);
			echo self::DATA_NOT_SET;
			return;
		}
		
		if (!$status)
		{
			http_response_code(400);
			echo self::DATA_NOT_SET;
			return;
		}
		
		$id = $this->parseNullableInteger($id, 1);
		
		if (!$id)
		{
			http_response_code(400);
			echo self::DATA_INVALID;
			return;
		}
		
		if (!$this->isStatusValid($status))
		{
			http_response_code(400);
			echo self::DATA_INVALID;
			return;
		}
		
		$isSuccess = $this->model->updateReportStatus($id, $status);
		
		if (!$isSuccess)
		{
			http_response_code(500);
			echo self::SERVER_ERROR;
			return;
		}
	}
	
	final public function handleAddLanguagePage(): void
	{
		if (empty($_POST))
		{
			$this->view->renderAddLanguagePage();
			return;
		}
		
		$ownName = $_POST['own-name'] ?? null;
		$ruName  = $_POST['ru-name']  ?? null;
		$enName  = $_POST['en-name']  ?? null;
		$jaName  = $_POST['ja-name']  ?? null;
		
		if (haveNullOrEmpty($ownName, $ruName, $enName, $jaName))
		{
			$this->handleBadRequest();
			return;
		}
		
		$this->model->addLanguage($ownName, $ruName, $enName, $jaName);
		$this->handleRedirect(buildInternalLink($this->language, 'control-panel'));
	}
	
	/*
	
	// I don't know whether I need it
	
	final public function handleEditLanguagePage(???): void
	{
		if (empty($_POST))
		{
			$this->getLanguage(???);
			
			$this->view->renderEditLanguagePage();
			return;
		}
		
		$ownName = $_POST['own-name'] ?? null;
		$ruName  = $_POST['ru-name']  ?? null;
		$enName  = $_POST['en-name']  ?? null;
		$jaName  = $_POST['ja-name']  ?? null;
		
		if (haveNullOrEmpty($ownName, $ruName, $enName, $jaName))
		{
			$this->handleBadRequest();
			return;
		}
		
		$this->model->editLanguage($ownName, $ruName, $enName, $jaName);
		$this->handleRedirect(buildInternalLink($this->language, 'control-panel'));
	}
	*/
	
	final public function handleReportListPage(): void
	{
		$reports = $this->model->getReportList();
		$this->view->renderReportListPage($reports);
	}
	
	final public function handleUserListPage(): void
	{
		$users = $this->model->getUserList();
		$this->view->renderUserListPage($users);
	}
	
	final public function handleFillAlbumPage(string $albumUri): void
	{
		if (empty($_POST))
		{
			$album            = $this->model->getAlbumId($albumUri);
			$currentSongCount = $this->model->getSongCurrentCount($albumUri);
			$discography      = $this->model->fetchDataFromVgmdbPage($albumUri);
			
			if (!$album)
			{
				$this->handleNotFound();
				return;
			}
			
			if ($album['status'] === 'hidden' && !isCurrentUserModerator())
			{
				$this->handleUnavailableForLegalReasons();
				return;
			}
			
			if ($album['status'] === 'checked' && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
			if (!isCurrentUser($album['user_added_id']) && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
			if ($currentSongCount !== 0)
			{
				$this->handleForbidden();
				return;
			}
			
			if (!$discography)
			{
				$this->handleBadRequest();
				return;
			}
			
			$this->view->renderFillAlbumPage($album, $discography);
			return;
		}
		
		$album            = $this->model->getAlbumId($albumUri);
		$currentSongCount = $this->model->getSongCurrentCount($albumUri);
		
		$discNumbers         = $_POST['disc-number']         ?? [];
		$trackNumbers        = $_POST['track-number']        ?? [];
		$originalNames       = $_POST['original-name']       ?? [];
		$transliteratedNames = $_POST['transliterated-name'] ?? [];
		$localizedNames      = $_POST['localized-name']      ?? [];
		$hasVocal            = $_POST['has-vocal']           ?? [];
		$userAddedId         = $_SESSION['user']['id'];
		
		$originalNames       = $this->trimNullableStringArray($originalNames);
		$transliteratedNames = $this->trimNullableStringArray($transliteratedNames);
		$localizedNames      = $this->trimNullableStringArray($localizedNames);
		$discNumbers         = $this->parseNullableIntegerArray($discNumbers, 1);
		$trackNumbers        = $this->parseNullableIntegerArray($trackNumbers, 1);
		$haveVocal           = $this->parseNullableIntegerArray($hasVocal, 0, 1);
		
		if (!$album)
		{
			$this->handleNotFound();
			return;
		}
		
		if ($album['status'] === 'hidden' && !isCurrentUserModerator())
		{
			$this->handleUnavailableForLegalReasons();
			return;
		}
		
		if ($album['status'] === 'checked' && !isCurrentUserModerator())
		{
			$this->handleForbidden();
			return;
		}
		
		if (!isCurrentUser($album['user_added_id']) && !isCurrentUserModerator())
		{
			$this->handleForbidden();
			return;
		}
		
		if ($currentSongCount !== 0)
		{
			$this->handleForbidden();
			return;
		}
		
		$haveArraysSameLength = $this->haveArraysSameLength
		(
			$discNumbers,
			$trackNumbers,
			$originalNames,
			$transliteratedNames,
			$localizedNames,
			$haveVocal
		);
		
		if (!$haveArraysSameLength)
		{
			$this->handleBadRequest();
			return;
		}
		
		// need a function for this?
		
		foreach ($transliteratedNames as $transliteratedName)
		{
			if (!$this->isPrintableAscii($transliteratedName))
			{
				$this->handleBadRequest();
				return;
			}
		}
		
		// Check that arrays are not empty
		// Don't check all of them because they have the same length
		
		if (count($discNumbers) === 0)
		{
			$this->handleBadRequest();
			return;
		}
		
		// Check that certain values in arrays are not empty
		// They must meet the requirement being "NOT NULL"
		
		$isInputInvalid = haveNullOrEmpty
		(
			...$discNumbers,
			...$trackNumbers,
			...$originalNames,
			...$transliteratedNames,
			...$haveVocal
		);
		
		if ($isInputInvalid)
		{
			$this->handleBadRequest();
			return;
		}
		
		// Checking that all discs and tracks have consequent numbers
		// starting from [1, 1]
		//
		// P.S. Is it possible to do it without a separate 'if'?
		//      It doesn't look nice and clean
		
		if (!($discNumbers[0] === 1 && $trackNumbers[0] === 1))
		{
			$this->handleBadRequest();
			return;
		}
		
		for ($i = 1; $i < count($discNumbers); $i++)
		{
			$isNextDiscFirstTrack =
			(
				($discNumbers[$i] === $discNumbers[$i - 1] + 1)
				&&
				($trackNumbers[$i] === 1)
			);
			
			$isSameDiscNextTrack =
			(
				($discNumbers[$i] === $discNumbers[$i - 1])
				&&
				($trackNumbers[$i] === $trackNumbers[$i - 1] + 1)
			);
			
			if (!$isNextDiscFirstTrack && !$isSameDiscNextTrack)
			{
				$this->handleBadRequest();
				return;
			}
		}
		
		$this->model->fillAlbum
		(
			$albumUri,
			$discNumbers,
			$trackNumbers,
			$originalNames,
			$transliteratedNames,
			$localizedNames,
			$haveVocal,
			$userAddedId
		);
		
		$this->handleRedirect(buildInternalLink($this->language, 'album', $albumUri));
	}
}
