<?php

require_once 'controllers/violator-controller.php';

class UserController extends ViolatorController
{
	public function __construct(string $language)
	{
		parent::__construct($language);
		
		require_once 'models/user-model.php';
		require_once 'views/user-view.php';

		$this->model = new UserModel;
		$this->view = new UserView($language);
	}
	
	final public function handleAddGamePage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleAddGamePageGet();
				break;
			
			case 'POST':
				$this->handleAddGamePagePost();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleAddGamePageGet(): void
	{
		$albums     = $this->model->getAlbumList(fetchMinInfo: true);
		$characters = $this->model->getCharacterList(fetchMinInfo: true);
		
		$this->view->renderAddGamePage($albums, $characters);
	}
	
	private function handleAddGamePagePost(): void
	{
		$allAlbumIds     = $this->model->getAlbumList(fetchMinInfo: true);
		$allAlbumIds     = array_column($allAlbumIds, 'id');
		$allCharacterIds = $this->model->getCharacterList(fetchMinInfo: true);
		$allCharacterIds = array_column($allCharacterIds, 'id');
		
		$originalName       = $_POST['original-name']       ?? null;
		$transliteratedName = $_POST['transliterated-name'] ?? null;
		$localizedName      = $_POST['localized-name']      ?? null;
		$logo               = $_FILES['logo']               ?? null;
		$vndbLink           = $_POST['vndb-link']           ?? null;
		$albumIds           = $_POST['album-ids']           ?? [];
		$characterIds       = $_POST['character-ids']       ?? [];
		$userAddedId        = $_SESSION['user']['id'];
		
		$originalName       = Parsing::trimNullableString($originalName);
		$transliteratedName = Parsing::trimNullableString($transliteratedName);
		$localizedName      = Parsing::trimNullableString($localizedName);
		$logo               = Parsing::getNullableFile($logo);
		$vndbId             = Parsing::parseNullableVndbId($vndbLink, 'v');
		$albumIds           = Parsing::parseNullableIntegerArray($albumIds, 1);
		$albumIds           = Parsing::removeNullValues($albumIds);
		$characterIds       = Parsing::parseNullableIntegerArray($characterIds, 1);
		$characterIds       = Parsing::removeNullValues($characterIds);
		
		if (Validation::haveNullOrEmpty($originalName, $transliteratedName))
			throw new HttpBadRequest400('At least one of not-null values was null/empty', get_defined_vars());
		
		if (!Validation::isPrintableAscii($transliteratedName))
			throw new HttpBadRequest400('transliteratedName was not ASCII', get_defined_vars());
		
		if (array_diff($albumIds, $allAlbumIds))
			throw new HttpBadRequest400('At least one of albumIds was invalid', get_defined_vars());
		
		if (array_diff($characterIds, $characterIds))
			throw new HttpBadRequest400('At least one of characterIds was invalid', get_defined_vars());
		
		[$gameId, $gameUri] = $this->model->addGame
		(
			$originalName, 
			$transliteratedName,
			$localizedName,
			$logo,
			$vndbId,
			$userAddedId
		);
		
		foreach ($albumIds as $albumId)
			$this->model->addGameAlbumRelation($gameId, $albumId);
		
		foreach ($characterIds as $characterId)
			$this->model->addCharacterGameRelation($gameId, $characterId);
		
		$link = Session::buildInternalLink($this->language, 'game', $gameUri);
		$this->handleRedirect($link);
	}
	
	final public function handleAddAlbumPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleAddAlbumPageGet();
				break;
			
			case 'POST':
				$this->handleAddAlbumPagePost();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleAddAlbumPageGet(): void
	{
		$games = $this->model->getGameList(fetchMinInfo: true);

		$this->view->renderAddAlbumPage($games);
	}
	
	private function handleAddAlbumPagePost(): void
	{
		$allGameIds         = $this->model->getGameList(fetchMinInfo: true);
		$allGameIds         = array_column($allGameIds, 'id');
		
		$originalName       = $_POST['original-name']       ?? null;
		$transliteratedName = $_POST['transliterated-name'] ?? null;
		$localizedName      = $_POST['localized-name']      ?? null;
		$cover              = $_FILES['cover']              ?? null;
		$vgmdbLink          = $_POST['vgmdb-link']          ?? null;
		$songCount          = $_POST['song-count']          ?? null;
		$gameIds            = $_POST['game-ids']            ?? [];
		$userAddedId        = $_SESSION['user']['id'];
		
		$originalName       = Parsing::trimNullableString($originalName);
		$transliteratedName = Parsing::trimNullableString($transliteratedName);
		$localizedName      = Parsing::trimNullableString($localizedName);
		$cover              = Parsing::getNullableFile($cover);
		$vgmdbId            = Parsing::parseNullableVgmdbId($vgmdbLink, 'album');
		$songCount          = Parsing::parseNullableInteger($songCount, 1);
		$gameIds            = Parsing::parseNullableIntegerArray($gameIds, 1);
		$gameIds            = Parsing::removeNullValues($gameIds);
		
		if (Validation::haveNullOrEmpty($originalName, $transliteratedName, $songCount))
			throw new HttpBadRequest400('At least one of not-null values was null/empty', get_defined_vars());
		
		if (!Validation::isPrintableAscii($transliteratedName))
			throw new HttpBadRequest400('transliteratedName was not ASCII', get_defined_vars());
		
		if (array_diff($gameIds, $allGameIds))
			throw new HttpBadRequest400('At least one of gameIds was invalid', get_defined_vars());
		
		[$albumId, $albumUri] = $this->model->addAlbum
		(
			$originalName,
			$transliteratedName,
			$localizedName,
			$cover,
			$vgmdbId,
			$songCount,
			$userAddedId
		);
		
		foreach ($gameIds as $gameId)
			$this->model->addGameAlbumRelation($gameId, $albumId);
		
		$link = Session::buildInternalLink($this->language, 'album', $albumUri);
		$this->handleRedirect($link);
	}
	
	final public function handleAddArtistPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleAddArtistPageGet();
				break;
			
			case 'POST':
				$this->handleAddArtistPagePost();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleAddArtistPageGet(): void
	{
		$originalArtists = $this->model->getArtistList(fetchMinInfo: true, mayBeAlias: false);
		
		$this->view->renderAddArtistPage($originalArtists);
	}
	
	private function handleAddArtistPagePost(): void
	{
		$originalArtists    = $this->model->getArtistList(fetchMinInfo: true, mayBeAlias: false);
		$originalArtists    = array_column($originalArtists, 'id');
		
		$originalName       = $_POST['original-name']       ?? null;
		$transliteratedName = $_POST['transliterated-name'] ?? null;
		$localizedName      = $_POST['localized-name']      ?? null;
		$photo              = $_FILES['photo']              ?? null;
		$vgmdbLink          = $_POST['vgmdb-link']          ?? null;
		$aliasOfId          = $_POST['original-artist-id']  ?? null;
		$userAddedId        = $_SESSION['user']['id'];
		
		$originalName       = Parsing::trimNullableString($originalName);
		$transliteratedName = Parsing::trimNullableString($transliteratedName);
		$localizedName      = Parsing::trimNullableString($localizedName);
		$photo              = Parsing::getNullableFile($photo);
		$vgmdbId            = Parsing::parseNullableVgmdbId($vgmdbLink, 'artist');
		$aliasOfId          = Parsing::parseNullableInteger($aliasOfId, 1);
		
		if (Validation::haveNullOrEmpty($originalName, $transliteratedName))
			throw new HttpBadRequest400('At least one of not-null values was null/empty', get_defined_vars());
		
		if (!Validation::isPrintableAscii($transliteratedName))
			throw new HttpBadRequest400('transliteratedName was not ASCII', get_defined_vars());
		
		if ($aliasOfId && !in_array($aliasOfId, $originalArtists))
			throw new HttpBadRequest400('aliasOfId was invalid', get_defined_vars());
		
		[$artistId, $artistUri] = $this->model->addArtist
		(
			$originalName,
			$transliteratedName,
			$localizedName,
			$photo,
			$vgmdbId,
			$aliasOfId,
			$userAddedId
		);
		
		$link = Session::buildInternalLink($this->language, 'artist', $artistUri);
		$this->handleRedirect($link);
	}
	
	final public function handleAddCharacterPage(): void
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleAddCharacterPageGet();
				break;
			
			case 'POST':
				$this->handleAddCharacterPagePost();
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleAddCharacterPageGet(): void
	{
		$games = $this->model->getGameList(fetchMinInfo: true);
		
		$this->view->renderAddCharacterPage($games);
	}
	
	private function handleAddCharacterPagePost(): void
	{
		$allGameIds         = $this->model->getGameList(fetchMinInfo: true);
		$allGameIds         = array_column($allGameIds, 'id');
		
		$originalName       = $_POST['original-name']       ?? null;
		$transliteratedName = $_POST['transliterated-name'] ?? null;
		$localizedName      = $_POST['localized-name']      ?? null;
		$image              = $_FILES['image']              ?? null;
		$vndbLink           = $_POST['vndb-link']           ?? null;
		$gameIds            = $_POST['game-ids']            ?? [];
		$userAddedId        = $_SESSION['user']['id'];
		
		$originalName       = Parsing::trimNullableString($originalName);
		$transliteratedName = Parsing::trimNullableString($transliteratedName);
		$localizedName      = Parsing::trimNullableString($localizedName);
		$image              = Parsing::getNullableFile($image);
		$vndbId             = Parsing::parseNullableVndbId($vndbLink, 'c');
		$gameIds            = Parsing::parseNullableIntegerArray($gameIds, 1);
		$gameIds            = Parsing::removeNullValues($gameIds);
		
		if (Validation::haveNullOrEmpty($originalName, $transliteratedName))
			throw new HttpBadRequest400('At least one of not-null values was null/empty', get_defined_vars());
		
		if (!Validation::isPrintableAscii($transliteratedName))
			throw new HttpBadRequest400('transliteratedName was not ASCII', get_defined_vars());
		
		if (array_diff($gameIds, $allGameIds))
			throw new HttpBadRequest400('At least one of gameIds was invalid', get_defined_vars());
		
		[$characterId, $characterUri] = $this->model->addCharacter
		(
			$originalName,
			$transliteratedName,
			$localizedName,
			$image,
			$vndbId,
			$userAddedId
		);
		
		foreach ($gameIds as $gameId)
			$this->model->addCharacterGameRelation($characterId, $gameId);
		
		$link = Session::buildInternalLink($this->language, 'character', $characterUri);
		$this->handleRedirect($link);
	}
	
	final public function handleAddSongPage(string $albumUri): void
	{
		$album     = $this->model->getAlbum($albumUri);
		$songCount = $this->model->getSongCurrentCount($albumUri);
		
		if (!$album)
			throw new HttpNotFound404();
		
		if ($album['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if ($album['status'] === 'checked' && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		if (!Session::isCurrentUser($album['user_added_id']) && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		if ($songCount >= $album['song_count'])
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleAddSongPageGet($album, $songCount);
				break;
			
			case 'POST':
				$this->handleAddSongPagePost($album, $songCount);
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleAddSongPageGet(array $album, int $songCount): void
	{
		$lastSongInfo = $this->model->getLastDiscAndTrack($album['uri']);
		$currentDisc  = $lastSongInfo ? $lastSongInfo['disc_number']      : 1;
		$currentTrack = $lastSongInfo ? $lastSongInfo['track_number'] + 1 : 1;
		$isLastSong   = ($album['song_count'] - $songCount === 1);
		
		$this->view->renderAddSongPage($album, $currentDisc, $currentTrack, $isLastSong);
	}
	
	private function handleAddSongPagePost(array $album, int $songCount): void
	{
		$lastSongInfo       = $this->model->getLastDiscAndTrack($album['uri']);
		$currentDiscNumber  = $lastSongInfo ? $lastSongInfo['disc_number']      : 1;
		$currentTrackNumber = $lastSongInfo ? $lastSongInfo['track_number'] + 1 : 1;
		$isLastSong         = ($album['song_count'] - $songCount === 1);
		
		$discNumber         = $_POST['disc-number']         ?? null;
		$trackNumber        = $_POST['track-number']        ?? null;
		$originalName       = $_POST['original-name']       ?? null;
		$transliteratedName = $_POST['transliterated-name'] ?? null;
		$localizedName      = $_POST['localized-name']      ?? null;
		$hasVocal           = $_POST['has-vocal']           ?? null;
		$userAddedId        = $_SESSION['user']['id'];
		
		$discNumber         = Parsing::parseNullableInteger($discNumber, 1);
		$trackNumber        = Parsing::parseNullableInteger($trackNumber, 1);
		$originalName       = Parsing::trimNullableString($originalName);
		$transliteratedName = Parsing::trimNullableString($transliteratedName);
		$localizedName      = Parsing::trimNullableString($localizedName);
		$hasVocal           = Parsing::parseNullableInteger($hasVocal, 0, 1);
		
		$isSameDiscNextTrack   = ($discNumber === $currentDiscNumber && $trackNumber === $currentTrackNumber);
		$isNextDiscFirstTrack  = ($discNumber === $currentDiscNumber + 1 && $trackNumber === 1);
		$isFirstDisc           = ($discNumber === 1 && $currentDiscNumber === 1);
		$isFirstTrack          = ($trackNumber === 1 && $currentTrackNumber === 1);
		$isFirstDiscFirstTrack = ($isFirstDisc && $isFirstTrack);
		
		if (Validation::haveNullOrEmpty($discNumber, $trackNumber, $originalName, $transliteratedName, $hasVocal))
			throw new HttpBadRequest400('At least one of not-null values was null/empty', get_defined_vars());
		
		if (!Validation::isPrintableAscii($transliteratedName))
			throw new HttpBadRequest400('transliteratedName was not ASCII', get_defined_vars());
		
		if (!$isSameDiscNextTrack && !$isNextDiscFirstTrack && !$isFirstDiscFirstTrack)
			throw new HttpBadRequest400('Track number or disk number was incorrect', get_defined_vars());
		
		$this->model->addSong
		(
			$album['uri'],
			$originalName,
			$transliteratedName,
			$localizedName,
			$discNumber,
			$trackNumber,
			$hasVocal,
			$userAddedId
		);
		
		$songCount++;
		
		if ($songCount === $album['song_count'])
			$this->handleRedirect(Session::buildInternalLink($this->language, 'album', $album['uri']));
		else
			$this->handleRedirect(Session::buildInternalLink($this->language, 'album', $album['uri'], 'add-song'));
	}
	
	final public function handleAddLyricsPage(string $albumUri, string $songUri): void
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
		
		if ($song['status'] === 'checked' && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		if (!$song['has_vocal'] || $song['lyrics'] || $song['original_song_id'])
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleAddLyricsPageGet($album, $song);
				break;
			
			case 'POST':
				$this->handleAddLyricsPagePost($album, $song);
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleAddLyricsPageGet(array $album, array $song): void
	{
		$artists    = $this->model->getArtistList(fetchMinInfo: true);
		$characters = $this->model->getCharacterList(fetchMinInfo: true);
		$languages  = $this->model->getLanguageList(orderBy: [$this->language.'_name ASC']);
		$originals    = $this->model->getSongList
		(
			fetchMinInfo: true,
			isOriginal:   true,
			excludeId:    $song['id'],
			hasVocal:     true,
			orderBy:      ['sn.transliterated_name ASC']
		);
		
		$this->view->renderAddLyricsPage
		(
			$album,
			$song,
			$artists,
			$characters,
			$originals,
			$languages
		);
	}
	
	private function handleAddLyricsPagePost(array $album, array $song): void
	{
		$allArtistIds      = $this->model->getArtistList(fetchMinInfo: true);
		$allArtistIds      = array_column($allArtistIds, 'id');
		$allCharacterIds   = $this->model->getCharacterList(fetchMinInfo: true);
		$allCharacterIds   = array_column($allCharacterIds, 'id');
		$allCharacterIds[] = null;
		$allLanguages      = $this->model->getLanguageList();
		$allLanguages      = array_column($allLanguages, 'id');
		$allOriginalIds    = $this->model->getSongList
		(
			fetchMinInfo: true,
			isOriginal:   true,
			hasVocal:     true,
			excludeId:    $song['id']
		);
		$allOriginalIds    = array_column($allOriginalIds, 'id');
		$allOriginalIds[]  = null;
		
		$artistIds         = $_POST['artist-ids']       ?? [];
		$characterIds      = $_POST['character-ids']    ?? [];
		$originalSongId    = $_POST['original-song-id'] ?? null;
		$languageId        = $_POST['language-id']      ?? null;
		$lyrics            = $_POST['lyrics']           ?? null;
		$notes             = $_POST['notes']            ?? null;
		$userAddedId       = $_SESSION['user']['id'];
		
		$artistIds         = Parsing::parseNullableIntegerArray($artistIds, 1);
		$characterIds      = Parsing::parseNullableIntegerArray($characterIds, 1);
		$originalSongId    = Parsing::parseNullableInteger($originalSongId, 1);
		$languageId        = Parsing::parseNullableInteger($languageId, 1);
		$lyrics            = Parsing::trimNullableText($lyrics);
		$notes             = Parsing::trimNullableText($notes);
		
		// Empty should be reduced to null
		$lyrics            = Parsing::trimNullableString($lyrics);
		$notes             = Parsing::trimNullableString($notes);
		
		if (count($artistIds) === 0)
			throw new HttpBadRequest400('Artists were not provided', get_defined_vars());
		
		if (count($artistIds) !== count($characterIds))
			throw new HttpBadRequest400('Artist count was not equal to character count', get_defined_vars());
		
		if (array_diff($artistIds, $allArtistIds))
			throw new HttpBadRequest400('One of artistIds was invalid', get_defined_vars());
		
		if (array_diff($characterIds, $allCharacterIds))
			throw new HttpBadRequest400('One of characterIds was invalid', get_defined_vars());
		
		if ($originalSongId && ($languageId || $lyrics || $notes))
			throw new HttpBadRequest400('originalSongId was set along with language, lyrics, notes', get_defined_vars());
		
		if (!$originalSongId && (!$languageId || !$lyrics))
			throw new HttpBadRequest400('None of originalSongId, languageId, lyrics was set', get_defined_vars());
		
		if ($originalSongId && !in_array($originalSongId, $allOriginalIds))
			throw new HttpBadRequest400('originalSongId was invalid', get_defined_vars());
		
		if ($languageId && !in_array($languageId, $allLanguages))
			throw new HttpBadRequest400('languageId was invalid', get_defined_vars());
		
		$this->model->addLyrics
		(
			$album['uri'],
			$song['uri'],
			$originalSongId,
			$languageId,
			$lyrics,
			$notes,
			$userAddedId
		);
		
		foreach (array_combine($artistIds, $characterIds) as $artistId => $characterId)
			$this->model->addSongArtistCharacterRelation($song['id'], $artistId, $characterId);
		
		$link = Session::buildInternalLink($this->language, 'album', $album['uri'], 'song', $song['uri']);
		$this->handleRedirect($link);
	}
	
	final public function handleAddTranslationPage(string $albumUri, string $songUri): void
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
		
		if (!$song['has_vocal'] || !$song['has_lyrics'] || $song['original_song_id'])
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleAddTranslationPageGet($album, $song);
				break;
			
			case 'POST':
				$this->handleAddTranslationPagePost($album, $song);
				break;
				
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleAddTranslationPageGet(array $album, array $song): void
	{
		$languages          = $this->model->getLanguageList(orderBy: [$this->language.'_name ASC']);
		$translationsByUser = $this->model->getTranslationList
		(
			fetchMinInfo: true,
			albumUri:     $album['uri'],
			songUri:      $song['uri'],
			userAddedUri: $_SESSION['user']['username']
		);
		
		$this->view->renderAddTranslationPage($album, $song, $languages, $translationsByUser);
	}
	
	private function handleAddTranslationPagePost(array $album, array $song): void
	{
		$translationsByUser = $this->model->getTranslationList
		(
			fetchMinInfo: true,
			albumUri:     $album['uri'],
			songUri:      $song['uri'],
			userAddedUri: $_SESSION['user']['username']
		);
		
		$allLanguages         = $this->model->getLanguageList();
		$allLanguages         = array_column($allLanguages, 'id');
		$forbiddenLanguages   = array_column($translationsByUser, 'language_id');
		$forbiddenLanguages[] = $song['language_id'];
		
		$languageId           = $_POST['translation-language-id'] ?? null;
		$name                 = $_POST['translation-name']        ?? null;
		$lyrics               = $_POST['translation-lyrics']      ?? null;
		$notes                = $_POST['translation-notes']       ?? null;
		$userAddedId          = $_SESSION['user']['id'];
		
		$languageId           = Parsing::parseNullableInteger($languageId, 1);
		$name                 = Parsing::trimNullableString($name);
		$lyrics               = Parsing::trimNullableText($lyrics);
		$notes                = Parsing::trimNullableText($notes);
		
		// Empty should be reduced to null
		$lyrics               = Parsing::trimNullableString($lyrics);
		$notes                = Parsing::trimNullableString($notes);
		
		if (Validation::haveNullOrEmpty($name, $lyrics, $languageId))
			throw new HttpBadRequest400('At least one of not-null values was null/empty', get_defined_vars());
		
		if (!in_array($languageId, $allLanguages))
			throw new HttpBadRequest400('languageId was invalid', get_defined_vars());
		
		if (in_array($languageId, $forbiddenLanguages))
			throw new HttpBadRequest400('languageId was forbidden', get_defined_vars());
		
		[$translationId, $translationUri] = $this->model->addTranslation
		(
			$album['uri'],
			$song['uri'],
			$name,
			$languageId,
			$lyrics,
			$notes,
			$userAddedId
		);
		
		$link = Session::buildInternalLink($this->language, 'album', $album['uri'], 'song', $song['uri'], 'translation', $translationUri);
		$this->handleRedirect($link);
	}
	
	final public function handleEditGamePage(string $gameUri): void
	{
		$game = $this->model->getGame($gameUri);
		
		if (!$game)
			throw new HttpNotFound404();
		
		if ($game['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if ($game['status'] === 'checked' && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleEditGamePageGet($game);
				break;
			
			case 'POST':
				$this->handleEditGamePagePost($game);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleEditGamePageGet(array $game): void
	{
		$relatedAlbumList     = $this->model->getAlbumList(fetchMinInfo: true, gameUri: $game['uri']);
		$relatedCharacterList = $this->model->getCharacterList(fetchMinInfo: true, gameUri: $game['uri']);
		$fullAlbumList        = $this->model->getAlbumList(fetchMinInfo: true);
		$fullCharacterList    = $this->model->getCharacterList(fetchMinInfo: true);
		
		$this->view->renderEditGamePage
		(
			$game,
			$relatedAlbumList,
			$relatedCharacterList,
			$fullAlbumList,
			$fullCharacterList
		);
	}
	
	private function handleEditGamePagePost(array $game): void
	{
		$allAlbumIds         = $this->model->getAlbumList(fetchMinInfo: true);
		$allAlbumIds         = array_column($allAlbumIds, 'id');
		$allCharacterIds     = $this->model->getCharacterList(fetchMinInfo: true);
		$allCharacterIds     = array_column($allCharacterIds, 'id');
		
		$originalName        = $_POST['original-name']       ?? null;
		$transliteratedName  = $_POST['transliterated-name'] ?? null;
		$localizedName       = $_POST['localized-name']      ?? null;
		$logo                = $_FILES['logo']               ?? null;
		$vndbLink            = $_POST['vndb-link']           ?? null;
		$albumIds            = $_POST['album-ids']           ?? [];
		$characterIds        = $_POST['character-ids']       ?? [];
		$userUpdatedId       = $_SESSION['user']['id'];
		
		$originalName        = Parsing::trimNullableString($originalName);
		$transliteratedName  = Parsing::trimNullableString($transliteratedName);
		$localizedName       = Parsing::trimNullableString($localizedName);
		$logo                = Parsing::getNullableFile($logo);
		$vndbId              = Parsing::parseNullableVndbId($vndbLink, 'v');
		$albumIds            = Parsing::parseNullableIntegerArray($albumIds, 1);
		$albumIds            = Parsing::removeNullValues($albumIds);
		$characterIds        = Parsing::parseNullableIntegerArray($characterIds, 1);
		$characterIds        = Parsing::removeNullValues($characterIds);
		
		if (Validation::haveNullOrEmpty($originalName, $transliteratedName))
			throw new HttpBadRequest400('At least one of not-null values was null/empty', get_defined_vars());
		
		if (!Validation::isPrintableAscii($transliteratedName))
			throw new HttpBadRequest400('transliteratedName was not ASCII', get_defined_vars());
		
		if (array_diff($albumIds, $allAlbumIds))
			throw new HttpBadRequest400('languageId was forbidden', get_defined_vars());
		
		if (array_diff($characterIds, $allCharacterIds))
			throw new HttpBadRequest400('At least one of characterIds was invalid', get_defined_vars());
		
		[$gameId, $gameUri] = $this->model->updateGame
		(
			$game['uri'],
			$originalName, 
			$transliteratedName,
			$localizedName,
			$logo,
			$vndbId,
			$userUpdatedId
		);
		
		$this->model->deleteGameAlbumRelation(gameId: $gameId);
		
		foreach ($albumIds as $albumId)
			$this->model->addGameAlbumRelation($gameId, $albumId);
		
		$this->model->deleteCharacterGameRelation(gameId: $gameId);
		
		foreach ($characterIds as $characterId)
			$this->model->addCharacterGameRelation($gameId, $characterId);
		
		$link = Session::buildInternalLink($this->language, 'game', $gameUri);
		$this->handleRedirect($link);
	}
	
	final public function handleEditAlbumPage(string $albumUri): void
	{
		$album = $this->model->getAlbum($albumUri);
		
		if (!$album)
			throw new HttpNotFound404();
		
		if ($album['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if ($album['status'] === 'checked' && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleEditAlbumPageGet($album);
				break;
			
			case 'POST':
				$this->handleEditAlbumPagePost($album);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleEditAlbumPageGet(array $album): void
	{
		$relatedGameList  = $this->model->getGameList(fetchMinInfo: true, albumUri: $album['uri']);
		$relatedSongList  = []; // now editing songs is done on a separate page
		$currentSongCount = $this->model->getSongCurrentCount($album['uri']);
		$fullGameList     = $this->model->getGameList(fetchMinInfo: true);
		
		$this->view->renderEditAlbumPage
		(
			$album,
			$relatedGameList,
			$relatedSongList,
			$currentSongCount,
			$fullGameList
		);
	}
	
	private function handleEditAlbumPagePost(array $album): void
	{
		$allGameIds          = $this->model->getGameList(fetchMinInfo: true);
		$allGameIds          = array_column($allGameIds, 'id');
		$currentSongCount    = $this->model->getSongCurrentCount($album['uri']);
		
		$originalName        = $_POST['original-name']       ?? null;
		$transliteratedName  = $_POST['transliterated-name'] ?? null;
		$localizedName       = $_POST['localized-name']      ?? null;
		$cover               = $_FILES['cover']              ?? null;
		$vgmdbLink           = $_POST['vgmdb-link']          ?? null;
		$songCount           = $_POST['song-count']          ?? null;
		$gameIds             = $_POST['game-ids']            ?? [];
		$userUpdatedId       = $_SESSION['user']['id'];
		
		$originalName        = Parsing::trimNullableString($originalName);
		$transliteratedName  = Parsing::trimNullableString($transliteratedName);
		$localizedName       = Parsing::trimNullableString($localizedName);
		$cover               = Parsing::getNullableFile($cover);
		$vgmdbId             = Parsing::parseNullableVgmdbId($vgmdbLink, 'album');
		$songCount           = Parsing::parseNullableInteger($songCount, 1);
		$gameIds             = Parsing::parseNullableIntegerArray($gameIds, 1);
		$gameIds             = Parsing::removeNullValues($gameIds);
		
		if (Validation::haveNullOrEmpty($originalName, $transliteratedName, $songCount))
			throw new HttpBadRequest400('At least one of not-null values was null/empty', get_defined_vars());
		
		if (!Validation::isPrintableAscii($transliteratedName))
			throw new HttpBadRequest400('transliteratedName was not ASCII', get_defined_vars());
		
		if (array_diff($gameIds, $allGameIds))
			throw new HttpBadRequest400('At least one of gameIds was invalid', get_defined_vars());
		
		if ($songCount < $currentSongCount)
			throw new HttpBadRequest400('New song count was less than current', get_defined_vars());
		
		[$albumId, $albumUri] = $this->model->updateAlbum
		(
			$album['uri'],
			$originalName,
			$transliteratedName,
			$localizedName,
			$cover,
			$vgmdbId,
			$songCount,
			$userUpdatedId
		);
		
		$this->model->deleteGameAlbumRelation(albumId: $albumId);
		
		foreach ($gameIds as $gameId)
			$this->model->addGameAlbumRelation($gameId, $albumId);
		
		$this->handleRedirect(Session::buildInternalLink($this->language, 'album', $albumUri));
	}
	
	final public function handleEditArtistPage(string $artistUri): void
	{
		$artist = $this->model->getArtist($artistUri);
		
		if (!$artist)
			throw new HttpNotFound404();
		
		if ($artist['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if ($artist['status'] === 'checked' && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleEditArtistPageGet($artist);
				break;
			
			case 'POST':
				$this->handleEditArtistPagePost($artist);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleEditArtistPageGet(array $artist): void
	{
		$originalArtists = $this->model->getArtistList(fetchMinInfo: true, mayBeAlias: false);
		
		$this->view->renderEditArtistPage($artist, $originalArtists);
	}
	
	private function handleEditArtistPagePost(array $artist): void
	{
		$originalArtists     = $this->model->getArtistList(fetchMinInfo: true, mayBeAlias: false);
		$originalArtists     = array_column($originalArtists, 'id');
		
		$originalName        = $_POST['original-name']       ?? null;
		$transliteratedName  = $_POST['transliterated-name'] ?? null;
		$localizedName       = $_POST['localized-name']      ?? null;
		$photo               = $_FILES['photo']              ?? null;
		$vgmdbLink           = $_POST['vgmdb-link']          ?? null;
		$aliasOfId           = $_POST['original-artist-id']  ?? null;
		$userUpdatedId       = $_SESSION['user']['id'];
		
		$originalName        = Parsing::trimNullableString($originalName);
		$transliteratedName  = Parsing::trimNullableString($transliteratedName);
		$localizedName       = Parsing::trimNullableString($localizedName);
		$photo               = Parsing::getNullableFile($photo);
		$vgmdbId             = Parsing::parseNullableVgmdbId($vgmdbLink, 'artist');
		$aliasOfId           = Parsing::parseNullableInteger($aliasOfId, 1);
		
		if (Validation::haveNullOrEmpty($originalName, $transliteratedName))
			throw new HttpBadRequest400('At least one of not-null values was null/empty', get_defined_vars());
		
		if (!Validation::isPrintableAscii($transliteratedName))
			throw new HttpBadRequest400('transliteratedName was not ASCII', get_defined_vars());
		
		if ($aliasOfId && !in_array($aliasOfId, $originalArtists))
			throw new HttpBadRequest400('aliasOfId was invalid', get_defined_vars());
		
		[$artistId, $artistUri] = $this->model->updateArtist
		(
			$artist['uri'],
			$originalName,
			$transliteratedName,
			$localizedName,
			$photo,
			$vgmdbId,
			$aliasOfId,
			$userUpdatedId
		);
		
		$this->handleRedirect(Session::buildInternalLink($this->language, 'artist', $artistUri));
	}
	
	final public function handleEditCharacterPage(string $characterUri): void
	{
		$character = $this->model->getCharacter($characterUri);
		
		if (!$character)
			throw new HttpNotFound404();
		
		if ($character['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if ($character['status'] === 'checked' && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleEditCharacterPageGet($character);
				break;
			
			case 'POST':
				$this->handleEditCharacterPagePost($character);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleEditCharacterPageGet(array $character): void
	{
		$relatedGamesList = $this->model->getGameList(fetchMinInfo: true, characterUri: $character['uri']);
		$fullGameList     = $this->model->getGameList(fetchMinInfo: true);
		
		$this->view->renderEditCharacterPage($character, $relatedGamesList, $fullGameList);
	}
	
	private function handleEditCharacterPagePost(array $character): void
	{
		$allGameIds          = $this->model->getGameList(fetchMinInfo: true);
		$allGameIds          = array_column($allGameIds, 'id');
		
		$originalName        = $_POST['original-name']       ?? null;
		$transliteratedName  = $_POST['transliterated-name'] ?? null;
		$localizedName       = $_POST['localized-name']      ?? null;
		$image               = $_FILES['image']              ?? null;
		$vndbLink            = $_POST['vndb-link']           ?? null;
		$gameIds             = $_POST['game-ids']            ?? [];
		$userUpdatedId       = $_SESSION['user']['id'];
		
		$originalName        = Parsing::trimNullableString($originalName);
		$transliteratedName  = Parsing::trimNullableString($transliteratedName);
		$localizedName       = Parsing::trimNullableString($localizedName);
		$image               = Parsing::getNullableFile($image);
		$vndbId              = Parsing::parseNullableVndbId($vndbLink, 'c');
		$gameIds             = Parsing::parseNullableIntegerArray($gameIds, 1);
		$gameIds             = Parsing::removeNullValues($gameIds);
		
		if (Validation::haveNullOrEmpty($originalName, $transliteratedName))
			throw new HttpBadRequest400('At least one of not-null values was null/empty', get_defined_vars());
		
		if (!Validation::isPrintableAscii($transliteratedName))
			throw new HttpBadRequest400('transliteratedName was not ASCII', get_defined_vars());
		
		if (array_diff($gameIds, $allGameIds))
			throw new HttpBadRequest400('At least one of gameIds was invalid', get_defined_vars());
		
		[$characterId, $characterUri] = $this->model->updateCharacter
		(
			$character['uri'],
			$originalName,
			$transliteratedName,
			$localizedName,
			$image,
			$vndbId,
			$userUpdatedId
		);
		
		$this->model->deleteCharacterGameRelation(characterId: $characterId);
		
		foreach ($gameIds as $gameId)
			$this->model->addCharacterGameRelation($characterId, $gameId);
		
		$this->handleRedirect(Session::buildInternalLink($this->language, 'character', $characterUri));
	}
	
	final public function handleEditSongPage(string $albumUri, string $songUri): void
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
		
		if (!Session::isCurrentUser($album['user_added_id']) && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleEditSongPageGet($album, $song);
				break;
			
			case 'POST':
				$this->handleEditSongPagePost($album, $song);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleEditSongPageGet(array $album, array $song): void
	{
		$this->view->renderEditSongPage($album, $song);
	}
	
	private function handleEditSongPagePost(array $album, array $song): void
	{	
		$originalName       = $_POST['original-name']       ?? null;
		$transliteratedName = $_POST['transliterated-name'] ?? null;
		$localizedName      = $_POST['localized-name']      ?? null;
		$hasVocal           = $_POST['has-vocal']           ?? null;
		$userUpdatedId      = $_SESSION['user']['id'];
		
		$originalName       = Parsing::trimNullableString($originalName);
		$transliteratedName = Parsing::trimNullableString($transliteratedName);
		$localizedName      = Parsing::trimNullableString($localizedName);
		$hasVocal           = Parsing::parseNullableInteger($hasVocal, 0, 1);
		
		if (Validation::haveNullOrEmpty($originalName, $transliteratedName, $hasVocal))
			throw new HttpBadRequest400('At least one of not-null values was null/empty', get_defined_vars());
		
		if (!Validation::isPrintableAscii($transliteratedName))
			throw new HttpBadRequest400('transliteratedName was not ASCII', get_defined_vars());
		
		$this->model->updateSong
		(
			$album['uri'],
			$song['uri'],
			$originalName,
			$transliteratedName,
			$localizedName,
			$hasVocal,
			$userUpdatedId
		);
		
		$this->handleRedirect(Session::buildInternalLink($this->language, 'album', $album['uri']));
	}
	
	final public function handleEditLyricsPage(string $albumUri, string $songUri): void
	{
		$album        = $this->model->getAlbum($albumUri);
		$song         = $this->model->getSong($albumUri, $songUri);
		$translations = $this->model->getTranslationList
		(
			fetchMinInfo: true,
			albumUri:     $albumUri,
			songUri:      $songUri
		);
		
		if (!$album)
			throw new HttpNotFound404();
		
		if ($album['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if (!$song)
			throw new HttpNotFound404();
		
		if ($song['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if ($song['status'] === 'checked' && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		if (!$song['has_vocal'])
			throw new HttpForbidden403();
		
		if (!Session::isCurrentUser($song['user_added_id']) && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		if ($translations && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleEditLyricsPageGet($album, $song);
				break;
			
			case 'POST':
				$this->handleEditLyricsPagePost($album, $song);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleEditLyricsPageGet(array $album, array $song): void
	{
		$performers   = $this->model->getPerformerList
		(
			fetchMinInfo: true,
			albumUri:     $album['uri'],
			songUri:      $song['uri']
		);
		$artists      = $this->model->getArtistList(fetchMinInfo: true);
		$characters   = $this->model->getCharacterList(fetchMinInfo: true);
		$languages    = $this->model->getLanguageList(orderBy: [$this->language.'_name ASC']);
		$originals    = $this->model->getSongList
		(
			fetchMinInfo: true,
			isOriginal:   true,
			excludeId:    $song['id'],
			hasVocal:     true,
			orderBy:      ['sn.transliterated_name ASC']
		);
		
		$this->view->renderEditLyricsPage
		(
			$album,
			$song,
			$performers,
			$artists,
			$characters,
			$originals,
			$languages
		);
	}
	
	private function handleEditLyricsPagePost(array $album, array $song): void
	{
		$allArtistIds        = $this->model->getArtistList(fetchMinInfo: true);
		$allArtistIds        = array_column($allArtistIds, 'id');
		$allCharacterIds     = $this->model->getCharacterList(fetchMinInfo: true);
		$allCharacterIds     = array_column($allCharacterIds, 'id');
		$allCharacterIds[]   = null;
		$allLanguages        = $this->model->getLanguageList();
		$allLanguages        = array_column($allLanguages, 'id');
		$allOriginalIds      = $this->model->getSongList
		(
			fetchMinInfo: true,
			isOriginal:   true,
			excludeId:    $song['id'],
			hasVocal:     true
		);
		$allOriginalIds      = array_column($allOriginalIds, 'id');
		$allOriginalIds[]    = null;
		
		$artistIds           = $_POST['artist-ids']       ?? [];
		$characterIds        = $_POST['character-ids']    ?? [];
		$originalSongId      = $_POST['original-song-id'] ?? null;
		$languageId          = $_POST['language-id']      ?? null;
		$lyrics              = $_POST['lyrics']           ?? null;
		$notes               = $_POST['notes']            ?? null;
		$userUpdatedId       = $_SESSION['user']['id'];
		
		$artistIds           = Parsing::parseNullableIntegerArray($artistIds, 1);
		$characterIds        = Parsing::parseNullableIntegerArray($characterIds, 1);
		$originalSongId      = Parsing::parseNullableInteger($originalSongId, 1);
		$languageId          = Parsing::parseNullableInteger($languageId, 1);
		$lyrics              = Parsing::trimNullableText($lyrics);
		$notes               = Parsing::trimNullableText($notes);
		
		// Empty should be reduced to null
		$lyrics              = Parsing::trimNullableString($lyrics);
		$notes               = Parsing::trimNullableString($notes);
		
		if (count($artistIds) === 0)
			throw new HttpBadRequest400('Artists were not provided', get_defined_vars());
		
		if (count($artistIds) !== count($characterIds))
			throw new HttpBadRequest400('Artist count was not equal to character count', get_defined_vars());
		
		if (array_diff($artistIds, $allArtistIds))
			throw new HttpBadRequest400('One of artistIds was invalid', get_defined_vars());
		
		if (array_diff($characterIds, $allCharacterIds))
			throw new HttpBadRequest400('One of characterIds was invalid', get_defined_vars());
		
		if ($originalSongId && ($languageId || $lyrics || $notes))
			throw new HttpBadRequest400('originalSongId was set along with language, lyrics, notes', get_defined_vars());
		
		if (!$originalSongId && (!$languageId || !$lyrics))
			throw new HttpBadRequest400('None of originalSongId, languageId, lyrics was set', get_defined_vars());
		
		if ($originalSongId && !in_array($originalSongId, $allOriginalIds))
			throw new HttpBadRequest400('originalSongId was invalid', get_defined_vars());
		
		if ($languageId && !in_array($languageId, $allLanguages))
			throw new HttpBadRequest400('languageId was invalid', get_defined_vars());
		
		$this->model->updateLyrics
		(
			$album['uri'],
			$song['uri'],
			$originalSongId,
			$languageId,
			$lyrics,
			$notes,
			$userUpdatedId
		);
		
		$this->model->deleteSongArtistCharacterRelation(songId: $song['id']);
		
		foreach (array_combine($artistIds, $characterIds) as $artistId => $characterId)
			$this->model->addSongArtistCharacterRelation($song['id'], $artistId, $characterId);
		
		$this->handleRedirect(Session::buildInternalLink($this->language, 'album', $album['uri'], 'song', $song['uri']));
	}
	
	final public function handleEditTranslationPage(string $albumUri, string $songUri, string $translationUri): void
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
		
		if (!$song['has_vocal'] || !$song['lyrics'] || $song['original_song_id'])
			throw new HttpForbidden403();
		
		if (!$translation)
			throw new HttpNotFound404();
		
		if ($translation['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if (!Session::isCurrentUser($translation['user_added_id']) && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleEditTranslationPageGet($album, $song, $translation);
				break;
			
			case 'POST':
				$this->handleEditTranslationPagePost($album, $song, $translation);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleEditTranslationPageGet(array $album, array $song, array $translation): void
	{
		$this->view->renderEditTranslationPage($album, $song, $translation);
	}
	
	private function handleEditTranslationPagePost(array $album, array $song, array $translation): void
	{
		$name          = $_POST['translation-name']   ?? null;
		$lyrics        = $_POST['translation-lyrics'] ?? null;
		$notes         = $_POST['translation-notes']  ?? null;
		$userUpdatedId = $_SESSION['user']['id'];
		
		$name          = Parsing::trimNullableString($name);
		$lyrics        = Parsing::trimNullableText($lyrics);
		$notes         = Parsing::trimNullableText($notes);
		
		// Empty should be reduced to null
		$lyrics        = Parsing::trimNullableString($lyrics);
		$notes         = Parsing::trimNullableString($notes);
		
		if (Validation::haveNullOrEmpty($name, $lyrics))
			throw new HttpBadRequest400('At least one of not-null values was null/empty', get_defined_vars());
		
		$this->model->updateTranslation
		(
			$album['uri'],
			$song['uri'],
			$translationUri['uri'],
			$name,
			$lyrics,
			$notes,
			$userUpdatedId
		);
		
		$link = Session::buildInternalLink($this->language, 'album', $album['uri'], 'song', $song['uri'], 'translation', $translation['uri']);
		$this->handleRedirect($link);
	}
	
	final public function handleDeleteGamePage(string $gameUri): void
	{
		$game = $this->model->getGame($gameUri);
		
		if (!$game)
			throw new HttpNotFound404();
		
		if (!Session::isCurrentUser($game['user_added_id']) && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		if ($game['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if ($game['status'] === 'checked' && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleDeleteGamePageGet($game);
				break;
			
			case 'POST':
				$this->handleDeleteGamePagePost($game);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleDeleteGamePageGet(array $game): void
	{
		$this->view->renderDeleteGamePage($game);
	}
	
	private function handleDeleteGamePagePost(array $game): void
	{
		$requestConfirmed = $_POST['confirmation'] ?? null;
		
		if (!$requestConfirmed)
			throw new HttpBadRequest400('Request was not confirmed', get_defined_vars());
		
		$this->model->deleteGame($game);
		
		$this->handleRedirect(Session::buildInternalLink($this->language, 'game-list'));
	}
	
	final public function handleDeleteAlbumPage(string $albumUri): void
	{
		$album = $this->model->getAlbum($albumUri);
		
		if (!$album)
			throw new HttpNotFound404();
		
		if (!Session::isCurrentUser($album['user_added_id']) && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		if ($album['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if ($album['status'] === 'checked' && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleDeleteAlbumPageGet($album);
				break;
			
			case 'POST':
				$this->handleDeleteAlbumPagePost($album);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleDeleteAlbumPageGet(array $album): void
	{
		$this->view->renderDeleteAlbumPage($album);
	}
	
	private function handleDeleteAlbumPagePost(array $album): void
	{
		$requestConfirmed = $_POST['confirmation'] ?? null;
		
		if (!$requestConfirmed)
			throw new HttpBadRequest400('Request was not confirmed', get_defined_vars());
		
		$this->model->deleteAlbum($album);
		
		$this->handleRedirect(Session::buildInternalLink($this->language, 'album-list'));
	}
	
	final public function handleDeleteArtistPage(string $artistUri): void
	{
		$artist = $this->model->getArtist($artistUri);
		
		if (!$artist)
			throw new HttpNotFound404();
		
		if (!Session::isCurrentUser($artist['user_added_id']) && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		if ($artist['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if ($artist['status'] === 'checked' && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleDeleteArtistPageGet($artist);
				break;
			
			case 'POST':
				$this->handleDeleteArtistPagePost($artist);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleDeleteArtistPageGet(array $artist): void
	{
		$this->view->renderDeleteArtistPage($artist);
	}
	
	private function handleDeleteArtistPagePost(array $artist): void
	{
		$requestConfirmed = $_POST['confirmation'] ?? null;
		
		if (!$requestConfirmed)
			throw new HttpBadRequest400('Request was not confirmed', get_defined_vars());
		
		$this->model->deleteArtist($artist);
		
		$this->handleRedirect(Session::buildInternalLink($this->language, 'artist-list'));
	}
	
	final public function handleDeleteCharacterPage(string $characterUri): void
	{
		$character = $this->model->getCharacter($characterUri);
		
		if (!$character)
			throw new HttpNotFound404();
		
		if (!Session::isCurrentUser($character['user_added_id']) && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		if ($character['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if ($character['status'] === 'checked' && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleDeleteCharacterPageGet($character);
				break;
			
			case 'POST':
				$this->handleDeleteCharacterPagePost($character);
				break;
			
			default:
				$this->handleMethodNotAllowed();
				break;
		}
	}
	
	private function handleDeleteCharacterPageGet(array $character): void
	{
		$this->view->renderDeleteCharacterPage($character);
	}
	
	private function handleDeleteCharacterPagePost(array $character): void
	{
		$requestConfirmed = $_POST['confirmation'] ?? null;
		
		if (!$requestConfirmed)
			throw new HttpBadRequest400('Request was not confirmed', get_defined_vars());
		
		$this->model->deleteCharacter($character);
		
		$this->handleRedirect(Session::buildInternalLink($this->language, 'character-list'));
	}
	
	final public function handleDeleteLyricsPage(string $albumUri, string $songUri): void
	{
		$album        = $this->model->getAlbum($albumUri);
		$song         = $this->model->getSong($albumUri, $songUri);
		$translations = $this->model->getTranslationList
		(
			fetchMinInfo: true,
			albumUri:     $albumUri,
			songUri:      $songUri
		);
		
		if (!$album)
			throw new HttpNotFound404();
		
		if ($album['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if (!$song)
			throw new HttpNotFound404();
		
		if (!Session::isCurrentUser($song['user_added_id']) && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		if ($song['status'] === 'hidden' && !Session::isCurrentUserModerator())
			throw new HttpUnavailableForLegalReasons451();
		
		if (!$song['has_vocal'] || !$song['has_lyrics'])
			throw new HttpForbidden403();
		
		if ($song['status'] === 'checked' && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		if ($translations)
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleDeleteLyricsPageGet($album, $song);
				break;
			
			case 'POST':
				$this->handleDeleteLyricsPagePost($album, $song);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleDeleteLyricsPageGet(array $album, array $song): void
	{
		$this->view->renderDeleteLyricsPage($album, $song);
	}
	
	private function handleDeleteLyricsPagePost(array $album, array $song): void
	{
		$requestConfirmed = $_POST['confirmation'] ?? null;
		
		if (!$requestConfirmed)
			throw new HttpBadRequest400('Request was not confirmed', get_defined_vars());
		
		$this->model->deleteLyrics($song);
		$this->model->deleteSongArtistCharacterRelation(songId: $song['id']);
		
		$this->handleRedirect(Session::buildInternalLink($this->language, 'album', $album['uri']));
	}
	
	final public function handleDeleteTranslationPage(string $albumUri, string $songUri, string $translationUri): void
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
		
		if (!$translation)
			throw new HttpNotFound404();
		
		if (!Session::isCurrentUser($translation['user_added_id']) && !Session::isCurrentUserModerator())
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleDeleteTranslationPageGet($album, $song, $translation);
				break;
			
			case 'POST':
				$this->handleDeleteTranslationPagePost($album, $song, $translation);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleDeleteTranslationPageGet(array $album, array $song, array $translation): void
	{
		$this->view->renderDeleteTranslationPage($album, $song, $translation);
	}
	
	private function handleDeleteTranslationPagePost(array $album, array $song, array $translation): void
	{
		$requestConfirmed = $_POST['confirmation'] ?? null;
		
		if (!$requestConfirmed)
			throw new HttpBadRequest400('Request was not confirmed', get_defined_vars());
		
		$this->model->deleteTranslation($translation);
		
		$this->handleRedirect(Session::buildInternalLink($this->language, 'album', $album['uri'], 'song', $song['uri']));
	}
	
	final public function handleChangeAccountDataPage(string $userUri): void
	{
		$user = $this->model->getUserData($_SESSION['user']['id']);
		
		if (!Session::isCurrentUser($user['user_id']) && !Session::isCurrentUserModerator())
			throw new HttpNotFound404();
		
		if (Session::isCurrentUserViolator())
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleChangeAccountDataPageGet($user);
				break;
			
			case 'POST':
				$this->handleChangeAccountDataPagePost($user);
				break;
			
			default:
				$this->handleMethodNotAllowed();
				break;
		}
	}
	
	private function handleChangeAccountDataPageGet(array $user, InputError $error = InputError::None): void
	{
		$this->view->renderChangeAccountDataPage($user);
	}
	
	private function handleChangeAccountDataPagePost(array $user): void
	{	
		$newUsername = $_POST['username']     ?? null;
		$oldPassword = $_POST['old-password'] ?? null;
		$newPassword = $_POST['new-password'] ?? null;
		$newEmail    = $_POST['email']        ?? null;
		
		if (Validation::haveNullOrEmpty($newUsername, $oldPassword, $newEmail))
			throw new HttpBadRequest400();
		
		if (!$this->model->isPasswordCorrect($user['id'], $oldPassword))
		{
			$this->handleChangeAccountDataPageGet($user, InputError::IncorrectPassword);
			return;
		}
		
		if (Parsing::trimNullableString($newUsername) !== $newUsername)
		{
			$this->handleChangeAccountDataPageGet($user, InputError::UsernameTrimmable);
			return;
		}
		
		if (!Validation::isLatinAlphabetAndNumbers($newUsername))
		{
			$this->handleChangeAccountDataPageGet($user, InputError::UsernameForbiddenSymbols);
			return;
		}
		
		if (mb_strlen($newUsername) < self::ACCOUNT_DATA_MIN_LENGTH)
		{
			$this->handleChangeAccountDataPageGet($user, InputError::UsernameLengthIncorrect);
			return;
		}
		
		if (mb_strlen($newUsername) > self::ACCOUNT_DATA_MAX_LENGTH)
		{
			$this->handleChangeAccountDataPageGet($user, InputError::UsernameLengthIncorrect);
			return;
		}
		
		if ($user['user_username'] !== $newUsername && $this->model->isUsernameRegistered($newUsername))
		{
			$this->handleChangeAccountDataPageGet($user, InputError::UsernameTaken);
			return;
		}
		
		if (!Validation::isEmailValid($newEmail))
		{
			$this->handleChangeAccountDataPageGet($user, InputError::EmailInvalid);
			return;
		}
		
		if ($user['user_email'] !== $newEmail && $this->model->isEmailRegistered($newEmail))
		{
			$this->handleChangeAccountDataPageGet($user, InputError::EmailTaken);
			return;
		}
		
		if ($newPassword && !Validation::isLatinAlphabetAndNumbers($newPassword))
		{
			$this->handleChangeAccountDataPageGet($user, InputError::PasswordForbiddenSymbols);
			return;
		}
		
		if ($newPassword && mb_strlen($newPassword) < self::ACCOUNT_DATA_MIN_LENGTH)
		{
			$this->handleChangeAccountDataPageGet($user, InputError::PasswordLengthIncorrect);
			return;
		}
		
		if ($newPassword && mb_strlen($newPassword) > self::ACCOUNT_DATA_MAX_LENGTH)
		{
			$this->handleChangeAccountDataPageGet($user, InputError::PasswordLengthIncorrect);
			return;
		}
		
		$this->model->updateUserData($user['user_id'], $newUsername, $newEmail, $newPassword);
		
		$this->model->getUserData($user['id']);
		$this->createUserSession($user);
		
		$this->handleRedirect(Session::buildInternalLink($this->language, 'user', $newUsername));
	}
	
	final public function handleDeleteAccountPage(string $userUri): void
	{
		$user = $this->model->getUserData(username: $userUri);
		
		if (!$user)
			throw new HttpNotFound404();
		
		if (!Session::isCurrentUser($user['user_id']) && !Session::isCurrentUserModerator())
			throw new HttpNotFound404();
		
		if (Session::isCurrentUserViolator())
			throw new HttpForbidden403();
		
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$this->handleDeleteAccountPageGet($user);
				break;
			
			case 'POST':
				$this->handleDeleteAccountPagePost($user);
				break;
			
			default:
				throw new HttpMethodNotAllowed405();
		}
	}
	
	private function handleDeleteAccountPageGet(array $user, InputError $error = InputError::None): void
	{
		$this->view->renderDeleteAccountPage($user, $error);
	}
	
	private function handleDeleteAccountPagePost(array $user): void
	{
		$password = $_POST['password'] ?? null;
		
		if (!$password)
			throw new HttpBadRequest400();
		
		if (!$this->model->isPasswordCorrect($user['id'], $password))
		{
			$this->handleDeleteAccountPageGet($user, InputError::IncorrectPassword);
			return;
		}
		
		$this->model->deleteUser($user);
		$this->handleLogOutPage();
	}
}
