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
		
		$feedbackId = $this->Parsing::parseNullableInteger($feedbackId, 1);
		
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
		
		$feedbackId = $this->Parsing::parseNullableInteger($feedbackId, 1);
		
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
		
		$id = Parsing::parseNullableInteger($id, 1);
		
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
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleAddLanguagePageGet();
				break;
				
			case 'POST':
				$this->handleAddLanguagePagePost();
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleAddLanguagePageGet(): void
	{
		$this->view->renderAddLanguagePage();
	}
	
	private function handleAddLanguagePagePost(): void
	{
		$ownName = $_POST['own-name'] ?? null;
		$ruName  = $_POST['ru-name']  ?? null;
		$enName  = $_POST['en-name']  ?? null;
		$jaName  = $_POST['ja-name']  ?? null;
		
		if (Validation::haveNullOrEmpty($ownName, $ruName, $enName, $jaName))
			throw new HttpBadRequest400('At least one of not-null values was null/empty', get_defined_vars());
		
		$this->model->addLanguage($ownName, $ruName, $enName, $jaName);
		$this->handleRedirect(Session::buildInternalLink($this->language, 'control-panel'));
	}
	
	final public function handleReportListPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleReportListPageGet();
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleReportListPageGet(): void
	{
		$reports = $this->model->getReportList();
		$this->view->renderReportListPage($reports);
	}
	
	final public function handleUserListPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleUserListPageGet();
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleUserListPageGet(): void
	{
		$users = $this->model->getUserList();
		$this->view->renderUserListPage($users);
	}
	
	final public function handleFillAlbumPage(string $albumUri): void
	{
		$album            = $this->model->getAlbumId($albumUri);
		$currentSongCount = $this->model->getSongCurrentCount($albumUri);
		
		if (!$album)
			throw new HttpNotFound404();
		
		if ($album['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if ($album['status'] === 'checked' && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		if (!Session::isCurrentUser($album['user_added_id']) && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		if ($currentSongCount !== 0)
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleUserListPageGet($album);
				break;
				
			case 'POST':
				$this->handleUserListPagePost($album);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleFillAlbumPageGet(array $album, int $currentSongCount): void
	{
		$discography = $this->model->fetchDataFromVgmdbPage($albumUri);
		
		if (!$discography)
			throw new HttpBadRequest400();
		
		$this->view->renderFillAlbumPage($album, $discography);
	}
	
	private function handleFillAlbumPagePost(array $album, int $currentSongCount): void
	{	
		$discNumbers         = $_POST['disc-number']         ?? [];
		$trackNumbers        = $_POST['track-number']        ?? [];
		$originalNames       = $_POST['original-name']       ?? [];
		$transliteratedNames = $_POST['transliterated-name'] ?? [];
		$localizedNames      = $_POST['localized-name']      ?? [];
		$hasVocal            = $_POST['has-vocal']           ?? [];
		$userAddedId         = $_SESSION['user']['id'];
		
		$originalNames       = Parsing::trimNullableStringArray($originalNames);
		$transliteratedNames = Parsing::trimNullableStringArray($transliteratedNames);
		$localizedNames      = Parsing::trimNullableStringArray($localizedNames);
		$discNumbers         = Parsing::parseNullableIntegerArray($discNumbers, 1);
		$trackNumbers        = Parsing::parseNullableIntegerArray($trackNumbers, 1);
		$haveVocal           = Parsing::parseNullableIntegerArray($hasVocal, 0, 1);
		
		$haveArraysSameLength = Validation::haveArraysSameLength
		(
			$discNumbers,
			$trackNumbers,
			$originalNames,
			$transliteratedNames,
			$localizedNames,
			$haveVocal
		);
		
		if (!$haveArraysSameLength)
			throw new HttpBadRequest400('Arrays did not have same length', get_defined_vars());
		
		if (count($discNumbers) === 0)
			throw new HttpBadRequest400('Discs were not found', get_defined_vars());
		
		foreach ($transliteratedNames as $transliteratedName)
		{
			if (!Validation::isPrintableAscii($transliteratedName))
				throw new HttpBadRequest400('One of transliteratedName was not ASCII', get_defined_vars());
		}
		
		$isNullPresent = Validation::haveNullOrEmpty
		(
			...$discNumbers,
			...$trackNumbers,
			...$originalNames,
			...$transliteratedNames,
			...$haveVocal
		);
		
		if ($isNullPresent)
			throw new HttpBadRequest400('At least one of not-null values was null/empty', get_defined_vars());
		
		if (!($discNumbers[0] === 1 && $trackNumbers[0] === 1))
			throw new HttpBadRequest400('Disc 1 and Track 1 were not first', get_defined_vars());
		
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
				throw new HttpBadRequest400('Order of discs and tracks was incorrect', get_defined_vars());
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
		
		$this->handleRedirect(Session::buildInternalLink($this->language, 'album', $albumUri));
	}
}
