<?php

use Uri\Rfc3986\Uri;

final class Router
{
	private static $routes = [];
	
	private static function addRoute(string $path, string $method): void
	{
		self::$routes[] =
		[
			'regex'  => '#^'.preg_replace('/\{([a-zA-Z0-9_-]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $path).'$#',
			'method' => $method
		];
	}
	
	private static function findRoute(string $path): array
	{
		foreach (self::$routes as $route)
		{
			$matches = [];
			
			if (preg_match($route['regex'], $path, $matches))
			{
				$parameters = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
				$language   = array_shift($parameters);
				
				return
				[
					'method'     => $route['method'],
					'language'   => $language,
					'parameters' => $parameters
				];
			}
		}
		
		return
		[
			'method'     => '',
			'language'   => explode('/', $path, 3)[1] ?? '',
			'parameters' => []
		];
	}
	
	private static function initializeRouteArray(): void
	{
		self::addRoute('/{language}',                'handleHomePage');
		self::addRoute('/{language}/about',          'handleAboutPage');
		self::addRoute('/{language}/policy',         'handlePolicyPage');
		self::addRoute('/{language}/rules',          'handleRulesPage');
		self::addRoute('/{language}/writing-guide',  'handleWritingGuidePage');
		self::addRoute('/{language}/lyrics-example', 'handleLyricsExamplePage');
		self::addRoute('/{language}/admin',          'handleFakeAdminPage');
		
		self::addRoute('/{language}/feedback',           'handleFeedbackPage');
		self::addRoute('/{language}/add-feedback-reply', 'handleAddFeedbackReply');
		self::addRoute('/{language}/delete-feedback',    'handleDeleteFeedback');
		
		self::addRoute('/{language}/log-in',  'handleLogInPage');
		self::addRoute('/{language}/sign-up', 'handleSignUpPage');
		self::addRoute('/{language}/log-out', 'handleLogOutPage');
		
		self::addRoute('/{language}/user/{userUri}',                 'handleUserPage');
		// self::addRoute('/{language}/user/{userUri}/change-email',    'handleChangeEmailPage');
		self::addRoute('/{language}/user/{userUri}/change-password', 'handleChangePasswordPage');
		self::addRoute('/{language}/user/{userUri}/change-username', 'handleChangeUsernamePage');
		self::addRoute('/{language}/user/{userUri}/change-about-me', 'handleChangeAboutMePage');
		self::addRoute('/{language}/user/{userUri}/delete-account',  'handleDeleteAccountPage');
		
		self::addRoute('/{language}/game-list',        'handleGameListPage');
		self::addRoute('/{language}/album-list',       'handleAlbumListPage');
		self::addRoute('/{language}/artist-list',      'handleArtistListPage');
		self::addRoute('/{language}/character-list',   'handleCharacterListPage');
		self::addRoute('/{language}/song-list',        'handleSongListPage');
		self::addRoute('/{language}/translation-list', 'handleTranslationListPage');
		
		self::addRoute('/{language}/game/{gameUri}',                                               'handleGamePage');
		self::addRoute('/{language}/album/{albumUri}',                                             'handleAlbumPage');
		self::addRoute('/{language}/artist/{artistUri}',                                           'handleArtistPage');
		self::addRoute('/{language}/character/{characterUri}',                                     'handleCharacterPage');
		self::addRoute('/{language}/album/{albumUri}/song/{songUri}',                              'handleLyricsPage');
		self::addRoute('/{language}/album/{albumUri}/song/{songUri}/translation/{translationUri}', 'handleTranslationPage');
		
		self::addRoute('/{language}/add-game',                                        'handleAddGamePage');
		self::addRoute('/{language}/add-album',                                       'handleAddAlbumPage');
		self::addRoute('/{language}/add-artist',                                      'handleAddArtistPage');
		self::addRoute('/{language}/add-character',                                   'handleAddCharacterPage');
		self::addRoute('/{language}/album/{albumUri}/add-song',                       'handleAddSongPage');
		self::addRoute('/{language}/album/{albumUri}/song/{songUri}/add-lyrics',      'handleAddLyricsPage');
		self::addRoute('/{language}/album/{albumUri}/song/{songUri}/add-translation', 'handleAddTranslationPage');
		
		self::addRoute('/{language}/game/{gameUri}/edit',                                               'handleEditGamePage');
		self::addRoute('/{language}/album/{albumUri}/edit',                                             'handleEditAlbumPage');
		self::addRoute('/{language}/artist/{artistUri}/edit',                                           'handleEditArtistPage');
		self::addRoute('/{language}/character/{characterUri}/edit',                                     'handleEditCharacterPage');
		self::addRoute('/{language}/album/{albumUri}/song/{songUri}/edit',                              'handleEditSongPage');
		self::addRoute('/{language}/album/{albumUri}/song/{songUri}/edit-lyrics',                       'handleEditLyricsPage');
		self::addRoute('/{language}/album/{albumUri}/song/{songUri}/translation/{translationUri}/edit', 'handleEditTranslationPage');
		
		self::addRoute('/{language}/game/{gameUri}/delete',                                               'handleDeleteGamePage');
		self::addRoute('/{language}/album/{albumUri}/delete',                                             'handleDeleteAlbumPage');
		self::addRoute('/{language}/artist/{artistUri}/delete',                                           'handleDeleteArtistPage');
		self::addRoute('/{language}/character/{characterUri}/delete',                                     'handleDeleteCharacterPage');
		self::addRoute('/{language}/album/{albumUri}/song/{songUri}/delete',                              'handleDeleteSongPage');
		self::addRoute('/{language}/album/{albumUri}/song/{songUri}/delete-lyrics',                       'handleDeleteLyricsPage');
		self::addRoute('/{language}/album/{albumUri}/song/{songUri}/translation/{translationUri}/delete', 'handleDeleteTranslationPage');
		
		self::addRoute('/{language}/game/{gameUri}/report',                                               'handleReportGamePage');
		self::addRoute('/{language}/album/{albumUri}/report',                                             'handleReportAlbumPage');
		self::addRoute('/{language}/artist/{artistUri}/report',                                           'handleReportArtistPage');
		self::addRoute('/{language}/character/{characterUri}/report',                                     'handleReportCharacterPage');
		// self::addRoute('/{language}/album/{albumUri}/song/{songUri}/report',                              'handleDeleteSongPage');
		self::addRoute('/{language}/album/{albumUri}/song/{songUri}/report-lyrics',                       'handleReportLyricsPage');
		self::addRoute('/{language}/album/{albumUri}/song/{songUri}/translation/{translationUri}/report', 'handleReportTranslationPage');
		
		self::addRoute('/{language}/game/{gameUri}/change-status',                                               'handleChangeGameStatus');
		self::addRoute('/{language}/album/{albumUri}/change-status',                                             'handleChangeAlbumStatus');
		self::addRoute('/{language}/artist/{artistUri}/change-status',                                           'handleChangeArtistStatus');
		self::addRoute('/{language}/character/{characterUri}/change-status',                                     'handleChangeCharacterStatus');
		self::addRoute('/{language}/album/{albumUri}/song/{songUri}/change-status',                              'handleChangeSongStatus');
		self::addRoute('/{language}/album/{albumUri}/song/{songUri}/translation/{translationUri}/change-status', 'handleChangeTranslationStatus');
		self::addRoute('/{language}/game-album-relation/{gameUri}/{albumUri}/change-status',                     'handleChangeGameAlbumRelationStatus');
		self::addRoute('/{language}/album-game-relation/{albumUri}/{gameUri}/change-status',                     'handleChangeGameAlbumRelationStatus');
		self::addRoute('/{language}/character-game-relation/{characterUri}/{gameUri}/change-status',             'handleChangeCharacterGameRelationStatus');
		self::addRoute('/{language}/game-character-relation/{gameUri}/{characterUri}/change-status',             'handleChangeCharacterGameRelationStatus');
		
		self::addRoute('/{language}/control-panel',                             'handleControlPanelPage');
		self::addRoute('/{language}/control-panel/report-list',                 'handleReportListPage');
		self::addRoute('/{language}/control-panel/user-list',                   'handleUserListPage');
		self::addRoute('/{language}/control-panel/unchecked-game-list',         'handleUncheckedGameListPage');
		self::addRoute('/{language}/control-panel/unchecked-album-list',        'handleUncheckedAlbumListPage');
		self::addRoute('/{language}/control-panel/unchecked-artist-list',       'handleUncheckedArtistListPage');
		self::addRoute('/{language}/control-panel/unchecked-character-list',    'handleUncheckedCharacterListPage');
		self::addRoute('/{language}/control-panel/unchecked-song-list',         'handleUncheckedSongListPage');
		self::addRoute('/{language}/control-panel/unchecked-translation-list',  'handleUncheckedTranslationListPage');
		
		self::addRoute('/{language}/control-panel/update-static-pages-sitemap',     'handleUpdateStaticPageSitemap');
		self::addRoute('/{language}/control-panel/update-game-list-sitemap',        'handleUpdateGameListSitemap');
		self::addRoute('/{language}/control-panel/update-album-list-sitemap',       'handleUpdateAlbumListSitemap');
		self::addRoute('/{language}/control-panel/update-artist-list-sitemap',      'handleUpdateArtistListSitemap');
		self::addRoute('/{language}/control-panel/update-character-list-sitemap',   'handleUpdateCharacterListSitemap');
		self::addRoute('/{language}/control-panel/update-song-list-sitemap',        'handleUpdateSongListSitemap');
		self::addRoute('/{language}/control-panel/update-translation-list-sitemap', 'handleUpdateTranslationListSitemap');
		
		self::addRoute('/{language}/control-panel/add-language',            'handleAddLanguagePage');
		self::addRoute('/{language}/control-panel/switch-maintenance-mode', 'handleSwitchMaintenanceMode');
		self::addRoute('/{language}/report/change-status',                  'handleReportStatus');
		self::addRoute('/{language}/album/{albumUri}/fill-album',           'handleFillAlbumPage');
		
		self::addRoute('/{language}/ikuinen-este-kiitos', 'handleBotTrapPage');
	}
	
	private static function isRootRequested(string $path): bool
	{
		return $path === '/';
	}
	
	private static function isUriMalformed(Uri|null $uri): bool
	{
		return $uri === null || !str_starts_with($uri->getPath(), '/');
	}
	
	private static function isNonexistentFileRequested(string $requestedPath): bool
	{
		// If a file is requested then the route ends with "/name.extension"
		$dot   = mb_strrpos($requestedPath, '.');
		$slash = mb_strrpos($requestedPath, '/');
		
		return ($dot > $slash);
	}
	
	public static function run(): void
	{
		$uri = Uri::parse($_SERVER['REQUEST_URI']);
		
		if (self::isUriMalformed($uri))
		{
			http_response_code(400);
			exit;
		}
		
		$path = $uri->getPath();
		
		if (self::isRootRequested($path))
		{
			$languages = Localizer::detectUserLanguages();
			$language  = Localizer::getSuitableLanguage($languages);
			
			http_response_code(302);
			header('Location: /'.$language);
			exit;
		}
		
		if (self::isNonexistentFileRequested($path))
		{
			http_response_code(404);
			exit;
		}
		
		try
		{
			Authorizer::startSession();
			Authorizer::updateRateLimit();
			
			self::initializeRouteArray();
			[
				'method'     => $method,
				'language'   => $language,
				'parameters' => $parameters
			] = self::findRoute($path);
			
			if (Localizer::isAcceptedLanguage($language))
				$controller = new ($_SESSION['user']['role'].'Controller')($language);
			else
				$controller = new ($_SESSION['user']['role'].'Controller')(Localizer::DEFAULT_LANGUAGE);
			
			if (Authorizer::isCurrentIpBlocked() || Authorizer::isCurrentUserAgentBlocked())
				throw HttpStatus::PermanentlyBlocked->exception();
			
			if (Authorizer::isRequestForbidden() || Authorizer::isRateLimitExceededToBlock())
			{
				Authorizer::blockCurrentIp();
				throw HttpStatus::PermanentlyBlocked->exception();
			}
			
			if (Authorizer::isMaintenanceModeActive() && !Authorizer::isCurrentUserAdministrator())
				throw HttpStatus::ServiceUnavailable->exception();
			
			if (Authorizer::isRateLimitExceeded())
				throw HttpStatus::TooManyRequests->exception();
			
			if (!Localizer::isAcceptedLanguage($language))
				throw HttpStatus::NotAcceptable->exception();
			
			if (!method_exists($controller, $method))
				throw HttpStatus::NotFound->exception();
			
			$controller->$method(...$parameters);
		}
		catch (HttpException $e)
		{
			$errorName = $e->getStatus()->name;
			$errorCode = $e->getStatus()->value;
			
			$method = 'handle'.$errorName.$errorCode;
			
			$controller->$method();
			Logger::logError($e);
		}
		catch (Throwable $e)
		{
			if (Localizer::isAcceptedLanguage($language))
				$controller = new ErrorController($language);
			else
				$controller = new ErrorController(Localizer::DEFAULT_LANGUAGE);
			
			$controller->handleInternalServerError500();
			Logger::logError($e);
		}
		
		Logger::logAccess();
	}
}
