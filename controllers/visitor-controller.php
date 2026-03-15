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
	
	/*
	final protected function sendMail(string $email, string $token): void
	{
		switch ($this->language)
		{
			case 'en':
				$subject  = 'Verifying account: vn-song-lyrics-db';
				$message1 = 'Follow the link to activate account: ';
				$message2 = '. After this, you may log in.';
				break;
			
			case 'ru':
				$subject  = 'Подтверждение аккаунта: vn-song-lyrics-db';
				$message1 = 'Перейдите по ссылке, чтобы активировать аккаунт: ';
				$message2 = '. После этого, вы сможете войти в аккаунт.';
				break;
			
			case 'ja':
				$subject  = 'アカウント確認: vn-song-lyrics-db';
				$message1 = 'アカウントを有効するために、リンクに従ってください：';
				$message2 = '。このあと、ログインできます。';
				break;
			
			default:
				throw new Exception(__METHOD__.': language not known: '.$this->language);
		}
		
		$link = 'https://vn-song-lyrics-db.ru/'.$this->language.'/activation?id='.$token;
		
		$body = $message1.'<a href="'.$link.'" target="_blank">'.$link.'</a>'.$message2;
		
		// 'mail()' don't work on the server
		// PHPMailer won't do because I need to be a company to use the server SMTP
		// Other foreign free SMTP providers don't register me
		// 
		// If you find a solution, it would be great
		// At the moment, we trick the user: they don't know that we don't verify email
	}
	*/
	
	final protected function createUserSession(array $userData): void
	{
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
		if (isset($_SESSION['user']['id']))
		{
			$this->handleRedirect(buildInternalLink($this->language));
			return;
		}
		
		if (!isset($_SESSION['lastPageBeforeLogIn']))
			$_SESSION['lastPageBeforeLogIn'] = $_SERVER['HTTP_REFERER'] ?? buildInternalLink($this->language);
		
		$error = AuthenticationError::None;
		
		if (isset($_POST['email']) && isset($_POST['password']))
		{
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
			
			if ($error === AuthenticationError::None)
			{
				$this->model->updateLastLogInTimestamp($_POST['email']);
				$userData = $this->model->getUserData(email: $_POST['email']);
				
				session_regenerate_id();
				$this->createUserSession($userData);
				
				$this->handleRedirect($_SESSION['lastPageBeforeLogIn']);
				unset($_SESSION['lastPageBeforeLogIn']);
				return;
			}
		}
		
		$this->view->renderLogInPage($error);
	}
	
	public function handleSignUpPage(): void
	{
		if (isset($_SESSION['user']['id']))
		{
			$this->handleRedirect(buildInternalLink($this->language));
			return;
		}
		
		$error = AuthenticationError::None;
		
		if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['captcha-code']))
		{
			// I forgot that constants can not be declared here :(
			$MIN_LENGTH = 4;
			$MAX_LENGTH = 32;
			
			if (!isset($_SESSION['signUpCaptchaCode']))
				$error = AuthenticationError::CaptchaInvalid;
			
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
			
			if ($error === AuthenticationError::None)
			{
				unset($_SESSION['signUpCaptchaCode']);
				
				// Generating a token to verify the account
				//$verificationToken = createToken($_POST['username']);
				//$this->sendMail($_POST['email'], $verificationToken);
				
				$this->model->createUser
				(
					$_POST['username'],
					$_POST['password'],
					$_POST['email'],
					$_SERVER['REMOTE_ADDR']
				);
				
				$this->model->updateLastLogInTimestamp($_POST['email']);
				$userData = $this->model->getUserData(email: $_POST['email']);
				
				session_regenerate_id();
				$this->createUserSession($userData);
				
				$this->handleRedirect(buildInternalLink($this->language));
				return;
			}
		}
		
		[$_SESSION['signUpCaptchaCode'], $captchaBase64Image] = $this->model->getRandomCaptcha(6, 3);
		$this->view->renderSignUpPage($error, $captchaBase64Image);
	}
	
	/*
	final public function handleVerificationRequiredPage(): void
	{
		if (isset($_SESSION['user']['id']))
		{
			$this->handleRedirect(buildInternalLink($this->language));
			return;
		}
		
		$this->view->renderVerificationRequiredPage();
	}
	
	final public function handleAccountActivationPage(): void
	{
		$token = $_GET['id'] ?? null;
		
		if (!$token)
		{
			$this->handleNotFound();
			return;
		}
		
		$success = $this->model->verifyUser($token);
		
		if (!$success)
		{
			$this->handleNotFound();
			return;
		}
		
		$this->handleRedirect(buildInternalLink($this->language, 'log-in'));
	}
	*/
	
	public function handleLogOutPage(): void
	{
		$this->handleRedirect(buildInternalLink($this->language));
	}
	
	//-----------------------//
	//      Error Pages      //
	//-----------------------//
	
	final public function handleRedirect(string $location): void
	{
		http_response_code(301);
		header('Location: '.$location);
	}
	
	final public function handleBadRequest(): void
	{
		http_response_code(400);
		$this->view->renderBadRequest();
	}
	
	final public function handleUnauthorized(): void
	{
		http_response_code(401);
		$this->view->renderUnauthorized();
	}
	
	final public function handlePaymentRequired(): void
	{
		http_response_code(402);
		$this->view->renderPaymentRequired();
	}
	
	final public function handleForbidden(): void
	{
		http_response_code(403);
		$this->view->renderForbidden();
	}
	
	final public function handleNotFound(): void
	{
		http_response_code(404);
		$this->view->renderNotFound();
	}
	
	final public function handleUnavailableForLegalReasons(): void
	{
		http_response_code(451);
		$this->view->renderUnavailableForLegalReasons();
	}
	
	final public function handleInternalServerError(): void
	{
		http_response_code(500);
		$this->view->renderInternalServerError();
	}
	
	//---------------------------------//
	//      Pages to view content      //
	//---------------------------------//
	
	final public function handleHomePage(): void
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
		$gameList = $this->model->getGameList();
		$this->view->renderGameListPage($gameList);
	}
	
	final public function handleAlbumListPage(): void
	{
		$albumList = $this->model->getAlbumList();
		$this->view->renderAlbumListPage($albumList);
	}
	
	final public function handleArtistListPage(): void
	{
		$artistList = $this->model->getArtistList();
		$this->view->renderArtistListPage($artistList);
	}
	
	final public function handleCharacterListPage(): void
	{
		$characterList = $this->model->getCharacterList();
		$this->view->renderCharacterListPage($characterList);
	}
	
	final public function handleSongListPage(): void
	{
		$songList = $this->model->getSongList(hasVocal: true);
		$this->view->renderSongListPage($songList);
	}
	
	final public function handleTranslationListPage(): void
	{
		$translationList = $this->model->getTranslationList();
		$this->view->renderTranslationListPage($translationList);
	}
	
	final public function handleGamePage(string $gameUri): void
	{
		$game = $this->model->getGame($gameUri);
		
		if (!$game)
		{
			$this->handleNotFound();
			return;
		}
		
		if ($game['status'] === 'hidden' && !isCurrentUserModerator())
		{
			$this->handleUnavailableForLegalReasons();
			return;
		}
		
		$albumList     = $this->model->getAlbumList    (gameUri: $gameUri);
		$characterList = $this->model->getCharacterList(gameUri: $gameUri);
		
		$this->view->renderGamePage($game, $albumList, $characterList);
	}
	
	final public function handleAlbumPage(string $albumUri): void
	{
		$album = $this->model->getAlbum($albumUri);
		
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
		
		$songList = $this->model->getSongList(albumUri: $albumUri);
		$gameList = $this->model->getGameList(albumUri: $albumUri);
		
		$this->view->renderAlbumPage($album, $songList, $gameList);
	}
	
	final public function handleArtistPage(string $artistUri): void
	{
		$artist = $this->model->getArtist($artistUri);
		
		if (!$artist)
		{
			$this->handleNotFound();
			return;
		}
		
		if ($artist['status'] === 'hidden' && !isCurrentUserModerator())
		{
			$this->handleUnavailableForLegalReasons();
			return;
		}
		
		$artistAliases = $this->model->getArtistList(aliasesOfId: $artist['id']);
		$songList      = $this->model->getSongList(artistUri: $artistUri);
		
		$this->view->renderArtistPage($artist, $artistAliases, $songList);
	}
	
	final public function handleCharacterPage(string $characterUri): void
	{
		$character = $this->model->getCharacter($characterUri);
		
		if (!$character)
		{
			$this->handleNotFound();
			return;
		}
		
		if ($character['status'] === 'hidden' && !isCurrentUserModerator())
		{
			$this->handleUnavailableForLegalReasons();
			return;
		}
		
		$gameList = $this->model->getGameList(characterUri: $characterUri);
		$songList = $this->model->getSongList(characterUri: $characterUri);
		
		$this->view->renderCharacterPage($character, $gameList, $songList);
	}
	
	final public function handleLyricsPage(string $albumUri, string $songUri): void
	{
		$album         = $this->model->getAlbum($albumUri);
		$song          = $this->model->getSong($albumUri, $songUri);
		$performerList = $this->model->getPerformerList(albumUri: $albumUri, songUri: $songUri);
		
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
		
		if (!$song)
		{
			$this->handleNotFound();
			return;
		}
		
		if ($song['status'] === 'hidden' && !isCurrentUserModerator())
		{
			$this->handleUnavailableForLegalReasons();
			return;
		}
		
		if ($song['original_song_id'])
		{
			$originalSong    = $this->model->getSong(songId: $song['original_song_id']);
			$translationList = $this->model->getTranslationList(songId: $song['original_song_id']);
		}
		else
		{
			$originalSong    = null;
			$translationList = $this->model->getTranslationList(albumUri: $albumUri, songUri: $songUri);
		}
		
		if (!$song['lyrics'] && !$originalSong || !$song['lyrics'] && $originalSong && !$originalSong['lyrics'])
			$this->view->renderNoLyricsPage($album, $song);
		else
			$this->view->renderLyricsPage($album, $song, $originalSong, $performerList, $translationList);
	}
	
	final public function handleTranslationPage(string $albumUri, string $songUri, string $translationUri): void
	{
		$album = $this->model->getAlbum($albumUri);
		$song  = $this->model->getSong($albumUri, $songUri);
		
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
		
		if (!$song)
		{
			$this->handleNotFound();
			return;
		}
		
		if ($song['status'] === 'hidden' && !isCurrentUserModerator())
		{
			$this->handleUnavailableForLegalReasons();
			return;
		}
		
		if ($song['original_song_id'])
		{
			$originalSong = $this->model->getSong(songId: $song['original_song_id']);
			$translation  = $this->model->getTranslation
			(
				$originalSong['album_uri'],
				$originalSong['uri'],
				$translationUri
			);
			
			if (!$translation)
			{
				$this->handleNotFound();
				return;
			}
			
			if ($translation['status'] === 'hidden' && !isCurrentUserModerator())
			{
				$this->handleUnavailableForLegalReasons();
				return;
			}
			
			$performerList   = $this->model->getPerformerList
			(
				albumUri: $albumUri,
				songUri:  $songUri
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
			
			if (!$translation)
			{
				$this->handleNotFound();
				return;
			}
			
			if ($translation['status'] === 'hidden' && !isCurrentUserModerator())
			{
				$this->handleUnavailableForLegalReasons();
				return;
			}
			
			$performerList   = $this->model->getPerformerList
			(
				albumUri: $albumUri,
				songUri:  $songUri
			);
			$translationList = $this->model->getTranslationList
			(
				albumUri: $albumUri,
				songUri:  $songUri
			);
		}
		
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
		if (empty($_POST))
		{
			[$_SESSION['feedbackCaptchaCode'], $captchaBase64Image] = $this->model->getRandomCaptcha(4, 0);
			$feedbacks = $this->model->getFeedbackList();
			
			$this->view->renderFeedbackPage($feedbacks, $captchaBase64Image);
			return;
		}
		
		$senderId = $_SESSION['user']['id']  ?? null;
		$senderIp = $_SERVER['REMOTE_ADDR'];
		$message  = $_POST['message']        ?? null;
		$captcha  = $_POST['captcha-code']   ?? null;
		
		if (isNullOrEmpty($message))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (mb_strtolower($_SESSION['feedbackCaptchaCode']) !== mb_strtolower($captcha))
		{
			$this->handleBadRequest();
			return;
		}
		
		$id = $this->model->addFeedback($senderId, $senderIp, $message);
		
		// Must redirect in order to avoid duplicating the message
		$this->handleRedirect(buildInternalLink($this->language, 'feedback'));
	}
	
	final public function handleReport(): void
	{
		if (empty($_POST))
		{
			$this->handleBadRequest();
			return;
		}
		
		$senderId  = $_SESSION['user']['id']     ?? null;
		$message   = $_POST['report-text']       ?? null;
		$entityUri = $_POST['entity-uri']        ?? null;
		$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
		
		if (haveNullOrEmpty($message, $entityUri, $userAgent))
		{
			$this->handleBadRequest();
			return;
		}
		
		$this->model->addReport
		(
			$senderId,
			$message,
			$entityUri,
			$userAgent
		);
		
		// language has been included on the report page
		$this->handleRedirect($entityUri);
	}
	
	final public function handleReportGamePage(string $gameUri): void
	{
		$game = $this->model->getGame($gameUri);
		
		if (!$game)
		{
			$this->handleNotFound();
			return;
		}
		
		if ($game['status'] === 'hidden' && !isCurrentUserModerator())
		{
			$this->handleUnavailableForLegalReasons();
			return;
		}
		
		$linkBack = buildInternalLink($this->language, 'game', $game['uri']);
		$this->view->renderReportPage('game', $game['transliterated_name'], $linkBack);
	}
	
	final public function handleReportAlbumPage(string $albumUri): void
	{
		$album = $this->model->getAlbum($albumUri);
		
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
		
		$linkBack = buildInternalLink($this->language, 'album', $album['uri']);
		$this->view->renderReportPage('album', $album['transliterated_name'], $linkBack);
	}
	
	final public function handleReportArtistPage(string $artistUri): void
	{
		$artist = $this->model->getArtist($artistUri);
		
		if (!$artist)
		{
			$this->handleNotFound();
			return;
		}
		
		if ($artist['status'] === 'hidden' && !isCurrentUserModerator())
		{
			$this->handleUnavailableForLegalReasons();
			return;
		}
		
		$linkBack = buildInternalLink($this->language, 'artist', $artist['uri']);
		$this->view->renderReportPage('artist', $artist['transliterated_name'], $linkBack);
	}
	
	final public function handleReportCharacterPage(string $characterUri): void
	{
		$character = $this->model->getCharacter($characterUri);
		
		if (!$character)
		{
			$this->handleNotFound();
			return;
		}
		
		if ($character['status'] === 'hidden' && !isCurrentUserModerator())
		{
			$this->handleUnavailableForLegalReasons();
			return;
		}
		
		$linkBack = buildInternalLink($this->language, 'character', $character['uri']);
		$this->view->renderReportPage('character', $character['transliterated_name'], $linkBack);
	}
	
	final public function handleReportLyricsPage(string $albumUri, string $songUri): void
	{
		$album = $this->model->getAlbum($albumUri);
		$song  = $this->model->getSong($albumUri, $songUri);
		
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
		
		if (!$song)
		{
			$this->handleNotFound();
			return;
		}
		
		if ($song['status'] === 'hidden' && !isCurrentUserModerator())
		{
			$this->handleUnavailableForLegalReasons();
			return;
		}
		
		$linkBack = buildInternalLink($this->language, 'album', $album['uri'], 'song', $song['uri']);
		$this->view->renderReportPage('lyrics', $song['transliterated_name'], $linkBack);
	}
	
	final public function handleReportTranslationPage(string $albumUri, string $songUri, string $translationUri): void
	{
		$album       = $this->model->getAlbum($albumUri);
		$song        = $this->model->getSong($albumUri, $songUri);
		$translation = $this->model->getTranslation($albumUri, $songUri, $translationUri);
		
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
		
		if (!$song)
		{
			$this->handleNotFound();
			return;
		}
		
		if ($song['status'] === 'hidden' && !isCurrentUserModerator())
		{
			$this->handleUnavailableForLegalReasons();
			return;
		}
		
		if (!$translation)
		{
			$this->handleNotFound();
			return;
		}
		
		if ($translation['status'] === 'hidden' && !isCurrentUserModerator())
		{
			$this->handleUnavailableForLegalReasons();
			return;
		}
		
		$linkBack = buildInternalLink($this->language, 'album', $album['uri'], 'song', $song['uri'], 'translation', $translation['uri']);
		$this->view->renderReportPage('translation', $translation['name'], $linkBack);
	}
	
	//-----------------------------------//
	//      Pages to modify content      //
	//-----------------------------------//
	
	public function handleAddGamePage(): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleAddAlbumPage(): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleAddArtistPage(): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleAddCharacterPage(): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleAddSongPage(string $albumUri): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleAddLyricsPage(string $albumUri, string $songUri): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleAddTranslationPage(string $albumUri, string $songUri): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleEditGamePage(string $gameUri): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleEditAlbumPage(string $albumUri): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleEditArtistPage(string $artistUri): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleEditCharacterPage(string $characterUri): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleEditSongPage(string $albumUri, string $songUri): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleEditLyricsPage(string $albumUri, string $songUri): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleEditTranslationPage(string $albumUri, string $songUri, string $translationUri): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleDeleteGamePage(string $gameUri): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleDeleteAlbumPage(string $albumUri): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleDeleteArtistPage(string $artistUri): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleDeleteCharacterPage(string $characterUri): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleDeleteSongPage(string $albumUri, string $songUri): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleDeleteLyricsPage(string $albumUri, string $songUri): void
	{
		$this->handleUnauthorized();
	}
	
	public function handleDeleteTranslationPage(string $albumUri, string $songUri, string $translationUri): void
	{
		$this->handleUnauthorized();
	}
	
	final public function handleUserPage(string $userUri): void
	{
		$user = $this->model->getUserData(username: $userUri);
		
		if (!$user)
		{
			$this->handleNotFound();
			return;
		}
		
		$games        = $this->model->getGameList(userUri: $userUri);
		$albums       = $this->model->getAlbumList(userUri: $userUri);
		$artists      = $this->model->getArtistList(userUri: $userUri);
		$characters   = $this->model->getCharacterList(userUri: $userUri);
		$songs        = $this->model->getSongList(userUri: $userUri);
		$translations = $this->model->getTranslationList(userUri: $userUri);
		
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
		$this->view->renderAboutPage();
	}
	
	final public function handlePolicyPage(): void
	{
		$this->view->renderPolicyPage();
	}
	
	final public function handleRulesPage(): void
	{
		$this->view->renderRulesPage();
	}
	
	final public function handleWritingGuidePage(): void
	{
		$this->view->renderWritingGuidePage();
	}
	
	final public function handleLyricsExamplePage(): void
	{
		$this->view->renderLyricsExamplePage();
	}
}
