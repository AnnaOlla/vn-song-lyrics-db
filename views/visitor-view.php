<?php

require_once 'views/view.php';

class VisitorView extends View
{
	public function __construct(string $language)
	{
		parent::__construct($language);
	}
	
	//---------------------------------------//
	//       Content Pages: Pagination       //
	//---------------------------------------//
	
	final protected function createResultsLimitBlock(int|null $limit): string
	{
		$selectedValue = ['toShow' => $limit, 'toSend' => $limit];
		
		$values =
		[
			['toShow' => 10,  'toSend' => 10],
			['toShow' => 25,  'toSend' => 25],
			['toShow' => 50,  'toSend' => 50],
			['toShow' => 100, 'toSend' => 100]
		];
		
		if (!in_array($selectedValue, $values) && !is_null($limit))
		{
			$values[] = $selectedValue;
			usort($values, function($a, $b) { return $a['toSend'] <=> $b['toSend']; });
		}
		
		$values[] = ['toShow' => \Localization\Controls\NoLimit, 'toSend' => null];
		
		$select = $this->createSelect
		(
			'limit',
			'limit-result-count-bar',
			null,
			true,
			false,
			false,
			$values,
			$selectedValue,
			'toShow',
			'toSend'
		);
		
		$html =
		'
			<section>'.\Localization\Controls\LimitHeading.'</section>
			<section class="results-limit">'.$select.'</section>
		';
		
		return $html;
	}
	
	final protected function createPaginationButton
	(
		string      $text,
		string|null $href,
		string      $state
	): string
	{
		$text = htmlspecialchars($text ?? '');
		$href = htmlspecialchars($href ?? '');
		
		if ($state === 'disabled')
			return '<a class="pagination-button disabled">'.$text.'</a>';
		else if ($state === 'current')
			return '<a class="pagination-button current">'.$text.'</a>';
		else
			return '<a href="'.$href.'" class="pagination-button">'.$text.'</a>';
	}
	
	final protected function createPaginationBlock
	(
		int         $currentPageIndex,
		int|null    $limit,
		string|null $search,
		int         $entityCount,
		string      $pageLink
	): string
	{
		if (is_null($limit))
			$pageCount = 1;
		else if ($entityCount > 0 && $entityCount % $limit === 0)
			$pageCount = intdiv($entityCount, $limit);
		else
			$pageCount = intdiv($entityCount, $limit) + 1;
		
		// If you change it,
		// then change divisor of pagination.button in search-filter-section.css
		$mostLeftPage       = 1;
		$pageCountFromLeft  = 3;
		$pageCountfromRight = 3;
		$mostRightPage      = $pageCount;
		
		$fromLeft = $currentPageIndex - $pageCountFromLeft;
		$fromLeft = $fromLeft > $mostLeftPage ? $fromLeft : $mostLeftPage;
		
		$fromRight = $currentPageIndex + $pageCountfromRight;
		$fromRight = $fromRight < $mostRightPage ? $fromRight : $mostRightPage;
		
		$html =
		'
			<section>'.\Localization\Controls\PageHeading.'</section>
			<section class="pagination">
		';
		
		if ($fromLeft > $mostLeftPage)
		{
			$href = $pageLink.$this->buildPaginationParameters($limit, $mostLeftPage, $search);
			$html .= $this->createPaginationButton($mostLeftPage, $href, 'enabled');
		}
		
		if ($fromLeft > $mostLeftPage + 1)
			$html .= $this->createPaginationButton('…', null, 'disabled');
		
		for ($i = $fromLeft; $i <= $fromRight; $i++)
		{
			$href  = $pageLink.$this->buildPaginationParameters($limit, $i, $search);
			$state = ($i === $currentPageIndex) ? 'current' : 'enabled';
			
			$html .= $this->createPaginationButton($i, $href, $state);
		}
		
		if ($fromRight < $mostRightPage - 1)
			$html .= $this->createPaginationButton('…', null, 'disabled');
		
		if ($fromRight < $mostRightPage)
		{
			$href = $pageLink.$this->buildPaginationParameters($limit, $mostRightPage, $search);
			$html .= $this->createPaginationButton($mostRightPage, $href, 'enabled');
		}
		
		$html .= 
		'
			</section>
		';
		
		return $html;
	}
	
	final protected function createSearchBarBlock(string|null $search): string
	{
		return
		'
		<section>'.\Localization\Controls\SearchHeading.'</section>
		<section class="search-elements">
			<input type="search" id="search-bar" value="'.htmlspecialchars($search ?? '').'" placeholder="'.\Localization\Controls\SearchPlaceholder.'" />
			<button id="search-bar-button">'.\Localization\Controls\SearchButton.'</button>
		</section>
		';
	}
	
	//---------------------------------------//
	//      Content Pages: Entity Lists      //
	//---------------------------------------//
	
	final protected function createGameList
	(
		array       $games,
		int         $headingLevel,
		string      $entityClass,
		string|null $relationKey = null
	): string
	{
		$html = '';
		
		foreach ($games as $game)
		{
			$href = buildInternalLink($this->language, 'game', $game['uri']);
			
			$image        = $this->createGameImage($game);
			$textEntities = [];
			
			$textEntities[] = $this->createHeadingAsLink($game['transliterated_name'], $headingLevel, $href, 'entity-name');
			$textEntities[] = $this->createParagraph($game['original_name'], 'entity-name');
			$textEntities[] = $this->createParagraph($game['localized_name'], 'entity-name');
			
			if ($relationKey)
			{
				if (isCurrentUserModerator())
					$textEntities[] = $this->createStatusSelect($game, $relationKey, $href);
				else
					$textEntities[] = $this->createStatus($game[$relationKey], true);
			}
			
			$html .= $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
		return $html;
	}
	
	final protected function createAlbumList
	(
		array       $albums,
		int         $headingLevel,
		string      $entityClass,
		string|null $relationKey = null
	): string
	{
		$html = '';
		
		foreach ($albums as $album)
		{
			$href = buildInternalLink($this->language, 'album', $album['uri']);
			
			$image        = $this->createAlbumImage($album);
			$textEntities = [];
			
			$textEntities[] = $this->createHeadingAsLink($album['transliterated_name'], $headingLevel, $href, 'entity-name');
			$textEntities[] = $this->createParagraph($album['original_name'], 'entity-name');
			$textEntities[] = $this->createParagraph($album['localized_name'], 'entity-name');
			
			if ($relationKey)
			{
				if (isCurrentUserModerator())
					$textEntities[] = $this->createStatusSelect($album, $relationKey, $href);
				else
					$textEntities[] = $this->createStatus($album[$relationKey], true);
			}
			
			$html .= $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
		return $html;
	}
	
	final protected function createArtistList
	(
		array       $artists,
		int         $headingLevel,
		string      $entityClass,
		string|null $relationKey = null
	): string
	{
		$html = '';
		
		foreach ($artists as $artist)
		{
			$href = buildInternalLink($this->language, 'artist', $artist['uri']);
			
			$image        = $this->createArtistImage($artist);
			$textEntities = [];
			
			$textEntities[] = $this->createHeadingAsLink($artist['transliterated_name'], $headingLevel, $href, 'entity-name');
			$textEntities[] = $this->createParagraph($artist['original_name'], 'entity-name');
			$textEntities[] = $this->createParagraph($artist['localized_name'], 'entity-name');
			
			if ($relationKey)
			{
				if (isCurrentUserModerator())
					$textEntities[] = $this->createStatusSelect($artist, $relationKey, $href);
				else
					$textEntities[] = $this->createStatus($artist[$relationKey], true);
			}
			
			$html .= $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
		return $html;
	}
	
	final protected function createCharacterList
	(
		array       $characters,
		int         $headingLevel,
		string      $entityClass,
		string|null $relationKey = null
	): string
	{
		$html = '';
		
		foreach ($characters as $character)
		{
			$href = buildInternalLink($this->language, 'character', $character['uri']);
			
			$image        = $this->createCharacterImage($character);
			$textEntities = [];
			
			$textEntities[] = $this->createHeadingAsLink($character['transliterated_name'], $headingLevel, $href, 'entity-name');
			$textEntities[] = $this->createParagraph($character['original_name'], 'entity-name');
			$textEntities[] = $this->createParagraph($character['localized_name'], 'entity-name');
			
			if ($relationKey)
			{
				if (isCurrentUserModerator())
					$textEntities[] = $this->createStatusSelect($character, $relationKey, $href);
				else
					$textEntities[] = $this->createStatus($character[$relationKey], true);
			}
			
			$html .= $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
		return $html;
	}
	
	final protected function createAlbumSongList(array $album, array $songs, int $headingLevel): string
	{
		$rows = [];
		
		for ($i = 0; $i < count($songs); $i++)
		{
			$cells = [];
			
			if ($songs[$i]['has_vocal'])
			{
				$href = buildInternalLink($this->language, 'album', $songs[$i]['album_uri'], 'song', $songs[$i]['uri']);
				$transliteratedName = $this->createParagraphAsLink($songs[$i]['transliterated_name'], $href);
			}
			else
				$transliteratedName = $this->createParagraph($songs[$i]['transliterated_name']);
			
			$href = buildInternalLink
			(
				$this->language,
				'album',
				$songs[$i]['album_uri'],
				'song',
				$songs[$i]['uri'],
				'edit'
			);
			
			[$isEnabled, $tooltipIfDisabled] = canCurrentUserEditEntity
			(
				$songs[$i]['user_added_id'],
				$songs[$i]['status']
			);
			
			$editButton = $this->createButton
			(
				\Localization\AlbumPage\EditSong,
				$href,
				$isEnabled,
				$tooltipIfDisabled
			);
			
			$cells['disc_number']         = htmlspecialchars($songs[$i]['disc_number']);
			$cells['track_number']        = htmlspecialchars($songs[$i]['track_number']);
			$cells['transliterated_name'] = $transliteratedName;
			$cells['original_name']       = $this->createParagraph($songs[$i]['original_name']);
			$cells['localized_name']      = $this->createParagraph($songs[$i]['localized_name']);
			$cells['edit_button']         = $editButton;
			
			$rows[] = $cells;
		}
		
		$html =
		'
		<section>
			'.$this->createHeading(\Localization\AlbumPage\SongList, 2).'
			<table>
				<tbody>
					<tr>
						<th>'.\Localization\AlbumPage\DiscNumber.'</th>
						<th>'.\Localization\AlbumPage\TrackNumber.'</th>
						<th>'.\Localization\AlbumPage\SongName.'</th>
						<th></th>
		';
		
		foreach ($rows as $row)
		{
			$html .=
			'
					<tr>
						<td>'.$row['disc_number'].'</td>
						<td>'.$row['track_number'].'</td>
						<td>
							'.$row['transliterated_name'].'
							'.$row['original_name'].'
							'.$row['localized_name'].'
						</td>
						<td>'.$row['edit_button'].'</td>
					</tr>
			';
		}
		
		if (count($songs) < $album['song_count'])
		{
			$title = \Localization\AlbumPage\AddSong;
			$href  = buildInternalLink($this->language, 'album', $album['uri'], 'add-song');
			
			[$isButtonEnabled, $tooltipIfDisabled] = canCurrentUserEditEntity
			(
				$album['user_added_id'],
				$album['status']
			);
			
			$html .=
			'
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td>'.$this->createButton($title, $href, $isButtonEnabled, $tooltipIfDisabled).'</td>
					</tr>
			';
		}
		
		if (count($songs) === 0 && isCurrentUserModerator())
		{
			$title = \Localization\AlbumPage\FillAlbum;
			$href  = buildInternalLink($this->language, 'album', $album['uri'], 'fill-album');
			
			[$isButtonEnabled, $tooltipIfDisabled] = canCurrentUserEditEntity
			(
				$album['user_added_id'],
				$album['status']
			);
			
			$html .=
			'
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td>'.$this->createButton($title, $href, $isButtonEnabled, $tooltipIfDisabled).'</td>
					</tr>
			';
		}
		
		$html .=
		'
				</tbody>
			</table>
		</section>
		';
		
		return $html;
	}
	
	final protected function createSongList
	(
		array       $songs,
		int         $headingLevel,
		string      $entityClass,
		string|null $relationKey = null
	): string
	{
		$html = '';
		
		foreach ($songs as $song)
		{
			// It is not how it is supposed to work
			// The song uses the cover of its album
			// The function requires keys transliterated_name and uri of ALBUM
			$album = [];
			$album['transliterated_name'] = $song['album_transliterated_name'];
			$album['uri']                 = $song['album_uri'];
			$album['is_image_uploaded']   = $song['is_image_uploaded'];
			
			$image        = $this->createAlbumImage($album);
			$textEntities = [];
			
			if ($song['has_vocal'])
			{
				$href = buildInternalLink($this->language, 'album', $song['album_uri'], 'song', $song['uri']);
				$textEntities[] = $this->createHeadingAsLink($song['transliterated_name'], $headingLevel, $href, 'entity-name');
			}
			else
				$textEntities[] = $this->createHeading($song['transliterated_name'], $headingLevel, 'entity-name');
			
			$textEntities[] = $this->createParagraph($song['original_name'], 'entity-name');
			$textEntities[] = $this->createParagraph($song['localized_name'], 'entity-name');
			
			if ($relationKey)
				$textEntities[] = $this->createStatus($song['song_artist_character_relation_status'], true);
			else
				$textEntities[] = '';
			
			$html .= $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
		return $html;
	}
	
	final protected function createTranslationList
	(
		array  $translations,
		int    $headingLevel,
		string $entityClass
	): string
	{
		$html = '';
		
		foreach ($translations as $translation)
		{
			// It is not how it is supposed to work
			// The song uses the cover of its album
			// The function requires keys transliterated_name and uri of ALBUM
			$album = [];
			$album['transliterated_name'] = $translation['album_transliterated_name'];
			$album['uri']                 = $translation['album_uri'];
			$album['is_image_uploaded']   = $translation['is_image_uploaded'];
			
			$image        = $this->createAlbumImage($album);
			$textEntities = [];
			
			$href = buildInternalLink
			(
				$this->language,
				'album',
				$translation['album_uri'],
				'song',
				$translation['song_uri'],
				'translation',
				$translation['uri']
			);
			
			$textEntities[] = $this->createHeadingAsLink($translation['name'], $headingLevel, $href, 'entity-name');
			$textEntities[] = $this->createParagraph(\Localization\Functions\localizeLanguageName($translation));
			
			$html .= $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
		$html .=
		'
		</section>
		';
		
		return $html;
	}
	
	//-------------------------//
	//----- Single Entity -----//
	//-------------------------//
	
	final protected function createGame(array $game, int $headingLevel): string
	{
		$image        = $this->createGameImage($game);
		$textEntities = [];
		
		$textEntities[] = $this->createHeading($game['transliterated_name'], $headingLevel);
		$textEntities[] = $this->createParagraph($game['original_name']);
		$textEntities[] = $this->createParagraph($game['localized_name']);
		$textEntities[] = $this->createVndbLink(\Localization\GamePage\Details, $game, 'v');
		
		return $this->createInfoBlockWithImage($image, $textEntities, 'main-entity');
	}
	
	final protected function createAlbum(array $album, int $headingLevel): string
	{
		$image        = $this->createAlbumImage($album);
		$textEntities = [];
		
		$textEntities[] = $this->createHeading($album['transliterated_name'], $headingLevel);
		$textEntities[] = $this->createParagraph($album['original_name']);
		$textEntities[] = $this->createParagraph($album['localized_name']);
		$textEntities[] = $this->createVgmdbLink(\Localization\AlbumPage\Details, $album, 'album');
		$textEntities[] = $this->createParagraph(\Localization\AlbumPage\SongCount.$album['song_count']);
		
		return $this->createInfoBlockWithImage($image, $textEntities, 'main-entity');
	}
	
	final protected function createArtist(array $artist, int $headingLevel): string
	{
		$image        = $this->createArtistImage($artist);
		$textEntities = [];
		
		$textEntities[] = $this->createHeading($artist['transliterated_name'], $headingLevel);
		$textEntities[] = $this->createParagraph($artist['original_name']);
		$textEntities[] = $this->createParagraph($artist['localized_name']);
		$textEntities[] = $this->createVgmdbLink(\Localization\ArtistPage\Details, $artist, 'artist');
		
		if ($artist['alias_of_transliterated_name'] && $artist['alias_of_uri'])
		{
			$href = buildInternalLink($this->language, 'artist', $artist['alias_of_uri']);
			$text = $artist['alias_of_transliterated_name'];
			
			$textEntities[] = '<p>'.\Localization\ArtistPage\AliasOf.$this->createLink($href, $text).'</p>';
		}
		
		return $this->createInfoBlockWithImage($image, $textEntities, 'main-entity');
	}
	
	final protected function createCharacter(array $character, int $headingLevel): string
	{
		$image        = $this->createCharacterImage($character);
		$textEntities = [];
		
		$textEntities[] = $this->createHeading($character['transliterated_name'], $headingLevel);
		$textEntities[] = $this->createParagraph($character['original_name']);
		$textEntities[] = $this->createParagraph($character['localized_name']);
		$textEntities[] = $this->createVndbLink(\Localization\CharacterPage\Details, $character, 'c');
		
		return $this->createInfoBlockWithImage($image, $textEntities, 'main-entity');
	}
	
	//---------------------//
	//      Home Page      //
	//---------------------//
	
	final public function renderHomePage(array $albums, array $lyrics, array $translations): void
	{
		$html = $this->startRender
		(
			title:        \Localization\HomePage\Heading,
			cssSheetUris: ['/css/home-page.css']
		);
		
		$html .=
		'
		<article>
			<section class="home-page-heading">
				<img class="website-mascot" src="/assets/static-images/wee-hagana.png" alt="Hagana from World End Economica"/>
				<section class="home-page-text">
					<h1 class="home-page-text">'.\Localization\HomePage\Heading.'</h1>
					<p class="home-page-text">'.\Localization\HomePage\DescriptionOne.'</p>
					<p class="home-page-text">'.\Localization\HomePage\DescriptionTwo.'</p>
					<p class="home-page-text">'.\Localization\HomePage\DescriptionThree.'</p>
				</section>
			</section>
			<section>
				<h2>'.\Localization\HomePage\LastAlbums.'</h2>
				<section class="entity-container">
		';
		
		foreach ($albums as $album)
		{
			$headingLevel = 3;
			$href = buildInternalLink($this->language, 'album', $album['uri']);
			
			$image   = $this->createAlbumImage($album);
			$heading = $this->createHeading($album['transliterated_name'], $headingLevel);
			
			$html .=
			'
					<a href='.$href.'>
						'.$image.'
						'.$heading.'
					</a>
			';
		}
		
		$html .=
		'
				</section>
			</section>
			<section>
				<h2>'.\Localization\HomePage\LastLyrics.'</h2>
				<section class="entity-container">
		';
		
		foreach ($lyrics as $lyric)
		{
			$headingLevel = 3;
			$href = buildInternalLink($this->language, 'album', $lyric['album_uri'], 'song', $lyric['uri']);
			
			$album = [];
			$album['transliterated_name'] = $lyric['album_transliterated_name'];
			$album['is_image_uploaded']   = $lyric['is_image_uploaded'];
			$album['uri']                 = $lyric['album_uri'];
			
			$image   = $this->createAlbumImage($album);
			$heading = $this->createHeading($lyric['transliterated_name'], $headingLevel);
			
			$html .=
			'
					<a href='.$href.'>
						'.$image.'
						'.$heading.'
					</a>
			';
		}
		
		$html .=
		'
				</section>
			</section>
			<section>
				<h2>'.\Localization\HomePage\LastTranslations.'</h2>
				<section class="entity-container">
		';
		
		foreach ($translations as $translation)
		{
			$headingLevel = 3;
			$href = buildInternalLink
			(
				$this->language,
				'album',
				$translation['album_uri'],
				'song',
				$translation['song_uri'],
				'translation',
				$translation['uri']
			);
			
			$album = [];
			$album['transliterated_name'] = $translation['album_transliterated_name'];
			$album['is_image_uploaded']   = $translation['is_image_uploaded'];
			$album['uri']                 = $translation['album_uri'];
			
			$image   = $this->createAlbumImage($album);
			$heading = $this->createHeading($translation['name'], $headingLevel);
			
			$html .=
			'
					<a href='.$href.'>
						'.$image.'
						'.$heading.'
					</a>
			';
		}
		
		$html .=
		'
			</section>
		</article>
		';
		
		$html .= $this->endRender();
		
		echo $html;
	}
	
	//------------------------//
	//      Log-In Pages      //
	//------------------------//
	
	final public function renderLogInPage(AuthenticationError $error = AuthenticationError::None): void
	{
		switch ($error)
		{
			case AuthenticationError::EmptyEmail:
				$errorMessage = \Localization\AuthenticationError\EmptyEmail;
				break;
			
			case AuthenticationError::EmptyPassword:
				$errorMessage = \Localization\AuthenticationError\EmptyPassword;
				break;
			
			case AuthenticationError::EmailNotFound:
				$errorMessage = \Localization\AuthenticationError\EmailNotFound;
				break;
			
			case AuthenticationError::IncorrectPassword:
				$errorMessage = \Localization\AuthenticationError\IncorrectPassword;
				break;
				
			case AuthenticationError::AccountNotVerified:
				$errorMessage = \Localization\AuthenticationError\AccountNotVerified;
				break;
				
			default:
				$errorMessage = '';
				break;
		}
		
		$html = $this->startRender
		(
			title:        \Localization\LogInPage\Heading,
			cssSheetUris: ['/css/window-in-center-page.css']
		);
		
		$html .= 
		'
		<article>
			<section>
				<h1>'.\Localization\HomePage\Heading.'</h1>
			</section>
			<section>
				<h2>'.\Localization\LogInPage\Heading.'</h2>
				<p>'.$errorMessage.'</p>
				<form method="POST">
					<section>
						<h3>'.\Localization\LogInPage\Email.'<span class="required-input"> *</span></h3>
						<input type="email" name="email" minlength="4" maxlength="32" required/>
					</section>
					<section>
						<h3>'.\Localization\LogInPage\Password.'<span class="required-input"> *</span></h3>
						<input type="password" name="password" pattern="[a-zA-Z0-9]+" minlength="4" maxlength="32" placeholder="'.\Localization\LogInPage\HintPassword.'" required/>
					</section>
					<section>
						<input type="submit" value="'.\Localization\LogInPage\Submit.'"/>
					</section>
				</form>
			</section>
		</article>
		';
		
		$html .= $this->endRender();
		
		echo $html;
	}
	
	final public function renderSignUpPage(AuthenticationError $error = AuthenticationError::None, string $captchaBase64Image = ''): void
	{
		switch ($error)
		{
			case AuthenticationError::CaptchaInvalid:
				$errorMessage = \Localization\AuthenticationError\CaptchaInvalid;
				break;
			
			case AuthenticationError::UsernameTrimmable:
				$errorMessage = \Localization\AuthenticationError\UsernameTrimmable;
				break;
			
			case AuthenticationError::UsernameForbiddenSymbols:
				$errorMessage = \Localization\AuthenticationError\UsernameForbiddenSymbols;
				break;
			
			case AuthenticationError::UsernameLengthIncorrect:
				$errorMessage = \Localization\AuthenticationError\UsernameLengthIncorrect;
				break;
			
			case AuthenticationError::UsernameTaken:
				$errorMessage = \Localization\AuthenticationError\UsernameTaken;
				break;
			
			case AuthenticationError::PasswordTrimmable:
				$errorMessage = \Localization\AuthenticationError\PasswordTrimmable;
				break;
				
			case AuthenticationError::PasswordForbiddenSymbols:
				$errorMessage = \Localization\AuthenticationError\PasswordForbiddenSymbols;
				break;
			
			case AuthenticationError::UsernameLengthIncorrect:
				$errorMessage = \Localization\AuthenticationError\UsernameLengthIncorrect;
				break;
			
			case AuthenticationError::EmailTaken:
				$errorMessage = \Localization\AuthenticationError\EmailTaken;
				break;
			
			case AuthenticationError::EmailInvalid:
				$errorMessage = \Localization\AuthenticationError\EmailInvalid;
				break;
				
			case AuthenticationError::MailSendFailed:
				$errorMessage = \Localization\AuthenticationError\MailSendFailed;
				break;
			
			default:
				$errorMessage = '';
				break;
		}
		
		$html = $this->startRender
		(
			title:        \Localization\SignUpPage\Heading,
			cssSheetUris: ['/css/window-in-center-page.css', '/css/shared/captcha.css']
		);
		
		$html .= 
		'
		<article>
			<section>
				<h1>'.\Localization\HomePage\Heading.'</h1>
			</section>
			<section>
				<h2>'.\Localization\SignUpPage\Heading.'</h2>
				<p>'.$errorMessage.'</p>
				<form method="POST">
					<section>
						<h3>'.\Localization\SignUpPage\Username.'<span class="required-input"> *</span></h3>
						<input type="text" name="username" pattern="[a-zA-Z0-9]+" minlength="4" maxlength="32" placeholder="'.\Localization\SignUpPage\HintUsername.'" required/>
					</section>
					<section>
						<h3>'.\Localization\SignUpPage\Email.'<span class="required-input"> *</span></h3>
						<input type="email" name="email" minlength="4" maxlength="32" placeholder="'.\Localization\SignUpPage\HintEmail.'" required/>
					</section>
					<section>
						<h3>'.\Localization\SignUpPage\Password.'<span class="required-input"> *</span></h3>
						<input type="password" name="password" pattern="[a-zA-Z0-9]+" minlength="4" maxlength="32" placeholder="'.\Localization\SignUpPage\HintPassword.'" required/>
					</section>
					<section>
						<p>'.\Localization\SignUpPage\Confirmation.'</p>
							<a href="'.buildInternalLink($this->language, 'policy').'" target="_blank">'.\Localization\SignUpPage\Policy.'</a>,
							<a href="'.buildInternalLink($this->language, 'rules').'" target="_blank">'.\Localization\SignUpPage\Rules.'</a>,
							<a href="'.buildInternalLink($this->language, 'writing-guide').'" target="_blank">'.\Localization\SignUpPage\WritingGuide.'</a>.
						<p>'.\Localization\SignUpPage\Warning.'</p>
					</section>
					<section>
						<input type="text" name="captcha-code" id="captcha-input" onkeydown="return /[a-zA-Z0-9]/i.test(event.key)" placeholder="code:" required/>
						<img src="'.htmlspecialchars($captchaBase64Image).'" alt="captcha" id="captcha-image"/>
						<input type="submit" value="'.\Localization\SignUpPage\Submit.'"/>
					</section>
				</form>
			</section>
		</article>
		';
		
		$html .= $this->endRender();
		
		echo $html;
	}
	
	//-------------------------//
	//      Content Pages      //
	//-------------------------//
	
	final public function renderGameListPage
	(
		array       $games,
		int         $gameCount,
		int|null    $page,
		int|null    $limit,
		string|null $search
	): void
	{
		$hrefButton = buildInternalLink($this->language, 'add-game');
		[$isButtonEnabled, $tooltipIfDisabled] = canCurrentUserAddEntity();
		
		$hrefThisPage      = buildInternalLink($this->language, 'game-list');
		$paginationBlock   = $this->createPaginationBlock($page ?? 1, $limit, $search, $gameCount, $hrefThisPage);
		$resultsLimitBlock = $this->createResultsLimitBlock($limit);
		$searchBarBlock    = $this->createSearchBarBlock($search);
		
		$html = $this->startRender
		(
			title: \Localization\GameListPage\Heading,
			cssSheetUris:
			[
				'/css/shared/entity.css',
				'/css/shared/search-filter-section.css'
			]
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading(\Localization\GameListPage\Heading, 1).'
				<section class="filter-section">
					'.$this->createFilterBar().'
					'.$this->createButton(\Localization\GameListPage\AddGame, $hrefButton, $isButtonEnabled, $tooltipIfDisabled).'
				</section>
			</section>
			<section>
				<section class="search-section">
					<section>'.$resultsLimitBlock.'</section>
					<section>'.$paginationBlock.'</section>
					<section>'.$searchBarBlock.'</section>
				</section>
			</section>
			'.$this->createGameList($games, 2, 'list-entity').'
			<section>
				<section class="search-section">
					<section></section>
					<section>'.$paginationBlock.'</section>
					<section></section>
				</section>
			</section>
		</article>
		';
		
		$html .= $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/entity-list-filter.js',
				'/js/shared/entity-list-search.js',
				'/js/entity-list-page.js'
			]
		);
		
		echo $html;
	}
	
	final public function renderAlbumListPage
	(
		array       $albums,
		int         $albumCount,
		int|null    $page,
		int|null    $limit,
		string|null $search
	): void
	{
		$hrefButton = buildInternalLink($this->language, 'add-album');
		[$isButtonEnabled, $tooltipIfDisabled] = canCurrentUserAddEntity();
		
		$hrefThisPage      = buildInternalLink($this->language, 'album-list');
		$paginationBlock   = $this->createPaginationBlock($page ?? 1, $limit, $search, $albumCount, $hrefThisPage);
		$resultsLimitBlock = $this->createResultsLimitBlock($limit);
		$searchBarBlock    = $this->createSearchBarBlock($search);
		
		$html = $this->startRender
		(
			title: \Localization\AlbumListPage\Heading,
			cssSheetUris:
			[
				'/css/shared/entity.css',
				'/css/shared/search-filter-section.css'
			]
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading(\Localization\AlbumListPage\Heading, 1).'
				<section class="filter-section">
					'.$this->createFilterBar().'
					'.$this->createButton(\Localization\AlbumListPage\AddAlbum, $hrefButton, $isButtonEnabled, $tooltipIfDisabled).'
				</section>
			</section>
			<section>
				<section class="search-section">
					<section>'.$resultsLimitBlock.'</section>
					<section>'.$paginationBlock.'</section>
					<section>'.$searchBarBlock.'</section>
				</section>
			</section>
			'.$this->createAlbumList($albums, 2, 'list-entity').'
			<section>
				<section class="search-section">
					<section></section>
					<section>'.$paginationBlock.'</section>
					<section></section>
				</section>
			</section>
		</article>
		';
		
		$html .= $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/entity-list-filter.js',
				'/js/shared/entity-list-search.js',
				'/js/entity-list-page.js'
			]
		);
		
		echo $html;
	}
	
	final public function renderArtistListPage
	(
		array       $artists,
		int         $artistCount,
		int|null    $page,
		int|null    $limit,
		string|null $search
	): void
	{
		$hrefButton = buildInternalLink($this->language, 'add-artist');
		[$isButtonEnabled, $tooltipIfDisabled] = canCurrentUserAddEntity();
		
		$hrefThisPage      = buildInternalLink($this->language, 'artist-list');
		$paginationBlock   = $this->createPaginationBlock($page ?? 1, $limit, $search, $artistCount, $hrefThisPage);
		$resultsLimitBlock = $this->createResultsLimitBlock($limit);
		$searchBarBlock    = $this->createSearchBarBlock($search);
		
		$html = $this->startRender
		(
			title: \Localization\ArtistListPage\Heading,
			cssSheetUris:
			[
				'/css/shared/entity.css',
				'/css/shared/search-filter-section.css'
			]
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading(\Localization\ArtistListPage\Heading, 1).'
				<section class="filter-section">
					'.$this->createFilterBar().'
					'.$this->createButton(\Localization\ArtistListPage\AddArtist, $hrefButton, $isButtonEnabled, $tooltipIfDisabled).'
				</section>
			</section>
			<section>
				<section class="search-section">
					<section>'.$resultsLimitBlock.'</section>
					<section>'.$paginationBlock.'</section>
					<section>'.$searchBarBlock.'</section>
				</section>
			</section>
			'.$this->createArtistList($artists, 2, 'list-entity').'
			<section>
				<section class="search-section">
					<section></section>
					<section>'.$paginationBlock.'</section>
					<section></section>
				</section>
			</section>
		</article>
		';
		
		$html .= $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/entity-list-filter.js',
				'/js/shared/entity-list-search.js',
				'/js/entity-list-page.js'
			]
		);
		
		echo $html;
	}
	
	final public function renderCharacterListPage
	(
		array       $characters,
		int         $characterCount,
		int|null    $page,
		int|null    $limit,
		string|null $search
	): void
	{
		$hrefButton = buildInternalLink($this->language, 'add-character');
		[$isButtonEnabled, $tooltipIfDisabled] = canCurrentUserAddEntity();
		
		$hrefThisPage      = buildInternalLink($this->language, 'character-list');
		$paginationBlock   = $this->createPaginationBlock($page ?? 1, $limit, $search, $characterCount, $hrefThisPage);
		$resultsLimitBlock = $this->createResultsLimitBlock($limit);
		$searchBarBlock    = $this->createSearchBarBlock($search);
		
		$html = $this->startRender
		(
			title: \Localization\CharacterListPage\Heading,
			cssSheetUris:
			[
				'/css/shared/entity.css',
				'/css/shared/search-filter-section.css'
			]
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading(\Localization\CharacterListPage\Heading, 1).'
				<section class="filter-section">
					'.$this->createFilterBar().'
					'.$this->createButton(\Localization\CharacterListPage\AddCharacter, $hrefButton, $isButtonEnabled, $tooltipIfDisabled).'
				</section>
			</section>
			<section>
				<section class="search-section">
					<section>'.$resultsLimitBlock.'</section>
					<section>'.$paginationBlock.'</section>
					<section>'.$searchBarBlock.'</section>
				</section>
			</section>
			'.$this->createCharacterList($characters, 2, 'list-entity').'
			<section>
				<section class="search-section">
					<section></section>
					<section>'.$paginationBlock.'</section>
					<section></section>
				</section>
			</section>
		</article>
		';
		
		$html .= $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/entity-list-filter.js',
				'/js/shared/entity-list-search.js',
				'/js/entity-list-page.js'
			]
		);
		
		echo $html;
	}
	
	final public function renderSongListPage
	(
		array       $songs,
		int         $songCount,
		int|null    $page,
		int|null    $limit,
		string|null $search
	): void
	{
		$hrefThisPage      = buildInternalLink($this->language, 'song-list');
		$paginationBlock   = $this->createPaginationBlock($page ?? 1, $limit, $search, $songCount, $hrefThisPage);
		$resultsLimitBlock = $this->createResultsLimitBlock($limit);
		$searchBarBlock    = $this->createSearchBarBlock($search);
		
		$html = $this->startRender
		(
			title: \Localization\SongListPage\Heading,
			cssSheetUris:
			[
				'/css/shared/entity.css',
				'/css/shared/search-filter-section.css'
			]
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading(\Localization\SongListPage\Heading, 1).'
				<section class="filter-section">
					'.$this->createFilterBar().'
				</section>
			</section>
			<section>
				<section class="search-section">
					<section>'.$resultsLimitBlock.'</section>
					<section>'.$paginationBlock.'</section>
					<section>'.$searchBarBlock.'</section>
				</section>
			</section>
			'.$this->createSongList($songs, 2, 'list-entity').'
			<section>
				<section class="search-section">
					<section></section>
					<section>'.$paginationBlock.'</section>
					<section></section>
				</section>
			</section>
		</article>
		';
		
		$html .= $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/entity-list-filter.js',
				'/js/shared/entity-list-search.js',
				'/js/entity-list-page.js'
			]
		);
		
		echo $html;
	}
	
	final public function renderTranslationListPage
	(
		array       $translations,
		int         $translationCount,
		int|null    $page,
		int|null    $limit,
		string|null $search
	): void
	{
		$hrefThisPage      = buildInternalLink($this->language, 'translation-list');
		$paginationBlock   = $this->createPaginationBlock($page ?? 1, $limit, $search, $translationCount, $hrefThisPage);
		$resultsLimitBlock = $this->createResultsLimitBlock($limit);
		$searchBarBlock    = $this->createSearchBarBlock($search);
		
		$html = $this->startRender
		(
			title: \Localization\TranslationListPage\Heading,
			cssSheetUris:
			[
				'/css/shared/entity.css',
				'/css/shared/search-filter-section.css'
			]
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading(\Localization\TranslationListPage\Heading, 1).'
				<section class="filter-section">
					'.$this->createFilterBar().'
				</section>
			</section>
			<section>
				<section class="search-section">
					<section>'.$resultsLimitBlock.'</section>
					<section>'.$paginationBlock.'</section>
					<section>'.$searchBarBlock.'</section>
				</section>
			</section>
			'.$this->createTranslationList($translations, 2, 'list-entity').'
			<section>
				<section class="search-section">
					<section></section>
					<section>'.$paginationBlock.'</section>
					<section></section>
				</section>
			</section>
		</article>
		';
		
		$html .= $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/entity-list-filter.js',
				'/js/shared/entity-list-search.js',
				'/js/entity-list-page.js'
			]
		);
		
		echo $html;
	}
	
	final public function renderGamePage(array $game, array $albums, array $characters): void
	{
		$html = $this->startRender
		(
			title: $game['transliterated_name'],
			cssSheetUris:
			[
				'/css/entity-page.css',
				'/css/shared/entity.css'
			]
		);
		
		$html .= 
		'
		<article>
			'.$this->createGame($game, 1).'
		';
		
		if ($characters)
		{
			$html .= 
			'
			<section>
				'.$this->createHeading(\Localization\GamePage\RelatedCharacters, 2).'
			</section>
			'.$this->createCharacterList($characters, 3, 'related-entity', 'character_game_relation_status').'
			';
		}
		
		if ($albums)
		{
			$html .= 
			'
			<section>
				'.$this->createHeading(\Localization\GamePage\RelatedAlbums, 2).'
			</section>
			'.$this->createAlbumList($albums, 3, 'related-entity', 'game_album_relation_status').'
			';
		}
		
		$html .= 
		'
			'.$this->createTimestampBlock($game).'
			'.$this->createControlButtonsBlock($game, 'game').'
		</article>
		';
		
		$js = [];
		if (isCurrentUserModerator())
		{
			$js[] = '/js/moderation/change-status-select.js';
		}
		
		$html .= $this->endRender
		(
			jsScriptUris: $js
		);
		
		echo $html;
	}
	
	final public function renderAlbumPage(array $album, array $songs, array $games): void
	{
		$html = $this->startRender
		(
			title: $album['transliterated_name'],
			cssSheetUris:
			[
				'/css/entity-page.css',
				'/css/shared/entity.css',
				'/css/track-list-page.css'
			]
		);
		
		$html .= 
		'
		<article>
			'.$this->createAlbum($album, 1).'
			'.$this->createAlbumSongList($album, $songs, 2).'
		';
		
		if ($games)
		{
			$html .= 
			'
			<section>
				'.$this->createHeading(\Localization\AlbumPage\RelatedGames, 2).'
			</section>
			'.$this->createGameList($games, 3, 'related-entity', 'game_album_relation_status').'
			';
		}
		
		$html .= 
		'
			'.$this->createTimestampBlock($album).'
			'.$this->createControlButtonsBlock($album, 'album').'
		</article>
		';
		
		$js = [];
		if (isCurrentUserModerator())
		{
			$js[] = '/js/moderation/change-status-select.js';
		}
		
		$html .= $this->endRender
		(
			jsScriptUris: $js
		);
		
		echo $html;
	}
	
	final public function renderArtistPage(array $artist, array $aliases, array $songs): void
	{
		$html = $this->startRender
		(
			title: $artist['transliterated_name'],
			cssSheetUris:
			[
				'/css/entity-page.css',
				'/css/shared/entity.css'
			]
		);
		
		$html .= 
		'
		<article>
			'.$this->createArtist($artist, 1).'
		';
		
		if ($aliases)
		{
			$html .= 
			'
			<section>
				'.$this->createHeading(\Localization\ArtistPage\Aliases, 2).'
			</section>
			'.$this->createArtistList($aliases, 3, 'related-entity').'
			';
		}
		
		if ($songs)
		{
			$html .= 
			'
			<section>
				'.$this->createHeading(\Localization\ArtistPage\RelatedSongs, 2).'
			</section>
			'.$this->createSongList($songs, 3, 'related-entity', 'song_artist_character_relation_status').'
			';
		}
		
		$html .= 
		'
			'.$this->createTimestampBlock($artist).'
			'.$this->createControlButtonsBlock($artist, 'artist').'
		</article>
		';
		
		$js = [];
		if (isCurrentUserModerator())
		{
			$js[] = '/js/moderation/change-status-select.js';
		}
		
		$html .= $this->endRender
		(
			jsScriptUris: $js
		);
		
		echo $html;
	}
	
	final public function renderCharacterPage(array $character, array $games, array $songs): void
	{
		$html = $this->startRender
		(
			title: $character['transliterated_name'],
			cssSheetUris:
			[
				'/css/entity-page.css',
				'/css/shared/entity.css'
			]
		);
		
		$html .= 
		'
		<article>
			'.$this->createCharacter($character, 1).'
		';
		
		if ($games)
		{
			$html .= 
			'
			<section>
				'.$this->createHeading(\Localization\CharacterPage\RelatedGames, 2).'
			</section>
			'.$this->createGameList($games, 3, 'related-entity', 'character_game_relation_status').'
			';
		}
		
		if ($songs)
		{
			$html .= 
			'
			<section>
				'.$this->createHeading(\Localization\CharacterPage\RelatedSongs, 2).'
			</section>
			'.$this->createSongList($songs, 3, 'related-entity', 'song_artist_character_relation_status').'
			';
		}
		
		$html .= 
		'
			'.$this->createTimestampBlock($character).'
			'.$this->createControlButtonsBlock($character, 'character').'
		</article>
		';
		
		$js = [];
		if (isCurrentUserModerator())
		{
			$js[] = '/js/moderation/change-status-select.js';
		}
		
		$html .= $this->endRender
		(
			jsScriptUris: $js
		);
		
		echo $html;
	}
	
	final public function renderNoLyricsPage(array $album, array $song): void
	{
		$headingText = \Localization\LyricsPage\LyricsHeadingStart.
		               $song['transliterated_name'].
					   \Localization\LyricsPage\LyricsHeadingEnd;
		
		$html = $this->startRender
		(
			title: $headingText,     
			cssSheetUris:
			[
				'/css/no-lyrics-page.css',
				'/css/shared/entity.css'
			]
		);
		
		$html .= 
		'
		<article>
			'.$this->createLyricsPageHeading($headingText, $album, $song, null, null, null, null).'
			<section id="no-lyrics">
				<section>
					<p>'.\Localization\LyricsPage\NoLyricsAdded.'</p>
		';
		
		$button = $this->createButton
		(
			\Localization\LyricsPage\AddLyrics,
			buildInternalLink($this->language, 'album', $album['uri'], 'song', $song['uri'], 'add-lyrics'),
			!isCurrentUserVisitor(),
			\Localization\Tooltip\UserVisitor
		);
		
		$html .= 
		'
					<p>'.$button.'</p>
				</section>
			</section>
		</article>
		';
		
		$html .= $this->endRender();
		
		echo $html;
	}
	
	final public function renderLyricsPage
	(
		array      $album,
		array      $song,
		array|null $originalSong,
		array      $performers,
		array      $translations
	): void
	{
		$headingText = \Localization\LyricsPage\LyricsHeadingStart.
		               $song['transliterated_name'].
					   \Localization\LyricsPage\LyricsHeadingEnd;
		
		$heading = $this->createLyricsPageHeading
		(
			$headingText,
			$album,
			$song,
			null,
			$performers,
			$translations,
			$originalSong
		);
		
		if ($originalSong)
		{
			$songToShow = $originalSong;
			
			$parts    = explode('/', $_SERVER['REQUEST_URI']);
			$parts[3] = $originalSong['album_uri'];
			$parts[5] = $originalSong['uri'];
			
			$canonicalUri = implode('/', $parts);
		}
		else
		{
			$songToShow   = $song;
			$canonicalUri = $_SERVER['REQUEST_URI'];
		}
		
		$html = $this->startRender
		(
			title:        $headingText,
			canonicalUri: $canonicalUri,
			cssSheetUris:
			[
				'/css/lyrics-page.css',
				'/css/shared/entity.css'
			]
		);
		
		$html .= 
		'
		<article>
			'.$heading.'
			<section>
				'.$this->createSongLyrics($songToShow).'
				'.$this->createSongNotes($songToShow).'
				'.$this->createTimestampBlock($song).'
				'.$this->createControlButtonsBlockForSong($album, $song, $translations).'
			</section>
		</article>
		';
		
		$js = [];
		if (isCurrentUserModerator())
		{
			$js[] = '/js/moderation/change-status-select.js';
		}
		
		$html .= $this->endRender
		(
			jsScriptUris: $js
		);
		
		echo $html;
	}
	
	final public function renderTranslationPage
	(
		array      $album,
		array      $song,
		array|null $originalSong,
		array      $translation,
		array      $performers,
		array      $translations
	): void
	{
		$headingText = 
			\Localization\LyricsPage\TranslationHeadingStart.
			$song['transliterated_name'].
			\Localization\LyricsPage\TranslationHeadingMiddle.
			\Localization\Functions\localizeLanguageName($translation).
			\Localization\LyricsPage\TranslationHeadingEnd;
		
		$heading = $this->createLyricsPageHeading
		(
			$headingText,
			$album,
			$song,
			$translation,
			$performers,
			$translations,
			$originalSong
		);
		
		if ($originalSong)
		{
			$songToShow = $originalSong;
			
			$parts    = explode('/', $_SERVER['REQUEST_URI']);
			$parts[3] = $originalSong['album_uri'];
			$parts[5] = $originalSong['uri'];
			
			$canonicalUri = implode('/', $parts);
		}
		else
		{
			$songToShow   = $song;
			$canonicalUri = $_SERVER['REQUEST_URI'];
		}
		
		$html = $this->startRender
		(
			title:        $headingText,
			canonicalUri: $canonicalUri,
			cssSheetUris:
			[
				'/css/translation-page.css',
				'/css/shared/entity.css'
			]
		);
		
		$html .= 
		'
		<article>
			'.$heading.'
			<section>
				'.$this->createTranslationLyrics($translation).'
				'.$this->createSongLyrics($songToShow).'
				'.$this->createTranslationNotes($translation).'
				'.$this->createSongNotes($songToShow).'
				'.$this->createTimestampBlock($translation).'
				'.$this->createTimestampBlock($songToShow).'
				'.$this->createControlButtonsBlockForTranslation($album, $songToShow, $translation).'
				'.$this->createControlButtonsBlockForSong($album, $songToShow, $translations).'
			</section>
		</article>
		';
		
		$js = ['/js/translation-page.js'];
		if (isCurrentUserModerator())
		{
			$js[] = '/js/moderation/change-status-select.js';
		}
		
		$html .= $this->endRender
		(
			jsScriptUris: $js
		);
		
		echo $html;
	}
	
	final public function renderFeedbackPage(array $feedbacks, string $captchaBase64Image): void
	{
		$html = $this->startRender
		(
			title: \Localization\FeedbackPage\Heading,
			cssSheetUris:
			[
				'/css/feedback-page.css',
				'/css/shared/captcha.css'
			]
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading(\Localization\FeedbackPage\Heading, 1).'
				'.$this->createParagraph(\Localization\FeedbackPage\Introduction).'
				'.$this->createParagraph(\Localization\FeedbackPage\MessagePublic).'
				'.$this->createParagraph(\Localization\FeedbackPage\AboutAnswer).'
				'.$this->createParagraph(\Localization\FeedbackPage\SymbolLimit).'
				<form method="POST">
					<textarea name="message" rows="4" maxlength="500" placeholder="'.\Localization\Controls\Textarea.'" required></textarea>
					<section>
						<input type="text" name="captcha-code" id="captcha-input" onkeydown="return /[a-zA-Z0-9]/i.test(event.key)" placeholder="code:" required/>
						<img src="'.htmlspecialchars($captchaBase64Image).'" alt="captcha" id="captcha-image"/>
						<input type="submit" value="'.\Localization\FeedbackPage\Submit.'"/>
					</section>
				</form>
			</section>
		';
		
		foreach ($feedbacks as $feedback)
		{
			$author    = $feedback['sender_username'] ?? \Localization\FeedbackPage\AnonymousAuthor;
			$ip        = encryptData($feedback['sender_ip']);
			$timestamp = $feedback['message_timestamp'];
			$message   = $feedback['message'];
			$id        = isCurrentUserModerator() ? ' data-id="'.$feedback['id'].'"' : '';
			
			$html .= 
			'
			<section class="feedback-message"'.$id.'>
				'.$this->createHeading("$author #$ip ($timestamp)", 3).'
				'.$this->createParagraph($message).'
			';
			
			if ($feedback['moderator_username'])
			{
				$moderator = $feedback['moderator_username'];
				$timestamp = $feedback['reply_timestamp'];
				$reply     = $feedback['reply'];
				
				$html .= 
				'
				<section class="feedback-reply">
					'.$this->createHeading(\Localization\FeedbackPage\ReplyFromStaff."($timestamp)", 3).'
					'.$this->createParagraph($reply).'
				</section>
				';
			}
			
			if (isCurrentUserModerator())
			{
				$html .= 
				'
				<section class="feedback-reply-controls">
					<textarea></textarea>
				</section>
				<section class="feedback-reply-controls">
					<button class="delete-feedback-button" type="button">'.\Localization\FeedbackPage\Delete.'</button>
					<section class="filler"></section>
					<button class="send-reply-button" type="button">'.\Localization\FeedbackPage\SendReply.'</button>
				</section>
				';
			}
			
			$html .= 
			'
			</section>
			';
		}
		
		$html .= 
		'
		</article>
		';
		
		$js = [];
		if (isCurrentUserModerator())
		{
			$js[] = '/js/moderation/feedback.js';
		}
		
		$html .= $this->endRender
		(
			jsScriptUris: $js
		);
		
		echo $html;
	}
	
	final public function renderReportPage(string $entityType, string $entityName, string $entityLink)
	{
		switch ($entityType)
		{
			case 'game':
				$entityType = \Localization\ReportPage\Game;
				break;
			
			case 'album':
				$entityType = \Localization\ReportPage\Album;
				break;
			
			case 'artist':
				$entityType = \Localization\ReportPage\Artist;
				break;
			
			case 'character':
				$entityType = \Localization\ReportPage\Character;
				break;
			
			case 'song':
				$entityType = \Localization\ReportPage\Song;
				break;
			
			case 'lyrics':
				$entityType = \Localization\ReportPage\Lyrics;
				break;
			
			case 'translation':
				$entityType = \Localization\ReportPage\Translation;
				break;
				
			default:
				$entityType = '';
				break;
		}
		
		$heading = \Localization\ReportPage\Heading.htmlspecialchars($entityName).htmlspecialchars($entityType);
		$reportLink = buildInternalLink($this->language, 'report');
		
		$html = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/window-in-center-page.css']
		);
		
		$html .= 
		'
		<article>
			<section>
				<h1>'.$heading.'</h1>
				<p>'.\Localization\ReportPage\Introduction.'</p>
				<p>'.\Localization\ReportPage\AboutReportContent.'</p>
				<p>'.\Localization\ReportPage\NoActionWarning.'</p>
				<p>'.\Localization\ReportPage\ReplyOpportunity.'</p>
				<p>'.\Localization\ReportPage\Redirect.'</p>
				<form method="POST" autocomplete="off" action="'.$reportLink.'">
					<textarea name="report-text" rows="4" maxlength="250" placeholder="'.\Localization\Controls\Textarea.'" required></textarea></td>
					<section>
						'.$this->createButton(\Localization\Controls\Cancel, $entityLink).'
						<section class="filler"></section>
						<input type="submit" value="'.\Localization\Controls\Submit.'"/>
						<input type="hidden" name="entity-uri" value="'.$entityLink.'"/>
					</section>
				</form>
			</section>
		</article>
		';
		
		$html .= $this->endRender();
		
		echo $html;
	}
	
	final public function renderUserPage
	(
		array $userData,
		array $relatedGames,
		array $relatedAlbums,
		array $relatedArtists,
		array $relatedCharacters,
		array $relatedSongs,
		array $relatedTranslations
	): void
	{
		$headingText = \Localization\UserPage\User.htmlspecialchars($userData['user_username']);
		$role = \Localization\Functions\localizeLanguageName($userData);
		
		$games        = $this->createGameList($relatedGames, 3, 'related-entity');
		$albums       = $this->createAlbumList($relatedAlbums, 3, 'related-entity');
		$artists      = $this->createArtistList($relatedArtists, 3, 'related-entity');
		$characters   = $this->createCharacterList($relatedCharacters, 3, 'related-entity');
		$songs        = $this->createSongList($relatedSongs, 3, 'related-entity');
		$translations = $this->createTranslationList($relatedTranslations, 3, 'related-entity');
		
		$html = $this->startRender
		(
			title:        $headingText,
			cssSheetUris:
			[
				'/css/user-page.css',
				'/css/shared/entity.css'
			]
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading($headingText, 1).'
				'.$this->createParagraph(\Localization\UserPage\Role.htmlspecialchars($role)).'
		';
		
		if (isCurrentUser($userData['user_id']) || isCurrentUserModerator())
		{
			$href1 = buildInternalLink($this->language, 'user', $userData['user_username'], 'change-account-data');
			$href4 = buildInternalLink($this->language, 'user', $userData['user_username'], 'delete-account');
			
			$condition = !isCurrentUserViolator();
			$tooltip = \Localization\Tooltip\UserViolator;
			
			$html .= 
			'
				<section class="account-control">
					'.$this->createButton(\Localization\UserPage\ChangeAccountData, $href1, $condition, $tooltip).'
					<section class="filler"></section>
					<section class="filler"></section>
					'.$this->createButton(\Localization\UserPage\DeleteAccount,  $href4, $condition, $tooltip).'
				</section>
			';
		}
		
		$html .= 
		'
			</section>
			<section>
				'.$this->createHeading(\Localization\UserPage\Contributions, 2).'
			</section>
			<section>
				'.$this->createHeading(\Localization\UserPage\RelatedGames.count($relatedGames), 3).'
			</section>
			'.$games.'
			<section>
				'.$this->createHeading(\Localization\UserPage\RelatedAlbums.count($relatedAlbums), 3).'
			</section>
			'.$albums.'
			<section>
				'.$this->createHeading(\Localization\UserPage\RelatedArtists.count($relatedArtists), 3).'
			</section>
			'.$artists.'
			<section>
				'.$this->createHeading(\Localization\UserPage\RelatedCharacters.count($relatedCharacters), 3).'
			</section>
			'.$characters.'
			<section>
				'.$this->createHeading(\Localization\UserPage\RelatedSongs.count($relatedSongs), 3).'
			</section>
			'.$songs.'
			<section>
				'.$this->createHeading(\Localization\UserPage\RelatedTranslations.count($relatedTranslations), 3).'
			</section>
			'.$translations.'
			</section>
		</article>
		';
		
		$html .= $this->endRender();
		
		echo $html;
	}
	
	final public function renderAboutPage(): void
	{
		$html = $this->startRender
		(
			title: \Localization\AboutPage\Heading
		);
		
		$html .= 
		'
		<article>
			<section>
				<h1>'.\Localization\AboutPage\Heading.'</h1>
				<h2>'.\Localization\AboutPage\HeadingWhat.'</h2>
				<p>'.\Localization\AboutPage\What.'</p>
				<br/>
				<h2>'.\Localization\AboutPage\HeadingWhy.'</h2>
				<p>'.\Localization\AboutPage\Why.'</p>
				<br/>
				<h2>'.\Localization\AboutPage\HeadingHow.'</h2>
				<p>'.\Localization\AboutPage\How.'</p>
				<br/>
				<h2>'.\Localization\AboutPage\HeadingWho.'</h2>
				<p>'.\Localization\AboutPage\Who.'</p>
			</section>
		</article>
		';
		
		$html .= $this->endRender();
		
		echo $html;
	}
	
	final public function renderPolicyPage(): void
	{
		$html = $this->startRender
		(
			title: \Localization\PolicyPage\Heading
		);
		
		$html .= 
		'
		<article>
			<section>
				<h1>'.\Localization\PolicyPage\Heading.'</h1>
				<p>'.\Localization\PolicyPage\Introduction.'</p>
				<br/>
				<h2>'.\Localization\PolicyPage\Warning.'</h2>
				<br/>
				<h2>'.\Localization\PolicyPage\HeadingContent.'</h2>
				<p>'.\Localization\PolicyPage\ContentPolicy1.'</p>
				<p>'.\Localization\PolicyPage\ContentPolicy2.'</p>
				<p>'.\Localization\PolicyPage\ContentPolicy3.'</p>
				<p>'.\Localization\PolicyPage\ContentPolicy4.'</p>
				<p>'.\Localization\PolicyPage\ContentPolicy5.'</p>
				<p>'.\Localization\PolicyPage\ContentPolicy6.'</p>
				<p>'.\Localization\PolicyPage\ContentPolicy7.'</p>
				<p>'.\Localization\PolicyPage\ContentPolicy8.'</p>
				<p>'.\Localization\PolicyPage\ContentPolicy9.'</p>
				<p>'.\Localization\PolicyPage\ContentPolicy10.'</p>
				<br/>
				<h2>'.\Localization\PolicyPage\HeadingPrivacy.'</h2>
				<p>'.\Localization\PolicyPage\PrivacyPolicy1.'</p>
				<p>'.\Localization\PolicyPage\PrivacyPolicy2.'</p>
				<p>'.\Localization\PolicyPage\PrivacyPolicy3.'</p>
				<p>'.\Localization\PolicyPage\PrivacyPolicy4.'</p>
				<p>'.\Localization\PolicyPage\PrivacyPolicy5.'</p>
				<p>'.\Localization\PolicyPage\PrivacyPolicy6.'</p>
				<br/>
				<h2>'.\Localization\PolicyPage\HeadingRightholder.'</h2>
				<p>'.\Localization\PolicyPage\RightholderPolicy1.'</p>
				<p>'.\Localization\PolicyPage\RightholderPolicy2.'</p>
				<br/>
				<p>'.\Localization\PolicyPage\LastUpdated.'</p>
				<p>'.\Localization\PolicyPage\Timezone.'</p>
			</section>
		</article>
		';
		
		$html .= $this->endRender();
		
		echo $html;
	}
	
	final public function renderRulesPage(): void
	{
		$html = $this->startRender
		(
			title: \Localization\RulesPage\Heading
		);
		
		$html .= 
		'
		<article>
			<section>
				<h1>'.\Localization\RulesPage\Heading.'</h1>
				<h2>'.\Localization\RulesPage\HeadingGeneral.'</h2>
				<p>'.\Localization\RulesPage\GeneralRule1.'</p>
				<p>'.\Localization\RulesPage\GeneralRule2.'</p>
				<p>'.\Localization\RulesPage\GeneralRule3.'</p>
				<p>'.\Localization\RulesPage\GeneralRule4.'</p>
				<p>'.\Localization\RulesPage\GeneralRule5.'</p>
				<p>'.\Localization\RulesPage\GeneralRule6.'</p>
				<p>'.
					\Localization\RulesPage\GeneralRule7a.
					'<a href="/'.$this->language.'/writing-guide" target="_blank">'.
					\Localization\RulesPage\GeneralRule7b.'</a>'.
					\Localization\RulesPage\GeneralRule7c.
				'</p>
				<p>'.\Localization\RulesPage\GeneralRule8.'</p>
				<br/>
				<h2>'.\Localization\RulesPage\HeadingAccess.'</h2>
				<p>'.\Localization\RulesPage\AccessRule1.'</p>
				<p>'.\Localization\RulesPage\AccessRule2.'</p>
				<p>'.\Localization\RulesPage\AccessRule3.'</p>
				<p>'.\Localization\RulesPage\AccessRule4.'</p>
				<p>'.\Localization\RulesPage\AccessRule5.'</p>
				<p>'.\Localization\RulesPage\AccessRule6.'</p>
				<p>'.\Localization\RulesPage\AccessRule7.'</p>
				<p>'.\Localization\RulesPage\AccessRule8.'</p>
			</section>
		</article>
		';
		
		$html .= $this->endRender();
		
		echo $html;
	}
	
	final public function renderWritingGuidePage(): void
	{
		$html = $this->startRender
		(
			title:        \Localization\WritingGuidePage\Heading,
			cssSheetUris: ['/css/writing-guide-page.css']
		);
		
		$html .= 
		'
		<article>
			<section>
				<h1>'.\Localization\WritingGuidePage\Heading.'</h1>
				
				<p>'.\Localization\WritingGuidePage\Contents.'</p>
				<p>- <a href="#lyrics">'.\Localization\WritingGuidePage\LyricsLink.'</a></p>
				<p>- <a href="#translation">'.\Localization\WritingGuidePage\TranslationLink.'</a></p>
				<p>- <a href="#romanization">'.\Localization\WritingGuidePage\RomanizationLink.'</a></p>
				<p>- <a href="#formatting">'.\Localization\WritingGuidePage\FormattingLink.'</a></p>
				<p>- <a href="#captcha">'.\Localization\WritingGuidePage\CaptchaLink.'</a></p>
				<br/>
				
				<h2 id="lyrics">'.\Localization\WritingGuidePage\HeadingLyrics.'</h2>
				<p>'.\Localization\WritingGuidePage\LyricsIntroduction1.'</p>
				<p>'.\Localization\WritingGuidePage\LyricsIntroduction2.'</p>
				<p>'.\Localization\WritingGuidePage\LyricsIntroduction3.'</p>
				<p>'.\Localization\WritingGuidePage\LyricsIntroduction4.'</p>
				<br/>
				
				<p>'.\Localization\WritingGuidePage\LyricsHeadsUp.'</p>
				<p>'.\Localization\WritingGuidePage\LyricsRule1.'</p>
				<p>'.\Localization\WritingGuidePage\LyricsRule2.'</p>
				<p>'.\Localization\WritingGuidePage\LyricsRule3.'</p>
				<p>'.\Localization\WritingGuidePage\LyricsRule4.'</p>
				<p>'.\Localization\WritingGuidePage\LyricsRule5.'</p>
				<p>'.\Localization\WritingGuidePage\LyricsRule6.'</p>
				<p>'.\Localization\WritingGuidePage\LyricsRule7.'</p>
				<p>'.\Localization\WritingGuidePage\LyricsRule8.'</p>
				<p>'.\Localization\WritingGuidePage\LyricsRule9.'</p>
				<br/>
				
				<h2 id="translation">'.\Localization\WritingGuidePage\HeadingTranslation.'</h2>
				<p>'.\Localization\WritingGuidePage\TranslationIntroduction.'<p>
				<p>'.\Localization\WritingGuidePage\TranslationRemember1.'</p>
				<p>'.\Localization\WritingGuidePage\TranslationRemember2.'</p>
				<p>'.\Localization\WritingGuidePage\TranslationRemember3.'</p>
				<p>'.\Localization\WritingGuidePage\TranslationRemember4.'</p>
				<p>'.\Localization\WritingGuidePage\TranslationRemember5.'</p>
				<p>'.\Localization\WritingGuidePage\TranslationRemember6.'</p>
				<p>'.\Localization\WritingGuidePage\TranslationRemember7.'</p>
				<p>'.\Localization\WritingGuidePage\TranslationRemember8.'</p>
				<br/>
				
				<p>'.\Localization\WritingGuidePage\TranslationHeadsUp.'</p>
				<p>'.\Localization\WritingGuidePage\TranslationRule1.'</p>
				<p>'.\Localization\WritingGuidePage\TranslationRule2.'</p>
				<p>'.\Localization\WritingGuidePage\TranslationRule3.'</p>
				<p>'.\Localization\WritingGuidePage\TranslationRule4.'</p>
				<p>'.\Localization\WritingGuidePage\TranslationRule5.'</p>
				<p>'.\Localization\WritingGuidePage\TranslationRule6.'</p>
				<p>'.\Localization\WritingGuidePage\TranslationLanguages.'</p>
				<br/>
				
				<h2 id="romanization">'.\Localization\WritingGuidePage\HeadingRomanization.'</h2>
				<p>'.\Localization\WritingGuidePage\RomanizationIntroduction.'</p>
				<p>'.\Localization\WritingGuidePage\RomanizationJapanese1.'</p>
				<p>'.\Localization\WritingGuidePage\RomanizationJapanese2.'</p>
				<br/>
				
				<h3>'.\Localization\WritingGuidePage\HeadingAllowedSymbols.'</h3>
				<p>'.\Localization\WritingGuidePage\AllowedSymbols.'</p>
				<br/>
				
				<h3>'.\Localization\WritingGuidePage\HeadingNameOrder.'</h3>
				<p>'.\Localization\WritingGuidePage\NameOrder.'</p>
				<br/>
				
				<h3>'.\Localization\WritingGuidePage\HeadingCapitalization.'</h3>
				<p>'.\Localization\WritingGuidePage\Capitalization.'</p>
				<br/>
				
				<h3>'.\Localization\WritingGuidePage\KanaConversionRules.'</h3>
				<section class="table-wrapper">
					<table class="kana-table">
						<tbody>
							<tr>
								<th></th>
								<th>－ａ</th>
								<th>－ｉ</th>
								<th>－ｕ</th>
								<th>－ｅ</th>
								<th>－ｏ</th>
								<th>－ｙａ</th>
								<th>－ｙｕ</th>
								<th>－ｙｏ</th>
								<th>－</th>
							</tr>
							<tr>
								<th>－</th>
								<td><ruby>あ<rt>a</rt></ruby></td>
								<td><ruby>い<rt>o</rt></ruby></td>
								<td><ruby>う<rt>u</rt></ruby></td>
								<td><ruby>え<rt>e</rt></ruby></td>
								<td><ruby>い<rt>i</rt></ruby></td>
								<td><ruby>や<rt>ya</rt></ruby></td>
								<td><ruby>ゆ<rt>yu</rt></ruby></td>
								<td><ruby>よ<rt>yo</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｗ－</th>
								<td><ruby>わ<rt>wa</rt></ruby></td>
								<td><ruby>ゐ／うぃ<rt>wi</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby>ゑ／うぇ<rt>we</rt></ruby></td>
								<td><ruby>を<rt>wo</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｒ－</th>
								<td><ruby>ら<rt>ra</rt></ruby></td>
								<td><ruby>り<rt>ri</rt></ruby></td>
								<td><ruby>る<rt>ru</rt></ruby></td>
								<td><ruby>れ<rt>re</rt></ruby></td>
								<td><ruby>ろ<rt>ro</rt></ruby></td>
								<td><ruby>りゃ<rt>rya</rt></ruby></td>
								<td><ruby>りゅ<rt>ryu</rt></ruby></td>
								<td><ruby>りょ<rt>ryo</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｍ－</th>
								<td><ruby>ま<rt>ma</rt></ruby></td>
								<td><ruby>み<rt>mi</rt></ruby></td>
								<td><ruby>む<rt>mu</rt></ruby></td>
								<td><ruby>め<rt>me</rt></ruby></td>
								<td><ruby>も<rt>mo</rt></ruby></td>
								<td><ruby>みゃ<rt>mya</rt></ruby></td>
								<td><ruby>みゅ<rt>myu</rt></ruby></td>
								<td><ruby>みょ<rt>myo</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｈ－</th>
								<td><ruby>は<rt>ha</rt></ruby></td>
								<td><ruby>ひ<rt>hi</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby>へ<rt>he</rt></ruby></td>
								<td><ruby>ほ<rt>ho</rt></ruby></td>
								<td><ruby>ひゃ<rt>hya</rt></ruby></td>
								<td><ruby>ひゅ<rt>hyu</rt></ruby></td>
								<td><ruby>ひょ<rt>hyo</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｐ－</th>
								<td><ruby>ぱ<rt>pa</rt></ruby></td>
								<td><ruby>ぴ<rt>pi</rt></ruby></td>
								<td><ruby>ぷ<rt>pu</rt></ruby></td>
								<td><ruby>ぺ<rt>pe</rt></ruby></td>
								<td><ruby>ぽ<rt>po</rt></ruby></td>
								<td><ruby>ぴゃ<rt>pya</rt></ruby></td>
								<td><ruby>ぴゅ<rt>pyu</rt></ruby></td>
								<td><ruby>ぴょ<rt>pyo</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｂ－</th>
								<td><ruby>ば<rt>ba</rt></ruby></td>
								<td><ruby>び<rt>bi</rt></ruby></td>
								<td><ruby>ぶ<rt>bu</rt></ruby></td>
								<td><ruby>べ<rt>be</rt></ruby></td>
								<td><ruby>ぼ<rt>bo</rt></ruby></td>
								<td><ruby>びゃ<rt>bya</rt></ruby></td>
								<td><ruby>びゅ<rt>byu</rt></ruby></td>
								<td><ruby>びょ<rt>byo</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｆ－</th>
								<td><ruby>ふぁ<rt>fa</rt></ruby></td>
								<td><ruby>ふぃ<rt>fi</rt></ruby></td>
								<td><ruby>ふ<rt>fu</rt></ruby></td>
								<td><ruby>ふぇ<rt>fe</rt></ruby></td>
								<td><ruby>ふぉ<rt>fo</rt></ruby></td>
								<td><ruby>ふゃ<rt>fya</rt></ruby></td>
								<td><ruby>ふゅ<rt>fyu</rt></ruby></td>
								<td><ruby>ふょ<rt>fyo</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｎ－</th>
								<td><ruby>な<rt>na</rt></ruby></td>
								<td><ruby>に<rt>ni</rt></ruby></td>
								<td><ruby>ぬ<rt>nu</rt></ruby></td>
								<td><ruby>ね<rt>ne</rt></ruby></td>
								<td><ruby>の<rt>no</rt></ruby></td>
								<td><ruby>にゃ<rt>nya</rt></ruby></td>
								<td><ruby>にゅ<rt>nyu</rt></ruby></td>
								<td><ruby>にょ<rt>nyo</rt></ruby></td>
								<td><ruby>ん<rt>n / n\'</rt></ruby></td>
							</tr>
							<tr>
								<th>ｔ－</th>
								<td><ruby>た<rt>ta</rt></ruby></td>
								<td><ruby>てぃ<rt>ti</rt></ruby></td>
								<td><ruby>とぅ<rt>tu</rt></ruby></td>
								<td><ruby>て<rt>te</rt></ruby></td>
								<td><ruby>と<rt>to</rt></ruby></td>
								<td><ruby>てゃ<rt>tya</rt></ruby></td>
								<td><ruby>てゅ<rt>tyu</rt></ruby></td>
								<td><ruby>てょ<rt>tyo</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｄ－</th>
								<td><ruby>だ<rt>da</rt></ruby></td>
								<td><ruby>でぃ<rt>di</rt></ruby></td>
								<td><ruby>どぅ<rt>du</rt></ruby></td>
								<td><ruby>で<rt>de</rt></ruby></td>
								<td><ruby>ど<rt>do</rt></ruby></td>
								<td><ruby>でゃ<rt>dya</rt></ruby></td>
								<td><ruby>でゅ<rt>dyu</rt></ruby></td>
								<td><ruby>でょ<rt>dyo</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｓ－</th>
								<td><ruby>さ<rt>sa</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby>す<rt>su</rt></ruby></td>
								<td><ruby>せ<rt>se</rt></ruby></td>
								<td><ruby>そ<rt>so</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｚ－</th>
								<td><ruby>ざ<rt>za</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby>ず／づ<rt>zu</rt></ruby></td>
								<td><ruby>ぜ<rt>ze</rt></ruby></td>
								<td><ruby>ぞ<rt>zo</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｋ－</th>
								<td><ruby>か<rt>ka</rt></ruby></td>
								<td><ruby>き<rt>ki</rt></ruby></td>
								<td><ruby>く<rt>ku</rt></ruby></td>
								<td><ruby>け<rt>ke</rt></ruby></td>
								<td><ruby>こ<rt>ko</rt></ruby></td>
								<td><ruby>きゃ<rt>kya</rt></ruby></td>
								<td><ruby>きゅ<rt>kyu</rt></ruby></td>
								<td><ruby>きょ<rt>kyo</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｇ－</th>
								<td><ruby>が<rt>ga</rt></ruby></td>
								<td><ruby>ぎ<rt>gi</rt></ruby></td>
								<td><ruby>ぐ<rt>gu</rt></ruby></td>
								<td><ruby>げ<rt>ge</rt></ruby></td>
								<td><ruby>ご<rt>go</rt></ruby></td>
								<td><ruby>ぎゃ<rt>gya</rt></ruby></td>
								<td><ruby>ぎゅ<rt>gyu</rt></ruby></td>
								<td><ruby>ぎょ<rt>gyo</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｔｓ－</th>
								<td><ruby>つぁ<rt>tsa</rt></ruby></td>
								<td><ruby>つぃ<rt>tsi</rt></ruby></td>
								<td><ruby>つ<rt>tsu</rt></ruby></td>
								<td><ruby>つぇ<rt>tse</rt></ruby></td>
								<td><ruby>つぉ<rt>tso</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｃｈ－</th>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby>ち<rt>chi</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby>ちぇ<rt>che</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby>ちゃ<rt>cha</rt></ruby></td>
								<td><ruby>ちゅ<rt>chu</rt></ruby></td>
								<td><ruby>ちょ<rt>cho</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｓｈ－</th>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby>し<rt>shi</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby>しぇ<rt>she</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby>しゃ<rt>sha</rt></ruby></td>
								<td><ruby>しゅ<rt>shu</rt></ruby></td>
								<td><ruby>しょ<rt>sho</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｊ－</th>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby>ぢ／じ<rt>ji</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby>ぢぇ／じぇ<rt>je</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
								<td><ruby>ぢゃ／じゃ<rt>ja</rt></ruby></td>
								<td><ruby>ぢゅ／じゅ<rt>ju</rt></ruby></td>
								<td><ruby>ぢょ／じょ<rt>jo</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
							<tr>
								<th>ｖ－</th>
								<td><ruby>ゔぁ<rt>va</rt></ruby></td>
								<td><ruby>ゔぃ<rt>vi</rt></ruby></td>
								<td><ruby>ゔ<rt>vu</rt></ruby></td>
								<td><ruby>ゔぇ<rt>ve</rt></ruby></td>
								<td><ruby>ゔぉ<rt>vo</rt></ruby></td>
								<td><ruby>ゔゃ<rt>vya</rt></ruby></td>
								<td><ruby>ゔゅ<rt>vyu</rt></ruby></td>
								<td><ruby>ゔょ<rt>vyo</rt></ruby></td>
								<td><ruby><rt></rt></ruby></td>
							</tr>
						</tbody>
					</table>
				</section>
				<p>'.\Localization\WritingGuidePage\KanaConversionNote.'</p>
				<p>1. <span class="highlight">ん</span>＋あ/い/う/え/お/や/ゆ/よ　→　<span class="highlight">n\'</span>＋a/i/u/e/o/ya/yu/yo;</p>
				<p>2. か<span class="highlight">あ</span> ＝ か<span class="highlight">ー</span> ＝ か<span class="highlight">ぁ</span> → ka<span class="highlight">a</span></p>
				<p>3. <span class="highlight">っ</span>か　→　<span class="highlight">k</span>ka</p>
				<p>---- <span class="highlight">っ</span>ち　→　<span class="highlight">c</span>chi, <span class="highlight">っ</span>し　→　<span class="highlight">s</span>shi, <span class="highlight">っ</span>つ　→　<span class="highlight">t</span>tsu</p>
				<p>4. 私<span class="highlight">は</span>、私<span class="highlight">へ</span>　→　watashi <span class="highlight">wa</span>, watashi <span class="highlight">e</span></p>
				<p>5. おう、えい　→　ou, ei</p>
				<p>6. カタカナ ＝ ひらがな</p>
				<br/>
				
				<h3>'.\Localization\WritingGuidePage\KanaLanguageRules.'</h3>
				<p>'.\Localization\WritingGuidePage\KanaLanguageRule1.'</p>
				<p>---- 「<span class="highlight">愛愛しい</span>」は日本語の言葉。　→　"<span class="highlight">Aiaishii</span>" wa nihongo no kotoba.</p>
				<p>'.\Localization\WritingGuidePage\KanaLanguageRule2.'</p>
				<p>---- 「<span class="highlight">らぶらぶ</span>」は日本語の言葉。　→　"<span class="highlight">Raburabu</span>" wa nihongo no kotoba.</p>
				<p>---- 「<span class="highlight">ラブラブ</span>」は日本語の言葉。　→　"<span class="highlight">Raburabu</span>" wa nihongo no kotoba.</p>
				<p>---- <span class="mistake">「<span class="highlight">ラブラブ</span>」は日本語の言葉。　→　"<span class="highlight">Love-love</span>" wa nihongo no kotoba.</span></p>
				<p>---- らぶらぶ ＝ ラブラブ　→　raburabu</p>
				<p>'.\Localization\WritingGuidePage\KanaLanguageRule3.'</p>
				<p>---- 「<span class="highlight">love-love</span>」は英語の言葉。　→　"<span class="highlight">LOVE-LOVE</span>" wa eigo no kotoba.</p>
				<p>---- 「<span class="highlight">любовь-морковь</span>」はロシア語の言葉。　→　"<span class="highlight">ЛЮБОВЬ-МОРКОВЬ</span>" wa roshiago no kotoba.</p>
				<p>---- らぶらぶ ＝ ラブラブ ≉ love-love</p>
				<br/>
				
				<h3>'.\Localization\WritingGuidePage\ParticlesRules.'</h3>
				<p>1. <span class="highlight">こ…、そ…、あ…</span>： '.\Localization\WritingGuidePage\Together.'</p>
				<p>---- ここ　→　koko</p>
				<p>---- それ　→　sore</p>
				<p>---- あの　→　ano</p>
				<p>2. <span class="highlight">〇＋は、じゃ、へ、と、が、か、を、に、まで、で、も、から、って、の、…</span>： '.\Localization\WritingGuidePage\Apart.'</p>
				<p>---- わたしは　→　watashi wa</p>
				<p>---- なにかの　→　nani ka no</p>
				<p>---- だって　→　da tte</p>
				<p>---- だから　→　da kara</p>
				<p>---- そうじゃない　→　sou ja nai</p>
				<p>---- ここに　→　koko ni</p>
				<p>---- それでも　→　sore de mo</p>
				<p>---- 駅へと　→　eki e to</p>
				<p>---- 日本語だけ　→　nihongo dake</p>
				<p>---- 晴れのち　→　hare nochi</p>
				<p>---- 来るまで　→　kuru made</p>
				<p>---- 使ってもいけない　→　tsukatte mo ikenai</p>
				<p>---- 幸福なのに　→　koufuku na no ni</p>
				<p>---- ように　→　you ni</p>
				<p>3. <span class="highlight">〇＋ん、の、よ、ね、わ、…</span>： '.\Localization\WritingGuidePage\Apart.'</p>
				<p>---- あるんだ　→　aru n da</p>
				<p>---- あるの　→　aru no</p>
				<p>---- そうだよね　→　sou da yo ne</p>
				<p>---- あるわ　→　aru wa</p>
				<p>4. <span class="highlight">〇＋な</span>： '.\Localization\WritingGuidePage\Together.'</p>
				<p>---- すてきな　→　sutekina</p>
				<p>---- 自由な　→　jiyuuna</p>
				<p>---- ような　→　youna</p>
				<p>5. <span class="highlight">〇＋たち、ら、ちゃん、くん、さん、だらけ、らしい、…</span>： '.\Localization\WritingGuidePage\Together.'</p>
				<p>---- 私達　→　watashitachi</p>
				<p>---- 僕ら　→　bokura</p>
				<p>---- 神様　→　kamisama</p>
				<p>---- 自分らしい　→　jibunrashii</p>
				<p>---- 花子ちゃん　→　Hanakochan</p>
				<p>---- 体中　→　karadajuu</p>
				<p>6. <span class="highlight">だい、…＋〇</span>： '.\Localization\WritingGuidePage\Together.'</p>
				<p>---- 大嫌い　→　daikirai</p>
				<p>7. <span class="highlight">〇＋時、分、秒、センチ、ミリ…</span>： '.\Localization\WritingGuidePage\Together.'</p>
				<p>---- １０時１７分　→　juuji juunanafun</p>
				<p>---- １５センチ　→　juugosenchi</p>
				<p>---- ゼロ秒　→　zerobyou</p>
				<p>---- ３６０°　→　sanbyakurokujuudo</p>
				<p>8. <span class="highlight">〇＋〇</span>： '.\Localization\WritingGuidePage\Together.'</p>
				<p>---- 使い方　→　tsukaikata</p>
				<p>---- 言い訳　→　iiwake</p>
				<p>---- 目覚め　→　mezame</p>
				<p>---- 夕立　→　yuudachi</p>
				<p>---- 今夜　→　kon\'ya</p>
				<p>---- キラキラ　→　kirakira</p>
				<p>---- 懐中時計　→　kaichuudokei</p>
				<p>9. <span class="highlight">お、ご＋〇</span>： '.\Localization\WritingGuidePage\Together.'</p>
				<p>---- お電話　→　odenwa</p>
				<p>---- ご家族　→　gokazoku</p>
				<p>---- ご主人様　→　goshujinsama</p>
				<p>10. <span class="highlight">〇＋する</span>： '.\Localization\WritingGuidePage\Together.'</p>
				<p>---- 電話する　→　denwasuru</p>
				<p>---- お電話できます　→　odenwadekimasu</p>
				<p>11. <span class="highlight">〇ながら、〇て、〇たら、〇たり、〇ちゃう、〇べき…</span>： '.\Localization\WritingGuidePage\Together.'</p>
				<p>---- 働きながら　→　hatarakinagara</p>
				<p>---- 飲んで　→　nonde</p>
				<p>---- したら　→　shitara</p>
				<p>---- 来たり　→　kitari</p>
				<p>---- 笑っちゃった　→　waracchatta</p>
				<p>---- あるべき　→　arubeki</p>
				<p>12. <span class="highlight">〇たり＋する</span>： '.\Localization\WritingGuidePage\Apart.'</p>
				<p>---- 来たりする　→　kitari suru</p>
				<p>13. <span class="highlight">〇い＋〇う、〇え＋〇う</span>： '.\Localization\WritingGuidePage\Together.'</p>
				<p>---- 歩き出す　→　arukidasu</p>
				<p>---- 信じ続く　→　shinjitsuzuku</p>
				<p>---- 燃え立つ　→　moetatsu</p>
				<p>14. <span class="highlight">〇て＋〇う</span>： '.\Localization\WritingGuidePage\Apart.'</p>
				<p>---- 働いて疲れる　→　hataraite tsukareru</p>
				<p>---- 教えてあげる　→　oshiete ageru</p>
				<p>---- 教えてくれた　→　oshiete kureta</p>
				<p>---- 落ちてしまう　→　ochite shimau</p>
				<p>---- 歩いていく　→　aruite iku</p>
				<p>---- 書いてある　→　kaite aru</p>
				<p>---- 見ている　→　mite iru</p>
				<p>---- 見ていた　→　mite ita</p>
				<p>15. <span class="highlight">〇て＋る、〇て＋く</span>： '.\Localization\WritingGuidePage\Together.'</p>
				<p>---- 歩いてく　→　aruiteku</p>
				<p>---- 見てる　→　miteru</p>
				<p>---- 見てた　→　miteta</p>
				<p>16. <span class="highlight">〇ず＋に</span>： '.\Localization\WritingGuidePage\Apart.'</p>
				<p>---- 後悔せずに　→　koukaisezu ni</p>
				<p>---- 思わずに　→　omowazu ni</p>
				<p>17. <span class="highlight">'.\Localization\WritingGuidePage\DifferenceInMeaning.'</span></p>
				<p>---- まるで　→　marude</p>
				<p>---- 丸で　→　maru de</p>
				<p>18. <span class="highlight">'.\Localization\WritingGuidePage\SpecialReadings.'</span></p>
				<p>---- 未来　→　mirai</p>
				<p>---- <ruby>未来<rt>あした</rt></ruby>　→　ashita</p>
				<p>19. <span class="highlight">'.\Localization\WritingGuidePage\DivisionBySyllables.'</span></p>
				<p>---- セ・ツ・ナ・イ　→　se-tsu-na-i</p>
				<br/>
				
				<h2 id="formatting">'.\Localization\WritingGuidePage\HeadingFormatting.'</h2>
				<p>'.\Localization\WritingGuidePage\FormattingIntroduction.'</p>
				<p>'.\Localization\WritingGuidePage\FormattingEntity1.'</p>
				<p>'.\Localization\WritingGuidePage\FormattingEntity2.'</p>
				<p>'.\Localization\WritingGuidePage\FormattingEntity3.'</p>
				
				<p>'.\Localization\WritingGuidePage\FormattingFurigana1.'</p>
				<p>---- <span class="highlight">{kj}*{fg}*{/fg}</span>'.\Localization\WritingGuidePage\FormattingFurigana2.'</p>
				<p>---- '.\Localization\WritingGuidePage\FormattingFurigana3.' {kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby></p>
				<p>---- '.\Localization\WritingGuidePage\FormattingFurigana4.'</p>
				
				<p>'.\Localization\WritingGuidePage\FormattingColor1.'</p>
				<p>---- <span class="highlight">{cl #XXXXXX}*{/cl}</span>'.\Localization\WritingGuidePage\FormattingColor2.'</p>
				<p>---- '.\Localization\WritingGuidePage\FormattingColor3.' {cl #FF00FF}色づいたテキスト{/cl}　→　<span class="color">色づいたテキスト</span></p>
				<p>---- '.\Localization\WritingGuidePage\FormattingColor4.'</p>
				
				<p>'.\Localization\WritingGuidePage\FormattingNotes1.'</p>
				<p>---- <span class="highlight">{nt}N{/nt}</span>'.\Localization\WritingGuidePage\FormattingNotes2.'</p>
				<p>---- '.\Localization\WritingGuidePage\FormattingNotes3.' click me{nt}1{/nt}　→　click me<a class="note" href="#formatting">[1]</a></p>
				<p>---- '.\Localization\WritingGuidePage\FormattingNotes4.'</p>
				
				<p>'.\Localization\WritingGuidePage\FormattingExample1.'<a href="lyrics-example" target="_blank">'.\Localization\WritingGuidePage\FormattingExample2.'</a>'.\Localization\WritingGuidePage\FormattingExample3.'</p>
				
				<p>'.\Localization\WritingGuidePage\FormattingWarnings.'</p>
				<p>'.\Localization\WritingGuidePage\FormattingWarning1.'</p>
				<p>'.\Localization\WritingGuidePage\FormattingWarning2.'</p>
				<p>'.\Localization\WritingGuidePage\FormattingWarning3.'</p>
				<p>'.\Localization\WritingGuidePage\FormattingWarning4.'</p>
				<p>'.\Localization\WritingGuidePage\FormattingWarning5.'</p>
				<br/>
				
				<h2 id="captcha">'.\Localization\WritingGuidePage\HeadingCaptcha.'</h2>
				<section><img src="/assets/static-images/captcha-solution.png" /></section>
			</section>
		</article>
		';
		
		$html .= $this->endRender();
		
		echo $html;
	}
	
	final public function renderLyricsExamplePage(): void
	{
		$html = $this->startRender
		(
			title:        \Localization\LyricsExamplePage\Heading,
			cssSheetUris: ['/css/translation-page.css']
		);
		
		$html .=
		'
		<article>
			<section>
				<h1>'.\Localization\LyricsExamplePage\Heading.'</h1>
			</section>
			<section>
				<section>
					<h2>'.\Localization\LyricsExamplePage\Formatting.'</h2>
					<h3>'.\Localization\LyricsExamplePage\Markup.'</h3>
					
					<span class="text-line">例えば、コメントを書いてほしいなら</span>
					<span class="text-line">以下の通りでリリックを飾ってください</span>
					<br/>
					
					<span class="text-line">「いろは」は分かりにくい歌{nt}1{/nt}</span>
					<br/>
					
					<span class="text-line">ｗｉｋｉｐｅｄｉａ{nt}2{/nt}で見つかった歌は：</span>
					<br/>
					
					<span class="text-line">{cl #FF0000}いろはにほへと{/cl}</span>
					<span class="text-line">{cl #FF7F00}ちりぬるをわ{/cl}か</span>
					<span class="text-line">{cl #FFFF00}よたれそつ{/cl}ねな</span>
					<span class="text-line">{cl #00FF00}らむうゐ{/cl}のおく</span>
					<span class="text-line">{cl #0000FF}やまけ{/cl}ふこえて</span>
					<span class="text-line">{cl #4B0082}あさ{/cl}きゆめみし</span>
					<span class="text-line">{cl #9400D3}ゑ{/cl}ひもせす……</span>
					<br/>
					
					<span class="text-line">あらら、歌のテキストに漢字がない！</span>
					<span class="text-line">どうすればいいかなぁ？</span>
					<span class="text-line">あ、いい考えがあるわ。</span>
					<br/>
					
					<span class="text-line">フリガナは</span>
					<span class="text-line">…難しい漢字と…</span>
					<span class="text-line">…珍しい漢字と…</span>
					<span class="text-line">…曖昧な読み方の漢字…</span>
					<span class="text-line">…の書き方で、</span>
					<span class="text-line">きっと{kj}役{fg}やく{/fg}に立つものなの！</span>
				</section>
				
				<section>
					<h2>'.\Localization\LyricsExamplePage\Result.'</h2>
					<h3>'.\Localization\LyricsExamplePage\Japanese.'</h3>
					
					<span class="text-line">例えば、コメントを書いてほしいなら</span>
					<span class="text-line">以下の通りでリリックを飾ってください</span>
					<br/>
					
					<span class="text-line">「いろは」は分かりにくい歌<a class="note-small" id="lyrics-reference-1" href="#lyrics-note-1">[1]</a></span>
					<br/>
					
					<span class="text-line">ｗｉｋｉｐｅｄｉａ<a class="note-small" id="lyrics-reference-2" href="#lyrics-note-2">[2]</a>で見つかった歌は：</span>
					<br/>
					
					<span class="text-line"><span style="color: #FF0000">いろはにほへと</span></span>
					<span class="text-line"><span style="color: #FF7F00">ちりぬるをわ</span>か</span>
					<span class="text-line"><span style="color: #FFFF00">よたれそつ</span>ねな</span>
					<span class="text-line"><span style="color: #00FF00">らむうゐ</span>のおく</span>
					<span class="text-line"><span style="color: #0000FF">やまけ</span>ふこえて</span>
					<span class="text-line"><span style="color: #4B0082">あさ</span>きゆめみし</span>
					<span class="text-line"><span style="color: #9400D3">ゑ</span>ひもせす……</span>
					<br/>
					
					<span class="text-line">あらら、歌のテキストに漢字がない！</span>
					<span class="text-line">どうすればいいかなぁ？</span>
					<span class="text-line">あ、いい考えがあるわ。</span>
					<br/>
					
					<span class="text-line">フリガナは</span>
					<span class="text-line">…難しい漢字と…</span>
					<span class="text-line">…珍しい漢字と…</span>
					<span class="text-line">…曖昧な読み方の漢字…</span>
					<span class="text-line">…の書き方で、</span>
					<span class="text-line">きっと<ruby>役<rt>やく</rt></ruby>に立つものなの！</span>
				</section>
				
				<section>
					<h2>'.\Localization\LyricsExamplePage\Notes.'</h2>
					<span class="text-line">{nt}1{/nt}　「{cl #FF9090}いろは{/cl}」は歌でひらがなのまとめ方です</span>
					<span class="text-line">{nt}2{/nt}　インターネットにある{kj}百科事典{fg}ひゃっかじてん{/fg}</span>
				</section>
				
				<section>
					<h2>'.\Localization\LyricsExamplePage\Notes.'</h2>
					<span class="text-line"><a class="note-big" id="lyrics-note-1" href="#lyrics-reference-1">[1]</a>　「<span style="color: #FF9090">いろは</span>」は歌でひらがなのまとめ方です</span>
					<span class="text-line"><a class="note-big" id="lyrics-note-2" href="#lyrics-reference-2">[2]</a>　インターネットにある<ruby>百科事典<rt>ひゃっかじてん</rt></ruby></span>
				</section>
			</section>
		</article>
		';
		
		$html .= $this->endRender(['/js/translation-page.js']);
		
		echo $html;
	}
}
