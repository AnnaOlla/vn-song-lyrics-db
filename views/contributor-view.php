<?php

require_once 'views/visitor-view.php';

class ContributorView extends VisitorView
{
	protected const ACCEPTED_IMAGE_TYPES = '.jpg, .jpeg, .png, .webp';
	
	public function __construct(string $language)
	{
		parent::__construct($language);
	}
	
	private function createTooltipWindow(): string
	{
		return
		'
		<aside>
			<section id="tooltip-window">
				<h3>'.__('contributor.edit.shared.hint').'</h3>
				<section>'.__('contributor.edit.shared.tooltip.hint').'</section>
			</section>
		</aside>
		';
	}
	
	private function renderGameEditorPage
	(
		array|null $game,
		array|null $relatedAlbums,
		array|null $relatedCharacters,
		array      $albums,
		array      $characters,
		string     $heading,
		string     $nonce
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
		
		$returnLink = Http::buildPath($this->language, 'game-list').Http::buildQuery(self::ENTITY_LIST_DEFAULT_QUERY);
		
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
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $nonce]).'
					<section class="has-tooltip" tooltip-id="1">
						'.$this->createHeadingForInput(__('contributor.edit.game.original_name'), 2, true).'
						'.$this->createTextInput(['name' => 'original_name', 'placeholder' => '蒼の彼方のフォーリズム', 'value' => $originalName, 'required' => true]).'
					</section>
					<section class="has-tooltip" tooltip-id="2">
						'.$this->createHeadingForInput(__('contributor.edit.game.transliterated_name'), 2, true).'
						'.$this->createTextInput(['name' => 'transliterated_name', 'placeholder' => 'Ao no Kanata no Foo Rizumu', 'value' => $transliteratedName, 'required' => true, 'pattern' => '[ -~]+']).'
					</section>
					<section class="has-tooltip" tooltip-id="3">
						'.$this->createHeadingForInput(__('contributor.edit.game.localized_name'), 2, false).'
						'.$this->createTextInput(['name' => 'localized_name', 'placeholder' => 'Aokana - Four Rhythms Across the Blue', 'value' => $localizedName]).'
					</section>
		';
		
		if ($isImageUploaded)
		{
			$html[] = 
			'
					<section class="has-tooltip" tooltip-id="4">
						'.$this->createHeadingForInput(__('contributor.edit.game.old_image'), 2, false).'
						'.$this->createGameImage($game).'
					</section>
					<section class="has-tooltip" tooltip-id="5">
						'.$this->createHeadingForInput(__('contributor.edit.game.new_image'), 2, false).'
						'.$this->createFileupload(['name' => 'logo', 'accept' => self::ACCEPTED_IMAGE_TYPES]).'
					</section>
			';
		}
		else
		{
			$html[] = 
			'
					<section class="has-tooltip" tooltip-id="6">
						'.$this->createHeadingForInput(__('contributor.edit.game.image'), 2, false).'
						'.$this->createFileupload(['name' => 'logo', 'accept' => self::ACCEPTED_IMAGE_TYPES]).'
					</section>
			';
		}
		
		$html[] = 
		'
					<section class="has-tooltip" tooltip-id="7">
						'.$this->createHeadingForInput(__('contributor.edit.game.vndb_link'), 2, false).'
						'.$this->createUrlInput(['name' => 'vndb_link', 'placeholder' => 'https://vndb.org/v12849', 'pattern' => 'https:\/\/vndb\.org\/v\d+', 'value' => $vndbLink]).'
					</section>
					<section class="has-tooltip" tooltip-id="8">
						'.$this->createHeadingForInput(__('contributor.edit.game.related_albums'), 2, false).'
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
								attributesForSentInput:  ['name' => 'album_ids[]', 'disabled' => $disabled],
								attributesForShownInput: [
								                              'placeholder'             => __('base.controls.select.hint_idle'),
															  'data-placeholder-select' => __('base.controls.select.hint_idle'),
															  'data-placeholder-filter' => __('base.controls.select.hint_filter'),
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
						'.$this->createHeadingForInput(__('contributor.edit.game.related_characters'), 2, false).'
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
								attributesForSentInput:  ['name' => 'character_ids[]', 'disabled' => $disabled],
								attributesForShownInput: [
								                              'placeholder'             => __('base.controls.select.hint_idle'),
															  'data-placeholder-select' => __('base.controls.select.hint_idle'),
															  'data-placeholder-filter' => __('base.controls.select.hint_filter'),
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
							'.$this->createReturnButton($returnLink, __('base.controls.button.return')).'
							'.$this->createFillerSection().'
							'.$this->createSubmitButton(__('base.controls.button.submit')).'
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
				__('contributor.edit.shared.hint'),
				__('contributor.edit.game.original_name'),
				__('contributor.edit.game.transliterated_name'),
				__('contributor.edit.game.localized_name'),
				__('contributor.edit.game.old_image'),
				__('contributor.edit.game.new_image'),
				__('contributor.edit.game.image'),
				__('contributor.edit.game.vndb_link'),
				__('contributor.edit.game.related_albums'),
				__('contributor.edit.game.related_characters'),
				__('contributor.edit.shared.controls')
			],
			attributes: ['id' => 'tooltip-headings']
		);
		$html[] = $this->createDatalist
		(
			options:
			[
				__('contributor.edit.shared.tooltip.hint'),
				__('contributor.edit.game.tooltip.original_name'),
				__('contributor.edit.game.tooltip.transliterated_name'),
				__('contributor.edit.game.tooltip.localized_name'),
				__('contributor.edit.game.tooltip.old_image'),
				__('contributor.edit.game.tooltip.new_image'),
				__('contributor.edit.game.tooltip.image'),
				__('contributor.edit.game.tooltip.vndb_link'),
				__('contributor.edit.game.tooltip.related_albums'),
				__('contributor.edit.game.tooltip.related_characters'),
				__('contributor.edit.shared.tooltip.controls')
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
		string     $heading,
		string     $nonce
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
		
		$returnLink = Http::buildPath($this->language, 'album-list').Http::buildQuery(self::ENTITY_LIST_DEFAULT_QUERY);
		
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
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $nonce]).'
					<section class="has-tooltip" tooltip-id="1">
						'.$this->createHeadingForInput(__('contributor.edit.album.original_name'), 2, true).'
						'.$this->createTextInput(['name' => 'original_name', 'placeholder' => '蒼の彼方のフォーリズム サウンドトラックCD vol.1', 'value' => $originalName, 'required' => true]).'
					</section>
					<section class="has-tooltip" tooltip-id="2">
						'.$this->createHeadingForInput(__('contributor.edit.album.transliterated_name'), 2, true).'
						'.$this->createTextInput(['name' => 'transliterated_name', 'placeholder' => 'Ao no Kanata no Foo Rizumu Saundotorakku CD VOL.1', 'value' => $transliteratedName, 'required' => true, 'pattern' => '[ -~]+']).'
					</section>
					<section class="has-tooltip" tooltip-id="3">
						'.$this->createHeadingForInput(__('contributor.edit.album.localized_name'), 2, false).'
						'.$this->createTextInput(['name' => 'localized_name', 'placeholder' => 'FOUR RHYTHM ACROSS THE BLUE SOUND TRACK CD VOL.01', 'value' => $localizedName]).'
					</section>
		';
		
		if ($isImageUploaded)
		{
			$html[] = 
			'
					<section class="has-tooltip" tooltip-id="4">
						'.$this->createHeadingForInput(__('contributor.edit.album.old_image'), 2, false).'
						'.$this->createAlbumImage($album).'
					</section>
					<section class="has-tooltip" tooltip-id="5">
						'.$this->createHeadingForInput(__('contributor.edit.album.new_image'), 2, false).'
						'.$this->createFileupload(['name' => 'cover', 'accept' => self::ACCEPTED_IMAGE_TYPES]).'
					</section>
			';
		}
		else
		{
			$html[] = 
			'
					<section class="has-tooltip" tooltip-id="6">
						'.$this->createHeadingForInput(__('contributor.edit.album.image'), 2, false).'
						'.$this->createFileupload(['name' => 'cover', 'accept' => self::ACCEPTED_IMAGE_TYPES]).'
					</section>
			';
		}
		
		$html[] = 
		'
					<section class="has-tooltip" tooltip-id="7">
						'.$this->createHeadingForInput(__('contributor.edit.album.vgmdb_link'), 2, false).'
						'.$this->createUrlInput(['name' => 'vgmdb_link', 'placeholder' => 'https://vgmdb.net/album/56642', 'pattern' => 'https:\/\/vgmdb\.net\/album\/\d+', 'value' => $vgmdbLink]).'
					</section>
					<section class="has-tooltip" tooltip-id="8">
						'.$this->createHeadingForInput(__('contributor.edit.album.related_games'), 2, false).'
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
								attributesForSentInput:  ['name' => 'game_ids[]', 'disabled' => $disabled],
								attributesForShownInput: [
								                              'placeholder'             => __('base.controls.select.hint_idle'),
															  'data-placeholder-select' => __('base.controls.select.hint_idle'),
															  'data-placeholder-filter' => __('base.controls.select.hint_filter'),
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
						'.$this->createHeadingForInput(__('contributor.edit.album.song_count'), 2, true).'
						'.$this->createTextInput(['name' => 'song_count', 'placeholder' => '28', 'pattern' => '\d+', 'value' => $songCount, 'required' => true]).'
					</section>
					<section class="has-tooltip" tooltip-id="10">
						<section class="page-controls">
							'.$this->createReturnButton($returnLink, __('base.controls.button.return')).'
							'.$this->createFillerSection().'
							'.$this->createSubmitButton(__('base.controls.button.submit')).'
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
				__('contributor.edit.shared.hint'),
				__('contributor.edit.album.original_name'),
				__('contributor.edit.album.transliterated_name'),
				__('contributor.edit.album.localized_name'),
				__('contributor.edit.album.old_image'),
				__('contributor.edit.album.new_image'),
				__('contributor.edit.album.image'),
				__('contributor.edit.album.vgmdb_link'),
				__('contributor.edit.album.related_games'),
				__('contributor.edit.album.song_count'),
				__('contributor.edit.shared.controls')
			],
			attributes: ['id' => 'tooltip-headings']
		);
		$html[] = $this->createDatalist
		(
			options:
			[
				__('contributor.edit.shared.tooltip.hint'),
				__('contributor.edit.album.tooltip.original_name'),
				__('contributor.edit.album.tooltip.transliterated_name'),
				__('contributor.edit.album.tooltip.localized_name'),
				__('contributor.edit.album.tooltip.old_image'),
				__('contributor.edit.album.tooltip.new_image'),
				__('contributor.edit.album.tooltip.image'),
				__('contributor.edit.album.tooltip.vgmdb_link'),
				__('contributor.edit.album.tooltip.related_games'),
				__('contributor.edit.album.tooltip.song_count'),
				__('contributor.edit.shared.tooltip.controls')
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
		string     $heading,
		string     $nonce
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
		
		$returnLink = Http::buildPath($this->language, 'artist-list').Http::buildQuery(self::ENTITY_LIST_DEFAULT_QUERY);
		
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
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $nonce]).'
					<section class="has-tooltip" tooltip-id="1">
						'.$this->createHeadingForInput(__('contributor.edit.artist.original_name'), 2, true).'
						'.$this->createTextInput(['name' => 'original_name', 'placeholder' => 'いとうかなこ', 'value' => $originalName, 'required' => true]).'
					</section>
					<section class="has-tooltip" tooltip-id="2">
						'.$this->createHeadingForInput(__('contributor.edit.artist.transliterated_name'), 2, true).'
						'.$this->createTextInput(['name' => 'transliterated_name', 'placeholder' => 'Itou Kanako', 'value' => $transliteratedName, 'required' => true, 'pattern' => '[ -~]+']).'
					</section>
					<section class="has-tooltip" tooltip-id="3">
						'.$this->createHeadingForInput(__('contributor.edit.artist.localized_name'), 2, false).'
						'.$this->createTextInput(['name' => 'localized_name', 'placeholder' => 'Kanako Ito', 'value' => $localizedName]).'
					</section>
		';
		
		if ($isImageUploaded)
		{
			$html[] = 
			'
					<section class="has-tooltip" tooltip-id="4">
						'.$this->createHeadingForInput(__('contributor.edit.artist.old_image'), 2, false).'
						'.$this->createArtistImage($artist).'
					</section>
					<section class="has-tooltip" tooltip-id="5">
						'.$this->createHeadingForInput(__('contributor.edit.artist.new_image'), 2, false).'
						'.$this->createFileupload(['name' => 'photo', 'accept' => self::ACCEPTED_IMAGE_TYPES]).'
					</section>
			';
		}
		else
		{
			$html[] = 
			'
					<section class="has-tooltip" tooltip-id="6">
						'.$this->createHeadingForInput(__('contributor.edit.artist.image'), 2, false).'
						'.$this->createFileupload(['name' => 'photo', 'accept' => self::ACCEPTED_IMAGE_TYPES]).'
					</section>
			';
		}
		
		$html[] = 
		'
					<section class="has-tooltip" tooltip-id="7">
						'.$this->createHeadingForInput(__('contributor.edit.artist.vgmdb_link'), 2, false).'
						'.$this->createUrlInput(['name' => 'vgmdb_link', 'placeholder' => 'https://vgmdb.net/artist/69', 'pattern' => 'https:\/\/vgmdb\.net\/artist\/\d+', 'value' => $vgmdbLink]).'
					</section>
					<section class="has-tooltip" tooltip-id="8">
						'.$this->createHeadingForInput(__('contributor.edit.artist.alias_of'), 2, false).'
						'.$this->createSearchableSelect
						(
							iteratedOptions:        $originalArtists,
							disabledOptions:        [$artist],
							selectedOption:         $originalArtist,
							addEmptyOption:         true,
							keyToShownValue:        'transliterated_name',
							keysToShownHints:       ['original_name', 'localized_name'],
							keyToSentValue:         'id',
							attributesForSentInput: ['name' => 'original_artist_id']
						).'
					</section>
					<section class="has-tooltip" tooltip-id="9">
						<section class="page-controls">
							'.$this->createReturnButton($returnLink, __('base.controls.button.return')).'
							'.$this->createFillerSection().'
							'.$this->createSubmitButton(__('base.controls.button.submit')).'
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
				__('contributor.edit.shared.hint'),
				__('contributor.edit.artist.original_name'),
				__('contributor.edit.artist.transliterated_name'),
				__('contributor.edit.artist.localized_name'),
				__('contributor.edit.artist.old_image'),
				__('contributor.edit.artist.new_image'),
				__('contributor.edit.artist.image'),
				__('contributor.edit.artist.vgmdb_link'),
				__('contributor.edit.artist.alias_of'),
				__('contributor.edit.shared.controls')
			],
			attributes: ['id' => 'tooltip-headings']
		);
		$html[] = $this->createDatalist
		(
			options:
			[
				__('contributor.edit.shared.tooltip.hint'),
				__('contributor.edit.artist.tooltip.original_name'),
				__('contributor.edit.artist.tooltip.transliterated_name'),
				__('contributor.edit.artist.tooltip.localized_name'),
				__('contributor.edit.artist.tooltip.old_image'),
				__('contributor.edit.artist.tooltip.new_image'),
				__('contributor.edit.artist.tooltip.image'),
				__('contributor.edit.artist.tooltip.vgmdb_link'),
				__('contributor.edit.artist.tooltip.alias_of'),
				__('contributor.edit.shared.tooltip.controls')
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
		string     $heading,
		string     $nonce
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
		
		$returnLink = Http::buildPath($this->language, 'character-list').Http::buildQuery(self::ENTITY_LIST_DEFAULT_QUERY);
		
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
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $nonce]).'
					<section class="has-tooltip" tooltip-id="1">
						'.$this->createHeadingForInput(__('contributor.edit.character.original_name'), 2, true).'
						'.$this->createTextInput(['name' => 'original_name', 'placeholder' => '桐生萌郁', 'value' => $originalName, 'required' => true]).'
					</section>
					<section class="has-tooltip" tooltip-id="2">
						'.$this->createHeadingForInput(__('contributor.edit.character.transliterated_name'), 2, true).'
						'.$this->createTextInput(['name' => 'transliterated_name', 'placeholder' => 'Kiryuu Moeka', 'value' => $transliteratedName, 'required' => true, 'pattern' => '[ -~]+']).'
					</section>
					<section class="has-tooltip" tooltip-id="3">
						'.$this->createHeadingForInput(__('contributor.edit.character.localized_name'), 2, false).'
						'.$this->createTextInput(['name' => 'localized_name', 'placeholder' => 'Moeka Kiryu', 'value' => $localizedName]).'
					</section>
		';
		
		if ($isImageUploaded)
		{
			$html[] = 
			'
					<section class="has-tooltip" tooltip-id="4">
						'.$this->createHeadingForInput(__('contributor.edit.character.old_image'), 2, false).'
						'.$this->createCharacterImage($character).'
					</section>
					<section class="has-tooltip" tooltip-id="5">
						'.$this->createHeadingForInput(__('contributor.edit.character.new_image'), 2, false).'
						'.$this->createFileupload(['name' => 'image', 'accept' => self::ACCEPTED_IMAGE_TYPES]).'
					</section>
			';
		}
		else
		{
			$html[] = 
			'
					<section class="has-tooltip" tooltip-id="6">
						'.$this->createHeadingForInput(__('contributor.edit.character.image'), 2, false).'
						'.$this->createFileupload(['name' => 'image', 'accept' => self::ACCEPTED_IMAGE_TYPES]).'
					</section>
			';
		}
		
		$html[] = 
		'
					<section class="has-tooltip" tooltip-id="7">
						'.$this->createHeadingForInput(__('contributor.edit.character.vndb_link'), 2, false).'
						'.$this->createUrlInput(['name' => 'vndb_link', 'placeholder' => 'https://vndb.org/c6496', 'pattern' => 'https:\/\/vndb\.org\/c\d+', 'value' => $vndbLink]).'
					</section>
					<section class="has-tooltip" tooltip-id="8">
						'.$this->createHeadingForInput(__('contributor.edit.character.related_games'), 2, false).'
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
								attributesForSentInput:  ['name' => 'game_ids[]', 'disabled' => $disabled],
								attributesForShownInput: [
								                              'placeholder'             => __('base.controls.select.hint_idle'),
															  'data-placeholder-select' => __('base.controls.select.hint_idle'),
															  'data-placeholder-filter' => __('base.controls.select.hint_filter'),
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
							'.$this->createReturnButton($returnLink, __('base.controls.button.return')).'
							'.$this->createFillerSection().'
							'.$this->createSubmitButton(__('base.controls.button.submit')).'
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
				__('contributor.edit.shared.hint'),
				__('contributor.edit.character.original_name'),
				__('contributor.edit.character.transliterated_name'),
				__('contributor.edit.character.localized_name'),
				__('contributor.edit.character.old_image'),
				__('contributor.edit.character.new_image'),
				__('contributor.edit.character.image'),
				__('contributor.edit.character.vndb_link'),
				__('contributor.edit.character.related_games'),
				__('contributor.edit.shared.controls')
			],
			attributes: ['id' => 'tooltip-headings']
		);
		$html[] = $this->createDatalist
		(
			options:
			[
				__('contributor.edit.shared.tooltip.hint'),
				__('contributor.edit.character.tooltip.original_name'),
				__('contributor.edit.character.tooltip.transliterated_name'),
				__('contributor.edit.character.tooltip.localized_name'),
				__('contributor.edit.character.tooltip.old_image'),
				__('contributor.edit.character.tooltip.new_image'),
				__('contributor.edit.character.tooltip.image'),
				__('contributor.edit.character.tooltip.vndb_link'),
				__('contributor.edit.character.tooltip.related_games'),
				__('contributor.edit.shared.tooltip.controls')
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
		string     $heading,
		string     $nonce
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
		
		$returnLink = Http::buildPath($this->language, 'album', $album['uri']);
		
		$vocalOptions =
		[
			['id' => 0, 'value' => __('contributor.edit.song.has_lyrics_false')],
			['id' => 1, 'value' => __('contributor.edit.song.has_lyrics_true')],
		];
		
		if ($isLastSong === true)
			$submitButtonLabel = __('contributor.edit.song.submit_last_song');
		else if ($isLastSong === false)
			$submitButtonLabel = __('contributor.edit.song.submit_nonlast_song');
		else
			$submitButtonLabel = __('base.controls.button.submit');
		
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
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $nonce]).'
					<section class="has-tooltip" tooltip-id="1">
						'.$this->createHeadingForInput(__('contributor.edit.song.disc_and_track'), 2, true).'
						<section class="disc-track-controls">
							'.$this->createTextInput(['name' => 'disc_number', 'id' => 'disc-number', 'value' => $discNumber, 'readonly' => true, 'required' => true]).'
							'.$this->createTextInput(['name' => 'track_number', 'id' => 'track-number', 'value' => $trackNumber, 'readonly' => true, 'required' => true]).'
							'.$this->createButton(__('contributor.edit.song.next_disc'), ['id' => 'next-disc', 'disabled' => $numberButtonDisabled]).'
							'.$this->createButton(__('contributor.edit.song.previous_disc'), ['id' => 'previous-disc', 'disabled' => $numberButtonDisabled]).'
						</section>
					</section>
					<section class="has-tooltip" tooltip-id="2">
						'.$this->createHeadingForInput(__('contributor.edit.song.original_name'), 2, true).'
						'.$this->createTextInput(['name' => 'original_name', 'placeholder' => '星たちの歌', 'value' => $originalName, 'required' => true]).'
					</section>
					<section class="has-tooltip" tooltip-id="3">
						'.$this->createHeadingForInput(__('contributor.edit.song.transliterated_name'), 2, true).'
						'.$this->createTextInput(['name' => 'transliterated_name', 'placeholder' => 'Hoshitachi no Uta', 'value' => $transliteratedName, 'required' => true, 'pattern' => '[ -~]+']).'
					</section>
					<section class="has-tooltip" tooltip-id="4">
						'.$this->createHeadingForInput(__('contributor.edit.song.localized_name'), 2, false).'
						'.$this->createTextInput(['name' => 'localized_name', 'placeholder' => 'Song of the Stars', 'value' => $localizedName]).'
					</section>
					<section class="has-tooltip" tooltip-id="5">
						'.$this->createHeadingForInput(__('contributor.edit.song.has_lyrics'), 2, true).'
						'.$this->createSearchableSelect
						(
							iteratedOptions:        $vocalOptions,
							selectedOption:         $hasVocal !== null ? $vocalOptions[$hasVocal] : null,
							addEmptyOption:         true,
							keyToShownValue:        'value',
							keyToSentValue:         'id',
							attributesForSentInput: ['name' => 'has_vocal']
						).'
					</section>
					<section class="has-tooltip" tooltip-id="6">
						<section class="page-controls">
							'.$this->createReturnButton($returnLink, __('base.controls.button.return')).'
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
				__('contributor.edit.shared.hint'),
				__('contributor.edit.song.disc_and_track'),
				__('contributor.edit.song.original_name'),
				__('contributor.edit.song.transliterated_name'),
				__('contributor.edit.song.localized_name'),
				__('contributor.edit.song.has_lyrics'),
				__('contributor.edit.shared.controls')
			], 
			attributes: ['id' => 'tooltip-headings']
		);
		$html[] = $this->createDatalist
		(
			options:
			[
				__('contributor.edit.shared.tooltip.hint'),
				__('contributor.edit.song.tooltip.disc_and_track'),
				__('contributor.edit.song.tooltip.original_name'),
				__('contributor.edit.song.tooltip.transliterated_name'),
				__('contributor.edit.song.tooltip.localized_name'),
				__('contributor.edit.song.tooltip.has_lyrics'),
				__('contributor.edit.shared.tooltip.controls')
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
		string     $heading,
		string     $nonce
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
			$artists[$i]['artist_original_name']       = $artists[$i]['original_name'];
			$artists[$i]['artist_transliterated_name'] = $artists[$i]['transliterated_name'];
			$artists[$i]['artist_localized_name']      = $artists[$i]['localized_name'];
			
			unset($artists[$i]['id']);
			unset($artists[$i]['original_name']);
			unset($artists[$i]['transliterated_name']);
			unset($artists[$i]['localized_name']);
		}
		
		for ($i = 0; $i < count($characters); $i++)
		{
			$characters[$i]['character_id']                  = $characters[$i]['id'];
			$characters[$i]['character_original_name']       = $characters[$i]['original_name'];
			$characters[$i]['character_transliterated_name'] = $characters[$i]['transliterated_name'];
			$characters[$i]['character_localized_name']      = $characters[$i]['localized_name'];
			
			unset($characters[$i]['id']);
			unset($characters[$i]['original_name']);
			unset($characters[$i]['transliterated_name']);
			unset($characters[$i]['localized_name']);
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
		
		$returnLink = Http::buildPath($this->language, 'album', $album['uri'], 'song', $song['uri']);
		
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
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $nonce]).'
					<section class="has-tooltip" tooltip-id="1">
						'.$this->createHeadingForInput(__('contributor.edit.lyrics.performers'), 2, true).'
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
								attributesForSentInput:  ['name' => 'artist_ids[]', 'disabled' => $disabled],
								attributesForShownInput: [
								                              'placeholder'             => __('base.controls.select.hint_idle'),
															  'data-placeholder-select' => __('base.controls.select.hint_idle'),
															  'data-placeholder-filter' => __('base.controls.select.hint_filter'),
															  'disabled'                => $disabled
														 ]
							).'
							<span>'.__('contributor.edit.lyrics.performs_as').'</span>
							'.$this->createSearchableSelect
							(
								iteratedOptions:         $characters,
								selectedOption:          $relatedPerformer,
								addEmptyOption:          true,
								keyToShownValue:         'character_transliterated_name',
								keysToShownHints:        ['character_original_name', 'character_localized_name'],
								keyToSentValue:          'character_id',
								attributesForSentInput:  ['name' => 'character_ids[]', 'disabled' => $disabled],
								attributesForShownInput: [
								                              'placeholder'             => __('base.controls.select.hint_idle'),
															  'data-placeholder-select' => __('base.controls.select.hint_idle'),
															  'data-placeholder-filter' => __('base.controls.select.hint_filter'),
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
						'.$this->createHeadingForInput(__('contributor.edit.lyrics.original_song'), 2, false).'
						'.$this->createSearchableSelect
						(
							iteratedOptions:        $originalSongs,
							selectedOption:         $originalSong,
							addEmptyOption:         true,
							keyToShownValue:        'transliterated_name',
							keysToShownHints:       ['original_name', 'localized_name'],
							keyToSentValue:         'id',
							attributesForSentInput: ['name' => 'original_song_id', 'id' => 'original-song-select']
						).'
					</section>
					<section class="has-tooltip" tooltip-id="3">
						'.$this->createHeadingForInput(__('contributor.edit.lyrics.language'), 2, true).'
						'.$this->createSearchableSelect
						(
							iteratedOptions:        $languages,
							selectedOption:         $song,
							addEmptyOption:         true,
							keyToShownValue:        Localizer::localizeLanguageKey(),
							keysToShownHints:       ['language_own_name'],
							keyToSentValue:         'language_id',
							attributesForSentInput: ['name' => 'language_id', 'id' => 'language-select', 'required' => true]
						).'
					</section>
					<section class="has-tooltip lyrics-textarea" tooltip-id="4">
						'.$this->createHeadingForInput(__('contributor.edit.lyrics.lyrics'), 2, true).'
						'.$this->createTextarea
						(
							value:      $song['lyrics'],
							attributes: ['name' => 'lyrics', 'id' => 'lyrics-area', 'placeholder' => __('base.controls.textarea.hint'), 'required' => true]
						).'
					</section>
					<section class="has-tooltip notes-textarea" tooltip-id="5">
						'.$this->createHeadingForInput(__('contributor.edit.lyrics.notes'), 2, false).'
						'.$this->createTextarea
						(
							value:      $song['notes'],
							attributes: ['name' => 'notes', 'id' => 'notes-area', 'placeholder' => __('base.controls.textarea.hint'), 'required' => false]
						).'
					</section>
					<section class="has-tooltip" tooltip-id="6">
						<section class="page-controls">
							'.$this->createReturnButton($returnLink, __('base.controls.button.return')).'
							'.$this->createFillerSection().'
							'.$this->createSubmitButton(__('base.controls.button.submit')).'
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
				__('contributor.edit.shared.hint'),
				__('contributor.edit.lyrics.performers'),
				__('contributor.edit.lyrics.original_song'),
				__('contributor.edit.lyrics.language'),
				__('contributor.edit.lyrics.lyrics'),
				__('contributor.edit.lyrics.notes'),
				__('contributor.edit.shared.controls')
			],
			attributes: ['id' => 'tooltip-headings']
		);
		$html[] = $this->createDatalist
		(
			options:
			[
				__('contributor.edit.shared.tooltip.hint'),
				__('contributor.edit.lyrics.tooltip.performers'),
				__('contributor.edit.lyrics.tooltip.original_song'),
				__('contributor.edit.lyrics.tooltip.language'),
				__('contributor.edit.lyrics.tooltip.lyrics'),
				__('contributor.edit.lyrics.tooltip.notes'),
				__('contributor.edit.shared.tooltip.controls')
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
		string     $heading,
		string     $nonce
	): void
	{
		if ($translation)
		{
			$translationLanguage = $translation['language_id'];
			$translationName     = $translation['name'];
			$translationLyrics   = $translation['lyrics'];
			$translationNotes    = $translation['notes'];
			
			$languageSelect = $this->createTextInput(['value' => Localizer::localizeLanguageName($translation), 'disabled' => true]);
			
			$returnLink = Http::buildPath($this->language, 'album', $album['uri'], 'song', $song['uri'], 'translation', $translation['uri']);
		}
		else
		{
			$translationLanguage = null;
			$translationName     = null;
			$translationLyrics   = null;
			$translationNotes    = null;
			
			$forbiddenLanguages = [];
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
				keyToShownValue:        Localizer::localizeLanguageKey(),
				keysToShownHints:       ['language_own_name'],
				keyToSentValue:         'id',
				attributesForSentInput: ['name' => 'translation_language_id', 'required' => true, 'disabled' => $translation !== null]
			);
			
			$returnLink = Http::buildPath($this->language, 'album', $album['uri'], 'song', $song['uri']);
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
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $nonce]).'
					<section class="has-tooltip" tooltip-id="1">
						'.$this->createHeadingForInput(__('contributor.edit.translation.target_language'), 2, true).'
						'.$languageSelect.'
					</section>
					<section class="has-tooltip" tooltip-id="2">
						'.$this->createHeadingForInput(__('contributor.edit.translation.target_name'), 2, true).'
						'.$this->createTextInput(['name' => 'translation_name', 'value' => $translationName, 'required' => true]).'
					</section>
					<section class="has-tooltip" tooltip-id="3">
						'.$this->createHeadingForInput(__('contributor.edit.translation.target_lyrics'), 2, true).'
						'.$this->createTextarea($translationLyrics, ['name' => 'translation_lyrics', 'placeholder' => \Localization\Controls\Textarea, 'required' => true]).'
					</section>
					<section class="has-tooltip" tooltip-id="4">
						'.$this->createHeadingForInput(__('contributor.edit.translation.target_notes'), 2, false).'
						'.$this->createTextarea($translationLyrics, ['name' => 'translation_notes', 'placeholder' => \Localization\Controls\Textarea, 'required' => false]).'
					</section>
					<section class="has-tooltip" tooltip-id="5">
						<section class="page-controls">
							'.$this->createReturnButton($returnLink, __('base.controls.button.return')).'
							'.$this->createFillerSection().'
							'.$this->createSubmitButton(__('base.controls.button.submit')).'
						</section>
					</section>
				</form>
			</section>
			<section>
				'.$this->createHeading($song['transliterated_name'], 1).'
				<section class="form-replacement">
					<section>
						'.$this->createHeadingForInput(__('contributor.edit.translation.source_language'), 2, false).'
						'.$this->createTextInput(['value' => \Localization\Functions\localizeLanguageName($song), 'disabled' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput(__('contributor.edit.translation.source_name'), 2, false).'
						'.$this->createTextInput(['value' => $song['original_name'], 'disabled' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput(__('contributor.edit.translation.source_lyrics'), 2, false).'
						'.$this->createTextarea($song['lyrics'], ['disabled' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput(__('contributor.edit.translation.source_notes'), 2, false).'
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
				__('contributor.edit.shared.hint'),
				__('contributor.edit.translation.target_language'),
				__('contributor.edit.translation.target_name'),
				__('contributor.edit.translation.target_lyrics'),
				__('contributor.edit.translation.target_notes'),
				__('contributor.edit.shared.controls')
			],
			attributes: ['id' => 'tooltip-headings']
		);
		$html[] = $this->createDatalist
		(
			options:
			[
				__('contributor.edit.shared.tooltip.hint'),
				__('contributor.edit.translation.tooltip.target_language'),
				__('contributor.edit.translation.tooltip.target_name'),
				__('contributor.edit.translation.tooltip.target_lyrics'),
				__('contributor.edit.translation.tooltip.target_notes'),
				__('contributor.edit.shared.tooltip.controls')
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
	
	private function renderDeleteEntityPage
	(
		string $heading,
		string $returnLink,
		string $nonce
	): void
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
				'.$this->createParagraph(__('contributor.delete.description.one')).'
				'.$this->createParagraph(__('contributor.delete.description.two')).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $nonce]).'
					<section>
						'.$this->createReturnButton($returnLink, __('base.controls.button.return')).'
						'.$this->createFillerSection().'
						'.$this->createCheckbox(__('base.controls.button.confirm'), true, ['name' => 'confirmation', 'id' => 'confirmation-button', 'value' => 1, 'required' => true]).'
						'.$this->createSubmitButton(__('base.controls.button.submit'), attributes: ['id' => 'submission-button', 'disabled' => true]).'
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
			__('contributor.edit.game.heading.add'),
			$_SESSION['add_game_page']['nonce']
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
			__('contributor.edit.album.heading.add'),
			$_SESSION['add_album_page']['nonce']
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
			__('contributor.edit.artist.heading.add'),
			$_SESSION['add_artist_page']['nonce']
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
			__('contributor.edit.character.heading.add'),
			$_SESSION['add_character_page']['nonce']
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
			__('contributor.edit.song.heading.add'),
			$_SESSION['add_song_page']['nonce']
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
			__('contributor.edit.lyrics.heading.add', ['name' => $song['transliterated_name']]),
			$_SESSION['add_lyrics_page']['nonce']
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
			__('contributor.edit.translation.heading.add'),
			$_SESSION['add_translation_page']['nonce']
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
			__('contributor.edit.game.heading.edit', ['name' => $game['transliterated_name']]),
			$_SESSION['edit_game_page']['nonce']
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
			__('contributor.edit.album.heading.edit', ['name' => $album['transliterated_name']]),
			$_SESSION['edit_album_page']['nonce']
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
			__('contributor.edit.artist.heading.edit', ['name' => $artist['transliterated_name']]),
			$_SESSION['edit_artist_page']['nonce']
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
			__('contributor.edit.character.heading.edit', ['name' => $character['transliterated_name']]),
			$_SESSION['edit_character_page']['nonce']
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
			__('contributor.edit.song.heading.edit', ['name' => $song['transliterated_name']]),
			$_SESSION['edit_song_page']['nonce']
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
			__('contributor.edit.lyrics.heading.edit', ['name' => $song['transliterated_name']]),
			$_SESSION['edit_lyrics_page']['nonce']
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
			__('contributor.edit.translation.heading.edit'),
			$_SESSION['edit_translation_page']['nonce']
		);
	}
	
	final public function renderDeleteGamePage(array $game): void
	{
		$heading    =__('contributor.delete.game', ['name' => htmlspecialchars($game['transliterated_name'])]);
		$returnLink = Http::buildPath($this->language, 'game', $game['uri']);
		
		$this->renderDeleteEntityPage
		(
			$heading,
			$returnLink,
			$_SESSION['delete_game_page']['nonce']
		);
}
	
	final public function renderDeleteAlbumPage(array $album): void
	{
		$heading    =__('contributor.delete.album', ['name' => htmlspecialchars($album['transliterated_name'])]);
		$returnLink = Http::buildPath($this->language, 'album', $album['uri']);
		
		$this->renderDeleteEntityPage
		(
			$heading,
			$returnLink,
			$_SESSION['delete_album_page']['nonce']
		);
	}
	
	final public function renderDeleteArtistPage(array $artist): void
	{
		$heading    =__('contributor.delete.artist', ['name' => htmlspecialchars($artist['transliterated_name'])]);
		$returnLink = Http::buildPath($this->language, 'artist', $artist['uri']);
		
		$this->renderDeleteEntityPage
		(
			$heading,
			$returnLink,
			$_SESSION['delete_artist_page']['nonce']
		);
	}
	
	final public function renderDeleteCharacterPage(array $character): void
	{
		$heading    =__('contributor.delete.character', ['name' => htmlspecialchars($character['transliterated_name'])]);
		$returnLink = Http::buildPath($this->language, 'character', $character['uri']);
		
		$this->renderDeleteEntityPage
		(
			$heading,
			$returnLink,
			$_SESSION['delete_character_page']['nonce']
		);
	}
	
	final public function renderDeleteSongPage(array $album, array $song): void
	{
		$heading    =__('contributor.delete.song', ['name' => htmlspecialchars($song['transliterated_name'])]);
		$returnLink = Http::buildPath($this->language, 'album', $album['uri']);
		
		$this->renderDeleteEntityPage
		(
			$heading,
			$returnLink,
			$_SESSION['delete_song_page']['nonce']
		);
	}
	
	final public function renderDeleteLyricsPage(array $album, array $song): void
	{
		$heading    =__('contributor.delete.lyrics', ['name' => htmlspecialchars($song['transliterated_name'])]);
		$returnLink = Http::buildPath($this->language, 'album', $album['uri'], 'song', $song['uri']);
		
		$this->renderDeleteEntityPage
		(
			$heading,
			$returnLink,
			$_SESSION['delete_lyrics_page']['nonce']
		);
	}
	
	final public function renderDeleteTranslationPage(array $album, array $song, array $translation): void
	{
		$heading    =__('contributor.delete.translation', ['name' => htmlspecialchars($translation['name'])]);
		$returnLink = Http::buildPath($this->language, 'album', $album['uri'], 'song', $song['uri'], 'translation', $translation['uri']);
		
		$this->renderDeleteEntityPage
		(
			$heading,
			$returnLink,
			$_SESSION['delete_translation_page']['nonce']
		);
	}
	
	final public function renderChangeEmailPage(array $user, InputError $error = InputError::None)
	{
		$heading    = __('contributor.account.change_email.heading', ['name' => htmlspecialchars($user['username'])]);
		$returnLink = Http::buildPath($this->language, 'user', $user['uri']);
		
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
				'.$this->createParagraph(__("base.input_error.{$error->value}")).'
				<form method="POST">
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $_SESSION['change_email_page']['nonce']]).'
					<section>
						'.$this->createHeadingForInput(__('contributor.account.change_email.new_email'), 3, true).'
						'.$this->createEmailInput(['name' => 'email', 'value' => Cryptography::decryptData($user['email']), 'placeholder' => __('visitor.sign_up.email.hint'), 'required' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput(__('contributor.account.change_email.old_password'), 3, true).'
						'.$this->createPasswordInput(['name' => 'current_password', 'placeholder' => __('visitor.log_in.password.hint'), 'required' => true]).'
					</section>
					<section>
						'.$this->createReturnButton($returnLink, __('base.controls.button.return')).'
						'.$this->createFillerSection().'
						'.$this->createSubmitButton(__('base.controls.button.submit')).'
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
		$heading    = __('contributor.account.change_username.heading', ['name' => htmlspecialchars($user['username'])]);
		$returnLink = Http::buildPath($this->language, 'user', $user['uri']);
		
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
				'.$this->createParagraph(__("base.input_error.{$error->value}")).'
				<form method="POST">
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $_SESSION['change_username_page']['nonce']]).'
					<section>
						'.$this->createHeadingForInput(__('contributor.account.change_username.new_username'), 3, true).'
						'.$this->createUsernameInput(['name' => 'username', 'value' => $user['username'], 'placeholder' => __('visitor.sign_up.username.hint'), 'required' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput(__('contributor.account.change_username.old_password'), 3, true).'
						'.$this->createPasswordInput(['name' => 'current_password', 'placeholder' => __('visitor.log_in.password.hint'), 'required' => true]).'
					</section>
					<section>
						'.$this->createReturnButton($returnLink, __('base.controls.button.return')).'
						'.$this->createFillerSection().'
						'.$this->createSubmitButton(__('base.controls.button.submit')).'
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
		$heading    = __('contributor.account.change_password.heading', ['name' => htmlspecialchars($user['username'])]);
		$returnLink = Http::buildPath($this->language, 'user', $user['uri']);
		
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
				'.$this->createParagraph(__("base.input_error.{$error->value}")).'
				<form method="POST">
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $_SESSION['change_password_page']['nonce']]).'
					<section>
						'.$this->createHeadingForInput(__('contributor.account.change_password.new_password'), 3, true).'
						'.$this->createPasswordInput(['name' => 'password', 'placeholder' => __('visitor.sign_up.password.hint'), 'required' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput(__('contributor.account.change_password.old_password'), 3, true).'
						'.$this->createPasswordInput(['name' => 'current_password', 'placeholder' => __('visitor.log_in.password.hint'), 'required' => true]).'
					</section>
					<section>
						'.$this->createReturnButton($returnLink, __('base.controls.button.return')).'
						'.$this->createFillerSection().'
						'.$this->createSubmitButton(__('base.controls.button.submit')).'
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
		$heading    = __('contributor.account.change_about_me.heading', ['name' => htmlspecialchars($user['username'])]);
		$returnLink = Http::buildPath($this->language, 'user', $user['uri']);
		
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
				'.$this->createParagraph(__("base.input_error.{$error->value}")).'
				<form method="POST">
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $_SESSION['change_about_me_page']['nonce']]).'
					<section>
						'.$this->createHeadingForInput(__('contributor.account.change_about_me.about_me'), 3, false).'
						'.$this->createTextarea
						(
							value: htmlspecialchars($user['about_me'] ?? ''),
							attributes:
							[
								'name'        => 'about_me',
								'placeholder' => __('base.controls.textarea.hint')
							]
						).'
					</section>
					<section>
						'.$this->createReturnButton($returnLink, __('base.controls.button.return')).'
						'.$this->createFillerSection().'
						'.$this->createSubmitButton(__('base.controls.button.submit')).'
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
		$heading    = __('contributor.account.delete_account.heading', ['name' => htmlspecialchars($user['username'])]);
		$returnLink = Http::buildPath($this->language, 'user', $user['username']);
		
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
				'.$this->createParagraph(__('contributor.account.delete_account.description.one')).'
				'.$this->createParagraph(__('contributor.account.delete_account.description.two')).'
				'.$this->createParagraph(__('contributor.account.delete_account.description.three')).'
				'.$this->createParagraph(__('contributor.account.delete_account.description.four')).'
				'.$this->createParagraph(__('contributor.account.delete_account.description.five')).'
				'.$this->createParagraph(__("base.input_error.{$error->value}")).'
				<form method="POST">
					'.$this->createHiddenInput(['name' => 'nonce', 'value' => $_SESSION['delete_account_page']['nonce']]).'
					<section>
						'.$this->createHeadingForInput(__('contributor.account.delete_account.old_password'), 3, true).'
						'.$this->createPasswordInput(['name' => 'password', 'placeholder' => __('visitor.log_in.password.hint'), 'required' => true]).'
					</section>
					<section>
						'.$this->createReturnButton($returnLink, __('base.controls.button.return')).'
						'.$this->createFillerSection().'
						'.$this->createSubmitButton(__('base.controls.button.submit')).'
					</section>
				</form>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
}
