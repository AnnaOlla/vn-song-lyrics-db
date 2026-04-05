<?php

class Router
{
	const BANNED_IPS_FILENAME      = 'banned-ip-list.txt';
	const BANNED_REQUESTS_FILENAME = 'banned-requests.txt';
	const ACCEPTED_LANGUAGES       = ['en', 'ru', 'ja'];
	const DEFAULT_LANGUAGE         = 'en';
	
	private static function isIpBanned(): bool
	{
		$bannedIps = file(self::BANNED_IPS_FILENAME, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		return in_array($_SERVER['REMOTE_ADDR'], $bannedIps);
	}
	
	private static function isIpToBan(): bool
	{
		$bannedStrings = file(self::BANNED_REQUESTS_FILENAME, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		
		foreach ($bannedStrings as $string)
		{
			if (mb_strstr($_SERVER['REQUEST_URI'], $string))
			{
				$file = fopen(self::BANNED_IPS_FILENAME, 'a');
				fwrite($file, $_SERVER['REMOTE_ADDR'].PHP_EOL);
				fclose($file);
				
				return true;
			}
		}
		
		return false;
	}
	
	private static function detectUserLanguages(): array
	{
		// My example: en-GB,en;q=0.9,ru;q=0.8,fi;q=0.7,ja;q=0.6
		// en-GB must be deduced to q=1.0
		// en;q=0.9 must be dropped
		
		$preferences = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
		$languages = [];
		
		foreach ($preferences as $preference)
		{
			// Split code and value
			$parts    = explode(';', $preference);
			
			// Strip country code if exists
			$language = explode('-', $parts[0])[0];
			
			// Deduce q=1.0 if parts[1] does not exist
			$weight   = explode('=', $parts[1] ?? 'q=1.0')[1];
			
			// Assign and avoid collision (same language, different countries)
			$languages[$language] = $languages[$language] ?? (float)$weight;
		}
		
		return $languages;
	}
	
	private static function getSuitableLanguage(array $languages): string
	{
		foreach ($languages as $language => $weight)
		{
			if (in_array($language, self::ACCEPTED_LANGUAGES, true))
				return $language;
		}
		
		return self::DEFAULT_LANGUAGE;
	}
	
	private static function isRootRequested(): bool
	{
		return ($_SERVER['REQUEST_URI'] === '/');
	}
	
	private static function isNonExistentFileRequested(): bool
	{
		// If the server has not found some file, then it forwards the request to index.php
		
		// If a file is requested then the route ends with "/name.extension"
		$dot   = mb_strrpos($_SERVER['REQUEST_URI'], '.');
		$slash = mb_strrpos($_SERVER['REQUEST_URI'], '/');
		
		return ($dot > $slash);
	}
	
	public static function run()
	{
		if (self::isIpBanned() || self::isIpToBan())
		{
			http_response_code(403);
			exit;
		}
		
		if (self::isRootRequested())
		{
			$languages = self::detectUserLanguages();
			$language  = self::getSuitableLanguage($languages);
			
			http_response_code(302);
			header('Location: /'.$language);
			exit;
		}
		
		if (self::isNonExistentFileRequested())
		{
			http_response_code(404);
			exit;
		}
		
		$routes     = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		$routeCount = count($routes);
		
		for ($i = 0; $i < $routeCount; $i++)
			$routes[$i] = rawurldecode($routes[$i]);
		
		$language = $routes[1];
		
		//-------------------//
		//      Routing      //
		//-------------------//
		
		if ($routeCount === 2)
		{
			$method = 'handleHomePage';
			$parameters = [];
		}
		
		//---------------------------------------//
		//      Methods related to accounts      //
		//---------------------------------------//
		
		else if ($routeCount === 3 && $routes[2] === 'log-in')
		{
			$method = 'handleLogInPage';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'sign-up')
		{
			$method = 'handleSignUpPage';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'log-out')
		{
			$method = 'handleLogOutPage';
			$parameters = [];
		}
		
		else if ($routeCount === 4 && $routes[2] === 'user')
		{
			$method = 'handleUserPage';
			$parameters = ['userUri' => $routes[3]];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'user' && $routes[4] === 'change-account-data')
		{
			$method = 'handleChangeAccountDataPage';
			$parameters = ['userUri' => $routes[3]];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'user' && $routes[4] === 'delete-account')
		{
			$method = 'handleDeleteAccountPage';
			$parameters = ['userUri' => $routes[3]];
		}
		
		//----------------------------------------------------------//
		//      Methods related to viewing general information      //
		//----------------------------------------------------------//
		
		else if ($routeCount === 3 && $routes[2] === 'game-list')
		{
			$method = 'handleGameListPage';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'artist-list')
		{
			$method = 'handleArtistListPage';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'character-list')
		{
			$method = 'handleCharacterListPage';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'album-list')
		{
			$method = 'handleAlbumListPage';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'song-list')
		{
			$method = 'handleSongListPage';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'translation-list')
		{
			$method = 'handleTranslationListPage';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'feedback')
		{
			$method = 'handleFeedbackPage';
			$parameters = [];
		}
		
		//-----------------------------------------------------------//
		//      Methods related to viewing detailed information      //
		//-----------------------------------------------------------//
		
		else if ($routeCount === 4 && $routes[2] === 'game')
		{
			$method = 'handleGamePage';
			$parameters = ['gameUri' => $routes[3]];
		}
		
		else if ($routeCount === 4 && $routes[2] === 'artist')
		{
			$method = 'handleArtistPage';
			$parameters = ['artistUri' => $routes[3]];
		}
		
		else if ($routeCount === 4 && $routes[2] === 'character')
		{
			$method = 'handleCharacterPage';
			$parameters = ['characterUri' => $routes[3]];
		}
		
		else if ($routeCount === 4 && $routes[2] === 'album')
		{
			$method = 'handleAlbumPage';
			$parameters = ['albumUri' => $routes[3]];
		}
		
		else if ($routeCount === 6 && $routes[2] === 'album' && $routes[4] === 'song')
		{
			$method = 'handleLyricsPage';
			$parameters = ['albumUri' => $routes[3], 'songUri' => $routes[5]];
		}
		
		else if ($routeCount === 8 && $routes[2] === 'album' && $routes[4] === 'song' && $routes[6] === 'translation')
		{
			$method = 'handleTranslationPage';
			$parameters = ['albumUri' => $routes[3], 'songUri' => $routes[5], 'translationUri' => $routes[7]];
		}
		
		//-------------------------------------------------//
		//      Methods related to adding information      //
		//-------------------------------------------------//
		
		else if ($routeCount === 3 && $routes[2] === 'add-game')
		{
			$method = 'handleAddGamePage';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'add-album')
		{
			$method = 'handleAddAlbumPage';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'add-artist')
		{
			$method = 'handleAddArtistPage';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'add-character')
		{
			$method = 'handleAddCharacterPage';
			$parameters = [];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'album' && $routes[4] === 'add-song')
		{
			$method = 'handleAddSongPage';
			$parameters = ['albumUri' => $routes[3]];
		}
		
		else if ($routeCount === 7 && $routes[2] === 'album' && $routes[4] === 'song' && $routes[6] === 'add-lyrics')
		{
			$method = 'handleAddLyricsPage';
			$parameters = ['albumUri' => $routes[3], 'songUri' => $routes[5]];
		}
		
		else if ($routeCount === 7 && $routes[2] === 'album' && $routes[4] === 'song' && $routes[6] === 'add-translation')
		{
			$method = 'handleAddTranslationPage';
			$parameters = ['albumUri' => $routes[3], 'songUri' => $routes[5]];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'album' && $routes[4] === 'fill-album')
		{
			$method = 'handleFillAlbumPage';
			$parameters = ['albumUri' => $routes[3]];
		}
		
		//--------------------------------------------------//
		//      Methods related to editing information      //
		//--------------------------------------------------//
		
		else if ($routeCount === 5 && $routes[2] === 'game' && $routes[4] === 'edit')
		{
			$method = 'handleEditGamePage';
			$parameters = ['gameUri' => $routes[3]];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'album' && $routes[4] === 'edit')
		{
			$method = 'handleEditAlbumPage';
			$parameters = ['albumUri' => $routes[3]];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'artist' && $routes[4] === 'edit')
		{
			$method = 'handleEditArtistPage';
			$parameters = ['artistUri' => $routes[3]];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'character' && $routes[4] === 'edit')
		{
			$method = 'handleEditCharacterPage';
			$parameters = ['characterUri' => $routes[3]];
		}
		
		else if ($routeCount === 7 && $routes[2] === 'album' && $routes[4] === 'song' && $routes[6] === 'edit')
		{
			$method = 'handleEditSongPage';
			$parameters = ['albumUri' => $routes[3], 'songUri' => $routes[5]];
		}
		
		else if ($routeCount === 7 && $routes[2] === 'album' && $routes[4] === 'song' && $routes[6] === 'edit-lyrics')
		{
			$method = 'handleEditLyricsPage';
			$parameters = ['albumUri' => $routes[3], 'songUri' => $routes[5]];
		}
		
		else if ($routeCount === 9 && $routes[2] === 'album' && $routes[4] === 'song' && $routes[6] === 'translation' && $routes[8] === 'edit')
		{
			$method = 'handleEditTranslationPage';
			$parameters = ['albumUri' => $routes[3], 'songUri' => $routes[5], 'translationUri' => $routes[7]];
		}
		
		//---------------------------------------------------//
		//      Methods related to deleting information      //
		//---------------------------------------------------//
		
		else if ($routeCount === 5 && $routes[2] === 'game' && $routes[4] === 'delete')
		{
			$method = 'handleDeleteGamePage';
			$parameters = ['gameUri' => $routes[3]];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'album' && $routes[4] === 'delete')
		{
			$method = 'handleDeleteAlbumPage';
			$parameters = ['albumUri' => $routes[3]];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'artist' && $routes[4] === 'delete')
		{
			$method = 'handleDeleteArtistPage';
			$parameters = ['artistUri' => $routes[3]];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'character' && $routes[4] === 'delete')
		{
			$method = 'handleDeleteCharacterPage';
			$parameters = ['characterUri' => $routes[3]];
		}
		
		/*
		else if ($routeCount === 6 && $routes[1] === 'album' && $routes[3] === 'song' && $routes[5] === 'delete')
		{
			$method = 'handleDeleteSongPage';
			$parameters = ['albumUri' => $routes[2], 'songUri' => $routes[4]];
		}
		*/
		
		else if ($routeCount === 7 && $routes[2] === 'album' && $routes[4] === 'song' && $routes[6] === 'delete-lyrics')
		{
			$method = 'handleDeleteLyricsPage';
			$parameters = ['albumUri' => $routes[3], 'songUri' => $routes[5]];
		}
		
		else if ($routeCount === 9 && $routes[2] === 'album' && $routes[4] === 'song' && $routes[6] === 'translation' && $routes[8] === 'delete')
		{
			$method = 'handleDeleteTranslationPage';
			$parameters = ['albumUri' => $routes[3], 'songUri' => $routes[5], 'translationUri' => $routes[7]];
		}
		
		//----------------------------------------//
		//      Methods related to reporting      //
		//----------------------------------------//
		
		else if ($routeCount === 3 && $routes[2] === 'report')
		{
			$method = 'handleReport';
			$parameters = [];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'game' && $routes[4] === 'report')
		{
			$method = 'handleReportGamePage';
			$parameters = ['gameUri' => $routes[3]];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'album' && $routes[4] === 'report')
		{
			$method = 'handleReportAlbumPage';
			$parameters = ['albumUri' => $routes[3]];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'artist' && $routes[4] === 'report')
		{
			$method = 'handleReportArtistPage';
			$parameters = ['artistUri' => $routes[3]];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'character' && $routes[4] === 'report')
		{
			$method = 'handleReportCharacterPage';
			$parameters = ['characterUri' => $routes[3]];
		}
		
		/*
		else if ($routeCount === 6 && $routes[1] === 'album' && $routes[3] === 'song' && $routes[5] === 'report')
		{
			$method = 'handleReportSongPage';
			$parameters = ['albumUri' => $routes[2], 'songUri' => $routes[4]];
		}
		*/
		
		else if ($routeCount === 7 && $routes[2] === 'album' && $routes[4] === 'song' && $routes[6] === 'report-lyrics')
		{
			$method = 'handleReportLyricsPage';
			$parameters = ['albumUri' => $routes[3], 'songUri' => $routes[5]];
		}
		
		else if ($routeCount === 9 && $routes[2] === 'album' && $routes[4] === 'song' && $routes[6] === 'translation' && $routes[8] === 'report')
		{
			$method = 'handleReportTranslationPage';
			$parameters = ['albumUri' => $routes[3], 'songUri' => $routes[5], 'translationUri' => $routes[7]];
		}
		
		//-----------------------------------------//
		//      Methods related to moderation      //
		//-----------------------------------------//
		
		else if ($routeCount === 5 && $routes[2] === 'game' && $routes[4] === 'change-status')
		{
			$method = 'handleChangeGameStatus';
			$parameters = ['gameUri' => $routes[3]];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'album' && $routes[4] === 'change-status')
		{
			$method = 'handleChangeAlbumStatus';
			$parameters = ['albumUri' => $routes[3]];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'artist' && $routes[4] === 'change-status')
		{
			$method = 'handleChangeArtistStatus';
			$parameters = ['artistUri' => $routes[3]];
		}
		
		else if ($routeCount === 5 && $routes[2] === 'character' && $routes[4] === 'change-status')
		{
			$method = 'handleChangeCharacterStatus';
			$parameters = ['characterUri' => $routes[3]];
		}
		
		else if ($routeCount === 7 && $routes[2] === 'album' && $routes[4] === 'song' && $routes[6] === 'change-status')
		{
			$method = 'handleChangeSongStatus';
			$parameters = ['albumUri' => $routes[3], 'songUri' => $routes[5]];
		}
		
		else if ($routeCount === 9 && $routes[2] === 'album' && $routes[4] === 'song' && $routes[6] === 'translation' && $routes[8] === 'change-status')
		{
			$method = 'handleChangeTranslationStatus';
			$parameters = ['albumUri' => $routes[3], 'songUri' => $routes[5], 'translationUri' => $routes[7]];
		}
		
		else if ($routeCount === 6 && $routes[2] === 'game-album-relation' && $routes[5] === 'change-status')
		{
			$method = 'handleChangeGameAlbumRelationStatus';
			$parameters = ['gameUri' => $routes[3], 'albumUri' => $routes[4]];
		}
		
		else if ($routeCount === 6 && $routes[2] === 'album-game-relation' && $routes[5] === 'change-status')
		{
			$method = 'handleChangeGameAlbumRelationStatus';
			$parameters = ['albumUri' => $routes[3], 'gameUri' => $routes[4]];
		}
		
		else if ($routeCount === 6 && $routes[2] === 'character-game-relation' && $routes[5] === 'change-status')
		{
			$method = 'handleChangeCharacterGameRelationStatus';
			$parameters = ['characterUri' => $routes[3], 'gameUri' => $routes[4]];
		}
		
		else if ($routeCount === 6 && $routes[2] === 'game-character-relation' && $routes[5] === 'change-status')
		{
			$method = 'handleChangeCharacterGameRelationStatus';
			$parameters = ['gameUri' => $routes[3], 'characterUri' => $routes[4]];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'add-feedback-reply')
		{
			$method = 'handleAddFeedbackReply';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'delete-feedback')
		{
			$method = 'handleDeleteFeedback';
			$parameters = [];
		}
		
		else if ($routeCount === 4 && $routes[2] === 'report' && $routes[3] === 'change-status')
		{
			$method = 'handleReportStatus';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'control-panel')
		{
			$method = 'handleControlPanelPage';
			$parameters = [];
		}
		
		else if ($routeCount === 4 && $routes[2] === 'control-panel' && $routes[3] === 'report-list')
		{
			$method = 'handleReportListPage';
			$parameters = [];
		}
		
		else if ($routeCount === 4 && $routes[2] === 'control-panel' && $routes[3] === 'add-language')
		{
			$method = 'handleAddLanguagePage';
			$parameters = [];
		}
		/*
		else if ($routeCount === 5 && $routes[2] === 'control-panel' && $routes[3] === 'edit-language')
		{
			$method = 'handleEditLanguagePage';
			$parameters = ['enName' => $routes[4]];
		}
		*/
		
		else if ($routeCount === 4 && $routes[2] === 'control-panel' && $routes[3] === 'user-list')
		{
			$method = 'handleUserListPage';
			$parameters = [];
		}
		
		//-------------------------//
		//      Other Methods      //
		//-------------------------//
		
		else if ($routeCount === 3 && $routes[2] === 'about')
		{
			$method = 'handleAboutPage';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'policy')
		{
			$method = 'handlePolicyPage';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'rules')
		{
			$method = 'handleRulesPage';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'writing-guide')
		{
			$method = 'handleWritingGuidePage';
			$parameters = [];
		}
		
		else if ($routeCount === 3 && $routes[2] === 'lyrics-example')
		{
			$method = 'handleLyricsExamplePage';
			$parameters = [];
		}
		
		else if ($routeCount >= 3 && $routes[2] === 'admin')
		{
			$method = 'handleFakeAdminPage';
			$parameters = [];
		}
		
		else
		{
			$method = '';
			$parameters = [];
		}
		
		//-------------------------------//
		//      Calling the handler      //
		//-------------------------------//
		
		try
		{
			$controllerPath = 'controllers/'.$_SESSION['user']['role'].'-controller.php';
			
			if (!file_exists($controllerPath))
			{
				// Fallback to show the error
				require_once 'controllers/visitor-controller.php';
				$controller = new VisitorController(self::DEFAULT_LANGUAGE);
				
				throw new HttpInternalServerError500();
			}
			
			if (!in_array($language, self::ACCEPTED_LANGUAGES))
			{
				// Fallback to show the error
				require_once 'controllers/visitor-controller.php';
				$controller = new VisitorController(self::DEFAULT_LANGUAGE);
				
				throw new HttpNotAcceptable406();
			}
			
			require_once $controllerPath;
			$controller = new ($_SESSION['user']['role'].'controller')($language);
			
			if (method_exists($controller, $method))
				$controller->$method(...$parameters);
			else
				throw new HttpNotFound404();
		}
		catch (HttpBadRequest400 $e)
		{
			error_log($e);
			$controller->handleBadRequest400();
		}
		catch (HttpUnauthorized401 $e)
		{
			error_log($e);
			$controller->handleUnauthorized401();
		}
		catch (HttpPaymentRequired402 $e)
		{
			error_log($e);
			$controller->handlePaymentRequired402();
		}
		catch (HttpForbidden403 $e)
		{
			error_log($e);
			$controller->handleForbidden403();
		}
		catch (HttpNotFound404 $e)
		{
			error_log($e);
			$controller->handleNotFound404();
		}
		catch (HttpMethodNotAllowed405 $e)
		{
			error_log($e);
			$controller->handleMethodNotAllowed405();
		}
		catch (HttpNotAcceptable406 $e)
		{
			error_log($e);
			$controller->handleNotAcceptable406();
		}
		catch (HttpUnavailableForLegalReasons451 $e)
		{
			error_log($e);
			$controller->handleUnavailableForLegalReasons451();
		}
		catch (HttpInternalServerError500 $e)
		{
			error_log($e);
			$controller->handleInternalServerError500();
		}
		catch (Throwable $e)
		{
			error_log($e);
			$controller->handleInternalServerError500();
		}
	}
}
