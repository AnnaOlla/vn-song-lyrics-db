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
		if (empty($_POST))
		{
			$albums     = $this->model->getAlbumIdList();
			$characters = $this->model->getCharacterIdList();
			
			$this->view->renderAddGamePage($albums, $characters);
			return;
		}
		
		$allAlbumIds     = $this->model->getAlbumIdList();
		$allAlbumIds     = array_column($allAlbumIds, 'id');
		$allCharacterIds = $this->model->getCharacterIdList();
		$allCharacterIds = array_column($allCharacterIds, 'id');
		
		$originalName       = $_POST['original-name']       ?? null;
		$transliteratedName = $_POST['transliterated-name'] ?? null;
		$localizedName      = $_POST['localized-name']      ?? null;
		$logo               = $_FILES['logo']               ?? null;
		$vndbLink           = $_POST['vndb-link']           ?? null;
		$albumIds           = $_POST['album-ids']           ?? [];
		$characterIds       = $_POST['character-ids']       ?? [];
		$userAddedId        = $_SESSION['user']['id'];
		
		$originalName       = $this->trimNullableString($originalName);
		$transliteratedName = $this->trimNullableString($transliteratedName);
		$localizedName      = $this->trimNullableString($localizedName);
		$logo               = $this->getNullableFile($logo);
		$vndbId             = $this->parseNullableVndbId($vndbLink, 'v');
		$albumIds           = $this->parseNullableIntegerArray($albumIds, 1);
		$albumIds           = $this->removeNullValues($albumIds);
		$characterIds       = $this->parseNullableIntegerArray($characterIds, 1);
		$characterIds       = $this->removeNullValues($characterIds);
		
		if (haveNullOrEmpty($originalName, $transliteratedName))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (!$this->isPrintableAscii($transliteratedName))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (array_diff($albumIds, $allAlbumIds))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (array_diff($characterIds, $characterIds))
		{
			$this->handleBadRequest();
			return;
		}
		
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
		
		$link = buildInternalLink($this->language, 'game', $gameUri);
		$this->handleRedirect($link);
	}
	
	final public function handleAddAlbumPage(): void
	{
		if (empty($_POST))
		{
			$games = $this->model->getGameIdList();
			
			$this->view->renderAddAlbumPage($games);
			return;
		}
		
		$allGameIds         = $this->model->getGameIdList();
		$allGameIds         = array_column($allGameIds, 'id');
		
		$originalName       = $_POST['original-name']       ?? null;
		$transliteratedName = $_POST['transliterated-name'] ?? null;
		$localizedName      = $_POST['localized-name']      ?? null;
		$cover              = $_FILES['cover']              ?? null;
		$vgmdbLink          = $_POST['vgmdb-link']          ?? null;
		$songCount          = $_POST['song-count']          ?? null;
		$gameIds            = $_POST['game-ids']            ?? [];
		$userAddedId        = $_SESSION['user']['id'];
		
		$originalName       = $this->trimNullableString($originalName);
		$transliteratedName = $this->trimNullableString($transliteratedName);
		$localizedName      = $this->trimNullableString($localizedName);
		$cover              = $this->getNullableFile($cover);
		$vgmdbId            = $this->parseNullableVgmdbId($vgmdbLink, 'album');
		$songCount          = $this->parseNullableInteger($songCount, 1);
		$gameIds            = $this->parseNullableIntegerArray($gameIds, 1);
		$gameIds            = $this->removeNullValues($gameIds);
		
		if (haveNullOrEmpty($originalName, $transliteratedName, $songCount))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (!$this->isPrintableAscii($transliteratedName))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (array_diff($gameIds, $allGameIds))
		{
			$this->handleBadRequest();
			return;
		}
		
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
		
		$link = buildInternalLink($this->language, 'album', $albumUri);
		$this->handleRedirect($link);
	}
	
	final public function handleAddArtistPage(): void
	{
		if (empty($_POST))
		{
			$this->view->renderAddArtistPage();
			return;
		}
		
		$originalName       = $_POST['original-name']       ?? null;
		$transliteratedName = $_POST['transliterated-name'] ?? null;
		$localizedName      = $_POST['localized-name']      ?? null;
		$photo              = $_FILES['photo']              ?? null;
		$vgmdbLink          = $_POST['vgmdb-link']          ?? null;
		$userAddedId        = $_SESSION['user']['id'];
		
		$originalName       = $this->trimNullableString($originalName);
		$transliteratedName = $this->trimNullableString($transliteratedName);
		$localizedName      = $this->trimNullableString($localizedName);
		$photo              = $this->getNullableFile($photo);
		$vgmdbId            = $this->parseNullableVgmdbId($vgmdbLink, 'artist');
		
		if (haveNullOrEmpty($originalName, $transliteratedName))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (!$this->isPrintableAscii($transliteratedName))
		{
			$this->handleBadRequest();
			return;
		}
		
		[$artistId, $artistUri] = $this->model->addArtist
		(
			$originalName,
			$transliteratedName,
			$localizedName,
			$photo,
			$vgmdbId,
			$userAddedId
		);
		
		$link = buildInternalLink($this->language, 'artist', $artistUri);
		$this->handleRedirect($link);
	}
	
	final public function handleAddCharacterPage(): void
	{
		if (empty($_POST))
		{
			$games = $this->model->getGameIdList();
			
			$this->view->renderAddCharacterPage($games);
			return;
		}
		
		$allGameIds         = $this->model->getGameIdList();
		$allGameIds         = array_column($allGameIds, 'id');
		
		$originalName       = $_POST['original-name']       ?? null;
		$transliteratedName = $_POST['transliterated-name'] ?? null;
		$localizedName      = $_POST['localized-name']      ?? null;
		$image              = $_FILES['image']              ?? null;
		$vndbLink           = $_POST['vndb-link']           ?? null;
		$gameIds            = $_POST['game-ids']            ?? [];
		$userAddedId        = $_SESSION['user']['id'];
		
		$originalName       = $this->trimNullableString($originalName);
		$transliteratedName = $this->trimNullableString($transliteratedName);
		$localizedName      = $this->trimNullableString($localizedName);
		$image              = $this->getNullableFile($image);
		$vndbId             = $this->parseNullableVndbId($vndbLink, 'c');
		$gameIds            = $this->parseNullableIntegerArray($gameIds, 1);
		$gameIds            = $this->removeNullValues($gameIds);
		
		if (haveNullOrEmpty($originalName, $transliteratedName))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (!$this->isPrintableAscii($transliteratedName))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (array_diff($gameIds, $allGameIds))
		{
			$this->handleBadRequest();
			return;
		}
		
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
		
		$link = buildInternalLink($this->language, 'character', $characterUri);
		$this->handleRedirect($link);
	}
	
	final public function handleAddSongPage(string $albumUri): void
	{
		if (empty($_POST))
		{
			$album        = $this->model->getAlbumId($albumUri);
			
			if (!$album)
			{
				$this->handleNotFound();
				return;
			}
			
			$lastSongInfo = $this->model->getLastDiscAndTrack($albumUri);
			
			$currentDisc  = $lastSongInfo ? $lastSongInfo['disc_number']      : 1;
			$currentTrack = $lastSongInfo ? $lastSongInfo['track_number'] + 1 : 1;
			
			$songCount    = $this->model->getSongCurrentCount($albumUri);
			$isLastSong   = ($album['song_count'] - $songCount === 1) ? true : false;
			
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
			
			if ($songCount >= $album['song_count'])
			{
				$this->handleForbidden();
				return;
			}
			
			$this->view->renderAddSongPage($album, $currentDisc, $currentTrack, $isLastSong);
			return;
		}
		
		$album              = $this->model->getAlbumId($albumUri);
		$currentSongCount   = $this->model->getSongCurrentCount($albumUri);
		$lastSongInfo       = $this->model->getLastDiscAndTrack($albumUri);
		
		$discNumber         = $_POST['disc-number']         ?? null;
		$trackNumber        = $_POST['track-number']        ?? null;
		$originalName       = $_POST['original-name']       ?? null;
		$transliteratedName = $_POST['transliterated-name'] ?? null;
		$localizedName      = $_POST['localized-name']      ?? null;
		$hasVocal           = $_POST['has-vocal']           ?? null;
		$userAddedId        = $_SESSION['user']['id'];
		
		$discNumber         = $this->parseNullableInteger($discNumber, 1);
		$trackNumber        = $this->parseNullableInteger($trackNumber, 1);
		$originalName       = $this->trimNullableString($originalName);
		$transliteratedName = $this->trimNullableString($transliteratedName);
		$localizedName      = $this->trimNullableString($localizedName);
		$hasVocal           = $this->parseNullableInteger($hasVocal, 0, 1);
		
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
		
		if ($currentSongCount >= $album['song_count'])
		{
			$this->handleForbidden();
			return;
		}
		
		if (haveNullOrEmpty($discNumber, $trackNumber, $originalName, $transliteratedName, $hasVocal))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (!$this->isPrintableAscii($transliteratedName))
		{
			$this->handleBadRequest();
			return;
		}
		
		if ($lastSongInfo)
		{
			$isSameDiscNextTrack =
			(
				$discNumber === $lastSongInfo['disc_number']
				&&
				$trackNumber === $lastSongInfo['track_number'] + 1
			);
			
			$isNextDiscFirstTrack =
			(
				$discNumber === $lastSongInfo['disc_number'] + 1
				&&
				$trackNumber === 1
			);
			
			if (!$isSameDiscNextTrack && !$isNextDiscFirstTrack)
			{
				$this->handleBadRequest();
				return;
			}
		}
		else
		{
			$isFirstDiscFirstTrack = ($discNumber === 1 && $trackNumber === 1);
			
			if (!$isFirstDiscFirstTrack)
			{
				$this->handleBadRequest();
				return;
			}
		}
		
		$this->model->addSong
		(
			$albumUri,
			$originalName,
			$transliteratedName,
			$localizedName,
			$discNumber,
			$trackNumber,
			$hasVocal,
			$userAddedId
		);
		
		$currentSongCount++;
		
		if ($currentSongCount === $album['song_count'])
			$this->handleRedirect(buildInternalLink($this->language, 'album', $albumUri));
		else
			$this->handleRedirect(buildInternalLink($this->language, 'album', $albumUri, 'add-song'));
	}
	
	final public function handleAddLyricsPage(string $albumUri, string $songUri): void
	{
		if (empty($_POST))
		{
			$album      = $this->model->getAlbumId($albumUri);
			$song       = $this->model->getSong($albumUri, $songUri);
			
			$artists    = $this->model->getArtistIdList();
			$characters = $this->model->getCharacterIdList();
			$languages  = $this->model->getLanguageList();
			$originals  = $this->model->getSongIdList(isOriginal: true, hasVocal: true, excludeId: $song['id']);
			
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
			
			if ($song['status'] === 'checked' && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
			if (!$song['has_vocal'] || $song['lyrics'] || $song['original_song_id'])
			{
				$this->handleForbidden();
				return;
			}
			
			$this->view->renderAddLyricsPage
			(
				$album,
				$song,
				$artists,
				$characters,
				$originals,
				$languages
			);
			return;
		}
		
		$album             = $this->model->getAlbumId($albumUri);
		$song              = $this->model->getSongId($albumUri, $songUri);
		
		$allArtistIds      = $this->model->getArtistIdList();
		$allArtistIds      = array_column($allArtistIds, 'id');
		$allCharacterIds   = $this->model->getCharacterIdList();
		$allCharacterIds   = array_column($allCharacterIds, 'id');
		$allCharacterIds[] = null;
		$allLanguages      = $this->model->getLanguageList();
		$allLanguages      = array_column($allLanguages, 'id');
		$allOriginalIds    = $this->model->getSongIdList(isOriginal: true, hasVocal: true, excludeId: $song['id']);
		$allOriginalIds    = array_column($allOriginalIds, 'id');
		$allOriginalIds[]  = null;
		
		$artistIds         = $_POST['artist-ids']       ?? [];
		$characterIds      = $_POST['character-ids']    ?? [];
		$originalSongId    = $_POST['original-song-id'] ?? null;
		$languageId        = $_POST['language-id']      ?? null;
		$lyrics            = $_POST['lyrics']           ?? null;
		$notes             = $_POST['notes']            ?? null;
		$userAddedId       = $_SESSION['user']['id'];
		
		$artistIds         = $this->parseNullableIntegerArray($artistIds, 1);
		$characterIds      = $this->parseNullableIntegerArray($characterIds, 1);
		$originalSongId    = $this->parseNullableInteger($originalSongId, 1);
		$languageId        = $this->parseNullableInteger($languageId, 1);
		$lyrics            = $this->trimNullableText($lyrics);
		$notes             = $this->trimNullableText($notes);
		
		// $this->trimNullableText does not remove empty lines (because they may be part of lyrics)
		// The problem is what if empty lines was the only content?
		$lyrics            = $this->trimNullableString($lyrics);
		$notes             = $this->trimNullableString($notes);
		
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
		
		if ($song['status'] === 'checked' && !isCurrentUserModerator())
		{
			$this->handleForbidden();
			return;
		}
		
		if (!$song['has_vocal'] || $song['has_lyrics'] || $song['original_song_id'])
		{
			$this->handleForbidden();
			return;
		}
		
		if (count($artistIds) === 0)
		{
			$this->handleBadRequest();
			return;
		}
		
		if (count($artistIds) !== count($characterIds))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (array_diff($artistIds, $allArtistIds))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (array_diff($characterIds, $allCharacterIds))
		{
			$this->handleBadRequest();
			return;
		}
		
		if ($originalSongId && ($languageId || $lyrics || $notes))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (!$originalSongId && (!$languageId || !$lyrics))
		{
			$this->handleBadRequest();
			return;
		}
		
		if ($originalSongId && !in_array($originalSongId, $allOriginalIds))
		{
			$this->handleBadRequest();
			return;
		}
		
		if ($languageId && !in_array($languageId, $allLanguages))
		{
			$this->handleBadRequest();
			return;
		}
		
		$this->model->addLyrics
		(
			$albumUri,
			$songUri,
			$originalSongId,
			$languageId,
			$lyrics,
			$notes,
			$userAddedId
		);
		
		foreach (array_combine($artistIds, $characterIds) as $artistId => $characterId)
			$this->model->addSongArtistCharacterRelation($song['id'], $artistId, $characterId);
		
		$link = buildInternalLink($this->language, 'album', $albumUri, 'song', $songUri);
		$this->handleRedirect($link);
	}
	
	final public function handleAddTranslationPage(string $albumUri, string $songUri): void
	{
		if (empty($_POST))
		{
			$album              = $this->model->getAlbumId($albumUri);
			$song               = $this->model->getSong($albumUri, $songUri);
			$languages          = $this->model->getLanguageList();
			$translationsByUser = $this->model->getTranslationIdList
			(
				albumUri:    $albumUri,
				songUri:     $songUri,
				userAddedId: $_SESSION['user']['id']
			);
			
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
			
			if (!$song['has_vocal'] || !$song['lyrics'] || $song['original_song_id'])
			{
				$this->handleForbidden();
				return;
			}
			
			$this->view->renderAddTranslationPage($album, $song, $languages, $translationsByUser);
			return;
		}
		
		$album                = $this->model->getAlbumId($albumUri);
		$song                 = $this->model->getSongId($albumUri, $songUri);
		$translationsByUser   = $this->model->getTranslationIdList
		(
			albumUri:    $albumUri,
			songUri:     $songUri,
			userAddedId: $_SESSION['user']['id']
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
		
		$languageId           = $this->parseNullableInteger($languageId, 1);
		$name                 = $this->trimNullableString($name);
		$lyrics               = $this->trimNullableText($lyrics);
		$notes                = $this->trimNullableText($notes);
		
		// $this->trimNullableText does not remove empty lines (because they may be part of lyrics)
		// The problem is what if empty lines was the only content?
		$lyrics               = $this->trimNullableString($lyrics);
		$notes                = $this->trimNullableString($notes);
		
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
		
		if (!$song['has_vocal'] || !$song['has_lyrics'] || $song['original_song_id'])
		{
			$this->handleForbidden();
			return;
		}
		
		if (haveNullOrEmpty($name, $lyrics, $languageId))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (!in_array($languageId, $allLanguages))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (in_array($languageId, $forbiddenLanguages))
		{
			$this->handleBadRequest();
			return;
		}
		
		[$translationId, $translationUri] = $this->model->addTranslation
		(
			$albumUri,
			$songUri,
			$name,
			$languageId,
			$lyrics,
			$notes,
			$userAddedId
		);
		
		$link = buildInternalLink($this->language, 'album', $albumUri, 'song', $songUri, 'translation', $translationUri);
		$this->handleRedirect($link);
	}
	
	final public function handleFillAlbumPage(string $albumUri): void
	{
		if (empty($_POST))
		{
			$album            = $this->model->getAlbumId($albumUri);
			$currentSongCount = $this->model->getSongCurrentCount($albumUri);
			
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
			
			// Fetching and processing a web page is not a cheap operation at all
			// This is the only case when I moved getting a value away from other variables
			$discography = $this->model->fetchDataFromVgmdbPage($albumUri);
			
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
	
	final public function handleEditGamePage(string $gameUri): void
	{
		if (empty($_POST))
		{
			$game                 = $this->model->getGame($gameUri);
			$relatedAlbumList     = $this->model->getAlbumIdList(gameUri: $gameUri);
			$relatedCharacterList = $this->model->getCharacterIdList(gameUri: $gameUri);
			$fullAlbumList        = $this->model->getAlbumIdList();
			$fullCharacterList    = $this->model->getCharacterIdList();
			
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
			
			if ($game['status'] === 'checked' && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
			$this->view->renderEditGamePage
			(
				$game,
				$relatedAlbumList,
				$relatedCharacterList,
				$fullAlbumList,
				$fullCharacterList
			);
			return;
		}
		
		$game                = $this->model->getGameId($gameUri);
		$allAlbumIds         = $this->model->getAlbumIdList();
		$allAlbumIds         = array_column($allAlbumIds, 'id');
		$allCharacterIds     = $this->model->getCharacterIdList();
		$allCharacterIds     = array_column($allCharacterIds, 'id');
		
		$originalName        = $_POST['original-name']       ?? null;
		$transliteratedName  = $_POST['transliterated-name'] ?? null;
		$localizedName       = $_POST['localized-name']      ?? null;
		$logo                = $_FILES['logo']               ?? null;
		$vndbLink            = $_POST['vndb-link']           ?? null;
		$albumIds            = $_POST['album-ids']           ?? [];
		$characterIds        = $_POST['character-ids']       ?? [];
		$userUpdatedId       = $_SESSION['user']['id'];
		
		$originalName        = $this->trimNullableString($originalName);
		$transliteratedName  = $this->trimNullableString($transliteratedName);
		$localizedName       = $this->trimNullableString($localizedName);
		$logo                = $this->getNullableFile($logo);
		$vndbId              = $this->parseNullableVndbId($vndbLink, 'v');
		$albumIds            = $this->parseNullableIntegerArray($albumIds, 1);
		$albumIds            = $this->removeNullValues($albumIds);
		$characterIds        = $this->parseNullableIntegerArray($characterIds, 1);
		$characterIds        = $this->removeNullValues($characterIds);
		
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
		
		if ($game['status'] === 'checked' && !isCurrentUserModerator())
		{
			$this->handleForbidden();
			return;
		}
		
		if (haveNullOrEmpty($originalName, $transliteratedName))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (!$this->isPrintableAscii($transliteratedName))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (array_diff($albumIds, $allAlbumIds))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (array_diff($characterIds, $characterIds))
		{
			$this->handleBadRequest();
			return;
		}
		
		[$gameId, $gameUri] = $this->model->updateGame
		(
			$gameUri,
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
		
		$link = buildInternalLink($this->language, 'game', $gameUri);
		$this->handleRedirect($link);
	}
	
	final public function handleEditAlbumPage(string $albumUri): void
	{
		if (empty($_POST))
		{
			$album            = $this->model->getAlbum($albumUri);
			$relatedGameList  = $this->model->getGameIdList(albumUri: $albumUri);
			$relatedSongList  = []; // now editing songs is done on a separate page
			$currentSongCount = $this->model->getSongCurrentCount($albumUri);
			$fullGameList     = $this->model->getGameIdList();
			
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
			
			$this->view->renderEditAlbumPage
			(
				$album,
				$relatedGameList,
				$relatedSongList,
				$currentSongCount,
				$fullGameList
			);
			return;
		}
		
		$album               = $this->model->getAlbumId($albumUri);
		$allGameIds          = $this->model->getGameIdList();
		$allGameIds          = array_column($allGameIds, 'id');
		$currentSongCount    = $this->model->getSongCurrentCount($albumUri);
		
		$originalName        = $_POST['original-name']       ?? null;
		$transliteratedName  = $_POST['transliterated-name'] ?? null;
		$localizedName       = $_POST['localized-name']      ?? null;
		$cover               = $_FILES['cover']              ?? null;
		$vgmdbLink           = $_POST['vgmdb-link']          ?? null;
		$songCount           = $_POST['song-count']          ?? null;
		$gameIds             = $_POST['game-ids']            ?? [];
		$userUpdatedId       = $_SESSION['user']['id'];
		
		$originalName        = $this->trimNullableString($originalName);
		$transliteratedName  = $this->trimNullableString($transliteratedName);
		$localizedName       = $this->trimNullableString($localizedName);
		$cover               = $this->getNullableFile($cover);
		$vgmdbId             = $this->parseNullableVgmdbId($vgmdbLink, 'album');
		$songCount           = $this->parseNullableInteger($songCount, 1);
		$gameIds             = $this->parseNullableIntegerArray($gameIds, 1);
		$gameIds             = $this->removeNullValues($gameIds);
		
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
		
		if (haveNullOrEmpty($originalName, $transliteratedName, $songCount))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (!$this->isPrintableAscii($transliteratedName))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (array_diff($gameIds, $allGameIds))
		{
			$this->handleBadRequest();
			return;
		}
		
		if ($songCount < $currentSongCount)
		{
			$this->handleForbidden();
			return;
		}
		
		[$albumId, $albumUri] = $this->model->updateAlbum
		(
			$albumUri,
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
		
		$this->handleRedirect(buildInternalLink($this->language, 'album', $albumUri));
	}
	
	final public function handleEditArtistPage(string $artistUri): void
	{
		if (empty($_POST))
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
			
			if ($artist['status'] === 'checked' && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
			$this->view->renderEditArtistPage($artist);
			return;
		}
		
		$artist              = $this->model->getArtistId($artistUri);
		
		$originalName        = $_POST['original-name']       ?? null;
		$transliteratedName  = $_POST['transliterated-name'] ?? null;
		$localizedName       = $_POST['localized-name']      ?? null;
		$photo               = $_FILES['photo']              ?? null;
		$vgmdbLink           = $_POST['vgmdb-link']          ?? null;
		$userUpdatedId       = $_SESSION['user']['id'];
		
		$originalName        = $this->trimNullableString($originalName);
		$transliteratedName  = $this->trimNullableString($transliteratedName);
		$localizedName       = $this->trimNullableString($localizedName);
		$photo               = $this->getNullableFile($photo);
		$vgmdbId             = $this->parseNullableVgmdbId($vgmdbLink, 'artist');
		
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
		
		if ($artist['status'] === 'checked' && !isCurrentUserModerator())
		{
			$this->handleForbidden();
			return;
		}
		
		if (haveNullOrEmpty($originalName, $transliteratedName))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (!$this->isPrintableAscii($transliteratedName))
		{
			$this->handleBadRequest();
			return;
		}
		
		[$artistId, $artistUri] = $this->model->updateArtist
		(
			$artistUri,
			$originalName,
			$transliteratedName,
			$localizedName,
			$photo,
			$vgmdbId,
			$userUpdatedId
		);
		
		$this->handleRedirect(buildInternalLink($this->language, 'artist', $artistUri));
	}
	
	final public function handleEditCharacterPage(string $characterUri): void
	{
		if (empty($_POST))
		{
			$character        = $this->model->getCharacter($characterUri);
			$relatedGamesList = $this->model->getGameIdList(characterUri: $characterUri);
			$fullGameList     = $this->model->getGameIdList();
			
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
			
			if ($character['status'] === 'checked' && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
			$this->view->renderEditCharacterPage($character, $relatedGamesList, $fullGameList);
			return;
		}
		
		$character           = $this->model->getCharacterId($characterUri);
		$allGameIds          = $this->model->getGameIdList();
		$allGameIds          = array_column($allGameIds, 'id');
		
		$originalName        = $_POST['original-name']       ?? null;
		$transliteratedName  = $_POST['transliterated-name'] ?? null;
		$localizedName       = $_POST['localized-name']      ?? null;
		$image               = $_FILES['image']              ?? null;
		$vndbLink            = $_POST['vndb-link']           ?? null;
		$gameIds             = $_POST['game-ids']            ?? [];
		$userUpdatedId       = $_SESSION['user']['id'];
		
		$originalName        = $this->trimNullableString($originalName);
		$transliteratedName  = $this->trimNullableString($transliteratedName);
		$localizedName       = $this->trimNullableString($localizedName);
		$image               = $this->getNullableFile($image);
		$vndbId              = $this->parseNullableVndbId($vndbLink, 'c');
		$gameIds             = $this->parseNullableIntegerArray($gameIds, 1);
		$gameIds             = $this->removeNullValues($gameIds);
		
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
		
		if ($character['status'] === 'checked' && !isCurrentUserModerator())
		{
			$this->handleForbidden();
			return;
		}
			
		if (haveNullOrEmpty($originalName, $transliteratedName, ...$gameIds))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (!$this->isPrintableAscii($transliteratedName))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (array_diff($gameIds, $allGameIds))
		{
			$this->handleBadRequest();
			return;
		}
		
		[$characterId, $characterUri] = $this->model->updateCharacter
		(
			$characterUri,
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
		
		$this->handleRedirect(buildInternalLink($this->language, 'character', $characterUri));
	}
	
	final public function handleEditSongPage(string $albumUri, string $songUri): void
	{
		if (empty($_POST))
		{
			$album = $this->model->getAlbumId($albumUri);
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
			
			if (!isCurrentUser($album['user_added_id']) && !isCurrentUserModerator())
			{
				$this->handleForbidden();
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
			
			$this->view->renderEditSongPage($album, $song);
			return;
		}
		
		$album              = $this->model->getAlbumId($albumUri);
		$song               = $this->model->getSongId($albumUri, $songUri);
		
		$originalName       = $_POST['original-name']       ?? null;
		$transliteratedName = $_POST['transliterated-name'] ?? null;
		$localizedName      = $_POST['localized-name']      ?? null;
		$hasVocal           = $_POST['has-vocal']           ?? null;
		$userUpdatedId      = $_SESSION['user']['id'];
		
		$originalName       = $this->trimNullableString($originalName);
		$transliteratedName = $this->trimNullableString($transliteratedName);
		$localizedName      = $this->trimNullableString($localizedName);
		$hasVocal           = $this->parseNullableInteger($hasVocal, 0, 1);
		
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
		
		if (!isCurrentUser($album['user_added_id']) && !isCurrentUserModerator())
		{
			$this->handleForbidden();
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
		
		if (haveNullOrEmpty($originalName, $transliteratedName, $hasVocal))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (!$this->isPrintableAscii($transliteratedName))
		{
			$this->handleBadRequest();
			return;
		}
		
		$this->model->updateSong
		(
			$albumUri,
			$songUri,
			$originalName,
			$transliteratedName,
			$localizedName,
			$hasVocal,
			$userUpdatedId
		);
		
		$this->handleRedirect(buildInternalLink($this->language, 'album', $albumUri));
	}
	
	final public function handleEditLyricsPage(string $albumUri, string $songUri): void
	{
		if (empty($_POST))
		{
			$album        = $this->model->getAlbumId($albumUri);
			$song         = $this->model->getSong($albumUri, $songUri);
			$translations = $this->model->getTranslationIdList($albumUri, $songUri);
			$performers   = $this->model->getPerformerIdList($albumUri, $songUri);
			$artists      = $this->model->getArtistIdList();
			$characters   = $this->model->getCharacterIdList();
			$languages    = $this->model->getLanguageList();
			$originals    = $this->model->getSongIdList(isOriginal: true, excludeId: $song['id'], hasVocal: true);
			
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
			
			if ($song['status'] === 'checked' && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
			if (!$song['has_vocal'])
			{
				$this->handleForbidden();
				return;
			}
			
			if (!isCurrentUser($song['user_added_id']) && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
			if ($translations && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
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
			return;
		}
		
		$album               = $this->model->getAlbumId($albumUri);
		$song                = $this->model->getSongId($albumUri, $songUri);
		$translations        = $this->model->getTranslationIdList($albumUri, $songUri);
		
		$allArtistIds        = $this->model->getArtistIdList();
		$allArtistIds        = array_column($allArtistIds, 'id');
		$allCharacterIds     = $this->model->getCharacterIdList();
		$allCharacterIds     = array_column($allCharacterIds, 'id');
		$allCharacterIds[]   = null;
		$allLanguages        = $this->model->getLanguageList();
		$allLanguages        = array_column($allLanguages, 'id');
		$allOriginalIds      = $this->model->getSongIdList(isOriginal: true, hasVocal: true, excludeId: $song['id']);
		$allOriginalIds      = array_column($allOriginalIds, 'id');
		$allOriginalIds[]    = null;
		
		$artistIds           = $_POST['artist-ids']       ?? [];
		$characterIds        = $_POST['character-ids']    ?? [];
		$originalSongId      = $_POST['original-song-id'] ?? null;
		$languageId          = $_POST['language-id']      ?? null;
		$lyrics              = $_POST['lyrics']           ?? null;
		$notes               = $_POST['notes']            ?? null;
		$userUpdatedId       = $_SESSION['user']['id'];
		
		$artistIds           = $this->parseNullableIntegerArray($artistIds, 1);
		$characterIds        = $this->parseNullableIntegerArray($characterIds, 1);
		$originalSongId      = $this->parseNullableInteger($originalSongId, 1);
		$languageId          = $this->parseNullableInteger($languageId, 1);
		$lyrics              = $this->trimNullableText($lyrics);
		$notes               = $this->trimNullableText($notes);
		
		// $this->trimNullableText does not remove empty lines (because they may be part of lyrics)
		// The problem is what if empty lines was the only content?
		$lyrics              = $this->trimNullableString($lyrics);
		$notes               = $this->trimNullableString($notes);
		
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
		
		if ($song['status'] === 'checked' && !isCurrentUserModerator())
		{
			$this->handleForbidden();
			return;
		}
		
		if (!$song['has_vocal'])
		{
			$this->handleForbidden();
			return;
		}
		
		if (!isCurrentUser($song['user_added_id']) && !isCurrentUserModerator())
		{
			$this->handleForbidden();
			return;
		}
		
		if ($translations && !isCurrentUserModerator())
		{
			$this->handleForbidden();
			return;
		}
		
		if (count($artistIds) === 0)
		{
			$this->handleBadRequest();
			return;
		}
		
		if (count($artistIds) !== count($characterIds))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (array_diff($artistIds, $allArtistIds))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (array_diff($characterIds, $allCharacterIds))
		{
			$this->handleBadRequest();
			return;
		}
		
		if ($originalSongId && ($languageId || $lyrics || $notes))
		{
			$this->handleBadRequest();
			return;
		}
		
		if (!$originalSongId && (!$languageId || !$lyrics))
		{
			$this->handleBadRequest();
			return;
		}
		
		if ($originalSongId && !in_array($originalSongId, $allOriginalIds))
		{
			$this->handleBadRequest();
			return;
		}
		
		if ($languageId && !in_array($languageId, $allLanguages))
		{
			$this->handleBadRequest();
			return;
		}
		
		$this->model->updateLyrics
		(
			$albumUri,
			$songUri,
			$originalSongId,
			$languageId,
			$lyrics,
			$notes,
			$userUpdatedId
		);
		
		$this->model->deleteSongArtistCharacterRelation(songId: $song['id']);
		
		foreach (array_combine($artistIds, $characterIds) as $artistId => $characterId)
			$this->model->addSongArtistCharacterRelation($song['id'], $artistId, $characterId);
		
		$this->handleRedirect(buildInternalLink($this->language, 'album', $albumUri, 'song', $songUri));
	}
	
	final public function handleEditTranslationPage(string $albumUri, string $songUri, string $translationUri): void
	{
		if (empty($_POST))
		{
			$album       = $this->model->getAlbumId($albumUri);
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
			
			if (!$song['has_vocal'] || !$song['lyrics'] || $song['original_song_id'])
			{
				$this->handleForbidden();
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
			
			if (!isCurrentUser($translation['user_added_id']) && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
			$this->view->renderEditTranslationPage($album, $song, $translation);
			return;
		}
		
		$album         = $this->model->getAlbumId($albumUri);
		$song          = $this->model->getSongId($albumUri, $songUri);
		$translation   = $this->model->getTranslationId($albumUri, $songUri, $translationUri);
		
		$name          = $_POST['translation-name']   ?? null;
		$lyrics        = $_POST['translation-lyrics'] ?? null;
		$notes         = $_POST['translation-notes']  ?? null;
		$userUpdatedId = $_SESSION['user']['id'];
		
		$name          = $this->trimNullableString($name);
		$lyrics        = $this->trimNullableText($lyrics);
		$notes         = $this->trimNullableText($notes);
		
		// $this->trimNullableText does not remove empty lines (because they may be part of lyrics)
		// The problem is what if empty lines was the only content?
		$lyrics        = $this->trimNullableString($lyrics);
		$notes         = $this->trimNullableString($notes);
		
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
		
		if (!$song['has_vocal'] || !$song['has_lyrics'] || $song['original_song_id'])
		{
			$this->handleForbidden();
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
		
		if (!isCurrentUser($translation['user_added_id']) && !isCurrentUserModerator())
		{
			$this->handleForbidden();
			return;
		}
		
		if (haveNullOrEmpty($name, $lyrics))
		{
			$this->handleBadRequest();
			return;
		}
		
		$this->model->updateTranslation
		(
			$albumUri,
			$songUri,
			$translationUri,
			$name,
			$lyrics,
			$notes,
			$userUpdatedId
		);
		
		$link = buildInternalLink($this->language, 'album', $albumUri, 'song', $songUri, 'translation', $translationUri);
		$this->handleRedirect($link);
	}
	
	final public function handleDeleteGamePage(string $gameUri): void
	{
		if (empty($_POST))
		{
			$game = $this->model->getGameId($gameUri);
			
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
			
			if ($game['status'] === 'checked' && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
			$this->view->renderDeleteGamePage($game);
			return;
		}

		$game             = $this->model->getGame($gameUri);
		$requestConfirmed = $_POST['confirmation'] ?? null;
		
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
		
		if ($game['status'] === 'checked' && !isCurrentUserModerator())
		{
			$this->handleForbidden();
			return;
		}
		
		if (!$requestConfirmed)
		{
			$this->handleBadRequest();
			return;
		}
		
		$this->model->deleteGame($game);
		
		$this->handleRedirect(buildInternalLink($this->language, 'game-list'));
	}
	
	final public function handleDeleteAlbumPage(string $albumUri): void
	{
		if (empty($_POST))
		{
			$album = $this->model->getAlbumId($albumUri);
			
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
			
			$this->view->renderDeleteAlbumPage($album);
			return;
		}

		$album            = $this->model->getAlbum($albumUri);
		$requestConfirmed = $_POST['confirmation'] ?? null;
		
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
		
		if (!$requestConfirmed)
		{
			$this->handleBadRequest();
			return;
		}
		
		$this->model->deleteAlbum($album);
		
		$this->handleRedirect(buildInternalLink($this->language, 'album-list'));
	}
	
	final public function handleDeleteArtistPage(string $artistUri): void
	{
		if (empty($_POST))
		{
			$artist = $this->model->getArtistId($artistUri);
			
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
			
			if ($artist['status'] === 'checked' && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
			$this->view->renderDeleteArtistPage($artist);
			return;
		}

		$artist           = $this->model->getArtist($artistUri);
		$requestConfirmed = $_POST['confirmation'] ?? null;
		
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
		
		if ($artist['status'] === 'checked' && !isCurrentUserModerator())
		{
			$this->handleForbidden();
			return;
		}
		
		if (!$requestConfirmed)
		{
			$this->handleBadRequest();
			return;
		}
		
		$this->model->deleteArtist($artist);
		
		$this->handleRedirect(buildInternalLink($this->language, 'artist-list'));
	}
	
	final public function handleDeleteCharacterPage(string $characterUri): void
	{
		if (empty($_POST))
		{
			$character = $this->model->getCharacterId($characterUri);
			
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
			
			if ($character['status'] === 'checked' && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
			$this->view->renderDeleteCharacterPage($character);
			return;
		}

		$character        = $this->model->getCharacter($characterUri);
		$requestConfirmed = $_POST['confirmation'] ?? null;
		
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
		
		if ($character['status'] === 'checked' && !isCurrentUserModerator())
		{
			$this->handleForbidden();
			return;
		}
		
		if (!$requestConfirmed)
		{
			$this->handleBadRequest();
			return;
		}
		
		$this->model->deleteCharacter($character);
		
		$this->handleRedirect(buildInternalLink($this->language, 'character-list'));
	}
	
	final public function handleDeleteLyricsPage(string $albumUri, string $songUri): void
	{
		if (empty($_POST))
		{
			$album        = $this->model->getAlbumId($albumUri);
			$song         = $this->model->getSongId($albumUri, $songUri);
			$translations = $this->model->getTranslationIdList($albumUri, $songUri);
			
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
			
			if (!$song['has_vocal'] || !$song['has_lyrics'])
			{
				$this->handleForbidden();
				return;
			}
			
			if (!(isCurrentUser($song['user_added_id']) && $song['status'] === 'unchecked') && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
			if ($translations)
			{
				$this->handleForbidden();
				return;
			}
			
			$this->view->renderDeleteLyricsPage($album, $song);
			return;
		}
		
		$album            = $this->model->getAlbumId($albumUri);
		$song             = $this->model->getSongId($albumUri, $songUri);
		$translations     = $this->model->getTranslationIdList($albumUri, $songUri);
		
		$requestConfirmed = $_POST['confirmation']  ?? null;
		$userUpdatedId    = $_SESSION['user']['id'];
		
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
		
		if (!$song['has_vocal'] || !$song['has_lyrics'])
		{
			$this->handleForbidden();
			return;
		}
		
		if (!(isCurrentUser($song['user_added_id']) && $song['status'] === 'unchecked') && !isCurrentUserModerator())
		{
			$this->handleForbidden();
			return;
		}
		
		if ($translations)
		{
			$this->handleForbidden();
			return;
		}
		
		if (!$requestConfirmed)
		{
			$this->handleBadRequest();
			return;
		}
		
		$this->model->deleteLyrics($song);
		$this->model->deleteSongArtistCharacterRelation(songId: $song['id']);
		
		$this->handleRedirect(buildInternalLink($this->language, 'album', $albumUri));
	}
	
	final public function handleDeleteTranslationPage(string $albumUri, string $songUri, string $translationUri): void
	{
		if (empty($_POST))
		{
			$album       = $this->model->getAlbumId($albumUri);
			$song        = $this->model->getSongId($albumUri, $songUri);
			$translation = $this->model->getTranslationId($albumUri, $songUri, $translationUri);
			
			if (!$translation)
			{
				$this->handleNotFound();
				return;
			}
			
			if (!isCurrentUser($translation['user_added_id']) && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
			$this->view->renderDeleteTranslationPage($album, $song, $translation);
			return;
		}
		
		$album            = $this->model->getAlbumId($albumUri);
		$song             = $this->model->getSongId($albumUri, $songUri);
		$translation      = $this->model->getTranslationId($albumUri, $songUri, $translationUri);
		
		$requestConfirmed = $_POST['confirmation']  ?? null;
		$userUpdatedId    = $_SESSION['user']['id'];
		
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
		
		if (!$translation)
		{
			$this->handleNotFound();
			return;
		}
		
		if (!isCurrentUser($translation['user_added_id']) && !isCurrentUserModerator())
		{
			$this->handleForbidden();
			return;
		}
		
		if (!$requestConfirmed)
		{
			$this->handleBadRequest();
			return;
		}
		
		$this->model->deleteTranslation($translation);
		
		$this->handleRedirect(buildInternalLink($this->language, 'album', $albumUri, 'song', $songUri));
	}
	
	final public function handleChangeAccountDataPage(string $userUri): void
	{
		if (empty($_POST))
		{
			$user = $this->model->getUserData(username: $userUri);
		
			if (!isCurrentUser($user['user_id']) && !isCurrentUserModerator())
			{
				$this->handleNotFound();
				return;
			}
			
			if (isCurrentUserViolator() && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
			$this->view->renderChangeAccountDataPage($user);
			return;
		}
		
		$user        = $this->model->getUserData(username: $userUri);
		
		$newUsername = $_POST['username']     ?? null;
		$oldPassword = $_POST['old-password'] ?? null;
		$newPassword = $_POST['new-password'] ?? null;
		$newEmail    = $_POST['email']        ?? null;
		
		// I forgot that constants can not be declared here :(
		$MIN_LENGTH = 4;
		$MAX_LENGTH = 32;
		
		if (!isCurrentUser($user['user_id']) && !isCurrentUserModerator())
		{
			$this->handleNotFound();
			return;
		}
		
		if (isCurrentUserViolator())
		{
			$this->handleForbidden();
			return;
		}
		
		if (!$newUsername || !$oldPassword || !$newEmail)
		{
			$this->handleBadRequest();
			return;
		}
		
		if (!$this->model->isPasswordCorrect($user['user_email'], $oldPassword))
		{
			$this->view->renderChangeAccountDataPage($user, AuthorizationError::IncorrectPassword);
			return;
		}
		
		if ($this->trimNullableString($newUsername) !== $newUsername)
		{
			$this->view->renderChangeAccountDataPage($user, AuthorizationError::UsernameTrimmable);
			return;
		}
		
		if (!$this->isLatinAlphabetAndNumbers($newUsername))
		{
			$this->view->renderChangeAccountDataPage($user, AuthorizationError::UsernameForbiddenSymbols);
			return;
		}
		
		if (mb_strlen($newUsername) < $MIN_LENGTH || mb_strlen($newUsername) > $MAX_LENGTH)
		{
			$this->view->renderChangeAccountDataPage($user, AuthorizationError::UsernameShort);
			return;
		}
		
		if ($user['user_username'] !== $newUsername && $this->model->isUserRegistered($newUsername))
		{
			$this->view->renderChangeAccountDataPage($user, AuthorizationError::UsernameTaken);
			return;
		}
		
		if (!$this->isEmailValid($newEmail))
		{
			$this->view->renderChangeAccountDataPage($user, AuthorizationError::EmailInvalid);
			return;
		}
		
		if ($user['user_email'] !== $newEmail && $this->model->isEmailRegistered($newEmail))
		{
			$this->view->renderChangeAccountDataPage($user, AuthorizationError::EmailTaken);
			return;
		}
		
		if ($newPassword && !$this->isLatinAlphabetAndNumbers($newPassword))
		{
			$this->view->renderChangeAccountDataPage($user, AuthorizationError::PasswordForbiddenSymbols);
			return;
		}
		
		if ($newPassword && (mb_strlen($newPassword) < $MIN_LENGTH || mb_strlen($newPassword) > $MAX_LENGTH))
		{
			$this->view->renderChangeAccountDataPage($user, AuthorizationError::PasswordShort);
			return;
		}
		
		$this->model->updateUserData($user['user_id'], $newUsername, $newEmail, $newPassword);
		
		$this->model->getUserData(username: $newUsername);
		$this->createUserSession($user);
		
		$this->handleRedirect(buildInternalLink($this->language, 'user', $newUsername));
	}
	
	final public function handleDeleteAccountPage(string $userUri): void
	{
		if (empty($_POST))
		{
			$user = $this->model->getUserData(username: $userUri);
			
			if (!$user)
			{
				$this->handleNotFound();
				return;
			}
			
			if (!isCurrentUser($user['user_id']) && !isCurrentUserModerator())
			{
				$this->handleNotFound();
				return;
			}
			
			if (isCurrentUserViolator() && !isCurrentUserModerator())
			{
				$this->handleForbidden();
				return;
			}
			
			$this->view->renderDeleteAccountPage($user);
			return;
		}
		
		$user = $this->model->getUserData(username: $userUri);
		$password = $_POST['password'] ?? null;
		
		if (!$user)
		{
			$this->handleNotFound();
			return;
		}
		
		if (!isCurrentUser($user['user_id']))
		{
			$this->handleNotFound();
			return;
		}
		
		if (isCurrentUserViolator())
		{
			$this->handleForbidden();
			return;
		}
		
		if (!$password)
		{
			$this->handleBadRequest();
			return;
		}
		
		if (!$this->model->isPasswordCorrect($user['user_email'], $password))
		{
			$this->view->renderDeleteAccountPage($user, AuthorizationError::IncorrectPassword);
			return;
		}
		
		$this->model->deleteUser($user);
		$this->handleLogOutPage();
		
		/*
		if (isCurrentUserModerator())
			$this->handleRedirect(buildInternalLink($this->language));
		else
			$this->handleLogOutPage();
		*/
	}
}
