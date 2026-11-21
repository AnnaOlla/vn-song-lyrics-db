<?php

require_once 'views/error-view.php';

class VisitorView extends ErrorView
{
	public function __construct(string $language)
	{
		parent::__construct($language);
	}
	
	//---------------------------------------//
	//       Content Pages: Pagination       //
	//---------------------------------------//
	
	private function createResultsLimitBlock(int|null $limit): string
	{
		$selectedOption = ['to_show' => $limit, 'to_send' => $limit];
		
		$options =
		[
			['to_show' => 10,  'to_send' => 10],
			['to_show' => 15,  'to_send' => 15],
			['to_show' => 20,  'to_send' => 20],
			['to_show' => 25,  'to_send' => 25],
			['to_show' => 30,  'to_send' => 30]
		];
		
		// sort nulls last
		if (!in_array($selectedOption, $options) && $limit !== null)
		{
			$options[] = $selectedOption;
			usort
			(
				$options,
				function($a, $b)
				{
					if ($a === null)
						return +1;
					if ($b === null)
						return -1;
					return $a <=> $b;
				}
			);
		}
		
		$select = $this->createSearchableSelect
		(
			iteratedOptions:         $options,
			selectedOption:          $selectedOption,
			addEmptyOption:          false,
			keyToShownValue:         'to_show',
			keyToSentValue:          'to_send',
			attributesForSentInput:  ['id' => 'limit-result-count-bar'],
			attributesForShownInput: [
										 'data-placeholder-filter' => __('base.controls.select.hint_idle'),
										 'readonly' => true
									 ]
		);
		
		return
		'
			<section>'.__('visitor.entity_list.results').'</section>
			<section class="results-limit">
				'.$select.'
			</section>
		';
	}
	
	private function createPaginationButton
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
	
	private function createPaginationBlock
	(
		int         $currentPageIndex,
		int|null    $limit,
		string|null $search,
		int         $entityCount,
		string      $pageLink
	): string
	{
		if ($limit === null)
			$pageCount = 1;
		else if ($entityCount > 0 && $entityCount % $limit === 0)
			$pageCount = intdiv($entityCount, $limit);
		else
			$pageCount = intdiv($entityCount, $limit) + 1;
		
		// If you change it,
		// then change the divisor of pagination.button in search-filter-section.css
		$mostLeftPage       = 1;
		$pageCountFromLeft  = 2;
		$pageCountfromRight = 2;
		$mostRightPage      = $pageCount;
		
		$fromLeft = $currentPageIndex - $pageCountFromLeft;
		$fromLeft = $fromLeft > $mostLeftPage ? $fromLeft : $mostLeftPage;
		
		$fromRight = $currentPageIndex + $pageCountfromRight;
		$fromRight = $fromRight < $mostRightPage ? $fromRight : $mostRightPage;
		
		$html[] =
		'
			<section>'.__('visitor.entity_list.page').'</section>
			<section class="pagination">
		';
		
		if ($fromLeft > $mostLeftPage)
		{
			$href = $pageLink.Http::buildQuery
			(
				[
					'limit'  => $limit,
					'page'   => $mostLeftPage,
					'search' => $search
				]
			);
			$html[] = $this->createPaginationButton($mostLeftPage, $href, 'enabled');
		}
		
		if ($fromLeft > $mostLeftPage + 1)
			$html[] = $this->createPaginationButton('…', null, 'disabled');
		
		for ($i = $fromLeft; $i <= $fromRight; $i++)
		{
			$href  = $pageLink.Http::buildQuery
			(
				[
					'limit'  => $limit,
					'page'   => $i,
					'search' => $search
				]
			);
			$state = ($i === $currentPageIndex) ? 'current' : 'enabled';
			
			$html[] = $this->createPaginationButton($i, $href, $state);
		}
		
		if ($fromRight < $mostRightPage - 1)
			$html[] = $this->createPaginationButton('…', null, 'disabled');
		
		if ($fromRight < $mostRightPage)
		{
			$href = $pageLink.Http::buildQuery
			(
				[
					'limit'  => $limit,
					'page'   => $mostRightPage,
					'search' => $search
				]
			);
			$html[] = $this->createPaginationButton($mostRightPage, $href, 'enabled');
		}
		
		$html[] = 
		'
			</section>
		';
		
		return implode($html);
	}
	
	private function createSearchBarBlock
	(
		int|null    $limit,
		int|null    $page,
		string|null $search
	): string
	{
		if ($limit !== null)
		{
			$limitInput  = '<input name="limit" type="hidden" value="'.htmlspecialchars($limit).'" />';
			$pageInput   = '<input name="page" type="hidden" value="1" />';
			
		}
		else
		{
			$limitInput = '';
			$pageInput  = '';
		}
		
		$searchInput = '<input class="custom-input" name="search" type="search" id="search-bar" value="'.htmlspecialchars($search ?? '').'" placeholder="'.__('base.controls.search.hint').'" required />';
		
		return
		'
		<section>'.__('visitor.entity_list.search').'</section>
		<section class="search-elements">
			'.$limitInput.'
			'.$pageInput.'
			'.$searchInput.'
			<button class="custom-button" id="search-bar-button">'.__('base.controls.button.search').'</button>
		</section>
		';
	}
	
	//---------------------------------------//
	//      Content Pages: Entity Lists      //
	//---------------------------------------//
	
	private function createGameList
	(
		array       $games,
		int         $headingLevel,
		string      $entityClass,
		string|null $relationKey         = null,
		bool        $statusChangeAllowed = true
	): array
	{
		$html = [];
		
		foreach ($games as $game)
		{
			$href = Http::buildPath($this->language, 'game', $game['uri']);
			
			$image        = $this->createGameImage($game);
			$textEntities = [];
			
			$textEntities[] = $this->createHeadingAsLink($game['transliterated_name'], $headingLevel, $href, ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($game['original_name'], ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($game['localized_name'], ['class' => ['entity-name']]);
			
			if ($relationKey)
			{
				if (Authorizer::isCurrentUserAdministrator() && $statusChangeAllowed)
					$textEntities[] = $this->createStatusSelect($game, $relationKey, $href);
				else
					$textEntities[] = $this->createStatus($game[$relationKey], true);
			}
			
			$html[] = $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
		return $html;
	}
	
	private function createAlbumList
	(
		array       $albums,
		int         $headingLevel,
		string      $entityClass,
		string|null $relationKey         = null,
		bool        $statusChangeAllowed = true
	): array
	{
		$html = [];
		
		foreach ($albums as $album)
		{
			$href = Http::buildPath($this->language, 'album', $album['uri']);
			
			$image        = $this->createAlbumImage($album);
			$textEntities = [];
			
			$textEntities[] = $this->createHeadingAsLink($album['transliterated_name'], $headingLevel, $href, ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($album['original_name'], ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($album['localized_name'], ['class' => ['entity-name']]);
			
			if ($relationKey)
			{
				if (Authorizer::isCurrentUserAdministrator()&& $statusChangeAllowed)
					$textEntities[] = $this->createStatusSelect($album, $relationKey, $href);
				else
					$textEntities[] = $this->createStatus($album[$relationKey], true);
			}
			
			$html[] = $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
		return $html;
	}
	
	private function createArtistList
	(
		array       $artists,
		int         $headingLevel,
		string      $entityClass,
		string|null $relationKey         = null,
		bool        $statusChangeAllowed = true
	): array
	{
		$html = [];
		
		foreach ($artists as $artist)
		{
			$href = Http::buildPath($this->language, 'artist', $artist['uri']);
			
			$image        = $this->createArtistImage($artist);
			$textEntities = [];
			
			$textEntities[] = $this->createHeadingAsLink($artist['transliterated_name'], $headingLevel, $href, ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($artist['original_name'], ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($artist['localized_name'], ['class' => ['entity-name']]);
			
			if ($relationKey)
			{
				if (Authorizer::isCurrentUserAdministrator() && $statusChangeAllowed)
					$textEntities[] = $this->createStatusSelect($artist, $relationKey, $href);
				else
					$textEntities[] = $this->createStatus($artist[$relationKey], true);
			}
			
			$html[] = $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
		return $html;
	}
	
	private function createCharacterList
	(
		array       $characters,
		int         $headingLevel,
		string      $entityClass,
		string|null $relationKey         = null,
		bool        $statusChangeAllowed = true
	): array
	{
		$html = [];
		
		foreach ($characters as $character)
		{
			$href = Http::buildPath($this->language, 'character', $character['uri']);
			
			$image        = $this->createCharacterImage($character);
			$textEntities = [];
			
			$textEntities[] = $this->createHeadingAsLink($character['transliterated_name'], $headingLevel, $href, ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($character['original_name'], ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($character['localized_name'], ['class' => ['entity-name']]);
			
			if ($relationKey)
			{
				if (Authorizer::isCurrentUserAdministrator() && $statusChangeAllowed)
					$textEntities[] = $this->createStatusSelect($character, $relationKey, $href);
				else
					$textEntities[] = $this->createStatus($character[$relationKey], true);
			}
			
			$html[] = $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
		return $html;
	}
	
	private function createAlbumSongList
	(
		array $album,
		array $songs,
		int   $headingLevel
	): array
	{
		$rows = [];
		
		for ($i = 0; $i < count($songs); $i++)
		{
			if ($songs[$i]['has_vocal'])
			{
				$href = Http::buildPath($this->language, 'album', $album['uri'], 'song', $songs[$i]['uri']);
				$transliteratedName = $this->createParagraphAsLink($songs[$i]['transliterated_name'], $href);
			}
			else
				$transliteratedName = $this->createParagraph($songs[$i]['transliterated_name']);
			
			$editLabel    = __('visitor.album.controls.edit_song');
			$editAccess   = RoleManager::getStateToEditAlbum($album);
			$editAccess   = ($editAccess === AccessState::Ok) ? RoleManager::getStateToEditSong($songs[$i]) : $editAccess;
			$editHref     = Http::buildPath($this->language, 'album', $album['uri'], 'song', $songs[$i]['uri'], 'edit');
			$editButton   = $this->createButtonAsRestrictedLink($editLabel, $editAccess, ['href' => $editHref]);
			
			$deleteLabel  = __('visitor.album.controls.delete_song');
			$deleteAccess = RoleManager::getStateToEditAlbum($album);
			$deleteAccess = ($deleteAccess === AccessState::Ok) ? RoleManager::getStateToDeleteSong($songs[$i]) : $deleteAccess;
			$deleteHref   = Http::buildPath($this->language, 'album', $album['uri'], 'song', $songs[$i]['uri'], 'delete');
			$deleteButton = $this->createButtonAsRestrictedLink($deleteLabel, $deleteAccess, ['href' => $deleteHref]);
			
			$cells                        = [];
			$cells['disc_number']         = htmlspecialchars($songs[$i]['disc_number']);
			$cells['track_number']        = htmlspecialchars($songs[$i]['track_number']);
			$cells['transliterated_name'] = $transliteratedName;
			$cells['original_name']       = $this->createParagraph($songs[$i]['original_name']);
			$cells['localized_name']      = $this->createParagraph($songs[$i]['localized_name']);
			$cells['edit_button']         = $editButton;
			$cells['delete_button']       = $deleteButton;
			
			$rows[] = $cells;
		}
		
		$html[] =
		'
		<section>
			'.$this->createHeading(__('visitor.album.song_list'), 2).'
			<table>
				<tbody>
					<tr>
						<th>'.__('visitor.album.song_table.disc_number').'</th>
						<th>'.__('visitor.album.song_table.song_number').'</th>
						<th>'.__('visitor.album.song_table.song_name').'</th>
						<th></th>
						<th></th>
		';
		
		foreach ($rows as $row)
		{
			$html[] =
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
						<td>'.$row['delete_button'].'</td>
					</tr>
			';
		}
		
		if (count($songs) < $album['song_count'])
		{
			$addLabel  = __('visitor.album.controls.add_song');
			$addAccess = RoleManager::getStateToEditAlbum($album);
			$addHref   = Http::buildPath($this->language, 'album', $album['uri'], 'add-song');
			$addButton = $this->createButtonAsRestrictedLink($addLabel, $addAccess, ['href' => $addHref]);
			
			$html[] =
			'
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td colspan="2">'.$addButton.'</td>
					</tr>
			';
		}
		
		if (count($songs) === 0 && Authorizer::isCurrentUserAdministrator())
		{
			$title = __('visitor.album.controls.fill_album');
			$href  = Http::buildPath($this->language, 'album', $album['uri'], 'fill-album');
			
			$html[] =
			'
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td colspan="2">'.$this->createButtonAsLink($title, ['href' => $href]).'</td>
					</tr>
			';
		}
		
		$html[] =
		'
				</tbody>
			</table>
		</section>
		';
		
		return $html;
	}
	
	private function createSongList
	(
		array       $songs,
		int         $headingLevel,
		string      $entityClass,
		string|null $relationKey = null
	): array
	{
		$html = [];
		
		foreach ($songs as $song)
		{
			$album = [];
			$album['transliterated_name'] = $song['album_transliterated_name'];
			$album['uri']                 = $song['album_uri'];
			$album['is_image_uploaded']   = $song['is_image_uploaded'];
			
			$image        = $this->createAlbumImage($album);
			$textEntities = [];
			
			if ($song['has_vocal'])
			{
				$href = Http::buildPath($this->language, 'album', $song['album_uri'], 'song', $song['uri']);
				$textEntities[] = $this->createHeadingAsLink($song['transliterated_name'], $headingLevel, $href, ['class' => ['entity-name']]);
			}
			else
				$textEntities[] = $this->createHeading($song['transliterated_name'], $headingLevel, ['class' => ['entity-name']]);
			
			$textEntities[] = $this->createParagraph($song['original_name'], ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($song['localized_name'], ['class' => ['entity-name']]);
			
			if ($relationKey)
				$textEntities[] = $this->createStatus($song['song_artist_character_relation_status'], true);
			else
				$textEntities[] = '';
			
			$html[] = $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
		return $html;
	}
	
	private function createTranslationList
	(
		array  $translations,
		int    $headingLevel,
		string $entityClass
	): array
	{
		$html = [];
		
		foreach ($translations as $translation)
		{
			$album = [];
			$album['transliterated_name'] = $translation['album_transliterated_name'];
			$album['uri']                 = $translation['album_uri'];
			$album['is_image_uploaded']   = $translation['is_image_uploaded'];
			
			$image        = $this->createAlbumImage($album);
			$textEntities = [];
			
			$href = Http::buildPath
			(
				$this->language,
				'album',
				$translation['album_uri'],
				'song',
				$translation['song_uri'],
				'translation',
				$translation['uri']
			);
			
			$textEntities[] = $this->createHeadingAsLink($translation['name'], $headingLevel, $href, ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph(Localizer::localizeLanguageName($translation));
			
			$html[] = $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
		return $html;
	}
	
	//-------------------------//
	//      Single Entity      //
	//-------------------------//
	
	private function createGamePageHeading(array $game, int $headingLevel): string
	{
		$image        = $this->createGameImage($game);
		$textEntities = [];
		
		$textEntities[] = $this->createHeading($game['transliterated_name'], $headingLevel);
		$textEntities[] = $this->createParagraph($game['original_name']);
		$textEntities[] = $this->createParagraph($game['localized_name']);
		$textEntities[] = $this->createVndbLinkParagraph(__('visitor.game.details'), $game, 'v');
		
		return $this->createInfoBlockWithImage($image, $textEntities, 'main-entity');
	}
	
	private function createAlbumPageHeading(array $album, int $headingLevel): string
	{
		$image        = $this->createAlbumImage($album);
		$textEntities = [];
		
		$textEntities[] = $this->createHeading($album['transliterated_name'], $headingLevel);
		$textEntities[] = $this->createParagraph($album['original_name']);
		$textEntities[] = $this->createParagraph($album['localized_name']);
		$textEntities[] = $this->createVgmdbLinkParagraph(__('visitor.album.details'), $album, 'album');
		$textEntities[] = $this->createParagraph(__('visitor.album.song_count').$album['song_count']);
		
		return $this->createInfoBlockWithImage($image, $textEntities, 'main-entity');
	}
	
	private function createArtistPageHeading(array $artist, int $headingLevel): string
	{
		$image        = $this->createArtistImage($artist);
		$textEntities = [];
		
		$textEntities[] = $this->createHeading($artist['transliterated_name'], $headingLevel);
		$textEntities[] = $this->createParagraph($artist['original_name']);
		$textEntities[] = $this->createParagraph($artist['localized_name']);
		$textEntities[] = $this->createVgmdbLinkParagraph(__('visitor.artist.details'), $artist, 'artist');
		
		if ($artist['alias_of_transliterated_name'] && $artist['alias_of_uri'])
		{
			$href = Http::buildPath($this->language, 'artist', $artist['alias_of_uri']);
			$text = $artist['alias_of_transliterated_name'];
			
			$textEntities[] = '<p>'.__('visitor.artist.alias_of').$this->createLink($href, $text).'</p>';
		}
		
		return $this->createInfoBlockWithImage($image, $textEntities, 'main-entity');
	}
	
	private function createCharacterPageHeading(array $character, int $headingLevel): string
	{
		$image        = $this->createCharacterImage($character);
		$textEntities = [];
		
		$textEntities[] = $this->createHeading($character['transliterated_name'], $headingLevel);
		$textEntities[] = $this->createParagraph($character['original_name']);
		$textEntities[] = $this->createParagraph($character['localized_name']);
		$textEntities[] = $this->createVndbLinkParagraph(__('visitor.character.details'), $character, 'c');
		
		return $this->createInfoBlockWithImage($image, $textEntities, 'main-entity');
	}
	
	private function createLyricsPageHeading
	(
		string     $headingText,
		array      $album,
		array      $song,
		array|null $translation,
		array|null $performers,
		array|null $translations,
		array|null $originalSong
	): string
	{
		$html[] = '<section class="main-entity">';
		$html[] = $this->createAlbumImage($album);
		
		$html[] = '<section>';
		$html[] = $this->createHeading($headingText, 1);
		$html[] = $this->createLyricsReferenceToAlbum($album);
		
		if ($translation !== null)
			$html[] = $this->createLyricsReferenceToCurrentSong($song);
		
		if ($performers !== null)
			$html[] = $this->createLyricsPerformerList($performers);
		
		if ($originalSong !== null)
			$html[] = $this->createLyricsReferenceToOriginalSong($originalSong);
		
		if ($translations !== null)
			$html[] = $this->createLyricsTranslationList($album, $song, $translation, $translations);
		
		$html[] = '</section></section>';
		
		return implode($html);
	}
	
	//--------------------------------//
	//       Helpers for Lyrics       //
	//--------------------------------//
	
	private function createLyricsReferenceToAlbum(array $album): string
	{
		$href      = Http::buildPath($this->language, 'album', $album['uri']);
		$link      = $this->createLink($href, $album['transliterated_name']);
		$paragraph = '<p>'.__('visitor.lyrics.description.album', ['album' => $link]).'</p>';
		
		return $paragraph;
	}
	
	private function createLyricsReferenceToCurrentSong(array $song): string
	{
		$href      = Http::buildPath($this->language, 'album', $song['album_uri'], 'song', $song['uri']);
		$link      = $this->createLink($href, $song['transliterated_name']);
		$paragraph = '<p>'.__('visitor.lyrics.description.song', ['song' => $link]).'</p>';
		
		return $paragraph;
	}
	
	private function createLyricsReferenceToOriginalSong(array $originalSong): string
	{
		$href      = Http::buildPath($this->language, 'album', $originalSong['album_uri'], 'song', $originalSong['uri']);
		$link      = $this->createLink($href, $originalSong['transliterated_name']);
		$paragraph = '<p>'.__('visitor.lyrics.description.original_song', ['song' => $link]).'</p>';
		
		return $paragraph;
	}
	
	private function createLyricsTranslationList
	(
		array      $album,
		array      $song,
		array|null $currentTranslation,
		array      $translations
	): string
	{
		$translationCount = [];
		
		foreach ($translations as $translation)
		{
			$language = Localizer::localizeLanguageName($translation);
			
			if (array_key_exists($language, $translationCount))
				$translationCount[$language]['total_count']++;
			else
				$translationCount[$language] = ['current_count' => 0, 'total_count' => 1];
		}
		
		$links = [];
		
		foreach ($translations as $translation)
		{
			$language = Localizer::localizeLanguageName($translation);
			
			if ($translationCount[$language]['total_count'] !== 1)
			{
				$translationCount[$language]['current_count']++;
				
				$number = Localizer::localizeTranslationNumber($translationCount[$language]['current_count']);
				$language = $language.$number;
			}
			
			if ($currentTranslation !== null && $translation['uri'] === $currentTranslation['uri'])
			{
				// If the current translation is shown, then no need to do a link to it
				$links[] = $language;
			}
			else
			{
				$href = Http::buildPath
				(
					$this->language,
					'album',
					$album['uri'],
					'song',
					$song['uri'],
					'translation',
					$translation['uri']
				);
				
				$links[] = $this->createLink($href, $language);
			}
		}
		
		if (!Authorizer::isCurrentUserVisitor() && $song['original_song_id'] === null)
		{
			$href = Http::buildPath
			(
				$this->language,
				'album',
				$song['album_uri'],
				'song',
				$song['uri'],
				'add-translation'
			);
			
			$links[] = $this->createLink($href, __('visitor.lyrics.controls.translation.add'));
		}
		
		$links     = implode(__('visitor.lyrics.description.separator'), $links);
		$paragraph = $links ? '<p>'.__('visitor.lyrics.description.translations', ['translations' => $links]).'</p>' : '';
		
		return $paragraph;
	}
	
	private function createLyricsPerformerList(array $performers): string
	{
		$parts = [];
		
		for ($i = 0; $i < count($performers); $i++)
		{
			if ($performers[$i]['character_uri'] !== null)
			{
				$hrefCharacter = Http::buildPath($this->language, 'character', $performers[$i]['character_uri']);
				$linkCharacter = $this->createLink($hrefCharacter, $performers[$i]['character_transliterated_name']);
				
				$hrefArtist = Http::buildPath($this->language, 'artist', $performers[$i]['artist_uri']);
				$linkArtist = $this->createLink($hrefArtist, $performers[$i]['artist_transliterated_name']);
				
				$parts[] = __('visitor.lyrics.description.character_voice', ['character' => $linkCharacter, 'artist' => $linkArtist]);
			}
			else
			{
				$hrefArtist = Http::buildPath($this->language, 'artist', $performers[$i]['artist_uri']);
				$linkArtist = $this->createLink($hrefArtist, $performers[$i]['artist_transliterated_name']);
				
				$parts[] = $linkArtist;
			}
		}
		
		$links     = implode(__('visitor.lyrics.description.separator'), $parts);
		$paragraph = $links ? '<p>'.__('visitor.lyrics.description.performers', ['performers' => $links]).'</p>' : '';
		
		return $paragraph;
	}
	
	private function createLyricsControls(): string
	{
		return
		'
		<section class="lyrics-controls">
			<section>
				<section>
					<span>'.__('visitor.lyrics.controls.font_size.name').'</span>
				</section>
					<section>
					'.$this->createSearchableSelect
					(
						iteratedOptions:         [
											          [0 => __('visitor.lyrics.controls.font_size.size1'), 1 => -4],
											          [0 => __('visitor.lyrics.controls.font_size.size2'), 1 => -2],
											          [0 => __('visitor.lyrics.controls.font_size.size3'), 1 =>  0],
											          [0 => __('visitor.lyrics.controls.font_size.size4'), 1 => +2],
											          [0 => __('visitor.lyrics.controls.font_size.size5'), 1 => +4]
										         ],
						selectedOption:          [0 => __('visitor.lyrics.controls.font_size.size3'), 1 => 0],
						addEmptyOption:          false,
						keyToShownValue:         0,
						keyToSentValue:          1,
						attributesForSentInput:  ['id' => 'font-size-select'],
						attributesForShownInput: [
						                             'readonly' => true,
													 'data-placeholder-filter' => __('base.controls.select.hint_idle')
												 ]
					).'
				</section>
			</section>
			<section>
				<section>
					<span>'.__('visitor.lyrics.controls.furigana.name').'</span>
				</section>
				<section>
					'.$this->createSearchableSelect
					(
						iteratedOptions:         [
											          [0 => __('visitor.lyrics.controls.furigana.show'), 1 => 1],
											          [0 => __('visitor.lyrics.controls.furigana.hide'), 1 => 0]
										         ],
						selectedOption:          [0 => __('visitor.lyrics.controls.furigana.show'), 1 => 1],
						addEmptyOption:          false,
						keyToShownValue:         0,
						keyToSentValue:          1,
						attributesForSentInput:  ['id' => 'show-furigana-select'],
						attributesForShownInput: [
						                              'readonly' => true,
													  'data-placeholder-filter' => __('base.controls.select.hint_idle')
												 ]
					).'
				</section>
			</section>
			<section>
				<section>
					<span>'.__('visitor.lyrics.controls.notes.name').'</span>
				</section>
				<section>
					'.$this->createSearchableSelect
					(
						iteratedOptions:         [
											          [0 => __('visitor.lyrics.controls.notes.show'), 1 => 1],
											          [0 => __('visitor.lyrics.controls.notes.hide'), 1 => 0]
										         ],
						selectedOption:          [0 => __('visitor.lyrics.controls.notes.show'), 1 => 1],
						addEmptyOption:          false,
						keyToShownValue:         0,
						keyToSentValue:          1,
						attributesForSentInput:  ['id' => 'show-notes-select'],
						attributesForShownInput: [
						                              'readonly' => true,
													  'data-placeholder-filter' => __('base.controls.select.hint_idle')
												 ]
					).'
				</section>
			</section>
			<section>
				<section>
					<span>'.__('visitor.lyrics.controls.colors.name').'</span>
				</section>
				<section>
					'.$this->createSearchableSelect
					(
						iteratedOptions:         [
											         [0 => __('visitor.lyrics.controls.colors.show'), 1 => 1],
											         [0 => __('visitor.lyrics.controls.colors.hide'), 1 => 0]
										         ],
						selectedOption:          [0 => __('visitor.lyrics.controls.colors.show'), 1 => 1],
						addEmptyOption:          false,
						keyToShownValue:         0,
						keyToSentValue:          1,
						attributesForSentInput:  ['id' => 'show-colors-select'],
					    attributesForShownInput: [
						                             'readonly' => true,
													 'data-placeholder-filter' => __('base.controls.select.hint_idle')
												 ]
					).'
				</section>
			</section>
		</section>
		';
	}
	
	private function createMarkupLine
	(
		string $line,
		string $noteId,
		string $noteName,
		string $noteClass,
	): string
	{
		// Ideal solution (maybe):
		
		// Look the string from the start and add opening pseudotags onto the stack
		// When we find a closing tag, we compare it with the top of the stack
		// If they are the same, replace both with HTML
		// If they are not, the markup is invalid, don't do anything
		
		// But the current solution works too, so who cares?
		// If the markup is invalid, working parts are replaced and broken are not.
		
		// Combined these two examples to allow nested markup:
		//
		// https://www.php.net/manual/en/function.preg-replace-callback.php
		// https://www.php.net/manual/en/function.preg-replace-callback-array.php
		
		// u = unicode, U = ungreedy, (...) - capture and reuse in ${N}
		
		$patterns = 
		[
			'/{nt}(\d+){\/nt}/uU' =>
			function (array $x) use ($noteName, $noteId, $noteClass)
			{
				return '<a href="#'.$noteName.$x[1].'" class="'.$noteClass.'" id="'.$noteId.$x[1].'">['.$x[1].']</a>';
			},
			
			'/{kj}(.+){fg}(.+){\/fg}/uU' =>
			function (array $x)
			{
				return '<ruby>'.$x[1].'<rt>'.$x[2].'</rt></ruby>';
			},
			
			'/{cl (#[0-9A-Fa-f]{6})}(.+){\/cl}/uU' =>
			function (array $x)
			{
				return '<span style="color: '.$x[1].'">'.$x[2].'</span>';
			}
		];
		
		$line = htmlspecialchars($line);
		
		do
		{
			$line = preg_replace_callback_array($patterns, $line, -1, $count);
		}
		while ($count > 0);
		
		return $line;
	}
	
	private function createMarkupText
	(
		string $lyrics,
		string $noteFrom,
		string $noteTo,
		string $noteClass
	): string
	{
		$html = [];
		
		$lines = explode("\n", $lyrics);
		
		foreach ($lines as $line)
		{
			if ($line !== '')
				$html[] = '<span class="text-line">'.$this->createMarkupLine($line, $noteFrom, $noteTo, $noteClass).'</span>';
			else
				$html[] = '<br/>';
		}
		
		return implode($html);
	}
	
	private function createSongLyrics(array $song): string
	{
		return
		'
			<section>
				'.$this->createHeading($song['original_name'], 2).'
				'.$this->createHeading(Localizer::localizeLanguageName($song), 3).'
				'.$this->createMarkupText($song['lyrics'], 'lyrics-reference-', 'lyrics-note-', 'note-small').'
			</section>
		';
	}
	
	private function createSongNotes(array $song): string
	{
		return
		'
			<section>
				'.$this->createHeading(__('visitor.lyrics.notes.heading'), 2).'
				'.$this->createMarkupText($song['notes'] ?? __('visitor.lyrics.notes.no_notes'), 'lyrics-note-', 'lyrics-reference-', 'note-big').'
			</section>
		';
	}
	
	private function createTranslationLyrics(array $translation): string
	{
		return
		'
			<section>
				'.$this->createHeading($translation['name'], 2).'
				'.$this->createHeading(Localizer::localizeLanguageName($translation), 3).'
				'.$this->createMarkupText($translation['lyrics'], 'translation-reference-', 'translation-note-', 'note-small').'
			</section>
		';
	}
	
	private function createTranslationNotes(array $translation): string
	{
		return
		'
			<section>
				'.$this->createHeading(__('visitor.lyrics.notes.heading'), 2).'
				'.$this->createMarkupText($translation['notes'] ?? __('visitor.lyrics.notes.no_notes'), 'translation-note-', 'translation-reference-', 'note-big').'
			</section>
		';
	}
	
	//---------------------//
	//      Home Page      //
	//---------------------//
	
	final public function renderHomePage(array $albums, array $lyrics, array $translations): void
	{
		$html[] = $this->startRender
		(
			title:        __('visitor.home.heading'),
			canonicalUri: Http::buildPath($this->language),
			cssSheetUris: ['/css/home-page.css']
		);
		
		$html[] =
		'
		<article>
			<section class="home-page-heading">
				<img class="website-mascot" src="/assets/static-images/wee-hagana.png" alt="'.__('visitor.home.hagana_alt_text').'"/>
				<section class="home-page-text">
					<h1 class="home-page-text">'.__('visitor.home.heading').'</h1>
					<p class="home-page-text">'.__('visitor.home.description.one').'</p>
					<p class="home-page-text">'.__('visitor.home.description.two').'</p>
					<p class="home-page-text">'.__('visitor.home.description.three').'</p>
					<p class="home-page-text">'.__('visitor.home.description.four').'</p>
				</section>
			</section>
		';
		/*
			<section class="search-section">
				<h2>'.\Localization\HomePage\Search.'</h2>
				<section class="search-elements">
					'.$this->createTextInput
					(
						attributes:
						[
							'name' => 'search',
							'type' => 'search',
							'id' => 'search-bar',
							'placeholder' => \Localization\Controls\SearchPlaceholder
						]
					).'
					'.$this->createSearchableSelect
					(
						iteratedOptions:
						[
							['toShow' => \Localization\HomePage\SearchAll,             'toSend' => 'entity-list'],
							['toShow' => \Localization\HomePage\SearchGameList,        'toSend' => 'game-list'],
							['toShow' => \Localization\HomePage\SearchAlbumList,       'toSend' => 'album-list'],
							['toShow' => \Localization\HomePage\SearchArtistList,      'toSend' => 'artist-list'],
							['toShow' => \Localization\HomePage\SearchCharacterList,   'toSend' => 'character-list'],
							['toShow' => \Localization\HomePage\SearchSongList,        'toSend' => 'song-list'],
							['toShow' => \Localization\HomePage\SearchTranslationList, 'toSend' => 'translation-list']
						],
						selectedOption:          ['toShow' => \Localization\HomePage\SearchAll, 'toSend' => 'entity-list'],
						addEmptyOption:          false,
						keyToShownValue:         'toShow',
						keyToSentValue:          'toSend',
						attributesForSentInput:  ['id' => 'search-entity-select'],
						attributesForShownInput: [
						                              'readonly' => true,
													  'data-placeholder-filter' => \Localization\Controls\SelectOption
												 ]
					).'
					<button class="custom-button" id="search-bar-button">'.\Localization\Controls\SearchButton.'</button>
				</section>
			</section>
		*/
		
		$html[] =
		'
			<section>
				<h2>'.__('visitor.home.recently_added.albums').'</h2>
				<section class="entity-container">
		';
		
		foreach ($albums as $album)
		{
			$headingLevel = 3;
			$href = Http::buildPath($this->language, 'album', $album['uri']);
			
			$image   = $this->createAlbumImage($album);
			$heading = $this->createHeading($album['transliterated_name'], $headingLevel);
			
			$html[] = '<a href='.htmlspecialchars($href).'>'.$image.$heading.'</a>';
		}
		
		$html[] =
		'
				</section>
			</section>
			<section>
				<h2>'.__('visitor.home.recently_added.lyrics').'</h2>
				<section class="entity-container">
		';
		
		foreach ($lyrics as $lyric)
		{
			$headingLevel = 3;
			$href = Http::buildPath($this->language, 'album', $lyric['album_uri'], 'song', $lyric['uri']);
			
			$album = [];
			$album['transliterated_name'] = $lyric['album_transliterated_name'];
			$album['is_image_uploaded']   = $lyric['is_image_uploaded'];
			$album['uri']                 = $lyric['album_uri'];
			
			$image   = $this->createAlbumImage($album);
			$heading = $this->createHeading($lyric['transliterated_name'], $headingLevel);
			
			$html[] = '<a href='.htmlspecialchars($href).'>'.$image.$heading.'</a>';
		}
		
		$html[] =
		'
				</section>
			</section>
			<section>
				<h2>'.__('visitor.home.recently_added.translations').'</h2>
				<section class="entity-container">
		';
		
		foreach ($translations as $translation)
		{
			$headingLevel = 3;
			$href = Http::buildPath
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
			
			$html[] = '<a href='.htmlspecialchars($href).'>'.$image.$heading.'</a>';
		}
		
		$html[] =
		'
			</section>
		</article>
		';
		
		$html[] = $this->endRender
		(
			// jsScriptUris: ['/js/home-page.js']
		);
		
		$this->echoHtml($html);
	}
	
	//------------------------//
	//      Log-In Pages      //
	//------------------------//
	
	final public function renderLogInPage
	(
		string|null $email = null,
		InputError  $error = InputError::None
	): void
	{
		$html[] = $this->startRender
		(
			title:        __('visitor.log_in.heading'),
			cssSheetUris: ['/css/window-in-center-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section class="page-heading">
				'.$this->createHeading(__('visitor.log_in.heading'), 1).'
			</section>
			<section>
				'.$this->createParagraph(__("base.input_error.{$error->value}")).'
				<form method="POST">
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $_SESSION['log_in_page']['nonce']]).'
					<section>
						'.$this->createHeadingForInput(__('visitor.log_in.email.heading'), 3, true).'
						'.$this->createEmailInput(['name' => 'email', 'value' => $email, 'placeholder' => __('visitor.log_in.email.hint'), 'required' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput(__('visitor.log_in.password.heading'), 3, true).'
						'.$this->createPasswordInput(['name' => 'password', 'placeholder' => __('visitor.log_in.password.hint'), 'required' => true]).'
					</section>
					<section>
						'.$this->createSubmitButton(__('visitor.log_in.submit')).'
					</section>
				</form>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	final public function renderSignUpPage
	(
		string|null $username           = null,
		string|null $email              = null,
		InputError  $error              = InputError::None,
		string      $captchaBase64Image = ''
	): void
	{
		$html[] = $this->startRender
		(
			title:        __('visitor.sign_up.heading'),
			cssSheetUris: ['/css/window-in-center-page.css', '/css/shared/captcha.css']
		);
		
		$policy = '<a href="'.Http::buildPath($this->language, 'policy').'" target="_blank">'.__('visitor.sign_up.confirmation.policy').'</a>';
		$rules  = '<a href="'.Http::buildPath($this->language, 'rules').'" target="_blank">'.__('visitor.sign_up.confirmation.rules').'</a>';
		$guide  = '<a href="'.Http::buildPath($this->language, 'writing-guide').'" target="_blank">'.__('visitor.sign_up.confirmation.guide').'</a>';
		
		$html[] = 
		'
		<article>
			<section class="page-heading">
				'.$this->createHeading(__('visitor.sign_up.heading'), 1).'
			</section>
			<section>
				'.$this->createParagraph(__("base.input_error.{$error->value}")).'
				<form method="POST">
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $_SESSION['sign_up_page']['nonce']]).'
					<section>
						'.$this->createHeadingForInput(__('visitor.sign_up.username.heading'), 3, true).'
						'.$this->createUsernameInput(['name' => 'username', 'value' => $username, 'placeholder' => __('visitor.sign_up.username.hint'), 'required' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput(__('visitor.sign_up.email.heading'), 3, true).'
						'.$this->createEmailInput(['name' => 'email', 'value' => $email, 'placeholder' => __('visitor.sign_up.email.hint'), 'required' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput(__('visitor.sign_up.password.heading'), 3, true).'
						'.$this->createPasswordInput(['name' => 'password', 'placeholder' => __('visitor.sign_up.password.hint'), 'required' => true]).'
					</section>
					<section>
						<p>'.__('visitor.sign_up.confirmation.warning').'</p>
						<p>'.__('visitor.sign_up.confirmation.readables', ['policy' => $policy, 'rules' => $rules, 'guide' => $guide]).'</p>
						<p>'.__('visitor.sign_up.confirmation.advice').'</p>
					</section>
					<section>
						'.$this->createCaptchaInput(['required' => true]).'
						'.$this->createCaptchaImage($captchaBase64Image).'
						'.$this->createSubmitButton(__('visitor.sign_up.submit')).'
					</section>
				</form>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
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
		$buttonAttributes = ['href'  => Http::buildPath($this->language, 'add-game')];
		$buttonLabel      = __('visitor.game_list.add_game');
		$buttonAccess     = RoleManager::getStateToAddGame();
		
		$hrefThisPage  = Http::buildPath($this->language, 'game-list');
		$parameters    = ['limit' => $limit, 'page' => $page, 'search' => $search];
		$queryThisPage = Http::buildQuery($parameters);
		
		$paginationBlock   = $this->createPaginationBlock($page ?? 1, $limit, $search, $gameCount, $hrefThisPage);
		$resultsLimitBlock = $this->createResultsLimitBlock($limit);
		$searchBarBlock    = $this->createSearchBarBlock($limit, $page, $search);
		
		$html[] = $this->startRender
		(
			title:        __('visitor.game_list.heading'),
			canonicalUri: $hrefThisPage.$queryThisPage,
			cssSheetUris:
			[
				'/css/shared/entity.css',
				'/css/entity-list-page.css'
			]
		);
		
		$html[] = 
		'
		<article>
			<section class="entity-list-heading">
				'.$this->createHeading(__('visitor.game_list.heading'), 1).'
				'.$this->createButtonAsRestrictedLink($buttonLabel, $buttonAccess, $buttonAttributes).'
			</section>
			<section>
				<form class="search-section">
					<section>'.$searchBarBlock.'</section>
					<section>'.$paginationBlock.'</section>
					<section>'.$resultsLimitBlock.'</section>
				</form>
			</section>
		';
		
		$html[] = $this->createGameList($games, 2, 'list-entity');
		
		$html[] = 
		'
			<section>
				<section class="search-section">
					<section></section>
					<section>'.$paginationBlock.'</section>
					<section></section>
				</section>
			</section>
		</article>
		';
		
		$html[] = $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/entity-list-search.js',
				'/js/entity-list-page.js'
			]
		);
		
		$this->echoHtml($html);
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
		$buttonAttributes = ['href'  => Http::buildPath($this->language, 'add-album')];
		$buttonLabel      = __('visitor.album_list.add_album');
		$buttonAccess     = RoleManager::getStateToAddAlbum();
		
		$hrefThisPage  = Http::buildPath($this->language, 'album-list');
		$parameters    = ['limit' => $limit, 'page' => $page, 'search' => $search];
		$queryThisPage = Http::buildQuery($parameters);
		
		$paginationBlock   = $this->createPaginationBlock($page ?? 1, $limit, $search, $albumCount, $hrefThisPage);
		$resultsLimitBlock = $this->createResultsLimitBlock($limit);
		$searchBarBlock    = $this->createSearchBarBlock($limit, $page, $search);
		
		$html[] = $this->startRender
		(
			title:        __('visitor.album_list.heading'),
			canonicalUri: $hrefThisPage.$queryThisPage,
			cssSheetUris:
			[
				'/css/shared/entity.css',
				'/css/entity-list-page.css'
			]
		);
		
		$html[] = 
		'
		<article>
			<section class="entity-list-heading">
				'.$this->createHeading(__('visitor.album_list.heading'), 1).'
				'.$this->createButtonAsRestrictedLink($buttonLabel, $buttonAccess, $buttonAttributes).'
			</section>
			<section>
				<form class="search-section">
					<section>'.$searchBarBlock.'</section>
					<section>'.$paginationBlock.'</section>
					<section>'.$resultsLimitBlock.'</section>
				</form>
			</section>
		';
		
		$html[] = $this->createAlbumList($albums, 2, 'list-entity');
		
		$html[] = 
		'
			<section>
				<section class="search-section">
					<section></section>
					<section>'.$paginationBlock.'</section>
					<section></section>
				</section>
			</section>
		</article>
		';
		
		$html[] = $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/entity-list-search.js',
				'/js/entity-list-page.js'
			]
		);
		
		$this->echoHtml($html);
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
		$buttonAttributes = ['href'  => Http::buildPath($this->language, 'add-artist')];
		$buttonLabel      = __('visitor.artist_list.add_artist');
		$buttonAccess     = RoleManager::getStateToAddArtist();
		
		$hrefThisPage  = Http::buildPath($this->language, 'artist-list');
		$parameters    = ['limit' => $limit, 'page' => $page, 'search' => $search];
		$queryThisPage = Http::buildQuery($parameters);
		
		$paginationBlock   = $this->createPaginationBlock($page ?? 1, $limit, $search, $artistCount, $hrefThisPage);
		$resultsLimitBlock = $this->createResultsLimitBlock($limit);
		$searchBarBlock    = $this->createSearchBarBlock($limit, $page, $search);
		
		$html[] = $this->startRender
		(
			title:        __('visitor.artist_list.heading'),
			canonicalUri: $hrefThisPage.$queryThisPage,
			cssSheetUris:
			[
				'/css/shared/entity.css',
				'/css/entity-list-page.css'
			]
		);
		
		$html[] = 
		'
		<article>
			<section class="entity-list-heading">
				'.$this->createHeading(__('visitor.artist_list.heading'), 1).'
				'.$this->createButtonAsRestrictedLink($buttonLabel, $buttonAccess, $buttonAttributes).'
			</section>
			<section>
				<form class="search-section">
					<section>'.$searchBarBlock.'</section>
					<section>'.$paginationBlock.'</section>
					<section>'.$resultsLimitBlock.'</section>
				</form>
			</section>
		';
		
		$html[] = $this->createArtistList($artists, 2, 'list-entity');
		
		$html[] = 
		'
			<section>
				<section class="search-section">
					<section></section>
					<section>'.$paginationBlock.'</section>
					<section></section>
				</section>
			</section>
		</article>
		';
		
		$html[] = $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/entity-list-search.js',
				'/js/entity-list-page.js'
			]
		);
		
		$this->echoHtml($html);
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
		$buttonAttributes = ['href'  => Http::buildPath($this->language, 'add-character')];
		$buttonLabel      = __('visitor.character_list.add_character');
		$buttonAccess     = RoleManager::getStateToAddCharacter();
		
		$hrefThisPage  = Http::buildPath($this->language, 'character-list');
		$parameters    = ['limit' => $limit, 'page' => $page, 'search' => $search];
		$queryThisPage = Http::buildQuery($parameters);
		
		$paginationBlock   = $this->createPaginationBlock($page ?? 1, $limit, $search, $characterCount, $hrefThisPage);
		$resultsLimitBlock = $this->createResultsLimitBlock($limit);
		$searchBarBlock    = $this->createSearchBarBlock($limit, $page, $search);
		
		$html[] = $this->startRender
		(
			title:        __('visitor.character_list.heading'),
			canonicalUri: $hrefThisPage.$queryThisPage,
			cssSheetUris:
			[
				'/css/shared/entity.css',
				'/css/entity-list-page.css'
			]
		);
		
		$html[] = 
		'
		<article>
			<section class="entity-list-heading">
				'.$this->createHeading(__('visitor.character_list.heading'), 1).'
				'.$this->createButtonAsRestrictedLink($buttonLabel, $buttonAccess, $buttonAttributes).'
			</section>
			<section>
				<form class="search-section">
					<section>'.$searchBarBlock.'</section>
					<section>'.$paginationBlock.'</section>
					<section>'.$resultsLimitBlock.'</section>
				</form>
			</section>
		';
		
		$html[] = $this->createCharacterList($characters, 2, 'list-entity');
		
		$html[] = 
		'
			<section>
				<section class="search-section">
					<section></section>
					<section>'.$paginationBlock.'</section>
					<section></section>
				</section>
			</section>
		</article>
		';
		
		$html[] = $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/entity-list-search.js',
				'/js/entity-list-page.js'
			]
		);
		
		$this->echoHtml($html);
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
		$hrefThisPage  = Http::buildPath($this->language, 'song-list');
		$parameters    = ['limit' => $limit, 'page' => $page, 'search' => $search];
		$queryThisPage = Http::buildQuery($parameters);
		
		$paginationBlock   = $this->createPaginationBlock($page ?? 1, $limit, $search, $songCount, $hrefThisPage);
		$resultsLimitBlock = $this->createResultsLimitBlock($limit);
		$searchBarBlock    = $this->createSearchBarBlock($limit, $page, $search);
		
		$html[] = $this->startRender
		(
			title:        __('visitor.song_list.heading'),
			canonicalUri: $hrefThisPage.$queryThisPage,
			cssSheetUris:
			[
				'/css/shared/entity.css',
				'/css/entity-list-page.css'
			]
		);
		
		$html[] = 
		'
		<article>
			<section class="entity-list-heading">
				'.$this->createHeading(__('visitor.song_list.heading'), 1).'
			</section>
			<section>
				<form class="search-section">
					<section>'.$searchBarBlock.'</section>
					<section>'.$paginationBlock.'</section>
					<section>'.$resultsLimitBlock.'</section>
				</form>
			</section>
		';
		
		$html[] = $this->createSongList($songs, 2, 'list-entity');
		
		$html[] = 
		'
			<section>
				<section class="search-section">
					<section></section>
					<section>'.$paginationBlock.'</section>
					<section></section>
				</section>
			</section>
		</article>
		';
		
		$html[] = $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/entity-list-search.js',
				'/js/entity-list-page.js'
			]
		);
		
		$this->echoHtml($html);
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
		$hrefThisPage  = Http::buildPath($this->language, 'translation-list');
		$parameters    = ['limit' => $limit, 'page' => $page, 'search' => $search];
		$queryThisPage = Http::buildQuery($parameters);
		
		$paginationBlock   = $this->createPaginationBlock($page ?? 1, $limit, $search, $translationCount, $hrefThisPage);
		$resultsLimitBlock = $this->createResultsLimitBlock($limit);
		$searchBarBlock    = $this->createSearchBarBlock($limit, $page, $search);
		
		$html[] = $this->startRender
		(
			title:        __('visitor.translation_list.heading'),
			canonicalUri: $hrefThisPage.$queryThisPage,
			cssSheetUris:
			[
				'/css/shared/entity.css',
				'/css/entity-list-page.css'
			]
		);
		
		$html[] = 
		'
		<article>
			<section class="entity-list-heading">
				'.$this->createHeading(__('visitor.translation_list.heading'), 1).'
			</section>
			<section>
				<form class="search-section">
					<section>'.$searchBarBlock.'</section>
					<section>'.$paginationBlock.'</section>
					<section>'.$resultsLimitBlock.'</section>
				</form>
			</section>
		';
		
		$html[] = $this->createTranslationList($translations, 2, 'list-entity');
		
		$html[] = 
		'
			<section>
				<section class="search-section">
					<section></section>
					<section>'.$paginationBlock.'</section>
					<section></section>
				</section>
			</section>
		</article>
		';
		
		$html[] = $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/entity-list-search.js',
				'/js/entity-list-page.js'
			]
		);
		
		$this->echoHtml($html);
	}
	
	final public function renderGamePage(array $game, array $albums, array $characters): void
	{
		$html[] = $this->startRender
		(
			title: $game['transliterated_name'],
			cssSheetUris:
			[
				'/css/entity-page.css',
				'/css/shared/entity.css'
			]
		);
		
		$html[] = 
		'
		<article>
			'.$this->createGamePageHeading($game, 1).'
		';
		
		if ($characters)
		{
			$html[] = 
			'
			<section>
				'.$this->createHeading(__('visitor.game.characters'), 2).'
			</section>
			';
			
			$html[] = $this->createCharacterList($characters, 3, 'related-entity', 'character_game_relation_status');
		}
		
		if ($albums)
		{
			$html[] = 
			'
			<section>
				'.$this->createHeading(__('visitor.game.albums'), 2).'
			</section>
			';
			
			$html[] = $this->createAlbumList($albums, 3, 'related-entity', 'game_album_relation_status');
		}
		
		$html[] = 
		'
			'.$this->createTimestampBlock($game).'
			'.$this->createEntityControlBlock
			(
				[$game],
				['game'],
				$game,
				'Game',
				'edit',
				'delete',
				'report',
				__('visitor.game.controls.edit'),
				__('visitor.game.controls.delete'),
				__('visitor.game.controls.report')
			).'
		</article>
		';
		
		$js = [];
		if (Authorizer::isCurrentUserAdministrator())
		{
			$js[] = '/js/moderation/change-status-select.js';
		}
		
		$html[] = $this->endRender
		(
			jsScriptUris: $js
		);
		
		$this->echoHtml($html);
	}
	
	final public function renderAlbumPage(array $album, array $songs, array $games): void
	{
		$html[] = $this->startRender
		(
			title: $album['transliterated_name'],
			cssSheetUris:
			[
				'/css/entity-page.css',
				'/css/shared/entity.css',
				'/css/track-list-page.css'
			]
		);
		
		$html[] = 
		'
		<article>
			'.$this->createAlbumPageHeading($album, 1).'
		';
		
		$html[] = $this->createAlbumSongList($album, $songs, 2);
		
		if ($games)
		{
			$html[] = 
			'
			<section>
				'.$this->createHeading(__('visitor.album.games'), 2).'
			</section>
			';
			
			$html[] = $this->createGameList($games, 3, 'related-entity', 'game_album_relation_status');
		}
		
		$html[] = 
		'
			'.$this->createTimestampBlock($album).'
			'.$this->createEntityControlBlock
			(
				[$album],
				['album'],
				$album,
				'Album',
				'edit',
				'delete',
				'report',
				__('visitor.album.controls.edit'),
				__('visitor.album.controls.delete'),
				__('visitor.album.controls.report')
			).'
		</article>
		';
		
		$js = [];
		if (Authorizer::isCurrentUserAdministrator())
		{
			$js[] = '/js/moderation/change-status-select.js';
		}
		
		$html[] = $this->endRender
		(
			jsScriptUris: $js
		);
		
		$this->echoHtml($html);
	}
	
	final public function renderArtistPage
	(
		array $artist,
		array $aliases,
		array $characters,
		array $songs
	): void
	{
		$html[] = $this->startRender
		(
			title: $artist['transliterated_name'],
			cssSheetUris:
			[
				'/css/entity-page.css',
				'/css/shared/entity.css'
			]
		);
		
		$html[] = 
		'
		<article>
			'.$this->createArtistPageHeading($artist, 1).'
		';
		
		if ($aliases)
		{
			$html[] = 
			'
			<section>
				'.$this->createHeading(__('visitor.artist.aliases'), 2).'
			</section>
			';
			
			$html[] = $this->createArtistList($aliases, 3, 'related-entity', 'status', false);
		}
		
		if ($characters)
		{
			$html[] = 
			'
			<section>
				'.$this->createHeading(__('visitor.artist.characters'), 2).'
			</section>
			';
			
			$html[] = $this->createCharacterList($characters, 3, 'related-entity', 'song_artist_character_relation_status', false);
		}
		
		if ($songs)
		{
			$html[] = 
			'
			<section>
				'.$this->createHeading(__('visitor.artist.songs'), 2).'
			</section>
			';
			
			$html[] = $this->createSongList($songs, 3, 'related-entity', 'song_artist_character_relation_status');
		}
		
		$html[] = 
		'
			'.$this->createTimestampBlock($artist).'
			'.$this->createEntityControlBlock
			(
				[$artist],
				['artist'],
				$artist,
				'Artist',
				'edit',
				'delete',
				'report',
				__('visitor.artist.controls.edit'),
				__('visitor.artist.controls.delete'),
				__('visitor.artist.controls.report')
			).'
		</article>
		';
		
		$js = [];
		if (Authorizer::isCurrentUserAdministrator())
		{
			$js[] = '/js/moderation/change-status-select.js';
		}
		
		$html[] = $this->endRender
		(
			jsScriptUris: $js
		);
		
		$this->echoHtml($html);
	}
	
	final public function renderCharacterPage
	(
		array $character,
		array $games,
		array $artists,
		array $songs
	): void
	{
		$html[] = $this->startRender
		(
			title: $character['transliterated_name'],
			cssSheetUris:
			[
				'/css/entity-page.css',
				'/css/shared/entity.css'
			]
		);
		
		$html[] = 
		'
		<article>
			'.$this->createCharacterPageHeading($character, 1).'
		';
		
		if ($games)
		{
			$html[] = 
			'
			<section>
				'.$this->createHeading(__('visitor.character.games'), 2).'
			</section>
			';
			
			$html[] = $this->createGameList($games, 3, 'related-entity', 'character_game_relation_status');
		}
		
		if ($artists)
		{
			$html[] = 
			'
			<section>
				'.$this->createHeading(__('visitor.character.artists'), 2).'
			</section>
			';
			
			$html[] = $this->createArtistList($artists, 3, 'related-entity', 'song_artist_character_relation_status', false);
		}
		
		if ($songs)
		{
			$html[] = 
			'
			<section>
				'.$this->createHeading(__('visitor.character.songs'), 2).'
			</section>
			';
			
			$html[] = $this->createSongList($songs, 3, 'related-entity', 'song_artist_character_relation_status');
		}
		
		$html[] = 
		'
			'.$this->createTimestampBlock($character).'
			'.$this->createEntityControlBlock
			(
				[$character],
				['character'],
				$character,
				'Character',
				'edit',
				'delete',
				'report',
				__('visitor.character.controls.edit'),
				__('visitor.character.controls.delete'),
				__('visitor.character.controls.report')
			).'
		</article>
		';
		
		$js = [];
		if (Authorizer::isCurrentUserAdministrator())
		{
			$js[] = '/js/moderation/change-status-select.js';
		}
		
		$html[] = $this->endRender
		(
			jsScriptUris: $js
		);
		
		$this->echoHtml($html);
	}
	
	final public function renderNoLyricsPage(array $album, array $song): void
	{
		$headingText = __('visitor.lyrics.heading.lyrics', ['name' => $song['transliterated_name']]);
		
		$html[] = $this->startRender
		(
			title:        $headingText,
			allowIndex:   false,
			cssSheetUris:
			[
				'/css/no-lyrics-page.css',
				'/css/shared/entity.css'
			]
		);
		
		$html[] = 
		'
		<article>
			'.$this->createLyricsPageHeading($headingText, $album, $song, null, null, null, null).'
			<section id="no-lyrics">
				<section>
					<p>'.__('visitor.lyrics.no_lyrics').'</p>
		';
		
		$buttonAttributes = ['href'  => Http::buildPath($this->language, 'album', $album['uri'], 'song', $song['uri'], 'add-lyrics')];
		$buttonLabel      = __('visitor.lyrics.controls.lyrics.add');
		$buttonAccess     = RoleManager::getStateToAddLyrics($song);
		
		$addLyricsButton = $this->createButtonAsRestrictedLink($buttonLabel, $buttonAccess, $buttonAttributes);
		
		$html[] = 
		'
					<p>'.$addLyricsButton.'</p>
				</section>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	final public function renderLyricsPage
	(
		array      $album,
		array      $song,
		array|null $originalAlbum,
		array|null $originalSong,
		array      $performers,
		array      $translations
	): void
	{
		$headingText = __('visitor.lyrics.heading.lyrics', ['name' => $song['transliterated_name']]);
		
		if ($originalAlbum && $originalSong)
		{
			$albumToShow = $originalAlbum;
			$songToShow  = $originalSong;
			
			$parts    = explode('/', $_SERVER['REQUEST_URI']);
			$parts[3] = $originalAlbum['uri'];
			$parts[5] = $originalSong['uri'];
			
			$canonicalUri = implode('/', $parts);
		}
		else
		{
			$albumToShow  = $album;
			$songToShow   = $song;
			$canonicalUri = $_SERVER['REQUEST_URI'];
		}
		
		$html[] = $this->startRender
		(
			title:        $headingText,
			canonicalUri: $canonicalUri,
			cssSheetUris:
			[
				'/css/lyrics-page.css',
				'/css/shared/entity.css'
			]
		);
		
		$html[] = 
		'
		<article>
			'.$this->createLyricsPageHeading
			(
				$headingText,
				$album,
				$song,
				null,
				$performers,
				$translations,
				$originalSong
			).'
			'.$this->createLyricsControls().'
			<section class="lyrics-section">
				'.$this->createSongLyrics($songToShow).'
				'.$this->createSongNotes($songToShow).'
				'.$this->createTimestampBlock(Authorizer::isCurrentUserAdministrator() ? $song : $songToShow).'
				'.$this->createEntityControlBlock
				(
					[$album, $song],
					['album', 'song'],
					$song,
					'Lyrics',
					'edit-lyrics',
					'delete-lyrics',
					'report-lyrics',
					__('visitor.lyrics.controls.lyrics.edit'),
					__('visitor.lyrics.controls.lyrics.report'),
					__('visitor.lyrics.controls.lyrics.delete')
				).'
			</section>
		</article>
		';
		
		$js =
		[
			'/js/shared/lyrics-controls.js',
			'/js/lyrics-page.js'
		];
		
		if (Authorizer::isCurrentUserAdministrator())
		{
			$js[] = '/js/moderation/change-status-select.js';
		}
		
		$html[] = $this->endRender
		(
			jsScriptUris: $js
		);
		
		$this->echoHtml($html);
	}
	
	final public function renderTranslationPage
	(
		array      $album,
		array      $song,
		array|null $originalAlbum,
		array|null $originalSong,
		array      $translation,
		array      $performers,
		array      $translations
	): void
	{
		$headingText = __('visitor.lyrics.heading.translation', ['name' => $song['transliterated_name'], 'language' => Localizer::localizeLanguageName($translation)]);
		
		if ($originalAlbum && $originalSong)
		{
			$albumToShow = $originalAlbum;
			$songToShow  = $originalSong;
			
			$parts    = explode('/', $_SERVER['REQUEST_URI']);
			$parts[3] = $originalAlbum['uri'];
			$parts[5] = $originalSong['uri'];
			
			$canonicalUri = implode('/', $parts);
		}
		else
		{
			$albumToShow  = $album;
			$songToShow   = $song;
			$canonicalUri = $_SERVER['REQUEST_URI'];
		}
		
		$html[] = $this->startRender
		(
			title:        $headingText,
			canonicalUri: $canonicalUri,
			cssSheetUris:
			[
				'/css/translation-page.css',
				'/css/shared/entity.css'
			]
		);
		
		$html[] = 
		'
		<article>
			'.$heading = $this->createLyricsPageHeading
			(
				$headingText,
				$album,
				$song,
				$translation,
				$performers,
				$translations,
				$originalSong
			).'
			'.$this->createLyricsControls().'
			<section class="lyrics-section">
				'.$this->createTranslationLyrics($translation).'
				'.$this->createSongLyrics($songToShow).'
				'.$this->createTranslationNotes($translation).'
				'.$this->createSongNotes($songToShow).'
				'.$this->createTimestampBlock($translation).'
				'.$this->createTimestampBlock(Authorizer::isCurrentUserAdministrator() ? $song : $songToShow).'
				'.$this->createEntityControlBlock
				(
					[$album, $song, $translation],
					['album', 'song', 'translation'],
					$translation,
					'Translation',
					'edit',
					'delete',
					'report',
					__('visitor.lyrics.controls.translation.edit'),
					__('visitor.lyrics.controls.translation.report'),
					__('visitor.lyrics.controls.translation.delete')
				).'
				'.$this->createEntityControlBlock
				(
					[$album, $song],
					['album', 'song'],
					$song,
					'Lyrics',
					'edit-lyrics',
					'delete-lyrics',
					'report-lyrics',
					__('visitor.lyrics.controls.lyrics.edit'),
					__('visitor.lyrics.controls.lyrics.report'),
					__('visitor.lyrics.controls.lyrics.delete')
				).'
			</section>
		</article>
		';
		
		$js =
		[
			'/js/shared/lyrics-controls.js',
			'/js/lyrics-page.js',
			'/js/translation-page.js'
		];
		if (Authorizer::isCurrentUserAdministrator())
		{
			$js[] = '/js/moderation/change-status-select.js';
		}
		
		$html[] = $this->endRender
		(
			jsScriptUris: $js
		);
		
		$this->echoHtml($html);
	}
	
	final public function renderFeedbackPage
	(
		array       $feedbacks,
		string|null $feedback           = null,
		InputError  $error              = InputError::None,
		string|null $captchaBase64Image = ''
	): void
	{
		$html[] = $this->startRender
		(
			title: __('visitor.feedback.heading'),
			allowIndex:   false,
			cssSheetUris:
			[
				'/css/feedback-page.css',
				'/css/shared/captcha.css'
			]
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading(__('visitor.feedback.heading'), 1).'
				'.$this->createParagraph(__('visitor.feedback.description.one')).'
				'.$this->createParagraph(__('visitor.feedback.description.two')).'
				'.$this->createParagraph(__('visitor.feedback.description.three')).'
				'.$this->createParagraph(__('visitor.feedback.description.four')).'
				<form method="POST">
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $_SESSION['feedback_page']['nonce']]).'
					'.$this->createTextarea(attributes: ['name' => 'message', 'maxlength' => self::FEEDBACK_MAX_LENGTH, 'placeholder' => __('base.controls.textarea.hint'), 'required' => true]).'
					'.$this->createParagraph(__("base.input_error.{$error->value}")).'
					<section>
						'.$this->createCaptchaInput(['required' => true]).'
						'.$this->createCaptchaImage($captchaBase64Image).'
						'.$this->createSubmitButton(__('base.controls.button.submit')).'
					</section>
				</form>
			</section>
		';
		
		foreach ($feedbacks as $feedback)
		{
			$author    = $feedback['sender_username'] ?? __('visitor.feedback.message.anonymous');
			$ip        = Cryptography::generateSimpleHash($feedback['sender_ip']);
			$timestamp = $feedback['message_timestamp'];
			$message   = $feedback['message'];
			$id        = Authorizer::isCurrentUserAdministrator() ? ' data-id="'.htmlspecialchars($feedback['id']).'"' : '';
			
			$heading = __('visitor.feedback.message.heading', ['username' => $author, 'id' => $ip, 'timestamp' => $timestamp]);
			
			$html[] = 
			'
			<section class="feedback-message"'.$id.'>
				'.$this->createHeading($heading, 3).'
				'.$this->createParagraph($message).'
			';
			
			if ($feedback['moderator_username'])
			{
				$moderator = $feedback['moderator_username'];
				$timestamp = $feedback['reply_timestamp'];
				$reply     = $feedback['reply'];
				
				$heading = __('visitor.feedback.message.heading_staff', ['timestamp' => $timestamp]);
				
				$html[] = 
				'
				<section class="feedback-reply">
					'.$this->createHeading($heading, 3).'
					'.$this->createParagraph($reply).'
				</section>
				';
			}
			
			if (Authorizer::isCurrentUserAdministrator())
			{
				$html[] = 
				'
				<section class="feedback-reply-controls">
					'.$this->createTextarea().'
				</section>
				<section class="feedback-reply-controls">
					'.$this->createButton(__('visitor.feedback.controls.delete'), ['class' => ['delete-feedback-button']]).'
					'.$this->createFillerSection().'
					'.$this->createButton(__('visitor.feedback.controls.reply'), ['class' => ['send-reply-button']]).'
				</section>
				';
			}
			
			$html[] = 
			'
			</section>
			';
		}
		
		$html[] = 
		'
		</article>
		';
		
		$js = [];
		if (Authorizer::isCurrentUserAdministrator())
		{
			$js[] = '/js/moderation/feedback.js';
		}
		
		$html[] = $this->endRender
		(
			jsScriptUris: $js
		);
		
		$this->echoHtml($html);
	}
	
	final public function renderReportPage(string $entityType, string $entityName, string $entityLink)
	{
		$heading = __("visitor.report.heading.{$entityType}", ['name' => $entityName]);
		$reportLink = Http::buildPath($this->language, 'report');
		
		$html[] = $this->startRender
		(
			title:        $heading,
			allowIndex:   false,
			allowFollow:  false,
			cssSheetUris: ['/css/window-in-center-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
			</section>
			<section>
				'.$this->createParagraph(__('visitor.report.description.one')).'
				'.$this->createParagraph(__('visitor.report.description.two')).'
				<form method="POST" autocomplete="off">
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $_SESSION['report_page']['nonce']]).'
					'.$this->createTextarea
					(
						attributes:
						[
							'name'        => 'report_text',
							'maxlength'   => self::REPORT_MAX_LENGTH,
							'placeholder' => __('base.controls.textarea.hint'),
							'required'    => true,
						]
					).'
					<section>
						'.$this->createReturnButton($entityLink, __('base.controls.button.return')).'
						'.$this->createFillerSection().'
						'.$this->createSubmitButton(__('base.controls.button.submit')).'
						'.$this->createHiddenInput(['name' => 'entity_uri', 'value' => $entityLink]).'
					</section>
				</form>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	final public function renderUserPage
	(
		array $userData,
		int   $relatedGameCount,
		int   $relatedAlbumCount,
		int   $relatedArtistCount,
		int   $relatedCharacterCount,
		int   $relatedSongCount,
		int   $relatedTranslationCount
	): void
	{
		$heading = __('visitor.user.heading', ['name' => htmlspecialchars($userData['username'])]);
		$role    = Localizer::localizeLanguageName($userData);
		
		$html[] = $this->startRender
		(
			title:        $heading,
			allowIndex:   false,
			cssSheetUris:
			[
				'/css/user-page.css',
				'/css/shared/entity.css'
			]
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				'.$this->createParagraph(__('visitor.user.role', ['role' => $role])).'
				'.$this->createParagraph(__('visitor.user.created', ['timestamp' => $userData['timestamp_created']])).'
			</section>
		';
		
		if (!Validation::isNullOrEmpty($userData['about_me']))
		{
			$html[] =
			'
			<section>
				'.$this->createHeading(__('visitor.user.about_me'), 2).'
			';
			
			$aboutMe = explode("\n", $userData['about_me']);
			
			foreach ($aboutMe as $line)
			{
				$html[] =
				'
				'.$this->createParagraph($line).'
				';
			}
			
			$html[] =
			'
			</section>
			';
		}
		
		$html[] = 
		'
			
			<section>
				'.$this->createHeading(__('visitor.user.contributions.heading'), 2).'
				'.$this->createParagraph(__('visitor.user.contributions.game', ['count' => $relatedGameCount])).'
				'.$this->createParagraph(__('visitor.user.contributions.album', ['count' => $relatedAlbumCount])).'
				'.$this->createParagraph(__('visitor.user.contributions.artist', ['count' => $relatedArtistCount])).'
				'.$this->createParagraph(__('visitor.user.contributions.character', ['count' => $relatedCharacterCount])).'
				'.$this->createParagraph(__('visitor.user.contributions.lyrics', ['count' => $relatedSongCount])).'
				'.$this->createParagraph(__('visitor.user.contributions.translation', ['count' => $relatedTranslationCount])).'
			</section>
		';
		
		if (Authorizer::isCurrentUser($userData['id']) || Authorizer::isCurrentUserAdministrator())
		{
			$label1 = __('visitor.user.controls.change_about_me');
			$label2 = __('visitor.user.controls.change_username');
			//$label3 = __('visitor.user.controls.change_email');
			$label4 = __('visitor.user.controls.change_password');
			$label5 = __('visitor.user.controls.delete_account');
			
			$href1 = Http::buildPath($this->language, 'user', $userData['uri'], 'change-about-me');
			$href2 = Http::buildPath($this->language, 'user', $userData['uri'], 'change-username');
			//$href3 = Http::buildPath($this->language, 'user', $userData['uri'], 'change-email');
			$href4 = Http::buildPath($this->language, 'user', $userData['uri'], 'change-password');
			$href5 = Http::buildPath($this->language, 'user', $userData['uri'], 'delete-account');
			
			$attributes1 = ['href' => $href1];
			$attributes2 = ['href' => $href2];
			//$attributes3 = ['href' => $href3];
			$attributes4 = ['href' => $href4];
			$attributes5 = ['href' => $href5];
			
			$access1 = (Authorizer::isCurrentUserViolator()) ? AccessState::AgentIsViolatorError : AccessState::Ok;
			$access2 = (Authorizer::isCurrentUserViolator()) ? AccessState::AgentIsViolatorError : AccessState::Ok;
			//$access3 = (Authorizer::isCurrentUserViolator()) ? AccessState::AgentIsViolatorError : AccessState::Ok;
			$access4 = (Authorizer::isCurrentUserViolator()) ? AccessState::AgentIsViolatorError : AccessState::Ok;
			$access5 = (Authorizer::isCurrentUserViolator()) ? AccessState::AgentIsViolatorError : AccessState::Ok;
			
			$html[] = 
			'
				<section class="account-control">
					'.$this->createButtonAsRestrictedLink($label1, $access1, $attributes1).'
					'.$this->createButtonAsRestrictedLink($label2, $access2, $attributes2).'
					'.$this->createButtonAsRestrictedLink($label4, $access4, $attributes4).'
					'.$this->createButtonAsRestrictedLink($label5, $access5, $attributes5).'
				</section>
			';
		}
		
		$html[] = 
		'
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	final public function renderAboutPage(): void
	{
		$html[] = $this->startRender
		(
			title: __('visitor.about.heading')
		);
		
		$html[] = 
		'
		<article>
			<section>
				<h1>'.__('visitor.about.heading').'</h1>
				<h2>'.__('visitor.about.subheading1').'</h2>
				<p>'.__('visitor.about.subtext1').'</p>
				<br/>
				<h2>'.__('visitor.about.subheading2').'</h2>
				<p>'.__('visitor.about.subtext2').'</p>
				<br/>
				<h2>'.__('visitor.about.subheading3').'</h2>
				<p>'.__('visitor.about.subtext3').'</p>
				<br/>
				<h2>'.__('visitor.about.subheading4').'</h2>
				<p>'.__('visitor.about.subtext4').'</p>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	final public function renderPolicyPage(): void
	{
		$html[] = $this->startRender
		(
			title: __('visitor.policy.heading')
		);
		
		$html[] = 
		'
		<article>
			<section>
				<h1>'.__('visitor.policy.heading').'</h1>
				<p>'.__('visitor.policy.description').'</p>
				<br/>
				<h2>'.__('visitor.policy.warning').'</h2>
				<br/>
				<h2>'.__('visitor.policy.content_policy.heading').'</h2>
				<p>'.__('visitor.policy.content_policy.one').'</p>
				<p>'.__('visitor.policy.content_policy.two').'</p>
				<p>'.__('visitor.policy.content_policy.three').'</p>
				<p>'.__('visitor.policy.content_policy.four').'</p>
				<p>'.__('visitor.policy.content_policy.five').'</p>
				<p>'.__('visitor.policy.content_policy.six').'</p>
				<p>'.__('visitor.policy.content_policy.seven').'</p>
				<p>'.__('visitor.policy.content_policy.eight').'</p>
				<p>'.__('visitor.policy.content_policy.nine').'</p>
				<p>'.__('visitor.policy.content_policy.ten').'</p>
				<br/>
				<h2>'.__('visitor.policy.content_warning.heading').'</h2>
				<p>'.__('visitor.policy.content_warning.one').'</p>
				<p>'.__('visitor.policy.content_warning.two').'</p>
				<p>'.__('visitor.policy.content_warning.three').'</p>
				<br/>
				<h2>'.__('visitor.policy.rightholder.heading').'</h2>
				<p>'.__('visitor.policy.rightholder.one').'</p>
				<p>'.__('visitor.policy.rightholder.two').'</p>
				<br/>
				<h2>'.__('visitor.policy.privacy_policy.heading').'</h2>
				<p>'.__('visitor.policy.privacy_policy.one').'</p>
				<p>'.__('visitor.policy.privacy_policy.two').'</p>
				<p>'.__('visitor.policy.privacy_policy.three').'</p>
				<p>'.__('visitor.policy.privacy_policy.four').'</p>
				<p>'.__('visitor.policy.privacy_policy.five').'</p>
				<p>'.__('visitor.policy.privacy_policy.six').'</p>
				<br/>
				<p>'.__('visitor.policy.last_updated').'</p>
				<p>'.__('visitor.policy.timezone').'</p>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	final public function renderRulesPage(): void
	{
		$html[] = $this->startRender
		(
			title: __('visitor.rules.heading')
		);
		
		$guide = '<a href="'.Http::buildPath($this->language, 'writing-guide').'" target="_blank">'.__('base.footer.guide').'</a>';
		
		$html[] = 
		'
		<article>
			<section>
				<h1>'.__('visitor.rules.heading').'</h1>
				<h2>'.__('visitor.rules.behavior.heading').'</h2>
				<p>'.__('visitor.rules.behavior.one').'</p>
				<p>'.__('visitor.rules.behavior.two').'</p>
				<p>'.__('visitor.rules.behavior.three').'</p>
				<p>'.__('visitor.rules.behavior.four').'</p>
				<p>'.__('visitor.rules.behavior.five', ['writing_guide' => $guide]).'</p>
				<p>'.__('visitor.rules.behavior.six').'</p>
				<p>'.__('visitor.rules.behavior.seven').'</p>
				<br/>
				<h2>'.__('visitor.rules.publication.heading').'</h2>
				<p>'.__('visitor.rules.publication.one').'</p>
				<p>'.__('visitor.rules.publication.two').'</p>
				<p>'.__('visitor.rules.publication.three').'</p>
				<p>'.__('visitor.rules.publication.four').'</p>
				<br/>
				<h2>'.__('visitor.rules.access.heading').'</h2>
				<p>'.__('visitor.rules.access.one').'</p>
				<p>'.__('visitor.rules.access.two').'</p>
				<p>'.__('visitor.rules.access.three').'</p>
				<p>'.__('visitor.rules.access.four').'</p>
				<p>'.__('visitor.rules.access.five').'</p>
				<p>'.__('visitor.rules.access.six').'</p>
				<p>'.__('visitor.rules.access.seven').'</p>
				<p>'.__('visitor.rules.access.eight').'</p>
				<br/>
				<p>'.__('visitor.rules.last_updated').'</p>
				<p>'.__('visitor.rules.timezone').'</p>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	final public function renderWritingGuidePage(): void
	{
		$html[] = $this->startRender
		(
			title:        __('visitor.writing_guide.heading'),
			cssSheetUris: ['/css/writing-guide-page.css']
		);
		
		$formattingLink = '<a href="'.Http::buildPath($this->language, 'lyrics-example').'" target="_blank">'.__('visitor.writing_guide.formatting.example.example').'</a>';
		
		$html[] = 
		'
		<article>
			<section>
				<h1>'.__('visitor.writing_guide.heading').'</h1>
				
				<p>'.__('visitor.writing_guide.contents').'</p>
				<p>- <a href="#lyrics">'.__('visitor.writing_guide.lyrics.heading').'</a></p>
				<p>- <a href="#translation">'.__('visitor.writing_guide.translation.heading').'</a></p>
				<p>- <a href="#romanization">'.__('visitor.writing_guide.romanization.heading').'</a></p>
				<p>- <a href="#formatting">'.__('visitor.writing_guide.formatting.heading').'</a></p>
				<p>- <a href="#useful-links">'.__('visitor.writing_guide.useful_links.heading').'</a></p>
				<p>- <a href="#captcha">'.__('visitor.writing_guide.captcha.heading').'</a></p>
				<br/>
				
				<h2 id="lyrics">'.__('visitor.writing_guide.lyrics.heading').'</h2>
				<p>'.__('visitor.writing_guide.lyrics.one').'</p>
				<p>'.__('visitor.writing_guide.lyrics.two').'</p>
				<p>'.__('visitor.writing_guide.lyrics.three').'</p>
				<p>'.__('visitor.writing_guide.lyrics.four').'</p>
				<p>'.__('visitor.writing_guide.lyrics.five').'</p>
				<p>'.__('visitor.writing_guide.lyrics.six').'</p>
				<p>'.__('visitor.writing_guide.lyrics.seven').'</p>
				<p>'.__('visitor.writing_guide.lyrics.eight').'</p>
				<p>'.__('visitor.writing_guide.lyrics.nine').'</p>
				<p>'.__('visitor.writing_guide.lyrics.ten').'</p>
				<br/>
				
				<h2 id="translation">'.__('visitor.writing_guide.translation.heading').'</h2>
				<p>'.__('visitor.writing_guide.translation.introduction').'<p>
				<p>'.__('visitor.writing_guide.translation.advise.one').'</p>
				<p>'.__('visitor.writing_guide.translation.advise.two').'</p>
				<p>'.__('visitor.writing_guide.translation.advise.three').'</p>
				<p>'.__('visitor.writing_guide.translation.advise.four').'</p>
				<p>'.__('visitor.writing_guide.translation.advise.five').'</p>
				<p>'.__('visitor.writing_guide.translation.advise.six').'</p>
				<p>'.__('visitor.writing_guide.translation.advise.seven').'</p>
				<p>'.__('visitor.writing_guide.translation.advise.eight').'</p>
				<br/>
				
				<p>'.__('visitor.writing_guide.translation.recommendations.one').'</p>
				<p>'.__('visitor.writing_guide.translation.recommendations.two').'</p>
				<p>'.__('visitor.writing_guide.translation.recommendations.three').'</p>
				<p>'.__('visitor.writing_guide.translation.recommendations.four').'</p>
				<p>'.__('visitor.writing_guide.translation.recommendations.five').'</p>
				<p>'.__('visitor.writing_guide.translation.recommendations.six').'</p>
				<p>'.__('visitor.writing_guide.translation.recommendations.seven').'</p>
				<p>'.__('visitor.writing_guide.translation.recommendations.eight').'</p>
				<br/>
				
				<h2 id="romanization">'.__('visitor.writing_guide.romanization.heading').'</h2>
				<p>'.__('visitor.writing_guide.romanization.introduction.one').'</p>
				<p>'.__('visitor.writing_guide.romanization.introduction.two').'</p>
				<p>'.__('visitor.writing_guide.romanization.introduction.three').'</p>
				<br/>
				
				<h3>'.__('visitor.writing_guide.romanization.allowed_symbols.heading').'</h3>
				<p>'.__('visitor.writing_guide.romanization.allowed_symbols.description').'</p>
				<br/>
				
				<h3>'.__('visitor.writing_guide.romanization.name_order.heading').'</h3>
				<p>'.__('visitor.writing_guide.romanization.name_order.description').'</p>
				<br/>
				
				<h3>'.__('visitor.writing_guide.romanization.capitalization.heading').'</h3>
				<p>'.__('visitor.writing_guide.romanization.capitalization.description').'</p>
				<br/>
				
				<h3>'.__('visitor.writing_guide.romanization.conversion.heading').'</h3>
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
				<p>'.__('visitor.writing_guide.romanization.conversion.notes').'</p>
				<p>1. <span class="highlight">ん</span>＋あ/い/う/え/お/や/ゆ/よ　→　<span class="highlight">n\'</span>＋a/i/u/e/o/ya/yu/yo;</p>
				<p>2. か<span class="highlight">あ</span> ＝ か<span class="highlight">ー</span> ＝ か<span class="highlight">ぁ</span> → ka<span class="highlight">a</span></p>
				<p>3. <span class="highlight">っ</span>か　→　<span class="highlight">k</span>ka</p>
				<p>---- <span class="highlight">っ</span>ち　→　<span class="highlight">c</span>chi, <span class="highlight">っ</span>し　→　<span class="highlight">s</span>shi, <span class="highlight">っ</span>つ　→　<span class="highlight">t</span>tsu</p>
				<p>4. 私<span class="highlight">は</span>、私<span class="highlight">へ</span>　→　watashi <span class="highlight">wa</span>, watashi <span class="highlight">e</span></p>
				<p>5. おお、おう、えい　→　oo, ou, ei</p>
				<p>6. カタカナ ＝ ひらがな</p>
				<br/>
				
				<h3>'.__('visitor.writing_guide.romanization.transliterating.heading').'</h3>
				<p>'.__('visitor.writing_guide.romanization.transliterating.description.one').'</p>
				<p>---- 「<span class="highlight">愛愛しい</span>」は日本語の言葉。　→　"<span class="highlight">Aiaishii</span>" wa nihongo no kotoba.</p>
				<p>'.__('visitor.writing_guide.romanization.transliterating.description.two').'</p>
				<p>---- 「<span class="highlight">らぶらぶ</span>」は日本語の言葉。　→　"<span class="highlight">Raburabu</span>" wa nihongo no kotoba.</p>
				<p>---- 「<span class="highlight">ラブラブ</span>」は日本語の言葉。　→　"<span class="highlight">Raburabu</span>" wa nihongo no kotoba.</p>
				<p>---- <span class="mistake">「<span class="highlight">ラブラブ</span>」は日本語の言葉。　→　"<span class="highlight">Love-love</span>" wa nihongo no kotoba.</span></p>
				<p>---- らぶらぶ ＝ ラブラブ　→　raburabu</p>
				<p>'.__('visitor.writing_guide.romanization.transliterating.description.three').'</p>
				<p>---- 「<span class="highlight">love-love</span>」は英語の言葉。　→　"<span class="highlight">LOVE-LOVE</span>" wa eigo no kotoba.</p>
				<p>---- 「<span class="highlight">любовь-морковь</span>」はロシア語の言葉。　→　"<span class="highlight">ЛЮБОВЬ-МОРКОВЬ</span>" wa roshiago no kotoba.</p>
				<p>---- らぶらぶ ＝ ラブラブ ≉ love-love</p>
				<br/>
				
				<h3>'.__('visitor.writing_guide.romanization.together_or_apart.heading').'</h3>
				<p>1. <span class="highlight">こ…、そ…、あ…、ど…</span>： '.__('visitor.writing_guide.romanization.together_or_apart.together').'</p>
				<p>---- ここ　→　koko</p>
				<p>---- それ　→　sore</p>
				<p>---- あの　→　ano</p>
				<p>---- どんな　→　donna</p>
				<p>2. <span class="highlight">〇＋は、じゃ、へ、と、が、か、を、に、まで、で、も、から、って、の、…</span>： '.__('visitor.writing_guide.romanization.together_or_apart.apart').'</p>
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
				<p>---- 後悔せずに　→　koukaisezu ni</p>
				<p>---- 使ってもいけない　→　tsukatte mo ikenai</p>
				<p>---- 幸福なのに　→　koufuku na no ni</p>
				<p>---- ように　→　you ni</p>
				<p>3. <span class="highlight">〇＋ん、の、よ、ね、わ、…</span>： '.__('visitor.writing_guide.romanization.together_or_apart.apart').'</p>
				<p>---- あるんだ　→　aru n da</p>
				<p>---- あるの　→　aru no</p>
				<p>---- そうだよね　→　sou da yo ne</p>
				<p>---- あるわ　→　aru wa</p>
				<p>4. <span class="highlight">〇＋な</span>： '.__('visitor.writing_guide.romanization.together_or_apart.together').'</p>
				<p>---- すてきな［キミ］　→　sutekina [kimi]</p>
				<p>---- 自由な［世界］　→　jiyuuna [sekai]</p>
				<p>---- ような［私］　→　youna [watashi]</p>
				<p>5. <span class="highlight">〇＋たち、ら、ちゃん、くん、さん、だらけ、らしい、…</span>： '.__('visitor.writing_guide.romanization.together_or_apart.together').'</p>
				<p>---- 私達　→　watashitachi</p>
				<p>---- 僕ら　→　bokura</p>
				<p>---- 神様　→　kamisama</p>
				<p>---- 自分らしい　→　jibunrashii</p>
				<p>---- 花子ちゃん　→　Hanakochan</p>
				<p>---- 体中　→　karadajuu</p>
				<p>6. <span class="highlight">だい、お、ご、…＋〇</span>： '.__('visitor.writing_guide.romanization.together_or_apart.together').'</p>
				<p>---- 大嫌い　→　daikirai</p>
				<p>---- お電話　→　odenwa</p>
				<p>---- ご家族　→　gokazoku</p>
				<p>---- ご主人様　→　goshujinsama</p>
				<p>7. <span class="highlight">〇＋時、分、秒、センチ、ミリ、…</span>： '.__('visitor.writing_guide.romanization.together_or_apart.together').'</p>
				<p>---- １０時１７分　→　juuji juunanafun</p>
				<p>---- １５センチ　→　juugosenchi</p>
				<p>---- ゼロ秒　→　zerobyou</p>
				<p>---- ３６０°　→　sanbyakurokujuudo</p>
				<p>8. <span class="highlight">〇＋〇</span>： '.__('visitor.writing_guide.romanization.together_or_apart.together').'</p>
				<p>---- 使い方　→　tsukaikata</p>
				<p>---- 言い訳　→　iiwake</p>
				<p>---- 目覚め　→　mezame</p>
				<p>---- 夕立　→　yuudachi</p>
				<p>---- 今夜　→　kon\'ya</p>
				<p>---- キラキラ　→　kirakira</p>
				<p>---- 懐中時計　→　kaichuudokei</p>
				<p>---- 無限軌道　→　mugenkidou</p>
				<p>9. <span class="highlight">〇＋する</span>： '.__('visitor.writing_guide.romanization.together_or_apart.together').'</p>
				<p>---- 電話する　→　denwasuru</p>
				<p>---- お電話できます　→　odenwadekimasu</p>
				<p>10. <span class="highlight">〇ながら、〇て、〇たら、〇たり、〇ちゃう、〇べき、…</span>： '.__('visitor.writing_guide.romanization.together_or_apart.together').'</p>
				<p>---- 働きながら　→　hatarakinagara</p>
				<p>---- 飲んで　→　nonde</p>
				<p>---- したら　→　shitara</p>
				<p>---- 来たり　→　kitari</p>
				<p>---- 笑っちゃった　→　waracchatta</p>
				<p>---- あるべき　→　arubeki</p>
				<p>11. <span class="highlight">〇たり＋する</span>： '.__('visitor.writing_guide.romanization.together_or_apart.apart').'</p>
				<p>---- 来たり出たりする　→　kitari detari suru</p>
				<p>12. <span class="highlight">〇い＋〇う、〇え＋〇う</span>： '.__('visitor.writing_guide.romanization.together_or_apart.together').'</p>
				<p>---- 歩き出す　→　arukidasu</p>
				<p>---- 語り始める　→　katarihajimeru</p>
				<p>---- 信じ続く　→　shinjitsuzuku</p>
				<p>---- 巡り回る　→　megurimawaru</p>
				<p>---- 舞い降り出す　→　maioridasu</p>
				<p>---- 燃え立つ　→　moetatsu</p>
				<p>---- 寝過ごす　→　nesugosu</p>
				<p>13. <span class="highlight">〇て＋〇う</span>： '.__('visitor.writing_guide.romanization.together_or_apart.apart').'</p>
				<p>---- 働いて疲れる　→　hataraite tsukareru</p>
				<p>---- 教えてあげる　→　oshiete ageru</p>
				<p>---- 教えてくれた　→　oshiete kureta</p>
				<p>---- 落ちてしまう　→　ochite shimau</p>
				<p>---- 歩いていく　→　aruite iku</p>
				<p>---- 書いてある　→　kaite aru</p>
				<p>---- 見ている　→　mite iru</p>
				<p>---- 見ていた　→　mite ita</p>
				<p>14. <span class="highlight">〇て＋る、〇て＋く</span>： '.__('visitor.writing_guide.romanization.together_or_apart.together').'</p>
				<p>---- 歩いてく　→　aruiteku</p>
				<p>---- 見てる　→　miteru</p>
				<p>---- 見てた　→　miteta</p>
				<br/>
				
				<h3>'.__('visitor.writing_guide.romanization.other_rules.heading').'</h3>
				<p>1. <span class="highlight">'.__('visitor.writing_guide.romanization.other_rules.special_readings').'</span></p>
				<p>---- 未来　→　mirai</p>
				<p>---- <ruby>未来<rt>あした</rt></ruby>　→　ashita</p>
				<p>2. <span class="highlight">'.__('visitor.writing_guide.romanization.other_rules.character_width').'</span></p>
				<p>---- '.__('visitor.writing_guide.romanization.other_rules.asian').__('visitor.writing_guide.romanization.other_rules.full_width').'（１，２。Ａ　Ｂ！）</p>
				<p>---- '.__('visitor.writing_guide.romanization.other_rules.european').__('visitor.writing_guide.romanization.other_rules.half_width').' (1, 2. A B!)</p>
				<p>3. <span class="highlight">'.__('visitor.writing_guide.romanization.other_rules.punctuation').'</span></p>
				<p>---- '.__('visitor.writing_guide.romanization.other_rules.asian').'「日本語、『引用符』」。　→　"Nihongo, \'In\'youfu\'".</p>
				<p>---- '.__('visitor.writing_guide.romanization.other_rules.european').'“English, ‘quotation marks’”.　→　"ENGLISH, \'QUOTATION MARKS\'".</p>
				<br/>
				
				<h2 id="formatting">'.__('visitor.writing_guide.formatting.heading').'<a class="note" href="#formatting-note">[1]</a></h2>
				<p>'.__('visitor.writing_guide.formatting.introduction.one').'</p>
				<p>'.__('visitor.writing_guide.formatting.introduction.two').'</p>
				<p>'.__('visitor.writing_guide.formatting.introduction.three').'</p>
				<p>'.__('visitor.writing_guide.formatting.introduction.four').'</p>
				
				<p>'.__('visitor.writing_guide.formatting.furigana.one').'</p>
				<p>---- <span class="highlight">{kj}*{fg}*{/fg}</span>'.__('visitor.writing_guide.formatting.furigana.two').'</p>
				<p>---- '.__('visitor.writing_guide.formatting.furigana.three').' {kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby></p>
				<p>---- '.__('visitor.writing_guide.formatting.furigana.four').'</p>
				
				<p>'.__('visitor.writing_guide.formatting.colors.one').'</p>
				<p>---- <span class="highlight">{cl #XXXXXX}*{/cl}</span>'.__('visitor.writing_guide.formatting.colors.two').'</p>
				<p>---- '.__('visitor.writing_guide.formatting.colors.three').' {cl #FF00FF}色づいたテキスト{/cl}　→　<span class="color">色づいたテキスト</span></p>
				<p>---- '.__('visitor.writing_guide.formatting.colors.four').'</p>
				
				<p>'.__('visitor.writing_guide.formatting.notes.one').'</p>
				<p>---- <span class="highlight">{nt}N{/nt}</span>'.__('visitor.writing_guide.formatting.notes.two').'</p>
				<p>---- '.__('visitor.writing_guide.formatting.notes.three').' click me{nt}1{/nt}　→　click me<a id="formatting-note" class="note" href="#formatting">[1]</a></p>
				<p>---- '.__('visitor.writing_guide.formatting.notes.four').'</p>
				
				<p>'.__('visitor.writing_guide.formatting.example.one', ['example' => $formattingLink]).'</p>
				
				<p>'.__('visitor.writing_guide.formatting.restrictions.one').'</p>
				<p>'.__('visitor.writing_guide.formatting.restrictions.two').'</p>
				<p>'.__('visitor.writing_guide.formatting.restrictions.three').'</p>
				<p>'.__('visitor.writing_guide.formatting.restrictions.four').'</p>
				<p>'.__('visitor.writing_guide.formatting.restrictions.five').'</p>
				<p>'.__('visitor.writing_guide.formatting.restrictions.six').'</p>
				<br/>
				
				<h2 id="useful-links">'.__('visitor.writing_guide.useful_links.heading').'</h2>
				<p>'.__('visitor.writing_guide.useful_links.vndb').'<a href="https://vndb.org" target="_blank">https://vndb.org</a></p>
				<p>'.__('visitor.writing_guide.useful_links.vgmdb').'<a href="https://vgmdb.net" target="_blank">https://vgmdb.net</a></p>
				<p>'.__('visitor.writing_guide.useful_links.steamgriddb').'<a href="https://www.steamgriddb.com" target="_blank">https://www.steamgriddb.com</a></p>
				<p>'.__('visitor.writing_guide.useful_links.jisho').'<a href="https://jisho.org" target="_blank">https://jisho.org</a></p>
				<p>'.__('visitor.writing_guide.useful_links.jpdb').'<a href="https://jpdb.io" target="_blank">https://jpdb.io</a></p>
				<p>'.__('visitor.writing_guide.useful_links.kotobank').'<a href="https://kotobank.jp" target="_blank">https://kotobank.jp</a></p>
				<br/>
				
				<h2 id="captcha">'.__('visitor.writing_guide.captcha.heading').'</h2>
				<section>
					<img id="captcha-solution" />
				</section>
			</section>
		</article>
		';
		
		$html[] = $this->endRender
		(
			jsScriptUris: ['/js/writing-guide-page.js']
		);
		
		$this->echoHtml($html);
	}
	
	final public function renderLyricsExamplePage(): void
	{
		$html[] = $this->startRender
		(
			title:        __('visitor.lyrics_example.heading'),
			cssSheetUris: ['/css/translation-page.css']
		);
		
		$html[] =
		'
		<article>
			<section>
				<h1>'.__('visitor.lyrics_example.heading').'</h1>
			</section>
			<section class="lyrics-section">
				<section>
					<h2>'.__('visitor.lyrics_example.formatting').'</h2>
					<h3>'.__('visitor.lyrics_example.markup').'</h3>
					
					<span class="text-line">例えば、コメントを書いてほしいなら</span>
					<span class="text-line">以下の通りでリリックを飾ってください</span>
					<br/>
					
					<span class="text-line">「いろは」は分かりにくい歌{nt}1{/nt}</span>
					<br/>
					
					<span class="text-line">ｗｉｋｉｐｅｄｉａ{nt}2{/nt}で見つかった歌は：</span>
					<br/>
					
					<span class="text-line">{cl #FF0000}いろはにほへと{/cl}</span>
					<span class="text-line">{cl #FF7F00}ちりぬるをわ{/cl}か</span>
					<span class="text-line">よ{cl #FFFF00}たれそつ{/cl}ねな</span>
					<span class="text-line">ら{cl #00FF00}むうゐ{/cl}のおく</span>
					<span class="text-line">やま{cl #0000FF}けふこ{/cl}えて</span>
					<span class="text-line">あさ{cl #4B0082}きゆ{/cl}めみし</span>
					<span class="text-line">ゑひも{cl #9400D3}せ{/cl}す……</span>
					<br/>
					
					<span class="text-line">あらら、歌のテキストに漢字がない！</span>
					<span class="text-line">どうすればいいかなあ？</span>
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
					<h2>'.__('visitor.lyrics_example.result').'</h2>
					<h3>'.__('visitor.lyrics_example.japanese').'</h3>
					
					<span class="text-line">例えば、コメントを書いてほしいなら</span>
					<span class="text-line">以下の通りでリリックを飾ってください</span>
					<br/>
					
					<span class="text-line">「いろは」は分かりにくい歌<a class="note-small" id="lyrics-reference-1" href="#lyrics-note-1">[1]</a></span>
					<br/>
					
					<span class="text-line">ｗｉｋｉｐｅｄｉａ<a class="note-small" id="lyrics-reference-2" href="#lyrics-note-2">[2]</a>で見つかった歌は：</span>
					<br/>
					
					<span class="text-line"><span style="color: #FF0000">いろはにほへと</span></span>
					<span class="text-line"><span style="color: #FF7F00">ちりぬるをわ</span>か</span>
					<span class="text-line">よ<span style="color: #FFFF00">たれそつね</span>な</span>
					<span class="text-line">ら<span style="color: #00FF00">むうゐの</span>おく</span>
					<span class="text-line">やま<span style="color: #0000FF">けふこ</span>えて</span>
					<span class="text-line">あさ<span style="color: #4B0082">きゆ</span>めみし</span>
					<span class="text-line">ゑひも<span style="color: #9400D3">せ</span>す……</span>
					<br/>
					
					<span class="text-line">あらら、歌のテキストに漢字がない！</span>
					<span class="text-line">どうすればいいかなあ？</span>
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
					<h2>'.__('visitor.lyrics_example.notes').'</h2>
					<span class="text-line">{nt}1{/nt}　「{cl #FF9090}いろは{/cl}」は歌でひらがなのまとめ方です</span>
					<span class="text-line">{nt}2{/nt}　インターネットにある{kj}百科事典{fg}ひゃっかじてん{/fg}</span>
				</section>
				
				<section>
					<h2>'.__('visitor.lyrics_example.notes').'</h2>
					<span class="text-line"><a class="note-big" id="lyrics-note-1" href="#lyrics-reference-1">[1]</a>　「<span style="color: #FF9090">いろは</span>」は歌でひらがなのまとめ方です</span>
					<span class="text-line"><a class="note-big" id="lyrics-note-2" href="#lyrics-reference-2">[2]</a>　インターネットにある<ruby>百科事典<rt>ひゃっかじてん</rt></ruby></span>
				</section>
			</section>
		</article>
		';
		
		$html[] = $this->endRender
		(
			jsScriptUris: ['/js/translation-page.js']
		);
		
		$this->echoHtml($html);
	}
}
