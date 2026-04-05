<?php

require_once 'controllers/controller.php';

class VisitorController extends Controller
{
	public function __construct(string $language)
	{
		parent::__construct($language);
		
		require_once 'models/visitor-model.php';
		require_once 'views/visitor-view.php';

		$this->model = new VisitorModel;
		$this->view = new VisitorView($language);
	}
	
	//------------------------//
	//      Log-In Pages      //
	//------------------------//
	
	final protected function createUserSession(array $userData): void
	{
		session_regenerate_id();
		
		$_SESSION['user'] = 
		[
			'id'         => $userData['user_id'],
			'username'   => $userData['user_username'],
			'role'       => $userData['role_technical_name'],
			'roleId'     => $userData['role_id'],
			'userRole' =>
			[
				'ru' => $userData['language_ru_name'],
				'en' => $userData['language_en_name'],
				'ja' => $userData['language_ja_name']
			]
		];
	}
	
	public function handleLogInPage(): void
	{
		if (!isset($_SESSION['lastPageBeforeLogIn']))
			$_SESSION['lastPageBeforeLogIn'] = $_SERVER['HTTP_REFERER'] ?? buildInternalLink($this->language);
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleLogInPageGet();
				break;
			
			case 'POST':
				$this->handleLogInPagePost();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleLogInPageGet(AuthenticationError $error = AuthenticationError::None): void
	{
		$this->view->renderLogInPage($error);
	}
	
	private function handleLogInPagePost(AuthenticationError $error = AuthenticationError::None): void
	{
		if (!isset($_POST['email']) || !isset($_POST['password']))
			throw new HttpBadRequest400('Email and password were not sent', get_defined_vars());
		
		if ($_POST['email'] === '')
			$error = AuthenticationError::EmptyEmail;
		
		if ($_POST['password'] === '')
			$error = AuthenticationError::EmptyPassword;
		
		if (!$this->model->isEmailRegistered($_POST['email']))
			$error = AuthenticationError::EmailNotFound;
		
		if (!$this->model->isPasswordCorrect($_POST['email'], $_POST['password']))
			$error = AuthenticationError::IncorrectPassword;
		
		if (!$this->model->isAccountVerified($_POST['email']))
			$error = AuthenticationError::AccountNotVerified;
		
		if ($error !== AuthenticationError::None)
		{
			$this->handleLogInPageGet($error);
			return;
		}
		
		$this->model->updateLastLogInTimestamp($_POST['email']);
		$userData = $this->model->getUserData(email: $_POST['email']);
		
		$this->createUserSession($userData);
		$this->handleRedirect($_SESSION['lastPageBeforeLogIn']);
		
		unset($_SESSION['lastPageBeforeLogIn']);
	}
	
	public function handleSignUpPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleSignUpPageGet();
				break;
			
			case 'POST':
				$this->handleSignUpPagePost();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleSignUpPageGet(AuthenticationError $error = AuthenticationError::None): void
	{
		$captchaData = $this->model->getRandomCaptcha(6, 3);
		
		$_SESSION['signUpCaptchaCode'] = $captchaData[0];
		$captchaBase64Image            = $captchaData[1];
		
		$this->view->renderSignUpPage($error, $captchaBase64Image);
	}
	
	private function handleSignUpPagePost(AuthenticationError $error = AuthenticationError::None): void
	{
		if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['email']) || !isset($_POST['captcha-code']))
			throw new HttpBadRequest400('Sign-up data was not sent', get_defined_vars());
		
		$MIN_LENGTH = 4;
		$MAX_LENGTH = 32;
		
		if (mb_strtolower($_POST['captcha-code']) !== mb_strtolower($_SESSION['signUpCaptchaCode']))
			$error = AuthenticationError::CaptchaInvalid;
		
		if (trimNullableString($_POST['username']) !== $_POST['username'])
			$error = AuthenticationError::UsernameTrimmable;
		
		if (!isLatinAlphabetAndNumbers($_POST['username']))
			$error = AuthenticationError::UsernameForbiddenSymbols;
		
		if (mb_strlen($_POST['username']) < $MIN_LENGTH || mb_strlen($_POST['username']) > $MAX_LENGTH)
			$error = AuthenticationError::UsernameLengthIncorrect;
		
		if ($this->model->isUserRegistered($_POST['username']))
			$error = AuthenticationError::UsernameTaken;
		
		if (!isEmailValid($_POST['email']))
			$error = AuthenticationError::EmailInvalid;
		
		if ($this->model->isEmailRegistered($_POST['email']))
			$error = AuthenticationError::EmailTaken;
		
		if (!isLatinAlphabetAndNumbers($_POST['password']))
			$error = AuthenticationError::PasswordForbiddenSymbols;
		
		if (mb_strlen($_POST['password']) < $MIN_LENGTH || mb_strlen($_POST['password']) > $MAX_LENGTH)
			$error = AuthenticationError::PasswordLengthIncorrect;
		
		if ($error !== AuthenticationError::None)
		{
			$this->handleSignUpPageGet($error);
			return;
		}
		
		$this->model->createUser
		(
			$_POST['username'],
			$_POST['password'],
			$_POST['email'],
			$_SERVER['REMOTE_ADDR']
		);
		
		$userData = $this->model->getUserData(email: $_POST['email']);
		$this->createUserSession($userData);
		$this->handleRedirect(buildInternalLink($this->language));
		unset($_SESSION['signUpCaptchaCode']);
	}
	
	public function handleLogOutPage(): void
	{
		$this->handleRedirect(buildInternalLink($this->language));
	}
	
	//-----------------------//
	//      Error Pages      //
	//-----------------------//
	
	final public function handleRedirect(string $location): void
	{
		http_response_code(303);
		header('Location: '.$location);
	}
	
	final public function handleBadRequest400(): void
	{
		http_response_code(400);
		$this->view->renderBadRequest400();
	}
	
	final public function handleUnauthorized401(): void
	{
		http_response_code(401);
		$this->view->renderUnauthorized401();
	}
	
	final public function handlePaymentRequired402(): void
	{
		http_response_code(402);
		$this->view->renderPaymentRequired402();
	}
	
	final public function handleForbidden403(): void
	{
		http_response_code(403);
		$this->view->renderForbidden403();
	}
	
	final public function handleNotFound404(): void
	{
		http_response_code(404);
		$this->view->renderNotFound404();
	}
	
	final public function handleMethodNotAllowed405(): void
	{
		http_response_code(405);
		$this->view->renderMethodNotAllowed405();
	}
	
	final public function handleNotAcceptable406(): void
	{
		http_response_code(406);
		$this->view->renderNotAcceptable406();
	}
	
	final public function handleUnavailableForLegalReasons451(): void
	{
		http_response_code(451);
		$this->view->renderUnavailableForLegalReasons451();
	}
	
	final public function handleInternalServerError500(): void
	{
		http_response_code(500);
		$this->view->renderInternalServerError500();
	}
	
	//---------------------------------//
	//      Pages to view content      //
	//---------------------------------//
	
	final public function handleHomePage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleHomePageGet();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleHomePageGet(): void
	{
		// 9 items fit in 1920x1080
		$count = 9;
		
		$albums        = $this->model->getLastAddedAlbums($count);
		$lyrics        = $this->model->getLastAddedLyrics($count);
		$translations  = $this->model->getLastAddedTranslations($count);
		
		$this->view->renderHomePage($albums, $lyrics, $translations);
	}
	
	final public function handleGameListPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleGameListPageGet();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleGameListPageGet(): void
	{
		$gameList = $this->model->getGameList();
		$this->view->renderGameListPage($gameList);
	}
	
	final public function handleAlbumListPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleAlbumListPageGet();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleAlbumListPageGet(): void
	{
		$albumList = $this->model->getAlbumList();
		$this->view->renderAlbumListPage($albumList);
	}
	
	final public function handleArtistListPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleArtistListPageGet();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleArtistListPageGet(): void
	{
		$artistList = $this->model->getArtistList();
		$this->view->renderArtistListPage($artistList);
	}
	
	final public function handleCharacterListPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleCharacterListPageGet();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleCharacterListPageGet(): void
	{
		$characterList = $this->model->getCharacterList();
		$this->view->renderCharacterListPage($characterList);
	}
	
	final public function handleSongListPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleSongListPageGet();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleSongListPageGet(): void
	{
		$songList = $this->model->getSongList(hasVocal: true);
		$this->view->renderSongListPage($songList);
	}
	
	final public function handleTranslationListPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleTranslationListPageGet();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleTranslationListPageGet(): void
	{
		$translationList = $this->model->getTranslationList();
		$this->view->renderTranslationListPage($translationList);
	}
	
	final public function handleGamePage(string $gameUri): void
	{
		$game = $this->model->getGame($gameUri);
		
		if (!$game)
			throw new HttpNotFound404();
		
		if ($game['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleGamePageGet($game);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleGamePageGet(array $game): void
	{
		$albumList     = $this->model->getAlbumList    (gameUri: $game['uri']);
		$characterList = $this->model->getCharacterList(gameUri: $game['uri']);
		
		$this->view->renderGamePage($game, $albumList, $characterList);
	}
	
	final public function handleAlbumPage(string $albumUri): void
	{
		$album = $this->model->getAlbum($albumUri);
		
		if (!$album)
			throw new HttpNotFound404();
		
		if ($album['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleAlbumPageGet($album);
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleAlbumPageGet(array $album): void
	{
		$songList = $this->model->getSongList
		(
			albumUri: $album['uri'],
			orderBy: ['sn.disc_number ASC', 'sn.track_number ASC']
		);
		$gameList = $this->model->getGameList(albumUri: $album['uri']);
		
		$this->view->renderAlbumPage($album, $songList, $gameList);
	}
	
	final public function handleArtistPage(string $artistUri): void
	{
		$artist = $this->model->getArtist($artistUri);
		
		if (!$artist)
			throw new HttpNotFound404();
		
		if ($artist['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleArtistPageGet($artist);
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleArtistPageGet(array $artist): void
	{
		$artistAliases = $this->model->getArtistList(aliasesOfId: $artist['id']);
		$songList      = $this->model->getSongList(artistUri: $artist['uri']);
		
		$this->view->renderArtistPage($artist, $artistAliases, $songList);
	}
	
	final public function handleCharacterPage(string $characterUri): void
	{
		$character = $this->model->getCharacter($characterUri);
		
		if (!$character)
			throw new HttpNotFound404();
		
		if ($character['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleCharacterPageGet($character);
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleCharacterPageGet(array $character): void
	{		
		$gameList = $this->model->getGameList(characterUri: $character['uri']);
		$songList = $this->model->getSongList(characterUri: $character['uri']);
		
		$this->view->renderCharacterPage($character, $gameList, $songList);
	}
	
	final public function handleLyricsPage(string $albumUri, string $songUri): void
	{
		$album = $this->model->getAlbum($albumUri);
		$song  = $this->model->getSong($albumUri, $songUri);
		
		if (!$album)
			throw new HttpNotFound404();
		
		if ($album['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if (!$song)
			throw new HttpNotFound404();
		
		if ($song['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleLyricsPageGet($album, $song);
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleLyricsPageGet(array $album, array $song): void
	{
		$performerList = $this->model->getPerformerList(albumUri: $album['uri'], songUri: $song['uri']);
		
		if ($song['original_song_id'])
		{
			$originalSong    = $this->model->getSong(songId: $song['original_song_id']);
			$translationList = $this->model->getTranslationList(albumUri: $originalSong['album_uri'], songUri: $originalSong['uri']);
		}
		else
		{
			$originalSong    = null;
			$translationList = $this->model->getTranslationList(albumUri: $album['uri'], songUri: $song['uri']);
		}
		
		if (!$song['lyrics'] && !$originalSong || !$song['lyrics'] && $originalSong && !$originalSong['lyrics'])
			$this->view->renderNoLyricsPage($album, $song);
		else
			$this->view->renderLyricsPage($album, $song, $originalSong, $performerList, $translationList);
	}
	
	final public function handleTranslationPage(string $albumUri, string $songUri, string $translationUri): void
	{
		$album         = $this->model->getAlbum($albumUri);
		$song          = $this->model->getSong($albumUri, $songUri);
		$performerList = $this->model->getPerformerList(albumUri: $albumUri, songUri: $songUri);
		
		if (!$album)
			throw new HttpNotFound404();
		
		if ($album['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if (!$song)
			throw new HttpNotFound404();
		
		if ($song['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if ($song['original_song_id'])
		{
			$originalSong = $this->model->getSong(songId: $song['original_song_id']);
			$translation  = $this->model->getTranslation
			(
				$originalSong['album_uri'],
				$originalSong['uri'],
				$translationUri
			);
			
			$translationList = $this->model->getTranslationList
			(
				albumUri: $originalSong['album_uri'],
				songUri:  $originalSong['uri']
			);
		}
		else
		{
			$originalSong = null;
			$translation  = $this->model->getTranslation
			(
				$albumUri,
				$songUri,
				$translationUri
			);
			
			$translationList = $this->model->getTranslationList
			(
				albumUri: $albumUri,
				songUri:  $songUri
			);
		}
		
		if (!$translation)
			throw new HttpNotFound404();
		
		if ($translation['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleTranslationPageGet($album, $song, $originalSong, $translation, $performerList, $translationList);
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleTranslationPageGet
	(
		array      $album,
		array      $song,
		array|null $originalSong,
		array      $translation,
		array      $performerList,
		array      $translationList
	): void
	{
		$this->view->renderTranslationPage
		(
			$album,
			$song,
			$originalSong,
			$translation,
			$performerList,
			$translationList
		);
	}
	
	final public function handleFeedbackPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleFeedbackPageGet();
				break;
			
			case 'POST':
				$this->handleFeedbackPagePost();
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleFeedbackPageGet(): void
	{
		$captchaData = $this->model->getRandomCaptcha(4, 0);
		
		$_SESSION['feedbackCaptchaCode'] = $captchaData[0];
		$captchaBase64Image              = $captchaData[1];
		
		$feedbacks = $this->model->getFeedbackList();
		
		$this->view->renderFeedbackPage($feedbacks, $captchaBase64Image);
	}
	
	private function handleFeedbackPagePost(): void
	{
		$senderId = $_SESSION['user']['id']  ?? null;
		$senderIp = $_SERVER['REMOTE_ADDR'];
		$message  = $_POST['message']        ?? null;
		$captcha  = $_POST['captcha-code']   ?? null;
		
		if (isNullOrEmpty($message))
			throw new HttpBadRequest400('Message was empty or null');
		
		if (mb_strtolower($_SESSION['feedbackCaptchaCode']) !== mb_strtolower($captcha))
			throw new HttpBadRequest400('Captcha was wrong');
		
		$this->model->addFeedback($senderId, $senderIp, $message);
		$this->handleRedirect(buildInternalLink($this->language, 'feedback'));
	}
	
	final public function handleReport(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'POST':
				$this->handleReportPost();
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleReportPost(): void
	{
		$senderId  = $_SESSION['user']['id']     ?? null;
		$message   = $_POST['report-text']       ?? null;
		$entityUri = $_POST['entity-uri']        ?? null;
		$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
		
		if (haveNullOrEmpty($message, $entityUri, $userAgent))
			throw new HttpBadRequest400('Null or empty submitted', [$message, $entityUri, $userAgent]);
		
		$this->model->addReport
		(
			$senderId,
			$message,
			$entityUri,
			$userAgent
		);
		
		$this->handleRedirect($entityUri);
	}
	
	final public function handleReportGamePage(string $gameUri): void
	{
		$game = $this->model->getGame($gameUri);
		
		if (!$game)
			throw new HttpNotFound404();
		
		if ($game['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleReportGamePageGet($game);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	final public function handleReportGamePageGet(array $game): void
	{
		$linkBack = buildInternalLink($this->language, 'game', $game['uri']);
		$this->view->renderReportPage('game', $game['transliterated_name'], $linkBack);
	}
	
	final public function handleReportAlbumPage(string $albumUri): void
	{
		$album = $this->model->getAlbum($albumUri);
		
		if (!$album)
			throw new HttpNotFound404();
		
		if ($album['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleReportAlbumPageGet($album);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleReportAlbumPageGet(array $album): void
	{
		$linkBack = buildInternalLink($this->language, 'album', $album['uri']);
		$this->view->renderReportPage('album', $album['transliterated_name'], $linkBack);
	}
	
	final public function handleReportArtistPage(string $artistUri): void
	{
		$artist = $this->model->getArtist($artistUri);
		
		if (!$artist)
			throw new HttpNotFound404();
		
		if ($artist['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleReportArtistPageGet($artist);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleReportArtistPageGet(array $artist): void
	{
		$linkBack = buildInternalLink($this->language, 'artist', $artist['uri']);
		$this->view->renderReportPage('artist', $artist['transliterated_name'], $linkBack);
	}
	
	final public function handleReportCharacterPage(string $characterUri): void
	{
		$character = $this->model->getCharacter($characterUri);
		
		if (!$character)
			throw new HttpNotFound404();
		
		if ($character['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleReportCharacterPageGet($character);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleReportCharacterPageGet(array $character): void
	{
		$linkBack = buildInternalLink($this->language, 'character', $character['uri']);
		$this->view->renderReportPage('character', $character['transliterated_name'], $linkBack);
	}
	
	final public function handleReportLyricsPage(string $albumUri, string $songUri): void
	{
		$album = $this->model->getAlbum($albumUri);
		$song  = $this->model->getSong($albumUri, $songUri);
		
		if (!$album)
			throw new HttpNotFound404();
		
		if ($album['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if (!$song)
			throw new HttpNotFound404();
		
		if ($song['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleReportLyricsPageGet($album, $song);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleReportLyricsPageGet(array $album, array $song): void
	{
		$linkBack = buildInternalLink($this->language, 'album', $album['uri'], 'song', $song['uri']);
		$this->view->renderReportPage('lyrics', $song['transliterated_name'], $linkBack);
	}
	
	final public function handleReportTranslationPage(string $albumUri, string $songUri, string $translationUri): void
	{
		$album       = $this->model->getAlbum($albumUri);
		$song        = $this->model->getSong($albumUri, $songUri);
		$translation = $this->model->getTranslation($albumUri, $songUri, $translationUri);
		
		if (!$album)
			throw new HttpNotFound404();
		
		if ($album['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if (!$song)
			throw new HttpNotFound404();
		
		if ($song['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if ($song['original_song_id'])
		{
			$originalSong = $this->model->getSong(songId: $song['original_song_id']);
			$translation  = $this->model->getTranslation
			(
				$originalSong['album_uri'],
				$originalSong['uri'],
				$translationUri
			);
		}
		else
		{
			$originalSong = null;
			$translation  = $this->model->getTranslation
			(
				$albumUri,
				$songUri,
				$translationUri
			);
		}
		
		if (!$translation)
			throw new HttpNotFound404();
		
		if ($translation['status'] === 'hidden' && !isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleReportTranslationPageGet($album, $song, $translation);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleReportTranslationPageGet(array $album, array $song, array $translation): void
	{
		$linkBack = buildInternalLink($this->language, 'album', $album['uri'], 'song', $song['uri'], 'translation', $translation['uri']);
		$this->view->renderReportPage('translation', $translation['name'], $linkBack);
	}
	
	//-----------------------------------//
	//      Pages to modify content      //
	//-----------------------------------//
	
	public function handleAddGamePage(): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleAddAlbumPage(): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleAddArtistPage(): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleAddCharacterPage(): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleAddSongPage(string $albumUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleAddLyricsPage(string $albumUri, string $songUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleAddTranslationPage(string $albumUri, string $songUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleEditGamePage(string $gameUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleEditAlbumPage(string $albumUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleEditArtistPage(string $artistUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleEditCharacterPage(string $characterUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleEditSongPage(string $albumUri, string $songUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleEditLyricsPage(string $albumUri, string $songUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleEditTranslationPage(string $albumUri, string $songUri, string $translationUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleDeleteGamePage(string $gameUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleDeleteAlbumPage(string $albumUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleDeleteArtistPage(string $artistUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleDeleteCharacterPage(string $characterUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleDeleteSongPage(string $albumUri, string $songUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleDeleteLyricsPage(string $albumUri, string $songUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	public function handleDeleteTranslationPage(string $albumUri, string $songUri, string $translationUri): void
	{
		throw new HttpUnauthorized401();
	}
	
	final public function handleUserPage(string $userUri): void
	{
		$user = $this->model->getUserData(username: $userUri);
		
		if (!$user)
			throw new HttpNotFound404();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleUserPageGet($user);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleUserPageGet(array $user): void
	{
		$games        = $this->model->getGameList(userAddedUri: $user['user_username']);
		$albums       = $this->model->getAlbumList(userAddedUri: $user['user_username']);
		$artists      = $this->model->getArtistList(userAddedUri: $user['user_username']);
		$characters   = $this->model->getCharacterList(userAddedUri: $user['user_username']);
		$songs        = $this->model->getSongList(userAddedUri: $user['user_username'], hasVocal: true, isOriginal: true);
		$translations = $this->model->getTranslationList(userAddedUri: $user['user_username']);
		
		$this->view->renderUserPage
		(
			$user,
			$games,
			$albums,
			$artists,
			$characters,
			$songs,
			$translations
		);
	}
	
	final public function handleAboutPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->view->renderAboutPage();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	final public function handlePolicyPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->view->renderPolicyPage();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	final public function handleRulesPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->view->renderRulesPage();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	final public function handleWritingGuidePage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->view->renderWritingGuidePage();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	final public function handleLyricsExamplePage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->view->renderLyricsExamplePage();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	final public function handleFakeAdminPage(): void
	{
		throw new HttpPaymentRequired402();
	}
}
