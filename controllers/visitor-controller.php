<?php

require_once 'controllers/error-controller.php';

class VisitorController extends ErrorController
{
	protected const ACCOUNT_DATA_MIN_LENGTH = 4;
	protected const ACCOUNT_DATA_MAX_LENGTH = 32;
	
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
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleLogInPageGet();
				break;
			
			case 'POST':
				$this->handleLogInPagePost();
				break;
			
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	private function handleLogInPageGet
	(
		string|null $email = null,
		InputError  $error = InputError::None
	): void
	{
		if (isset($_SERVER['HTTP_REFERER']))
			$_SESSION['redirect']['logIn'] = $_SERVER['HTTP_REFERER'];
		else
			$_SESSION['redirect']['logIn'] = Http::buildInternalPath($this->language);
		
		$this->view->renderLogInPage($email, $error);
	}
	
	private function handleLogInPagePost(): void
	{
		if (!Validation::isDataEncodedInUTF8($_POST))
			throw new HttpBadRequest400('Data was sent in incorrect encoding', get_defined_vars());
		
		$email     = $_POST['email']         ?? null;
		$password  = $_POST['password']      ?? null;
		$ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
		
		if (Validation::haveNullOrEmpty($email, $password, $ipAddress))
			throw new HttpBadRequest400('Log-in data was not sent', get_defined_vars());
		
		if (!$this->model->isEmailRegistered($email))
		{
			$this->handleLogInPageGet($email, InputError::EmailNotFound);
			return;
		}
		
		$userId = $this->model->getUserIdWithEmail($email);
		
		if (!$this->model->isPasswordCorrect($userId, $password))
		{
			$this->handleLogInPageGet($email, InputError::IncorrectPassword);
			return;
		}
		
		$userData = $this->model->getUserData($userId);
		$this->createUserSession($userData);
		
		$this->model->updateLastLogInTimestamp($userId);
		$this->model->addUserFingerprint($userId, $ipAddress);
		
		$this->handleRedirect($_SESSION['redirect']['logIn']);
		unset($_SESSION['redirect']['logIn']);
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
				
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	private function handleSignUpPageGet
	(
		string|null $username = null,
		string|null $email    = null,
		InputError  $error    = InputError::None
	): void
	{
		if (isset($_SERVER['HTTP_REFERER']))
			$_SESSION['redirect']['signUp'] = $_SERVER['HTTP_REFERER'];
		else
			$_SESSION['redirect']['signUp'] = Http::buildInternalPath($this->language);
		
		$captchaData = $this->model->getRandomCaptcha(6, 3);
		
		$_SESSION['captcha']['signUp'] = $captchaData[0];
		$captchaBase64Image            = $captchaData[1];
		
		$this->view->renderSignUpPage($username, $email, $error, $captchaBase64Image);
	}
	
	private function handleSignUpPagePost(): void
	{
		if (!Validation::isDataEncodedInUTF8($_POST))
			throw new HttpBadRequest400('Data was sent in incorrect encoding', get_defined_vars());
		
		$username    = $_POST['username']             ?? null;
		$email       = $_POST['email']                ?? null;
		$password    = $_POST['password']             ?? null;
		$ipAddress   = $_SERVER['REMOTE_ADDR']        ?? null;
		$sentCaptcha = $_POST['captcha-code']         ?? null;
		$madeCaptcha = $_SESSION['captcha']['signUp'] ?? null;
		
		if (Validation::haveNullOrEmpty($username, $email, $password, $ipAddress, $sentCaptcha, $madeCaptcha))
			throw new HttpBadRequest400('Sign-up data was not sent', get_defined_vars());
		
		if (!Validation::areCaptchasEqual($madeCaptcha, $sentCaptcha))
		{
			$this->handleSignUpPageGet($username, $email, InputError::CaptchaInvalid);
			return;
		}
		
		if (!Validation::isLatinAlphabetAndNumbers($username))
		{
			$this->handleSignUpPageGet($username, $email, InputError::UsernameForbiddenSymbols);
			return;
		}
		
		if (mb_strlen($username) < self::ACCOUNT_DATA_MIN_LENGTH)
		{
			$this->handleSignUpPageGet($username, $email, InputError::UsernameLengthIncorrect);
			return;
		}
		
		if (mb_strlen($username) > self::ACCOUNT_DATA_MAX_LENGTH)
		{
			$this->handleSignUpPageGet($username, $email, InputError::UsernameLengthIncorrect);
			return;
		}
		
		if ($this->model->isUsernameRegistered($username))
		{
			$this->handleSignUpPageGet($username, $email, InputError::UsernameTaken);
			return;
		}
		
		if (!Validation::isEmailValid($email))
		{
			$this->handleSignUpPageGet($username, $email, InputError::EmailInvalid);
			return;
		}
		
		if ($this->model->isEmailRegistered($email))
		{
			$this->handleSignUpPageGet($username, $email, InputError::EmailTaken);
			return;
		}
		
		if (!Validation::isLatinAlphabetAndNumbers($password))
		{
			$this->handleSignUpPageGet($username, $email, InputError::PasswordForbiddenSymbols);
			return;
		}
		
		if (mb_strlen($password) < self::ACCOUNT_DATA_MIN_LENGTH)
		{
			$this->handleSignUpPageGet($username, $email, InputError::PasswordLengthIncorrect);
			return;
		}
		
		if (mb_strlen($password) > self::ACCOUNT_DATA_MAX_LENGTH)
		{
			$this->handleSignUpPageGet($username, $email, InputError::PasswordLengthIncorrect);
			return;
		}
		
		$userId   = $this->model->createUser($username, $password, $email);
		$userData = $this->model->getUserData($userId);
		
		$this->createUserSession($userData);
		$this->model->addUserFingerprint($userId, $ipAddress);
		
		$this->handleRedirect($_SESSION['redirect']['signUp']);
		unset($_SESSION['redirect']['signUp']);
		unset($_SESSION['captcha']['signUp']);
	}
	
	public function handleLogOutPage(): void
	{
		$this->handleRedirect(Http::buildInternalPath($this->language));
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
			
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
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
				
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	private function handleGameListPageGet(): void
	{
		if (!Validation::isDataEncodedInUTF8($_GET))
			throw new HttpBadRequest400('Data was sent in incorrect encoding', get_defined_vars());
		
		$limit  = $_GET['limit']  ?? null;
		$page   = $_GET['page']   ?? null;
		$search = $_GET['search'] ?? null;
		
		$limit  = Parsing::parseNullableInteger($limit, 1);
		$page   = Parsing::parseNullableInteger($page, 1);
		$search = Parsing::trimNullableString($search);
		
		if (!Validation::isNullOrEmpty($_GET['page'] ?? null) && Validation::isNullOrEmpty($page))
			throw new HttpBadRequest400();
		
		if (!Validation::isNullOrEmpty($_GET['limit'] ?? null) && Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		if (!Validation::isNullOrEmpty($page) && Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		if (Validation::isNullOrEmpty($page) && !Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		$list  = $this->model->getGameList(page: $page, limit: $limit, search: $search);
		$count = $this->model->getGameCount(search: $search);
		
		$this->view->renderGameListPage($list, $count, $page, $limit, $search);
	}
	
	final public function handleAlbumListPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleAlbumListPageGet();
				break;
				
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	private function handleAlbumListPageGet(): void
	{
		if (!Validation::isDataEncodedInUTF8($_GET))
			throw new HttpBadRequest400('Data was sent in incorrect encoding', get_defined_vars());
		
		$limit  = $_GET['limit']  ?? null;
		$page   = $_GET['page']   ?? null;
		$search = $_GET['search'] ?? null;
		
		$limit  = Parsing::parseNullableInteger($limit, 1);
		$page   = Parsing::parseNullableInteger($page, 1);
		$search = Parsing::trimNullableString($search);
		
		if (!Validation::isNullOrEmpty($_GET['page'] ?? null) && Validation::isNullOrEmpty($page))
			throw new HttpBadRequest400();
		
		if (!Validation::isNullOrEmpty($_GET['limit'] ?? null) && Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		if (!Validation::isNullOrEmpty($page) && Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		if (Validation::isNullOrEmpty($page) && !Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		$list  = $this->model->getAlbumList(page: $page, limit: $limit, search: $search);
		$count = $this->model->getAlbumCount(search: $search);
		
		$this->view->renderAlbumListPage($list, $count, $page, $limit, $search);
	}
	
	final public function handleArtistListPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleArtistListPageGet();
				break;
				
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	private function handleArtistListPageGet(): void
	{
		if (!Validation::isDataEncodedInUTF8($_GET))
			throw new HttpBadRequest400('Data was sent in incorrect encoding', get_defined_vars());
		
		$limit  = $_GET['limit']  ?? null;
		$page   = $_GET['page']   ?? null;
		$search = $_GET['search'] ?? null;
		
		$limit  = Parsing::parseNullableInteger($limit, 1);
		$page   = Parsing::parseNullableInteger($page, 1);
		$search = Parsing::trimNullableString($search);
		
		if (!Validation::isNullOrEmpty($_GET['page'] ?? null) && Validation::isNullOrEmpty($page))
			throw new HttpBadRequest400();
		
		if (!Validation::isNullOrEmpty($_GET['limit'] ?? null) && Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		if (!Validation::isNullOrEmpty($page) && Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		if (Validation::isNullOrEmpty($page) && !Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		$list  = $this->model->getArtistList(page: $page, limit: $limit, search: $search);
		$count = $this->model->getArtistCount(search: $search);
		
		$this->view->renderArtistListPage($list, $count, $page, $limit, $search);
	}
	
	final public function handleCharacterListPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleCharacterListPageGet();
				break;
				
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	private function handleCharacterListPageGet(): void
	{
		if (!Validation::isDataEncodedInUTF8($_GET))
			throw new HttpBadRequest400('Data was sent in incorrect encoding', get_defined_vars());
		
		$limit  = $_GET['limit']  ?? null;
		$page   = $_GET['page']   ?? null;
		$search = $_GET['search'] ?? null;
		
		$limit  = Parsing::parseNullableInteger($limit, 1);
		$page   = Parsing::parseNullableInteger($page, 1);
		$search = Parsing::trimNullableString($search);
		
		if (!Validation::isNullOrEmpty($_GET['page'] ?? null) && Validation::isNullOrEmpty($page))
			throw new HttpBadRequest400();
		
		if (!Validation::isNullOrEmpty($_GET['limit'] ?? null) && Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		if (!Validation::isNullOrEmpty($page) && Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		if (Validation::isNullOrEmpty($page) && !Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		$list  = $this->model->getCharacterList(page: $page, limit: $limit, search: $search);
		$count = $this->model->getCharacterCount(search: $search);
		
		$this->view->renderCharacterListPage($list, $count, $page, $limit, $search);
	}
	
	final public function handleSongListPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleSongListPageGet();
				break;
				
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	private function handleSongListPageGet(): void
	{
		if (!Validation::isDataEncodedInUTF8($_GET))
			throw new HttpBadRequest400('Data was sent in incorrect encoding', get_defined_vars());
		
		$limit  = $_GET['limit']  ?? null;
		$page   = $_GET['page']   ?? null;
		$search = $_GET['search'] ?? null;
		
		$limit  = Parsing::parseNullableInteger($limit, 1);
		$page   = Parsing::parseNullableInteger($page, 1);
		$search = Parsing::trimNullableString($search);
		
		if (!Validation::isNullOrEmpty($_GET['page'] ?? null) && Validation::isNullOrEmpty($page))
			throw new HttpBadRequest400();
		
		if (!Validation::isNullOrEmpty($_GET['limit'] ?? null) && Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		if (!Validation::isNullOrEmpty($page) && Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		if (Validation::isNullOrEmpty($page) && !Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		$list  = $this->model->getSongList(page: $page, limit: $limit, search: $search, hasVocal: true);
		$count = $this->model->getSongCount(search: $search, hasVocal: true);
		
		$this->view->renderSongListPage($list, $count, $page, $limit, $search);
	}
	
	final public function handleTranslationListPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleTranslationListPageGet();
				break;
				
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	private function handleTranslationListPageGet(): void
	{
		if (!Validation::isDataEncodedInUTF8($_GET))
			throw new HttpBadRequest400('Data was sent in incorrect encoding', get_defined_vars());
		
		$limit  = $_GET['limit']  ?? null;
		$page   = $_GET['page']   ?? null;
		$search = $_GET['search'] ?? null;
		
		$limit  = Parsing::parseNullableInteger($limit, 1);
		$page   = Parsing::parseNullableInteger($page, 1);
		$search = Parsing::trimNullableString($search);
		
		if (!Validation::isNullOrEmpty($_GET['page'] ?? null) && Validation::isNullOrEmpty($page))
			throw new HttpBadRequest400();
		
		if (!Validation::isNullOrEmpty($_GET['limit'] ?? null) && Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		if (!Validation::isNullOrEmpty($page) && Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		if (Validation::isNullOrEmpty($page) && !Validation::isNullOrEmpty($limit))
			throw new HttpBadRequest400();
		
		$list  = $this->model->getTranslationList(page: $page, limit: $limit, search: $search);
		$count = $this->model->getTranslationCount(search: $search);
		
		$this->view->renderTranslationListPage($list, $count, $page, $limit, $search);
	}
	
	final public function handleGamePage(string $gameUri): void
	{
		$game = $this->model->getGame($gameUri);
		
		if (!$game)
			throw new HttpNotFound404();
		
		if ($game['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleGamePageGet($game);
				break;
			
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
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
		
		if ($album['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleAlbumPageGet($album);
				break;
				
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
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
		
		if ($artist['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleArtistPageGet($artist);
				break;
				
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
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
		
		if ($character['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleCharacterPageGet($character);
				break;
				
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
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
		
		if ($album['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if (!$song)
			throw new HttpNotFound404();
		
		if ($song['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if (!$song['has_vocal'])
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleLyricsPageGet($album, $song);
				break;
				
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
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
		
		if ($album['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if (!$song)
			throw new HttpNotFound404();
		
		if ($song['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if (!$song['has_vocal'])
			throw new HttpForbidden403();
		
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
		
		if ($translation['status'] === 'hidden' && !Session::isCurrentUserModerator())
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
			
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	private function handleFeedbackPageGet
	(
		string|null $feedback = null,
		InputError  $error   = InputError::None
	): void
	{
		$captchaData = $this->model->getRandomCaptcha(4, 0);
		
		$_SESSION['captcha']['feedback'] = $captchaData[0];
		$captchaBase64Image              = $captchaData[1];
		
		$feedbacks = $this->model->getFeedbackList();
		$this->view->renderFeedbackPage($feedbacks, $feedback, $error, $captchaBase64Image);
	}
	
	private function handleFeedbackPagePost(): void
	{
		if (!Validation::isDataEncodedInUTF8($_POST))
			throw new HttpBadRequest400('Data was sent in incorrect encoding', get_defined_vars());
		
		$senderId    = $_SESSION['user']['id']          ?? null;
		$senderIp    = $_SERVER['REMOTE_ADDR']          ?? null;
		$message     = $_POST['message']                ?? null;
		$sentCaptcha = $_POST['captcha-code']           ?? null;
		$madeCaptcha = $_SESSION['captcha']['feedback'] ?? null;
		
		$message     = Parsing::trimNullableString($message);
		
		if (Validation::isNullOrEmpty($senderIp, $message))
			throw new HttpUnprocessableEntity422('Feedback data was not sent');
		
		if (!Validation::areCaptchasEqual($madeCaptcha, $sentCaptcha))
		{
			$this->handleFeedbackPageGet($message, InputError::CaptchaInvalid);
			return;
		}
		
		$this->model->addFeedback($senderId, $senderIp, $message);
		$this->handleRedirect(Http::buildInternalPath($this->language, 'feedback'));
	}
	
	final public function handleReport(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'POST':
				$this->handleReportPost();
				break;
			
			case 'CONNECT':
			case 'DELETE':
			case 'GET':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	private function handleReportPost(): void
	{
		if (!Validation::isDataEncodedInUTF8($_POST))
			throw new HttpBadRequest400('Data was sent in incorrect encoding', get_defined_vars());
		
		$senderId  = $_SESSION['user']['id']     ?? null;
		$ipAddress = $_SERVER['REMOTE_ADDR']     ?? null;
		$message   = $_POST['report-text']       ?? null;
		$entityUri = $_POST['entity-uri']        ?? null;
		$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
		
		if (Validation::haveNullOrEmpty($ipAddress, $message, $entityUri, $userAgent))
			throw new HttpUnprocessableEntity422('Null or empty submitted', get_defined_vars());
		
		$this->model->addReport
		(
			$senderId,
			$ipAddress,
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
		
		if ($game['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleReportGamePageGet($game);
				break;
			
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	final public function handleReportGamePageGet(array $game): void
	{
		$linkBack = Http::buildInternalPath($this->language, 'game', $game['uri']);
		$this->view->renderReportPage('game', $game['transliterated_name'], $linkBack);
	}
	
	final public function handleReportAlbumPage(string $albumUri): void
	{
		$album = $this->model->getAlbum($albumUri);
		
		if (!$album)
			throw new HttpNotFound404();
		
		if ($album['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleReportAlbumPageGet($album);
				break;
			
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	private function handleReportAlbumPageGet(array $album): void
	{
		$linkBack = Http::buildInternalPath($this->language, 'album', $album['uri']);
		$this->view->renderReportPage('album', $album['transliterated_name'], $linkBack);
	}
	
	final public function handleReportArtistPage(string $artistUri): void
	{
		$artist = $this->model->getArtist($artistUri);
		
		if (!$artist)
			throw new HttpNotFound404();
		
		if ($artist['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleReportArtistPageGet($artist);
				break;
			
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	private function handleReportArtistPageGet(array $artist): void
	{
		$linkBack = Http::buildInternalPath($this->language, 'artist', $artist['uri']);
		$this->view->renderReportPage('artist', $artist['transliterated_name'], $linkBack);
	}
	
	final public function handleReportCharacterPage(string $characterUri): void
	{
		$character = $this->model->getCharacter($characterUri);
		
		if (!$character)
			throw new HttpNotFound404();
		
		if ($character['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleReportCharacterPageGet($character);
				break;
			
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	private function handleReportCharacterPageGet(array $character): void
	{
		$linkBack = Http::buildInternalPath($this->language, 'character', $character['uri']);
		$this->view->renderReportPage('character', $character['transliterated_name'], $linkBack);
	}
	
	final public function handleReportLyricsPage(string $albumUri, string $songUri): void
	{
		$album = $this->model->getAlbum($albumUri);
		$song  = $this->model->getSong($albumUri, $songUri);
		
		if (!$album)
			throw new HttpNotFound404();
		
		if ($album['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if (!$song)
			throw new HttpNotFound404();
		
		if ($song['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleReportLyricsPageGet($album, $song);
				break;
			
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	private function handleReportLyricsPageGet(array $album, array $song): void
	{
		$linkBack = Http::buildInternalPath($this->language, 'album', $album['uri'], 'song', $song['uri']);
		$this->view->renderReportPage('lyrics', $song['transliterated_name'], $linkBack);
	}
	
	final public function handleReportTranslationPage(string $albumUri, string $songUri, string $translationUri): void
	{
		$album       = $this->model->getAlbum($albumUri);
		$song        = $this->model->getSong($albumUri, $songUri);
		$translation = $this->model->getTranslation($albumUri, $songUri, $translationUri);
		
		if (!$album)
			throw new HttpNotFound404();
		
		if ($album['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if (!$song)
			throw new HttpNotFound404();
		
		if ($song['status'] === 'hidden' && !Session::isCurrentUserModerator())
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
		
		if ($translation['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleReportTranslationPageGet($album, $song, $translation);
				break;
			
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	private function handleReportTranslationPageGet(array $album, array $song, array $translation): void
	{
		$linkBack = Http::buildInternalPath($this->language, 'album', $album['uri'], 'song', $song['uri'], 'translation', $translation['uri']);
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
			
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
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
				
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	final public function handlePolicyPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->view->renderPolicyPage();
				break;
				
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	final public function handleRulesPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->view->renderRulesPage();
				break;
				
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	final public function handleWritingGuidePage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->view->renderWritingGuidePage();
				break;
				
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	final public function handleLyricsExamplePage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->view->renderLyricsExamplePage();
				break;
				
			case 'CONNECT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
			case 'POST':
			case 'PUT':
			case 'TRACE':
				throw new HttpMethodNotAllowed405();
			
			default:
				throw new HttpNotImplemented501();
		}
	}
	
	final public function handleFakeAdminPage(): void
	{
		throw new HttpPaymentRequired402();
	}
}
