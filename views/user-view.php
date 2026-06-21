<?php

require_once 'views/violator-view.php';

class UserView extends ViolatorView
{
	protected const ACCEPTED_IMAGE_TYPES = '.jpg, .jpeg, .png, .webp';
	
	public function __construct(string $language)
	{
		parent::__construct($language);
	}
	
	private function renderGameEditorPage
	(
		array|null $game,
		array|null $relatedAlbums,
		array|null $relatedCharacters,
		array      $albums,
		array      $characters,
		string     $heading
	): void
	{
		if ($game)
		{
			$originalName       = $game['original_name'];
			$transliteratedName = $game['transliterated_name'];
			$localizedName      = $game['localized_name'];
			$isImageUploaded    = $game['is_image_uploaded'];
			$vndbLink           = $game['vndb_id'] ? 'https://vndb.org/v'.$game['vndb_id'] : null;
		}
		else
		{
			$originalName       = null;
			$transliteratedName = null;
			$localizedName      = null;
			$isImageUploaded    = false;
			$vndbLink           = null;
		}
		
		if (Validation::isNullOrEmpty($relatedAlbums))
		{
			$relatedAlbums =
			[
				[
					'id'                         => '',
					'transliterated_name'        => '',
					'game_album_relation_status' => 'unchecked'
				]
			];
		}
		
		if (Validation::isNullOrEmpty($relatedCharacters))
		{
			$relatedCharacters =
			[
				[
					'id'                             => '',
					'transliterated_name'            => '',
					'character_game_relation_status' => 'unchecked'
				]
			];
		}
		
		$defaultReturnLink = Http::buildInternalPath($this->language, 'game-list').'?'.self::ENTITY_LIST_DEFAULT_QUERY;
		
		$html[] = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/editor-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section class="has-tooltip" tooltip-id="1">
						'.$this->createHeadingForInput(\Localization\GameEditorPage\OriginalName, 2, true).'
						'.$this->createTextInput(['name' => 'original-name', 'placeholder' => '蒼の彼方のフォーリズム', 'value' => $originalName, 'required' => true]).'
					</section>
					<section class="has-tooltip" tooltip-id="2">
						'.$this->createHeadingForInput(\Localization\GameEditorPage\TransliteratedName, 2, true).'
						'.$this->createTextInput(['name' => 'transliterated-name', 'placeholder' => 'Ao no Kanata no Foo Rizumu', 'value' => $transliteratedName, 'required' => true, 'pattern' => '[ -~]+']).'
					</section>
					<section class="has-tooltip" tooltip-id="3">
						'.$this->createHeadingForInput(\Localization\GameEditorPage\LocalizedName, 2, false).'
						'.$this->createTextInput(['name' => 'localized-name', 'placeholder' => 'Aokana - Four Rhythms Across the Blue', 'value' => $localizedName]).'
					</section>
		';
		
		if ($isImageUploaded)
		{
			$html[] = 
			'
					<section class="has-tooltip" tooltip-id="4">
						'.$this->createHeadingForInput(\Localization\GameEditorPage\OldLogo, 2, false).'
						'.$this->createGameImage($game).'
					</section>
					<section class="has-tooltip" tooltip-id="5">
						'.$this->createHeadingForInput(\Localization\GameEditorPage\NewLogo, 2, false).'
						'.$this->createFileupload(['name' => 'logo', 'accept' => self::ACCEPTED_IMAGE_TYPES]).'
					</section>
			';
		}
		else
		{
			$html[] = 
			'
					<section class="has-tooltip" tooltip-id="6">
						'.$this->createHeadingForInput(\Localization\GameEditorPage\Logo, 2, false).'
						'.$this->createFileupload(['name' => 'logo', 'accept' => self::ACCEPTED_IMAGE_TYPES]).'
					</section>
			';
		}
		
		$html[] = 
		'
					<section class="has-tooltip" tooltip-id="7">
						'.$this->createHeadingForInput(\Localization\GameEditorPage\VndbLink, 2, false).'
						'.$this->createUrlInput(['name' => 'vndb-link', 'placeholder' => 'https://vndb.org/v12849', 'pattern' => 'https:\/\/vndb\.org\/v\d+', 'value' => $vndbLink]).'
					</section>
					<section class="has-tooltip" tooltip-id="8">
						'.$this->createHeadingForInput(\Localization\GameEditorPage\RelatedAlbums, 2, false).'
		';
		
		foreach ($relatedAlbums as $relatedAlbum)
		{
			$disabled = ($relatedAlbum['game_album_relation_status'] !== 'unchecked');
			
			$html[] = 
			'
						<section class="related-entity-controls">
							'.$this->createSearchableSelect
							(
								iteratedOptions:         $albums,
								selectedOption:          $relatedAlbum,
								addEmptyOption:          true,
								keyToShownValue:         'transliterated_name',
								keysToShownHints:        ['original_name', 'localized_name'],
								keyToSentValue:          'id',
								attributesForSentInput:  ['name' => 'album-ids[]', 'disabled' => $disabled],
								attributesForShownInput: [
								                              'placeholder'             => \Localization\Controls\SelectOption,
															  'data-placeholder-select' => \Localization\Controls\SelectOption,
															  'data-placeholder-filter' => \Localization\Controls\SelectFilter,
															  'disabled'                => $disabled
														 ]
							).'
							'.$this->createAddRowButton
							(
								attributes: ['class' => ['album-select']]
							).'
							'.$this->createDeleteRowButton
							(
								attributes: ['class' => ['album-select'], 'disabled' => $disabled]
							).'
						</section>
			';
		}
		
		$html[] = 
		'
					</section>
					<section class="has-tooltip" tooltip-id="9">
						'.$this->createHeadingForInput(\Localization\GameEditorPage\RelatedCharacters, 2, false).'
		';
		
		foreach ($relatedCharacters as $relatedCharacter)
		{
			$disabled = ($relatedCharacter['character_game_relation_status'] !== 'unchecked');
			
			$html[] = 
			'
						<section class="related-entity-controls">
							'.$this->createSearchableSelect
							(
								iteratedOptions:         $characters,
								selectedOption:          $relatedCharacter,
								addEmptyOption:          true,
								keyToShownValue:         'transliterated_name',
								keysToShownHints:        ['original_name', 'localized_name'],
								keyToSentValue:          'id',
								attributesForSentInput:  ['name' => 'character-ids[]', 'disabled' => $disabled],
								attributesForShownInput: [
								                              'placeholder'             => \Localization\Controls\SelectOption,
															  'data-placeholder-select' => \Localization\Controls\SelectOption,
															  'data-placeholder-filter' => \Localization\Controls\SelectFilter,
															  'disabled'                => $disabled
														 ]
							).'
							'.$this->createAddRowButton
							(
								attributes: ['class' => ['character-select']]
							).'
							'.$this->createDeleteRowButton
							(
								attributes: ['class' => ['character-select'], 'disabled' => $disabled]
							).'
						</section>
			';
		}
		
		$html[] = 
		'
					</section>
					<section class="has-tooltip" tooltip-id="10">
						<section class="page-controls">
							'.$this->createReturnButton($defaultReturnLink).'
							'.$this->createFillerSection().'
							'.$this->createSubmitButton().'
						</section>
					</section>
				</form>
			</section>
			<section>
			</section>
		</article>
		';
		
		$html[] = $this->createTooltipWindow();
		$html[] = $this->createDatalist
		(
			options:
			[
				\Localization\TooltipWindow\DefaultHeading,
				\Localization\GameEditorPage\TooltipHeading\OriginalName,
				\Localization\GameEditorPage\TooltipHeading\TransliteratedName,
				\Localization\GameEditorPage\TooltipHeading\LocalizedName,
				\Localization\GameEditorPage\TooltipHeading\OldLogo,
				\Localization\GameEditorPage\TooltipHeading\NewLogo,
				\Localization\GameEditorPage\TooltipHeading\Logo,
				\Localization\GameEditorPage\TooltipHeading\VndbLink,
				\Localization\GameEditorPage\TooltipHeading\RelatedAlbums,
				\Localization\GameEditorPage\TooltipHeading\RelatedCharacters,
				\Localization\GameEditorPage\TooltipHeading\Controls
			],
			attributes: ['id' => 'tooltip-headings']
		);
		$html[] = $this->createDatalist
		(
			options:
			[
				\Localization\TooltipWindow\DefaultContent,
				\Localization\GameEditorPage\TooltipContent\OriginalName,
				\Localization\GameEditorPage\TooltipContent\TransliteratedName,
				\Localization\GameEditorPage\TooltipContent\LocalizedName,
				\Localization\GameEditorPage\TooltipContent\OldLogo,
				\Localization\GameEditorPage\TooltipContent\NewLogo,
				\Localization\GameEditorPage\TooltipContent\Logo,
				\Localization\GameEditorPage\TooltipContent\VndbLink,
				\Localization\GameEditorPage\TooltipContent\RelatedAlbums,
				\Localization\GameEditorPage\TooltipContent\RelatedCharacters,
				\Localization\GameEditorPage\TooltipContent\Controls
			],
			attributes: ['id' => 'tooltip-contents']
		);
		
		$html[] = $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/add-delete-row-buttons.js',
				'/js/shared/tooltip-window.js',
				'/js/entity-editor-page.js'
			]
		);
		
		$this->echoHtml($html);
	}
	
	private function renderAlbumEditorPage
	(
		array|null $album,
		array|null $relatedGames,
		array|null $relatedSongs,
		int  |null $currentSongCount,
		array      $games,
		string     $heading
	): void
	{
		if ($album)
		{
			$originalName       = $album['original_name'];
			$transliteratedName = $album['transliterated_name'];
			$localizedName      = $album['localized_name'];
			$isImageUploaded    = $album['is_image_uploaded'];
			$vgmdbLink          = $album['vgmdb_id'] ? 'https://vgmdb.net/album/'.$album['vgmdb_id'] : null;
			$songCount          = $album['song_count'];
		}
		else
		{
			$originalName       = null;
			$transliteratedName = null;
			$localizedName      = null;
			$isImageUploaded    = false;
			$vgmdbLink          = null;
			$songCount          = null;
		}
		
		if (Validation::isNullOrEmpty($relatedGames))
		{
			$relatedGames =
			[
				[
					'id'                         => '',
					'transliterated_name'        => '',
					'game_album_relation_status' => 'unchecked'
				]
			];
		}
		
		$defaultReturnLink = Http::buildInternalPath($this->language, 'album-list').'?'.self::ENTITY_LIST_DEFAULT_QUERY;
		
		$html[] = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/editor-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section class="has-tooltip" tooltip-id="1">
						'.$this->createHeadingForInput(\Localization\AlbumEditorPage\OriginalName, 2, true).'
						'.$this->createTextInput(['name' => 'original-name', 'placeholder' => '蒼の彼方のフォーリズム サウンドトラックCD vol.1', 'value' => $originalName, 'required' => true]).'
					</section>
					<section class="has-tooltip" tooltip-id="2">
						'.$this->createHeadingForInput(\Localization\AlbumEditorPage\TransliteratedName, 2, true).'
						'.$this->createTextInput(['name' => 'transliterated-name', 'placeholder' => 'Ao no Kanata no Foo Rizumu Saundotorakku CD VOL.1', 'value' => $transliteratedName, 'required' => true, 'pattern' => '[ -~]+']).'
					</section>
					<section class="has-tooltip" tooltip-id="3">
						'.$this->createHeadingForInput(\Localization\AlbumEditorPage\LocalizedName, 2, false).'
						'.$this->createTextInput(['name' => 'localized-name', 'placeholder' => 'FOUR RHYTHM ACROSS THE BLUE SOUND TRACK CD VOL.01', 'value' => $localizedName]).'
					</section>
		';
		
		if ($isImageUploaded)
		{
			$html[] = 
			'
					<section class="has-tooltip" tooltip-id="4">
						'.$this->createHeadingForInput(\Localization\AlbumEditorPage\OldCover, 2, false).'
						'.$this->createAlbumImage($album).'
					</section>
					<section class="has-tooltip" tooltip-id="5">
						'.$this->createHeadingForInput(\Localization\AlbumEditorPage\NewCover, 2, false).'
						'.$this->createFileupload(['name' => 'cover', 'accept' => self::ACCEPTED_IMAGE_TYPES]).'
					</section>
			';
		}
		else
		{
			$html[] = 
			'
					<section class="has-tooltip" tooltip-id="6">
						'.$this->createHeadingForInput(\Localization\AlbumEditorPage\Cover, 2, false).'
						'.$this->createFileupload(['name' => 'cover', 'accept' => self::ACCEPTED_IMAGE_TYPES]).'
					</section>
			';
		}
		
		$html[] = 
		'
					<section class="has-tooltip" tooltip-id="7">
						'.$this->createHeadingForInput(\Localization\AlbumEditorPage\VgmdbLink, 2, false).'
						'.$this->createUrlInput(['name' => 'vgmdb-link', 'placeholder' => 'https://vgmdb.net/album/56642', 'pattern' => 'https:\/\/vgmdb\.net\/album\/\d+', 'value' => $vgmdbLink]).'
					</section>
					<section class="has-tooltip" tooltip-id="8">
						'.$this->createHeadingForInput(\Localization\AlbumEditorPage\RelatedGames, 2, false).'
		';
		
		foreach ($relatedGames as $relatedGame)
		{
			$disabled = ($relatedGame['game_album_relation_status'] !== 'unchecked');
			
			$html[] = 
			'
						<section class="related-entity-controls">
							'.$this->createSearchableSelect
							(
								iteratedOptions:         $games,
								selectedOption:          $relatedGame,
								addEmptyOption:          true,
								keyToShownValue:         'transliterated_name',
								keysToShownHints:        ['original_name', 'localized_name'],
								keyToSentValue:          'id',
								attributesForSentInput:  ['name' => 'game-ids[]', 'disabled' => $disabled],
								attributesForShownInput: [
								                              'placeholder'             => \Localization\Controls\SelectOption,
															  'data-placeholder-select' => \Localization\Controls\SelectOption,
															  'data-placeholder-filter' => \Localization\Controls\SelectFilter,
															  'disabled'                => $disabled
														 ]
							).'
							'.$this->createAddRowButton
							(
								attributes: ['class' => ['game-select']]
							)
							.'
							'.$this->createDeleteRowButton
							(
								attributes: ['class' => ['game-select'], 'disabled' => $disabled]
							).'
						</section>
			';
		}
		
		$html[] = 
		'
					</section>
					<section class="has-tooltip" tooltip-id="9">
						'.$this->createHeadingForInput(\Localization\AlbumEditorPage\SongCount, 2, true).'
						'.$this->createTextInput(['name' => 'song-count', 'placeholder' => '28', 'pattern' => '\d+', 'value' => $songCount, 'required' => true]).'
					</section>
					<section class="has-tooltip" tooltip-id="10">
						<section class="page-controls">
							'.$this->createReturnButton($defaultReturnLink).'
							'.$this->createFillerSection().'
							'.$this->createSubmitButton().'
						</section>
					</section>
				</form>
			</section>
			<section>
			</section>
		</article>
		';
		
		$html[] = $this->createTooltipWindow();
		$html[] = $this->createDatalist
		(
			options:
			[
				\Localization\TooltipWindow\DefaultHeading,
				\Localization\AlbumEditorPage\TooltipHeading\OriginalName,
				\Localization\AlbumEditorPage\TooltipHeading\TransliteratedName,
				\Localization\AlbumEditorPage\TooltipHeading\LocalizedName,
				\Localization\AlbumEditorPage\TooltipHeading\OldCover,
				\Localization\AlbumEditorPage\TooltipHeading\NewCover,
				\Localization\AlbumEditorPage\TooltipHeading\Cover,
				\Localization\AlbumEditorPage\TooltipHeading\VgmdbLink,
				\Localization\AlbumEditorPage\TooltipHeading\RelatedGames,
				\Localization\AlbumEditorPage\TooltipHeading\SongCount,
				\Localization\AlbumEditorPage\TooltipHeading\Controls
			],
			attributes: ['id' => 'tooltip-headings']
		);
		$html[] = $this->createDatalist
		(
			options:
			[
				\Localization\TooltipWindow\DefaultContent,
				\Localization\AlbumEditorPage\TooltipContent\OriginalName,
				\Localization\AlbumEditorPage\TooltipContent\TransliteratedName,
				\Localization\AlbumEditorPage\TooltipContent\LocalizedName,
				\Localization\AlbumEditorPage\TooltipContent\OldCover,
				\Localization\AlbumEditorPage\TooltipContent\NewCover,
				\Localization\AlbumEditorPage\TooltipContent\Cover,
				\Localization\AlbumEditorPage\TooltipContent\VgmdbLink,
				\Localization\AlbumEditorPage\TooltipContent\RelatedGames,
				\Localization\AlbumEditorPage\TooltipContent\SongCount,
				\Localization\AlbumEditorPage\TooltipContent\Controls
			],
			attributes: ['id' => 'tooltip-contents']
		);
		
		$html[] = $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/add-delete-row-buttons.js',
				'/js/shared/tooltip-window.js',
				'/js/entity-editor-page.js'
			]
		);
		
		$this->echoHtml($html);
	}
	
	private function renderArtistEditorPage
	(
		array|null $artist,
		array      $originalArtists,
		string     $heading
	): void
	{
		if ($artist)
		{
			$originalName       = $artist['original_name'];
			$transliteratedName = $artist['transliterated_name'];
			$localizedName      = $artist['localized_name'];
			$isImageUploaded    = $artist['is_image_uploaded'];
			$vgmdbLink          = $artist['vgmdb_id'] ? 'https://vgmdb.net/artist/'.$artist['vgmdb_id'] : null;
			$originalArtist     = array_find
			(
				$originalArtists,
				function (array $original) use ($artist)
				{
					return $artist['alias_of_artist_id'] === $original['id'];
				}
			);
		}
		else
		{
			$originalName       = null;
			$transliteratedName = null;
			$localizedName      = null;
			$isImageUploaded    = false;
			$vgmdbLink          = null;
			$originalArtist     = null;
		}
		
		$defaultReturnLink = Http::buildInternalPath($this->language, 'artist-list').'?'.self::ENTITY_LIST_DEFAULT_QUERY;
		
		$html[] = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/editor-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section class="has-tooltip" tooltip-id="1">
						'.$this->createHeadingForInput(\Localization\ArtistEditorPage\OriginalName, 2, true).'
						'.$this->createTextInput(['name' => 'original-name', 'placeholder' => 'いとうかなこ', 'value' => $originalName, 'required' => true]).'
					</section>
					<section class="has-tooltip" tooltip-id="2">
						'.$this->createHeadingForInput(\Localization\ArtistEditorPage\TransliteratedName, 2, true).'
						'.$this->createTextInput(['name' => 'transliterated-name', 'placeholder' => 'Itou Kanako', 'value' => $transliteratedName, 'required' => true, 'pattern' => '[ -~]+']).'
					</section>
					<section class="has-tooltip" tooltip-id="3">
						'.$this->createHeadingForInput(\Localization\ArtistEditorPage\LocalizedName, 2, false).'
						'.$this->createTextInput(['name' => 'localized-name', 'placeholder' => 'Kanako Ito', 'value' => $localizedName]).'
					</section>
		';
		
		if ($isImageUploaded)
		{
			$html[] = 
			'
					<section class="has-tooltip" tooltip-id="4">
						'.$this->createHeadingForInput(\Localization\ArtistEditorPage\OldPhoto, 2, false).'
						'.$this->createArtistImage($artist).'
					</section>
					<section class="has-tooltip" tooltip-id="5">
						'.$this->createHeadingForInput(\Localization\ArtistEditorPage\NewPhoto, 2, false).'
						'.$this->createFileupload(['name' => 'photo', 'accept' => self::ACCEPTED_IMAGE_TYPES]).'
					</section>
			';
		}
		else
		{
			$html[] = 
			'
					<section class="has-tooltip" tooltip-id="6">
						'.$this->createHeadingForInput(\Localization\ArtistEditorPage\Photo, 2, false).'
						'.$this->createFileupload(['name' => 'photo', 'accept' => self::ACCEPTED_IMAGE_TYPES]).'
					</section>
			';
		}
		
		$html[] = 
		'
					<section class="has-tooltip" tooltip-id="7">
						'.$this->createHeadingForInput(\Localization\ArtistEditorPage\VgmdbLink, 2, false).'
						'.$this->createUrlInput(['name' => 'vgmdb-link', 'placeholder' => 'https://vgmdb.net/artist/69', 'pattern' => 'https:\/\/vgmdb\.net\/artist\/\d+', 'value' => $vgmdbLink]).'
					</section>
					<section class="has-tooltip" tooltip-id="8">
						'.$this->createHeadingForInput(\Localization\ArtistEditorPage\OriginalArtist, 2, false).'
						'.$this->createSearchableSelect
						(
							iteratedOptions:        $originalArtists,
							disabledOptions:        [$artist],
							selectedOption:         $originalArtist,
							addEmptyOption:         true,
							keyToShownValue:        'transliterated_name',
							keysToShownHints:       ['original_name', 'localized_name'],
							keyToSentValue:         'id',
							attributesForSentInput: ['name' => 'original-artist-id']
						).'
					</section>
					<section class="has-tooltip" tooltip-id="9">
						<section class="page-controls">
							'.$this->createReturnButton($defaultReturnLink).'
							'.$this->createFillerSection().'
							'.$this->createSubmitButton().'
						</section>
					</section>
				</form>
			</section>
			<section>
			</section>
		</article>
		';
		
		$html[] = $this->createTooltipWindow();
		$html[] = $this->createDatalist
		(
			options:
			[
				\Localization\TooltipWindow\DefaultHeading,
				\Localization\ArtistEditorPage\TooltipHeading\OriginalName,
				\Localization\ArtistEditorPage\TooltipHeading\TransliteratedName,
				\Localization\ArtistEditorPage\TooltipHeading\LocalizedName,
				\Localization\ArtistEditorPage\TooltipHeading\OldPhoto,
				\Localization\ArtistEditorPage\TooltipHeading\NewPhoto,
				\Localization\ArtistEditorPage\TooltipHeading\Photo,
				\Localization\ArtistEditorPage\TooltipHeading\VgmdbLink,
				\Localization\ArtistEditorPage\TooltipHeading\OriginalArtist,
				\Localization\ArtistEditorPage\TooltipHeading\Controls
			],
			attributes: ['id' => 'tooltip-headings']
		);
		$html[] = $this->createDatalist
		(
			options:
			[
				\Localization\TooltipWindow\DefaultContent,
				\Localization\ArtistEditorPage\TooltipContent\OriginalName,
				\Localization\ArtistEditorPage\TooltipContent\TransliteratedName,
				\Localization\ArtistEditorPage\TooltipContent\LocalizedName,
				\Localization\ArtistEditorPage\TooltipContent\OldPhoto,
				\Localization\ArtistEditorPage\TooltipContent\NewPhoto,
				\Localization\ArtistEditorPage\TooltipContent\Photo,
				\Localization\ArtistEditorPage\TooltipContent\VgmdbLink,
				\Localization\ArtistEditorPage\TooltipContent\OriginalArtist,
				\Localization\ArtistEditorPage\TooltipContent\Controls
			],
			attributes: ['id' => 'tooltip-contents']
		);
		
		$html[] = $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/add-delete-row-buttons.js',
				'/js/shared/tooltip-window.js',
				'/js/entity-editor-page.js'
			]
		);
		
		$this->echoHtml($html);
	}
	
	private function renderCharacterEditorPage
	(
		array|null $character,
		array|null $relatedGames,
		array      $games,
		string     $heading
	): void
	{
		if ($character)
		{
			$originalName       = $character['original_name'];
			$transliteratedName = $character['transliterated_name'];
			$localizedName      = $character['localized_name'];
			$isImageUploaded    = $character['is_image_uploaded'];
			$vndbLink           = $character['vndb_id'] ? 'https://vndb.org/c'.$character['vndb_id'] : null;
		}
		else
		{
			$originalName       = null;
			$transliteratedName = null;
			$localizedName      = null;
			$isImageUploaded    = false;
			$vndbLink           = null;
		}
		
		if (Validation::isNullOrEmpty($relatedGames))
		{
			$relatedGames =
			[
				[
					'id'                             => '',
					'transliterated_name'            => '',
					'character_game_relation_status' => 'unchecked'
				]
			];
		}
		
		$defaultReturnLink = Http::buildInternalPath($this->language, 'character-list').'?'.self::ENTITY_LIST_DEFAULT_QUERY;
		
		$html[] = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/editor-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section class="has-tooltip" tooltip-id="1">
						'.$this->createHeadingForInput(\Localization\CharacterEditorPage\OriginalName, 2, true).'
						'.$this->createTextInput(['name' => 'original-name', 'placeholder' => '桐生萌郁', 'value' => $originalName, 'required' => true]).'
					</section>
					<section class="has-tooltip" tooltip-id="2">
						'.$this->createHeadingForInput(\Localization\CharacterEditorPage\TransliteratedName, 2, true).'
						'.$this->createTextInput(['name' => 'transliterated-name', 'placeholder' => 'Kiryuu Moeka', 'value' => $transliteratedName, 'required' => true, 'pattern' => '[ -~]+']).'
					</section>
					<section class="has-tooltip" tooltip-id="3">
						'.$this->createHeadingForInput(\Localization\CharacterEditorPage\LocalizedName, 2, false).'
						'.$this->createTextInput(['name' => 'localized-name', 'placeholder' => 'Moeka Kiryu', 'value' => $localizedName]).'
					</section>
		';
		
		if ($isImageUploaded)
		{
			$html[] = 
			'
					<section class="has-tooltip" tooltip-id="4">
						'.$this->createHeadingForInput(\Localization\CharacterEditorPage\OldImage, 2, false).'
						'.$this->createCharacterImage($character).'
					</section>
					<section class="has-tooltip" tooltip-id="5">
						'.$this->createHeadingForInput(\Localization\CharacterEditorPage\NewImage, 2, false).'
						'.$this->createFileupload(['name' => 'image', 'accept' => self::ACCEPTED_IMAGE_TYPES]).'
					</section>
			';
		}
		else
		{
			$html[] = 
			'
					<section class="has-tooltip" tooltip-id="6">
						'.$this->createHeadingForInput(\Localization\CharacterEditorPage\Image, 2, false).'
						'.$this->createFileupload(['name' => 'image', 'accept' => self::ACCEPTED_IMAGE_TYPES]).'
					</section>
			';
		}
		
		$html[] = 
		'
					<section class="has-tooltip" tooltip-id="7">
						'.$this->createHeadingForInput(\Localization\CharacterEditorPage\VndbLink, 2, false).'
						'.$this->createUrlInput(['name' => 'vndb-link', 'placeholder' => 'https://vndb.org/c6496', 'pattern' => 'https:\/\/vndb\.org\/c\d+', 'value' => $vndbLink]).'
					</section>
					<section class="has-tooltip" tooltip-id="8">
						'.$this->createHeadingForInput(\Localization\CharacterEditorPage\RelatedGames, 2, false).'
		';
		
		foreach ($relatedGames as $relatedGame)
		{
			$disabled = ($relatedGame['character_game_relation_status'] !== 'unchecked');
			
			$html[] = 
			'
						<section class="related-entity-controls">
							'.$this->createSearchableSelect
							(
								iteratedOptions:         $games,
								selectedOption:          $relatedGame,
								addEmptyOption:          true,
								keyToShownValue:         'transliterated_name',
								keysToShownHints:        ['original_name', 'localized_name'],
								keyToSentValue:          'id',
								attributesForSentInput:  ['name' => 'game-ids[]', 'disabled' => $disabled],
								attributesForShownInput: [
								                              'placeholder'             => \Localization\Controls\SelectOption,
															  'data-placeholder-select' => \Localization\Controls\SelectOption,
															  'data-placeholder-filter' => \Localization\Controls\SelectFilter,
															  'disabled'                => $disabled
														 ]
							).'
							'.$this->createAddRowButton
							(
								attributes: ['class' => ['game-select']]
							).'
							'.$deleteRowButton = $this->createDeleteRowButton
							(
								attributes: ['class' => ['game-select'], 'disabled' => $disabled]
							)
							.'
						</section>
			';
		}
		
		$html[] = 
		'
					</section>
					<section class="has-tooltip" tooltip-id="9">
						<section class="page-controls">
							'.$this->createReturnButton($defaultReturnLink).'
							'.$this->createFillerSection().'
							'.$this->createSubmitButton().'
						</section>
					</section>
				</form>
			</section>
			<section>
			</section>
		</article>
		';
		
		$html[] = $this->createTooltipWindow();
		$html[] = $this->createDatalist
		(
			options:
			[
				\Localization\TooltipWindow\DefaultHeading,
				\Localization\CharacterEditorPage\TooltipHeading\OriginalName,
				\Localization\CharacterEditorPage\TooltipHeading\TransliteratedName,
				\Localization\CharacterEditorPage\TooltipHeading\LocalizedName,
				\Localization\CharacterEditorPage\TooltipHeading\OldImage,
				\Localization\CharacterEditorPage\TooltipHeading\NewImage,
				\Localization\CharacterEditorPage\TooltipHeading\Image,
				\Localization\CharacterEditorPage\TooltipHeading\VndbLink,
				\Localization\CharacterEditorPage\TooltipHeading\RelatedGames,
				\Localization\CharacterEditorPage\TooltipHeading\Controls
			],
			attributes: ['id' => 'tooltip-headings']
		);
		$html[] = $this->createDatalist
		(
			options:
			[
				\Localization\TooltipWindow\DefaultContent,
				\Localization\CharacterEditorPage\TooltipContent\OriginalName,
				\Localization\CharacterEditorPage\TooltipContent\TransliteratedName,
				\Localization\CharacterEditorPage\TooltipContent\LocalizedName,
				\Localization\CharacterEditorPage\TooltipContent\OldImage,
				\Localization\CharacterEditorPage\TooltipContent\NewImage,
				\Localization\CharacterEditorPage\TooltipContent\Image,
				\Localization\CharacterEditorPage\TooltipContent\VndbLink,
				\Localization\CharacterEditorPage\TooltipContent\RelatedGames,
				\Localization\CharacterEditorPage\TooltipContent\Controls
			],
			attributes: ['id' => 'tooltip-contents']
		);
		
		$html[] = $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/add-delete-row-buttons.js',
				'/js/shared/tooltip-window.js',
				'/js/entity-editor-page.js'
			]
		);
		
		$this->echoHtml($html);
	}
	
	final public function renderSongEditorPage
	(
		array      $album,
		array|null $song,
		int|null   $discNumber,
		int|null   $trackNumber,
		bool|null  $isLastSong,
		string     $heading
	): void
	{
		if ($song)
		{
			$discNumber           = $song['disc_number'];
			$trackNumber          = $song['track_number'];
			$originalName         = $song['original_name'];
			$transliteratedName   = $song['transliterated_name'];
			$localizedName        = $song['localized_name'];
			$hasVocal             = $song['has_vocal'];
			
			$numberButtonDisabled = true;
		}
		else
		{
			$originalName         = null;
			$transliteratedName   = null;
			$localizedName        = null;
			$hasVocal             = null;
			
			$numberButtonDisabled = false;
		}
		
		$defaultReturnLink = Http::buildInternalPath($this->language, 'album', $album['uri']);
		
		$vocalOptions =
		[
			['id' => 0, 'value' => \Localization\SongEditorPage\HasVocalFalse],
			['id' => 1, 'value' => \Localization\SongEditorPage\HasVocalTrue],
		];
		
		if ($isLastSong === true)
			$submitButtonLabel = \Localization\SongEditorPage\SubmitLastSong;
		else if ($isLastSong === false)
			$submitButtonLabel = \Localization\SongEditorPage\SubmitNonLastSong;
		else
			$submitButtonLabel = \Localization\SongEditorPage\SubmitChanges;
		
		$html[] = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/editor-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section class="has-tooltip" tooltip-id="1">
						'.$this->createHeadingForInput(\Localization\SongEditorPage\DiscAndTrack, 2, true).'
						<section class="disc-track-controls">
							'.$this->createTextInput(['name' => 'disc-number', 'id' => 'disc-number', 'value' => $discNumber, 'readonly' => true, 'required' => true]).'
							'.$this->createTextInput(['name' => 'track-number', 'id' => 'track-number', 'value' => $trackNumber, 'readonly' => true, 'required' => true]).'
							'.$this->createButton(\Localization\SongEditorPage\NextDisc, ['id' => 'next-disc', 'disabled' => $numberButtonDisabled]).'
							'.$this->createButton(\Localization\SongEditorPage\PreviousDisc, ['id' => 'previous-disc', 'disabled' => $numberButtonDisabled]).'
						</section>
					</section>
					<section class="has-tooltip" tooltip-id="2">
						'.$this->createHeadingForInput(\Localization\SongEditorPage\OriginalName, 2, true).'
						'.$this->createTextInput(['name' => 'original-name', 'placeholder' => '星たちの歌', 'value' => $originalName, 'required' => true]).'
					</section>
					<section class="has-tooltip" tooltip-id="3">
						'.$this->createHeadingForInput(\Localization\SongEditorPage\TransliteratedName, 2, true).'
						'.$this->createTextInput(['name' => 'transliterated-name', 'placeholder' => 'Hoshitachi no Uta', 'value' => $transliteratedName, 'required' => true, 'pattern' => '[ -~]+']).'
					</section>
					<section class="has-tooltip" tooltip-id="4">
						'.$this->createHeadingForInput(\Localization\SongEditorPage\LocalizedName, 2, false).'
						'.$this->createTextInput(['name' => 'localized-name', 'placeholder' => 'Song of the Stars', 'value' => $localizedName]).'
					</section>
					<section class="has-tooltip" tooltip-id="5">
						'.$this->createHeadingForInput(\Localization\SongEditorPage\HasVocal, 2, true).'
						'.$this->createSearchableSelect
						(
							iteratedOptions:        $vocalOptions,
							selectedOption:         is_null($hasVocal) ? null : $vocalOptions[$hasVocal],
							addEmptyOption:         true,
							keyToShownValue:        'value',
							keyToSentValue:         'id',
							attributesForSentInput: ['name' => 'has-vocal']
						).'
					</section>
					<section class="has-tooltip" tooltip-id="6">
						<section class="page-controls">
							'.$this->createReturnButton($defaultReturnLink).'
							'.$this->createFillerSection().'
							'.$this->createSubmitButton($submitButtonLabel).'
						</section>
					</section>
				</form>
			</section>
			<section>
			</section>
		</article>
		';
		
		$html[] = $this->createTooltipWindow();
		$html[] = $this->createDatalist
		(
			options:
			[
				\Localization\TooltipWindow\DefaultHeading,
				\Localization\SongEditorPage\TooltipHeading\DiscAndTrack,
				\Localization\SongEditorPage\TooltipHeading\OriginalName,
				\Localization\SongEditorPage\TooltipHeading\TransliteratedName,
				\Localization\SongEditorPage\TooltipHeading\LocalizedName,
				\Localization\SongEditorPage\TooltipHeading\HasVocal,
				\Localization\SongEditorPage\TooltipHeading\Controls
			], 
			attributes: ['id' => 'tooltip-headings']
		);
		$html[] = $this->createDatalist
		(
			options:
			[
				\Localization\TooltipWindow\DefaultContent,
				\Localization\SongEditorPage\TooltipContent\DiscAndTrack,
				\Localization\SongEditorPage\TooltipContent\OriginalName,
				\Localization\SongEditorPage\TooltipContent\TransliteratedName,
				\Localization\SongEditorPage\TooltipContent\LocalizedName,
				\Localization\SongEditorPage\TooltipContent\HasVocal,
				\Localization\SongEditorPage\TooltipContent\Controls
			], 
			attributes: ['id' => 'tooltip-contents']
		);
		
		$html[] = $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/add-delete-row-buttons.js',
				'/js/shared/tooltip-window.js',
				'/js/song-editor-page.js'
			]
		);
		
		$this->echoHtml($html);
	}
	
	private function renderLyricsEditorPage
	(
		array      $album,
		array      $song,
		array|null $relatedPerformers,
		array      $artists,
		array      $characters,
		array      $originalSongs,
		array      $languages,
		string     $heading
	): void
	{
		if (!$relatedPerformers)
		{
			$relatedPerformers =
			[
				[
					'artist_id'                             => '',
					'artist_transliterated_name'            => '',
					'character_id'                          => '',
					'character_transliterated_name'         => '',
					'song_artist_character_relation_status' => 'unchecked'
				]
			];
		}
		
		// Renaming keys to use them in $this->createSelect
		
		for ($i = 0; $i < count($artists); $i++)
		{
			$artists[$i]['artist_id']                  = $artists[$i]['id'];
			$artists[$i]['artist_transliterated_name'] = $artists[$i]['transliterated_name'];
			
			unset($artists[$i]['id']);
			unset($artists[$i]['transliterated_name']);
		}
		
		for ($i = 0; $i < count($characters); $i++)
		{
			$characters[$i]['character_id']                  = $characters[$i]['id'];
			$characters[$i]['character_transliterated_name'] = $characters[$i]['transliterated_name'];
			
			unset($characters[$i]['id']);
			unset($characters[$i]['transliterated_name']);
		}
		
		for ($i = 0; $i < count($languages); $i++)
		{
			$languages[$i]['language_id'] = $languages[$i]['id'];
			
			unset($languages[$i]['id']);
		}
		
		$originalSong = array_find
		(
			$originalSongs,
			function (array $original) use ($song)
			{
				return $song['original_song_id'] === $original['id'];
			}
		);
		
		$defaultReturnLink = Http::buildInternalPath($this->language, 'album', $album['uri'], 'song', $song['uri']);
		
		$html[] = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/editor-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section class="has-tooltip" tooltip-id="1">
						'.$this->createHeadingForInput(\Localization\LyricsEditorPage\ArtistAndCharacter, 2, true).'
		';
		
		foreach ($relatedPerformers as $relatedPerformer)
		{
			$disabled = $relatedPerformer['song_artist_character_relation_status'] !== 'unchecked';
			
			$html[] = 
			'
						<section class="related-entity-controls">
							'.$this->createSearchableSelect
							(
								iteratedOptions:         $artists,
								selectedOption:          $relatedPerformer,
								addEmptyOption:          true,
								keyToShownValue:         'artist_transliterated_name',
								keysToShownHints:        ['artist_original_name', 'artist_localized_name'],
								keyToSentValue:          'artist_id',
								attributesForSentInput:  ['name' => 'artist-ids[]', 'disabled' => $disabled],
								attributesForShownInput: [
								                              'placeholder'             => \Localization\Controls\SelectOption,
															  'data-placeholder-select' => \Localization\Controls\SelectOption,
															  'data-placeholder-filter' => \Localization\Controls\SelectFilter,
															  'disabled'                => $disabled
														 ]
							).'
							<span>'.\Localization\LyricsEditorPage\PerformsAs.'</span>
							'.$this->createSearchableSelect
							(
								iteratedOptions:         $characters,
								selectedOption:          $relatedPerformer,
								addEmptyOption:          true,
								keyToShownValue:         'character_transliterated_name',
								keysToShownHints:        ['character_original_name', 'character_localized_name'],
								keyToSentValue:          'character_id',
								attributesForSentInput:  ['name' => 'character-ids[]', 'disabled' => $disabled],
								attributesForShownInput: [
								                              'placeholder'             => \Localization\Controls\SelectOption,
															  'data-placeholder-select' => \Localization\Controls\SelectOption,
															  'data-placeholder-filter' => \Localization\Controls\SelectFilter,
															  'disabled'                => $disabled
														 ]
							).'
							'.$addRowButton = $this->createAddRowButton
							(
								attributes: ['class' => ['artist-select', 'character-select']]
							).'
							'.$deleteRowButton = $this->createDeleteRowButton
							(
								attributes: ['class' => ['artist-select', 'character-select'], 'disabled' => $disabled]
							).'
						</section>
			';
		}
		
		$html[] = 
		'
					</section>
					<section class="has-tooltip" tooltip-id="2">
						'.$this->createHeadingForInput(\Localization\LyricsEditorPage\OriginalSong, 2, false).'
						'.$this->createSearchableSelect
						(
							iteratedOptions:        $originalSongs,
							selectedOption:         $originalSong,
							addEmptyOption:         true,
							keyToShownValue:        'transliterated_name',
							keysToShownHints:       ['original_name', 'localized_name'],
							keyToSentValue:         'id',
							attributesForSentInput: ['name' => 'original-song-id', 'id' => 'original-song-select']
						).'
					</section>
					<section class="has-tooltip" tooltip-id="3">
						'.$this->createHeadingForInput(\Localization\LyricsEditorPage\Language, 2, true).'
						'.$this->createSearchableSelect
						(
							iteratedOptions:        $languages,
							selectedOption:         $song,
							addEmptyOption:         true,
							keyToShownValue:        \Localization\Functions\localizeLanguageKey(),
							keyToSentValue:         'language_id',
							attributesForSentInput: ['name' => 'language-id', 'id' => 'language-select', 'required' => true]
						).'
					</section>
					<section class="has-tooltip lyrics-textarea" tooltip-id="4">
						'.$this->createHeadingForInput(\Localization\LyricsEditorPage\Lyrics, 2, true).'
						'.$this->createTextarea
						(
							value:      $song['lyrics'],
							attributes: ['name' => 'lyrics', 'id' => 'lyrics-area', 'placeholder' => \Localization\Controls\Textarea, 'required' => true]
						).'
					</section>
					<section class="has-tooltip notes-textarea" tooltip-id="5">
						'.$this->createHeadingForInput(\Localization\LyricsEditorPage\Notes, 2, false).'
						'.$this->createTextarea
						(
							value:      $song['notes'],
							attributes: ['name' => 'notes', 'id' => 'notes-area', 'placeholder' => \Localization\Controls\Textarea, 'required' => false]
						).'
					</section>
					<section class="has-tooltip" tooltip-id="6">
						<section class="page-controls">
							'.$this->createReturnButton($defaultReturnLink).'
							'.$this->createFillerSection().'
							'.$this->createSubmitButton().'
						</section>
					</section>
				</form>
			</section>
			<section>
			</section>
		</article>
		';
		
		$html[] = $this->createTooltipWindow();
		$html[] = $this->createDatalist
		(
			options:
			[
				\Localization\TooltipWindow\DefaultHeading,
				\Localization\LyricsEditorPage\TooltipHeading\ArtistAndCharacter,
				\Localization\LyricsEditorPage\TooltipHeading\OriginalSong,
				\Localization\LyricsEditorPage\TooltipHeading\Language,
				\Localization\LyricsEditorPage\TooltipHeading\Lyrics,
				\Localization\LyricsEditorPage\TooltipHeading\Notes,
				\Localization\LyricsEditorPage\TooltipHeading\Controls
			],
			attributes: ['id' => 'tooltip-headings']
		);
		$html[] = $this->createDatalist
		(
			options:
			[
				\Localization\TooltipWindow\DefaultContent,
				\Localization\LyricsEditorPage\TooltipContent\ArtistAndCharacter,
				\Localization\LyricsEditorPage\TooltipContent\OriginalSong,
				\Localization\LyricsEditorPage\TooltipContent\Language,
				\Localization\LyricsEditorPage\TooltipContent\Lyrics,
				\Localization\LyricsEditorPage\TooltipContent\Notes,
				\Localization\LyricsEditorPage\TooltipContent\Controls
			],
			attributes: ['id' => 'tooltip-contents']
		);
		
		$html[] = $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/add-delete-row-buttons.js',
				'/js/shared/tooltip-window.js',
				'/js/lyrics-editor-page.js'
			]
		);
		
		$this->echoHtml($html);
	}
	
	final public function renderTranslationEditorPage
	(
		array      $album,
		array      $song,
		array|null $translation,
		array      $languages,
		array      $translationsByCurrentUser,
		string     $heading
	): void
	{
		if ($translation)
		{
			$translationLanguage = $translation['language_id'];
			$translationName     = $translation['name'];
			$translationLyrics   = $translation['lyrics'];
			$translationNotes    = $translation['notes'];
			
			$languageSelect = $this->createTextInput(['value' => \Localization\Functions\localizeLanguageName($translation), 'disabled' => true]);
			
			$defaultReturnLink = Http::buildInternalPath($this->language, 'album', $album['uri'], 'song', $song['uri'], 'translation', $translation['uri']);
		}
		else
		{
			$translationLanguage = null;
			$translationName     = null;
			$translationLyrics   = null;
			$translationNotes    = null;
			
			// Forbidden languages:
			// - language of the song
			// - translations made by the user
			
			$forbiddenLanguages = [];
			
			$forbiddenLanguages[] = array_find
			(
				$languages,
				function (array $language) use ($song)
				{
					return $language['id'] === $song['language_id'];
				}
			);
			
			foreach ($translationsByCurrentUser as $translationByCurrentUser)
			{
				$forbiddenLanguages[] = array_find
				(
					$languages,
					function (array $language) use ($translationByCurrentUser)
					{
						return $language['id'] === $translationByCurrentUser['language_id'];
					}
				);
			}
			
			$languageSelect = $this->createSearchableSelect
			(
				iteratedOptions:        $languages,
				disabledOptions:        $forbiddenLanguages,
				addEmptyOption:         true,
				keyToShownValue:        \Localization\Functions\localizeLanguageKey(),
				keyToSentValue:         'id',
				attributesForSentInput: ['name' => 'translation-language-id', 'required' => true, 'disabled' => !is_null($translation)]
			);
			
			$defaultReturnLink = Http::buildInternalPath($this->language, 'album', $album['uri'], 'song', $song['uri']);
		}
		
		$html[] = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/editor-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section class="has-tooltip" tooltip-id="1">
						'.$this->createHeadingForInput(\Localization\TranslationEditorPage\TargetLanguage, 2, true).'
						'.$languageSelect.'
					</section>
					<section class="has-tooltip" tooltip-id="2">
						'.$this->createHeadingForInput(\Localization\TranslationEditorPage\TranslationName, 2, true).'
						'.$this->createTextInput(['name' => 'translation-name', 'value' => $translationName, 'required' => true]).'
					</section>
					<section class="has-tooltip" tooltip-id="3">
						'.$this->createHeadingForInput(\Localization\TranslationEditorPage\TranslationLyrics, 2, true).'
						'.$this->createTextarea($translationLyrics, ['name' => 'translation-lyrics', 'placeholder' => \Localization\Controls\Textarea, 'required' => true]).'
					</section>
					<section class="has-tooltip" tooltip-id="4">
						'.$this->createHeadingForInput(\Localization\TranslationEditorPage\TranslationNotes, 2, false).'
						'.$this->createTextarea($translationLyrics, ['name' => 'translation-notes', 'placeholder' => \Localization\Controls\Textarea, 'required' => false]).'
					</section>
					<section class="has-tooltip" tooltip-id="5">
						<section class="page-controls">
							'.$this->createReturnButton($defaultReturnLink).'
							'.$this->createFillerSection().'
							'.$this->createSubmitButton().'
						</section>
					</section>
				</form>
			</section>
			<section>
				'.$this->createHeading($song['transliterated_name'], 1).'
				<section class="form-replacement">
					<section>
						'.$this->createHeadingForInput(\Localization\TranslationEditorPage\SourceLanguage, 2, false).'
						'.$this->createTextInput(['value' => \Localization\Functions\localizeLanguageName($song), 'disabled' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput(\Localization\TranslationEditorPage\SongName, 2, false).'
						'.$this->createTextInput(['value' => $song['original_name'], 'disabled' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput(\Localization\TranslationEditorPage\SongLyrics, 2, false).'
						'.$this->createTextarea($song['lyrics'], ['disabled' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput(\Localization\TranslationEditorPage\SongNotes, 2, false).'
						'.$this->createTextarea($song['notes'], ['disabled' => true]).'
					</section>
				</section>
			</section>
		</article>
		';
		
		$html[] = $this->createTooltipWindow();
		$html[] = $this->createDatalist
		(
			options:
			[
				\Localization\TooltipWindow\DefaultHeading,
				\Localization\TranslationEditorPage\TooltipHeading\TranslationLanguage,
				\Localization\TranslationEditorPage\TooltipHeading\TranslationName,
				\Localization\TranslationEditorPage\TooltipHeading\TranslationLyrics,
				\Localization\TranslationEditorPage\TooltipHeading\TranslationNotes,
				\Localization\TranslationEditorPage\TooltipHeading\Controls
			],
			attributes: ['id' => 'tooltip-headings']
		);
		$html[] = $this->createDatalist
		(
			options:
			[
				\Localization\TooltipWindow\DefaultContent,
				\Localization\TranslationEditorPage\TooltipContent\TranslationLanguage,
				\Localization\TranslationEditorPage\TooltipContent\TranslationName,
				\Localization\TranslationEditorPage\TooltipContent\TranslationLyrics,
				\Localization\TranslationEditorPage\TooltipContent\TranslationNotes,
				\Localization\TranslationEditorPage\TooltipContent\Controls
			],
			attributes: ['id' => 'tooltip-contents']
		);
		
		$html[] = $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/add-delete-row-buttons.js',
				'/js/shared/tooltip-window.js',
				'/js/translation-editor-page.js'
			]
		);
		
		$this->echoHtml($html);
	}
	
	private function renderDeleteEntityPage(string $heading, string $defaultReturnLink)
	{
		$html[] = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/window-in-center-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				'.$this->createParagraph(\Localization\DeleteEntityPage\Introduction).'
				'.$this->createParagraph(\Localization\DeleteEntityPage\Warning).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section>
						'.$this->createReturnButton($defaultReturnLink).'
						'.$this->createFillerSection().'
						'.$this->createCheckbox(\Localization\Controls\Confirmation, true, ['name' => 'confirmation', 'id' => 'confirmation-button', 'value' => 1, 'required' => true]).'
						'.$this->createSubmitButton(attributes: ['id' => 'submission-button', 'disabled' => true]).'
					</section>
				</form>
			</section>
		</article>
		';
		
		$html[] = $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/switch-element-availability.js',
				'/js/entity-deletion-page.js'
			]
		);
		
		$this->echoHtml($html);
	}
	
	final public function renderAddGamePage
	(
		array $albums,
		array $characters
	): void
	{
		$this->renderGameEditorPage
		(
			null,
			null,
			null,
			$albums,
			$characters,
			\Localization\GameEditorPage\HeadingAdd
		);
	}
	
	final public function renderAddAlbumPage
	(
		array $games
	): void
	{
		$this->renderAlbumEditorPage
		(
			null,
			null,
			null,
			null,
			$games,
			\Localization\AlbumEditorPage\HeadingAdd
		);
	}
	
	final public function renderAddArtistPage
	(
		array $originalArtists
	): void
	{
		$this->renderArtistEditorPage
		(
			null,
			$originalArtists,
			\Localization\ArtistEditorPage\HeadingAdd
		);
	}
	
	final public function renderAddCharacterPage
	(
		array $games
	): void
	{
		$this->renderCharacterEditorPage
		(
			null,
			null,
			$games,
			\Localization\CharacterEditorPage\HeadingAdd
		);
	}
	
	final public function renderAddSongPage
	(
		array $album,
		int $discNumber,
		int $trackNumber,
		bool $isLastSong
	): void
	{
		$this->renderSongEditorPage
		(
			$album,
			null,
			$discNumber,
			$trackNumber,
			$isLastSong,
			$album['transliterated_name'].\Localization\SongEditorPage\HeadingAdd
		);
	}
	
	final public function renderAddLyricsPage
	(
		array $album,
		array $song,
		array $artists,
		array $characters,
		array $originalSongs,
		array $languages
	): void
	{	
		$this->renderLyricsEditorPage
		(
			$album,
			$song,
			null,
			$artists,
			$characters,
			$originalSongs,
			$languages,
			\Localization\LyricsEditorPage\HeadingAdd.$song['transliterated_name']
		);
	}

	final public function renderAddTranslationPage
	(
		array $album,
		array $song,
		array $languages,
		array $translationsByCurrentUser
	): void
	{
		$this->renderTranslationEditorPage
		(
			$album,
			$song,
			null,
			$languages,
			$translationsByCurrentUser,
			\Localization\TranslationEditorPage\HeadingAdd
		);
	}
	
	final public function renderEditGamePage
	(
		array $game,
		array $relatedAlbums,
		array $relatedCharacters,
		array $albums,
		array $characters
	): void
	{
		$this->renderGameEditorPage
		(
			$game,
			$relatedAlbums,
			$relatedCharacters,
			$albums,
			$characters,
			\Localization\GameEditorPage\HeadingEdit.$game['transliterated_name']
		);
	}
	
	final public function renderEditAlbumPage
	(
		array $album,
		array $relatedGames,
		int   $currentSongCount,
		array $games
	): void
	{
		$this->renderAlbumEditorPage
		(
			$album,
			$relatedGames,
			null,
			$currentSongCount,
			$games,
			\Localization\AlbumEditorPage\HeadingEdit.$album['transliterated_name']
		);
	}
	
	final public function renderEditArtistPage
	(
		array $artist,
		array $originalArtists
	): void
	{
		$this->renderArtistEditorPage
		(
			$artist,
			$originalArtists,
			\Localization\ArtistEditorPage\HeadingEdit.$artist['transliterated_name']
		);
	}
	
	final public function renderEditCharacterPage
	(
		array $character,
		array $relatedGames,
		array $games
	): void
	{
		$this->renderCharacterEditorPage
		(
			$character,
			$relatedGames,
			$games,
			\Localization\CharacterEditorPage\HeadingEdit.$character['transliterated_name']
		);
	}
	
	final public function renderEditSongPage
	(
		array $album,
		array $song
	): void
	{
		$this->renderSongEditorPage
		(
			$album,
			$song,
			null,
			null,
			null,
			\Localization\SongEditorPage\HeadingEdit.$song['transliterated_name']
		);
	}
	
	final public function renderEditLyricsPage
	(
		array $album,
		array $song,
		array $relatedPerformers,
		array $artists,
		array $characters,
		array $originalSongs,
		array $languages
	): void
	{
		$this->renderLyricsEditorPage
		(
			$album,
			$song,
			$relatedPerformers,
			$artists,
			$characters,
			$originalSongs,
			$languages,
			\Localization\LyricsEditorPage\HeadingEdit.$song['transliterated_name']
		);
	}
	
	final public function renderEditTranslationPage
	(
		array $album,
		array $song,
		array $translation
	): void
	{
		$this->renderTranslationEditorPage
		(
			$album,
			$song,
			$translation,
			[],
			[],
			\Localization\TranslationEditorPage\HeadingEdit
		);
	}
	
	final public function renderDeleteGamePage(array $game): void
	{
		$heading = \Localization\DeleteEntityPage\DeleteGame.$game['transliterated_name'];
		$defaultReturnLink = Http::buildInternalPath($this->language, 'game', $game['uri']);
		
		$this->renderDeleteEntityPage($heading, $defaultReturnLink);
}
	
	final public function renderDeleteAlbumPage(array $album): void
	{
		$heading           = \Localization\DeleteEntityPage\DeleteAlbum.$album['transliterated_name'];
		$defaultReturnLink = Http::buildInternalPath($this->language, 'album', $album['uri']);
		
		$this->renderDeleteEntityPage($heading, $defaultReturnLink);
	}
	
	final public function renderDeleteArtistPage(array $artist): void
	{
		$heading           = \Localization\DeleteEntityPage\DeleteArtist.$artist['transliterated_name'];
		$defaultReturnLink = Http::buildInternalPath($this->language, 'artist', $artist['uri']);
		
		$this->renderDeleteEntityPage($heading, $defaultReturnLink);
	}
	
	final public function renderDeleteCharacterPage(array $character): void
	{
		$heading           = \Localization\DeleteEntityPage\DeleteCharacter.$character['transliterated_name'];
		$defaultReturnLink = Http::buildInternalPath($this->language, 'character', $character['uri']);
		
		$this->renderDeleteEntityPage($heading, $defaultReturnLink);
	}
	
	final public function renderDeleteSongPage(array $album, array $song): void
	{
		$heading           = \Localization\DeleteEntityPage\DeleteSong.$song['transliterated_name'];
		$defaultReturnLink = Http::buildInternalPath($this->language, 'album', $album['uri']);
		
		$this->renderDeleteEntityPage($heading, $defaultReturnLink);
	}
	
	final public function renderDeleteLyricsPage(array $album, array $song): void
	{
		$heading           = \Localization\DeleteEntityPage\DeleteLyrics.$song['transliterated_name'];
		$defaultReturnLink = Http::buildInternalPath($this->language, 'album', $album['uri'], 'song', $song['uri']);
		
		$this->renderDeleteEntityPage($heading, $defaultReturnLink);
	}
	
	final public function renderDeleteTranslationPage(array $album, array $song, array $translation): void
	{
		$heading           = \Localization\DeleteEntityPage\DeleteTranslation.$song['transliterated_name'];
		$defaultReturnLink = Http::buildInternalPath($this->language, 'album', $album['uri'], 'song', $song['uri'], 'translation', $translation['uri']);
		
		$this->renderDeleteEntityPage($heading, $defaultReturnLink);
	}
	
	final public function renderChangeAccountDataPage(array $user, InputError $error = InputError::None)
	{
		$heading           = $user['username'].\Localization\UserAccountDataPage\Edit;
		$defaultReturnLink = Http::buildInternalPath($this->language, 'user', $user['username']);
		
		$html[] = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/window-in-center-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
			</section>
			<section>
				'.$this->createHeading(\Localization\UserAccountDataPage\AccountData, 2).'
				'.$this->createParagraph(\Localization\Functions\localizeInputError($error)).'
				<form method="POST">
					<section>
						'.$this->createHeadingForInput(\Localization\UserAccountDataPage\Username, 3, true).'
						'.$this->createTextInput(['name' => 'username', 'value' => $user['username'], 'placeholder' => \Localization\SignUpPage\HintUsername, 'required' => true, 'pattern' => '[a-zA-Z0-9]+']).'
					</section>
					<section>
						'.$this->createHeadingForInput(\Localization\UserAccountDataPage\Email, 3, true).'
						'.$this->createEmailInput(['name' => 'email', 'value' => Cryptography::decryptData($user['email']), 'placeholder' => 'name@mailserver.domain', 'required' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput(\Localization\UserAccountDataPage\NewPassword, 3, false).'
						'.$this->createPasswordInput(['name' => 'new-password', 'placeholder' => \Localization\SignUpPage\HintPassword]).'
					</section>
					<section>
						'.$this->createHeadingForInput(\Localization\UserAccountDataPage\OldPassword, 3, true).'
						'.$this->createPasswordInput(['name' => 'old-password', 'placeholder' => \Localization\LogInPage\HintPassword, 'required' => true]).'
					</section>
					<section>
						'.$this->createReturnButton($defaultReturnLink).'
						'.$this->createFillerSection().'
						'.$this->createSubmitButton().'
					</section>
				</form>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	final public function renderChangeEmailPage(array $user, InputError $error = InputError::None)
	{
		$heading2          = $user['username'].\Localization\ChangeEmailPage\Edit;
		$defaultReturnLink = Http::buildInternalPath($this->language, 'user', $user['uri']);
		
		$html[] = $this->startRender
		(
			title:        $heading2,
			cssSheetUris: ['/css/window-in-center-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading(\Localization\HomePage\Heading, 1).'
			</section>
			<section>
				'.$this->createHeading($heading2, 2).'
				'.$this->createParagraph(\Localization\Functions\localizeInputError($error)).'
				<form method="POST">
					<section>
						'.$this->createHeadingForInput(\Localization\ChangeEmailPage\Email, 3, true).'
						'.$this->createEmailInput(['name' => 'email', 'value' => Cryptography::decryptData($user['email']), 'placeholder' => \Localization\SignUpPage\HintEmail, 'required' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput(\Localization\ChangeEmailPage\Password, 3, true).'
						'.$this->createPasswordInput(['name' => 'current-password', 'placeholder' => \Localization\LogInPage\HintPassword, 'required' => true]).'
					</section>
					<section>
						'.$this->createReturnButton($defaultReturnLink).'
						'.$this->createFillerSection().'
						'.$this->createSubmitButton().'
					</section>
				</form>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	final public function renderChangeUsernamePage(array $user, InputError $error = InputError::None)
	{
		$heading2          = $user['username'].\Localization\ChangeUsernamePage\Edit;
		$defaultReturnLink = Http::buildInternalPath($this->language, 'user', $user['uri']);
		
		$html[] = $this->startRender
		(
			title:        $heading2,
			cssSheetUris: ['/css/window-in-center-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading(\Localization\HomePage\Heading, 1).'
			</section>
			<section>
				'.$this->createHeading($heading2, 2).'
				'.$this->createParagraph(\Localization\Functions\localizeInputError($error)).'
				<form method="POST">
					<section>
						'.$this->createHeadingForInput(\Localization\ChangeUsernamePage\Username, 3, true).'
						'.$this->createUsernameInput(['name' => 'username', 'value' => $user['username'], 'placeholder' => \Localization\SignUpPage\HintUsername, 'required' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput(\Localization\ChangeUsernamePage\Password, 3, true).'
						'.$this->createPasswordInput(['name' => 'current-password', 'placeholder' => \Localization\LogInPage\HintPassword, 'required' => true]).'
					</section>
					<section>
						'.$this->createReturnButton($defaultReturnLink).'
						'.$this->createFillerSection().'
						'.$this->createSubmitButton().'
					</section>
				</form>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	final public function renderChangePasswordPage(array $user, InputError $error = InputError::None)
	{
		$heading2          = $user['username'].\Localization\ChangePasswordPage\Edit;
		$defaultReturnLink = Http::buildInternalPath($this->language, 'user', $user['uri']);
		
		$html[] = $this->startRender
		(
			title:        $heading2,
			cssSheetUris: ['/css/window-in-center-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading(\Localization\HomePage\Heading, 1).'
			</section>
			<section>
				'.$this->createHeading($heading2, 2).'
				'.$this->createParagraph(\Localization\Functions\localizeInputError($error)).'
				<form method="POST">
					<section>
						'.$this->createHeadingForInput(\Localization\ChangePasswordPage\NewPassword, 3, true).'
						'.$this->createPasswordInput(['name' => 'password', 'placeholder' => \Localization\SignUpPage\HintPassword, 'required' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput(\Localization\ChangePasswordPage\Password, 3, true).'
						'.$this->createPasswordInput(['name' => 'current-password', 'placeholder' => \Localization\LogInPage\HintPassword, 'required' => true]).'
					</section>
					<section>
						'.$this->createReturnButton($defaultReturnLink).'
						'.$this->createFillerSection().'
						'.$this->createSubmitButton().'
					</section>
				</form>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	final public function renderChangeAboutMePage(array $user, InputError $error = InputError::None)
	{
		$heading2          = $user['username'].\Localization\ChangeAboutMePage\Edit;
		$defaultReturnLink = Http::buildInternalPath($this->language, 'user', $user['uri']);
		
		$html[] = $this->startRender
		(
			title:        $heading2,
			cssSheetUris: ['/css/window-in-center-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading(\Localization\HomePage\Heading, 1).'
			</section>
			<section>
				'.$this->createHeading($heading2, 2).'
				'.$this->createParagraph(\Localization\Functions\localizeInputError($error)).'
				<form method="POST">
					<section>
						'.$this->createHeadingForInput(\Localization\ChangeAboutMePage\AboutMe, 3, false).'
						'.$this->createTextarea
						(
							value: htmlspecialchars($user['about_me'] ?? ''),
							attributes:
							[
								'name' => 'about-me',
								'placeholder' => \Localization\Controls\Textarea
							]
						).'
					</section>
					<section>
						'.$this->createReturnButton($defaultReturnLink).'
						'.$this->createFillerSection().'
						'.$this->createSubmitButton().'
					</section>
				</form>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	final public function renderDeleteAccountPage(array $user, InputError $error = InputError::None)
	{
		$heading2          = $user['username'].\Localization\DeleteAccountPage\Delete;
		$defaultReturnLink = Http::buildInternalPath($this->language, 'user', $user['username']);
		
		$html[] = $this->startRender
		(
			title:        $heading2,
			cssSheetUris: ['/css/window-in-center-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading(\Localization\HomePage\Heading, 1).'
			</section>
			<section>
				'.$this->createHeading($heading2, 2).'
				'.$this->createParagraph(\Localization\DeleteAccountPage\Warning2).'
				'.$this->createParagraph(\Localization\DeleteAccountPage\Warning3).'
				'.$this->createParagraph(\Localization\DeleteAccountPage\Warning4).'
				'.$this->createParagraph(\Localization\Functions\localizeInputError($error)).'
				<form method="POST">
					<section>
						'.$this->createHeadingForInput(\Localization\DeleteAccountPage\Password, 3, true).'
						'.$this->createPasswordInput(['name' => 'password', 'placeholder' => \Localization\LogInPage\HintPassword, 'required' => true]).'
					</section>
					<section>
						'.$this->createReturnButton($defaultReturnLink).'
						'.$this->createFillerSection().'
						'.$this->createSubmitButton().'
					</section>
				</form>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
}
