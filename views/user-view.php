<?php

require_once 'views/violator-view.php';

class UserView extends ViolatorView
{
	protected const ACCEPTED_IMAGE_TYPES = ['.jpg', '.jpeg', '.png', '.webp'];
	
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
			$originalName       = htmlspecialchars($game['original_name']);
			$transliteratedName = htmlspecialchars($game['transliterated_name']);
			$localizedName      = htmlspecialchars($game['localized_name'] ?? '');
			$isImageUploaded    = $game['is_image_uploaded'];
			
			if ($game['vndb_id'])
				$vndbLink       = 'https://vndb.org/v'.htmlspecialchars($game['vndb_id']);
			else
				$vndbLink       = '';
			
			$cancelLink = Http::buildInternalPath($this->language, 'game', $game['uri']);
		}
		else
		{
			$originalName       = '';
			$transliteratedName = '';
			$localizedName      = '';
			$isImageUploaded    = false;
			$vndbLink           = '';
			
			$query = Http::getLastVisitedQuery(queryIfNull: self::ENTITY_LIST_DEFAULT_QUERY);
			$cancelLink = Http::buildInternalPath($this->language, 'game-list').$query;
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
		
		$html = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/editor-page.css']
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section class="has-tooltip" tooltip-id="1">
						<h2>'.\Localization\GameEditorPage\OriginalName.'<span class="required-input"> *</span></h2>
						<input type="text" name="original-name" placeholder="蒼の彼方のフォーリズム" value="'.$originalName.'" required/>
					</section>
					<section class="has-tooltip" tooltip-id="2">
						<h2>'.\Localization\GameEditorPage\TransliteratedName.'<span class="required-input"> *</span></h2>
						<input type="text" name="transliterated-name" placeholder="Ao no Kanata no Foo Rizumu" pattern="[ -~]+" value="'.$transliteratedName.'" required/>
					</section>
					<section class="has-tooltip" tooltip-id="3">
						<h2>'.\Localization\GameEditorPage\LocalizedName.'</h2>
						<input type="text" name="localized-name" placeholder="Aokana - Four Rhythms Across the Blue" value="'.$localizedName.'"/>
					</section>
		';
		
		$fileupload = $this->createFileupload
		(
			'logo',
			'logo-input',
			null,
			true,
			false,
			false,
			self::ACCEPTED_IMAGE_TYPES
		);
		
		if ($isImageUploaded)
		{
			$html .= 
			'
					<section class="has-tooltip" tooltip-id="4">
						<h2>'.\Localization\GameEditorPage\OldLogo.'</h2>
						'.$this->createGameImage($game).'
					</section>
					<section class="has-tooltip" tooltip-id="5">
						<h2>'.\Localization\GameEditorPage\NewLogo.'</h2>
						'.$fileupload.'
					</section>
			';
		}
		else
		{
			$html .= 
			'
					<section class="has-tooltip" tooltip-id="6">
						<h2>'.\Localization\GameEditorPage\Logo.'</h2>
						'.$fileupload.'
					</section>
			';
		}
		
		$html .= 
		'
					<section class="has-tooltip" tooltip-id="7">
						<h2>'.\Localization\GameEditorPage\VndbLink.'</h2>
						<input type="url" name="vndb-link" placeholder="https://vndb.org/v12849" value="'.$vndbLink.'"/>
					</section>
					<section class="has-tooltip" tooltip-id="8">
						<h2>'.\Localization\GameEditorPage\RelatedAlbums.'</h2>
		';
		
		foreach ($relatedAlbums as $relatedAlbum)
		{
			$albumInput = $this->createSelect
			(
				'album-ids[]',
				null,
				null,
				$relatedAlbum['game_album_relation_status'] === 'unchecked',
				false,
				false,
				true,
				$albums,
				$relatedAlbum,
				'transliterated_name',
				'id'
			);
			
			$addRowButton = $this->createAddRowButton
			(
				'album-select'
			);
			
			$deleteRowButton = $this->createDeleteRowButton
			(
				'album-select',
				$relatedAlbum['game_album_relation_status'] === 'unchecked'
			);
			
			$html .= 
			'
					<section class="related-entity-controls">
						'.$albumInput.'
						'.$addRowButton.'
						'.$deleteRowButton.'
					</section>
			';
		}
		
		$html .= 
		'
					</section>
					<section class="has-tooltip" tooltip-id="9">
						<h2>'.\Localization\GameEditorPage\RelatedCharacters.'</h2>
		';
		
		foreach ($relatedCharacters as $relatedCharacter)
		{
			$characterInput = $this->createSelect
			(
				'character-ids[]',
				null,
				null,
				$relatedCharacter['character_game_relation_status'] === 'unchecked',
				false,
				false,
				true,
				$characters,
				$relatedCharacter,
				'transliterated_name',
				'id'
			);
			
			$addRowButton = $this->createAddRowButton
			(
				'character-select'
			);
			
			$deleteRowButton = $this->createDeleteRowButton
			(
				'character-select',
				$relatedCharacter['character_game_relation_status'] === 'unchecked',
			);
			
			$html .= 
			'
					<section class="related-entity-controls">
						'.$characterInput.'
						'.$addRowButton.'
						'.$deleteRowButton.'
					</section>
			';
		}
		
		$html .= 
		'
					</section>
					<section class="has-tooltip" tooltip-id="10">
						<section class="page-controls">
							'.$this->createButton(\Localization\Controls\Cancel, $cancelLink).'
							<section></section>
							<input type="submit" value="'.\Localization\Controls\Submit.'"/>
						</section>
					</section>
				</form>
			</section>
			<section>
			</section>
		</article>
		';
		
		$tooltipWindow = $this->createTooltipWindow();
		$tooltipHeadings = $this->createTooltipHeadingDatalist
		(
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
		);
		$tooltipContents = $this->createTooltipContentDatalist
		(
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
		);
		
		$html .= 
		'
		'.$tooltipWindow.'
		'.$tooltipHeadings.'
		'.$tooltipContents.'
		';
		
		$html .= $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/add-delete-row-buttons.js',
				'/js/shared/tooltip-window.js',
				'/js/entity-editor-page.js'
			]
		);
		
		echo $html;
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
			$originalName       = htmlspecialchars($album['original_name']);
			$transliteratedName = htmlspecialchars($album['transliterated_name']);
			$localizedName      = htmlspecialchars($album['localized_name'] ?? '');
			$isImageUploaded    = $album['is_image_uploaded'];
			
			if ($album['vgmdb_id'])
				$vgmdbLink      = 'https://vgmdb.net/album/'.htmlspecialchars($album['vgmdb_id']);
			else
				$vgmdbLink      = '';
			
			$songCount          = htmlspecialchars($album['song_count']);
			
			$cancelLink = Http::buildInternalPath($this->language, 'album', $album['uri']);
		}
		else
		{
			$originalName       = '';
			$transliteratedName = '';
			$localizedName      = '';
			$isImageUploaded    = false;
			$vgmdbLink          = '';
			$songCount          = '';
			
			$query = Http::getLastVisitedQuery(queryIfNull: self::ENTITY_LIST_DEFAULT_QUERY);
			$cancelLink = Http::buildInternalPath($this->language, 'album-list').$query;
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
		
		$html = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/editor-page.css']
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section class="has-tooltip" tooltip-id="1">
						<h2>'.\Localization\AlbumEditorPage\OriginalName.'<span class="required-input"> *</span></h2>
						<input type="text" name="original-name" placeholder="蒼の彼方のフォーリズム サウンドトラックCD vol.1" value="'.$originalName.'" required/>
					</section>
					<section class="has-tooltip" tooltip-id="2">
						<h2>'.\Localization\AlbumEditorPage\TransliteratedName.'<span class="required-input"> *</span></h2>
						<input type="text" name="transliterated-name" placeholder="Ao no Kanata no Foo Rizumu Saundotorakku CD VOL.1" pattern="[ -~]+" value="'.$transliteratedName.'" required/>
					</section>
					<section class="has-tooltip" tooltip-id="3">
						<h2>'.\Localization\AlbumEditorPage\LocalizedName.'</h2>
						<input type="text" name="localized-name" placeholder="FOUR RHYTHM ACROSS THE BLUE SOUND TRACK CD VOL.01" value="'.$localizedName.'"/>
					</section>
		';
		
		$fileupload = $this->createFileupload
		(
			'cover',
			'cover-input',
			null,
			true,
			false,
			false,
			self::ACCEPTED_IMAGE_TYPES
		);
		
		if ($isImageUploaded)
		{
			$html .= 
			'
					<section class="has-tooltip" tooltip-id="4">
						<h2>'.\Localization\AlbumEditorPage\OldCover.'</h2>
						'.$this->createAlbumImage($album).'
					</section>
					<section class="has-tooltip" tooltip-id="5">
						<h2>'.\Localization\AlbumEditorPage\NewCover.'</h2>
						'.$fileupload.'
					</section>
			';
		}
		else
		{
			$html .= 
			'
					<section class="has-tooltip" tooltip-id="6">
						<h2>'.\Localization\AlbumEditorPage\Cover.'</h2>
						'.$fileupload.'
					</section>
			';
		}
		
		$html .= 
		'
					<section class="has-tooltip" tooltip-id="7">
						<h2>'.\Localization\AlbumEditorPage\VgmdbLink.'</h2>
						<input type="url" name="vgmdb-link" placeholder="https://vgmdb.net/album/56642" value="'.$vgmdbLink.'"/>
					</section>
					<section class="has-tooltip" tooltip-id="8">
						<h2>'.\Localization\AlbumEditorPage\RelatedGames.'</h2>
		';
		
		foreach ($relatedGames as $relatedGame)
		{
			$gameInput = $this->createSelect
			(
				'game-ids[]',
				null,
				null,
				$relatedGame['game_album_relation_status'] === 'unchecked',
				false,
				false,
				true,
				$games,
				$relatedGame,
				'transliterated_name',
				'id'
			);
			
			$addRowButton = $this->createAddRowButton
			(
				'game-select'
			);
			
			$deleteRowButton = $this->createDeleteRowButton
			(
				'game-select',
				$relatedGame['game_album_relation_status'] === 'unchecked'
			);
			
			$html .= 
			'
					<section class="related-entity-controls">
						'.$gameInput.'
						'.$addRowButton.'
						'.$deleteRowButton.'
					</section>
			';
		}
		
		$html .= 
		'
					</section>
					<section class="has-tooltip" tooltip-id="9">
						<h2>'.\Localization\AlbumEditorPage\SongCount.'<span class="required-input"> *</span></h2>
						<input type="text" pattern="\d+" name="song-count" placeholder="28" value="'.$songCount.'" required/>
					</section>
					<section class="has-tooltip" tooltip-id="10">
						<section class="page-controls">
							'.$this->createButton(\Localization\Controls\Cancel, $cancelLink).'
							<section></section>
							<input type="submit" value="'.\Localization\Controls\Submit.'"/>
						</section>
					</section>
				</form>
			</section>
			<section>
			</section>
		</article>
		';
		
		$tooltipWindow = $this->createTooltipWindow();
		$tooltipHeadings = $this->createTooltipHeadingDatalist
		(
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
		);
		$tooltipContents = $this->createTooltipContentDatalist
		(
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
		);
		
		$html .= 
		'
		'.$tooltipWindow.'
		'.$tooltipHeadings.'
		'.$tooltipContents.'
		';
		
		$html .= $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/add-delete-row-buttons.js',
				'/js/shared/tooltip-window.js',
				'/js/entity-editor-page.js'
			]
		);
		
		echo $html;
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
			$originalName       = htmlspecialchars($artist['original_name']);
			$transliteratedName = htmlspecialchars($artist['transliterated_name']);
			$localizedName      = htmlspecialchars($artist['localized_name'] ?? '');
			$isImageUploaded    = $artist['is_image_uploaded'];
			
			if ($artist['vgmdb_id'])
				$vgmdbLink      = 'https://vgmdb.net/artist/'.htmlspecialchars($artist['vgmdb_id']);
			else
				$vgmdbLink      = '';
			
			$originalArtist     = array_find($originalArtists, function (array $original) use ($artist) { return $artist['alias_of_artist_id'] === $original['id']; });
			
			$cancelLink = Http::buildInternalPath($this->language, 'artist', $artist['uri']);
		}
		else
		{
			$originalName       = '';
			$transliteratedName = '';
			$localizedName      = '';
			$isImageUploaded    = false;
			$vgmdbLink          = '';
			$originalArtist     = null;
			
			$query = Http::getLastVisitedQuery(queryIfNull: self::ENTITY_LIST_DEFAULT_QUERY);
			$cancelLink = Http::buildInternalPath($this->language, 'artist-list').$query;
		}
		
		$html = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/editor-page.css']
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section class="has-tooltip" tooltip-id="1">
						<h2>'.\Localization\ArtistEditorPage\OriginalName.'<span class="required-input"> *</span></h2>
						<input type="text" name="original-name" placeholder="いとうかなこ" value="'.$originalName.'" required />
					</section>
					<section class="has-tooltip" tooltip-id="2">
						<h2>'.\Localization\ArtistEditorPage\TransliteratedName.'<span class="required-input"> *</span></h2>
						<input type="text" name="transliterated-name" placeholder="Itou Kanako" pattern="[ -~]+" value="'.$transliteratedName.'" required />
					</section>
					<section class="has-tooltip" tooltip-id="3">
						<h2>'.\Localization\ArtistEditorPage\LocalizedName.'</h2>
						<input type="text" name="localized-name" placeholder="Kanako Ito" value="'.$localizedName.'" />
					</section>
		';
		
		$fileupload = $this->createFileupload
		(
			'photo',
			'photo-input',
			null,
			true,
			false,
			false,
			self::ACCEPTED_IMAGE_TYPES
		);
		
		if ($isImageUploaded)
		{
			$html .= 
			'
					<section class="has-tooltip" tooltip-id="4">
						<h2>'.\Localization\ArtistEditorPage\OldPhoto.'</h2>
						'.$this->createArtistImage($artist).'
					</section>
					<section class="has-tooltip" tooltip-id="5">
						<h2>'.\Localization\ArtistEditorPage\NewPhoto.'</h2>
						'.$fileupload.'
					</section>
			';
		}
		else
		{
			$html .= 
			'
					<section class="has-tooltip" tooltip-id="6">
						<h2>'.\Localization\ArtistEditorPage\Photo.'</h2>
						'.$fileupload.'
					</section>
			';
		}
		
		$html .= 
		'
					<section class="has-tooltip" tooltip-id="7">
						<h2>'.\Localization\ArtistEditorPage\VgmdbLink.'</h2>
						<input type="url" name="vgmdb-link" placeholder="https://vgmdb.net/artist/69" value="'.$vgmdbLink.'"/>
					</section>
		';
		
		$originalArtistSelect = $this->createSelect
		(
			'original-artist-id',
			null,
			null,
			true,
			false,
			false,
			true,
			$originalArtists,
			$originalArtist,
			'transliterated_name',
			'id'
		);
		
		$html .=
		'
					<section class="has-tooltip" tooltip-id="8">
						<h2>'.\Localization\ArtistEditorPage\OriginalArtist.'</h2>
						'.$originalArtistSelect.'
					</section>
		';
		
		$html .=
		'
					<section class="has-tooltip" tooltip-id="9">
						<section class="page-controls">
							'.$this->createButton(\Localization\Controls\Cancel, $cancelLink).'
							<section></section>
							<input type="submit" value="'.\Localization\Controls\Submit.'"/>
						</section>
					</section>
				</form>
			</section>
			<section>
			</section>
		</article>
		';
		
		$tooltipWindow = $this->createTooltipWindow();
		$tooltipHeadings = $this->createTooltipHeadingDatalist
		(
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
		);
		$tooltipContents = $this->createTooltipContentDatalist
		(
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
		);
		
		$html .= 
		'
		'.$tooltipWindow.'
		'.$tooltipHeadings.'
		'.$tooltipContents.'
		';
		
		$html .= $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/add-delete-row-buttons.js',
				'/js/shared/tooltip-window.js',
				'/js/entity-editor-page.js'
			]
		);
		
		echo $html;
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
			$originalName       = htmlspecialchars($character['original_name']);
			$transliteratedName = htmlspecialchars($character['transliterated_name']);
			$localizedName      = htmlspecialchars($character['localized_name'] ?? '');
			$isImageUploaded    = $character['is_image_uploaded'];
			
			if ($character['vndb_id'])
				$vndbLink       = 'https://vndb.org/c'.htmlspecialchars($character['vndb_id']);
			else
				$vndbLink       = '';
			
			$cancelLink = Http::buildInternalPath($this->language, 'character', $character['uri']);
		}
		else
		{
			$originalName       = '';
			$transliteratedName = '';
			$localizedName      = '';
			$isImageUploaded    = false;
			$vndbLink           = '';
			
			$query = Http::getLastVisitedQuery(queryIfNull: self::ENTITY_LIST_DEFAULT_QUERY);
			$cancelLink = Http::buildInternalPath($this->language, 'character-list').$query;
		}
		
		if (Validation::isNullOrEmpty($relatedGames))
		{
			$relatedGames =
			[
				[
					'id'                         => '',
					'transliterated_name'        => '',
					'character_game_relation_status' => 'unchecked'
				]
			];
		}
		
		$html = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/editor-page.css']
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section class="has-tooltip" tooltip-id="1">
						<h2>'.\Localization\CharacterEditorPage\OriginalName.'<span class="required-input"> *</span></h2>
						<input type="text" name="original-name" placeholder="桐生萌郁" value="'.$originalName.'" required/>
					</section>
					<section class="has-tooltip" tooltip-id="2">
						<h2>'.\Localization\CharacterEditorPage\TransliteratedName.'<span class="required-input"> *</span></h2>
						<input type="text" name="transliterated-name" placeholder="Kiryuu Moeka" pattern="[ -~]+" value="'.$transliteratedName.'" required/>
					</section>
					<section class="has-tooltip" tooltip-id="3">
						<h2>'.\Localization\CharacterEditorPage\LocalizedName.'</h2>
						<input type="text" name="localized-name" placeholder="Moeka Kiryu" value="'.$localizedName.'"/>
					</section>
		';
		
		$fileupload = $this->createFileupload
		(
			'image',
			'image-input',
			null,
			true,
			false,
			false,
			self::ACCEPTED_IMAGE_TYPES
		);
		
		if ($isImageUploaded)
		{
			$html .= 
			'
					<section class="has-tooltip" tooltip-id="4">
						<h2>'.\Localization\CharacterEditorPage\OldImage.'</h2>
						'.$this->createCharacterImage($character).'
					</section>
					<section class="has-tooltip" tooltip-id="5">
						<h2>'.\Localization\CharacterEditorPage\NewImage.'</h2>
						'.$fileupload.'
					</section>
			';
		}
		else
		{
			$html .= 
			'
					<section class="has-tooltip" tooltip-id="6">
						<h2>'.\Localization\CharacterEditorPage\Image.'</h2>
						'.$fileupload.'
					</section>
			';
		}
		
		$html .= 
		'
					<section class="has-tooltip" tooltip-id="7">
						<h2>'.\Localization\CharacterEditorPage\VndbLink.'</h2>
						<input type="url" name="vndb-link" placeholder="https://vndb.org/c6496" value="'.$vndbLink.'"/>
					</section>
					<section class="has-tooltip" tooltip-id="8">
						<h2>'.\Localization\CharacterEditorPage\RelatedGames.'</h2>
		';
		
		foreach ($relatedGames as $relatedGame)
		{
			$gameInput = $this->createSelect
			(
				'game-ids[]',
				null,
				null,
				$relatedGame['character_game_relation_status'] === 'unchecked',
				false,
				false,
				true,
				$games,
				$relatedGame,
				'transliterated_name',
				'id'
			);
			
			$addRowButton = $this->createAddRowButton
			(
				'game-select'
			);

			$deleteRowButton = $this->createDeleteRowButton
			(
				'game-select',
				$relatedGame['character_game_relation_status'] === 'unchecked'
			);

			$html .= 
			'
					<section class="related-entity-controls">
						'.$gameInput.'
						'.$addRowButton.'
						'.$deleteRowButton.'
					</section>
			';
		}
		
		$html .= 
		'
					</section>
					<section class="has-tooltip" tooltip-id="9">
						<section class="page-controls">
							'.$this->createButton(\Localization\Controls\Cancel, $cancelLink).'
							<section></section>
							<input type="submit" value="'.\Localization\Controls\Submit.'"/>
						</section>
					</section>
				</form>
			</section>
			<section>
			</section>
		</article>
		';
		
		$tooltipWindow = $this->createTooltipWindow();
		$tooltipHeadings = $this->createTooltipHeadingDatalist
		(
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
		);
		$tooltipContents = $this->createTooltipContentDatalist
		(
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
		);
		
		$html .= 
		'
		'.$tooltipWindow.'
		'.$tooltipHeadings.'
		'.$tooltipContents.'
		';
		
		$html .= $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/add-delete-row-buttons.js',
				'/js/shared/tooltip-window.js',
				'/js/entity-editor-page.js'
			]
		);
		
		echo $html;
	}
	
	final public function renderSongEditorPage
	(
		array      $album,
		array|null $song,
		int  |null $discNumber,
		int  |null $trackNumber,
		bool |null $isLastSong,
		string     $heading
	): void
	{
		if ($song)
		{
			$discNumber         = htmlspecialchars($song['disc_number']);
			$trackNumber        = htmlspecialchars($song['track_number']);
			$originalName       = htmlspecialchars($song['original_name']);
			$transliteratedName = htmlspecialchars($song['transliterated_name']);
			$localizedName      = htmlspecialchars($song['localized_name'] ?? '');
			$hasVocal           = $song['has_vocal'];
			
			$fieldFlag          = 'disabled';
			$buttonFlag         = 'disabled';
		}
		else
		{
			$originalName       = '';
			$transliteratedName = '';
			$localizedName      = '';
			$hasVocal           = null;
			
			$fieldFlag          = 'readonly';
			$buttonFlag         = '';
		}
		
		$cancelLink = Http::buildInternalPath($this->language, 'album', $album['uri']);
		
		$vocalOptions =
		[
			['id' => 0, 'value' => \Localization\SongEditorPage\HasVocalFalse],
			['id' => 1, 'value' => \Localization\SongEditorPage\HasVocalTrue],
		];
		
		$select = $this->createSelect
		(
			'has-vocal',
			null,
			null,
			true,
			false,
			true,
			true,
			$vocalOptions,
			(is_null($hasVocal) ? null : $vocalOptions[$hasVocal]),
			'value',
			'id'
		);
		
		if ($isLastSong === true)
			$submitButtonValue = \Localization\SongEditorPage\SubmitLastSong;
		else if ($isLastSong === false)
			$submitButtonValue = \Localization\SongEditorPage\SubmitNonLastSong;
		else
			$submitButtonValue = \Localization\SongEditorPage\SubmitChanges;
		
		$html = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/editor-page.css']
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section class="has-tooltip" tooltip-id="1">
						<h2>'.\Localization\SongEditorPage\DiscAndTrack.'<span class="required-input"> *</span></h2>
						<section class="disc-track-controls">
							<input type="text" name="disc-number" value="'.$discNumber.'" id="disc-number" '.$fieldFlag.' required/>
							<input type="text" name="track-number" value="'.$trackNumber.'" id="track-number" '.$fieldFlag.' required/>
							<button id="next-disc" type="button" '.$buttonFlag.'>'.\Localization\SongEditorPage\NextDisc.'</button>
							<button id="previous-disc" type="button" '.$buttonFlag.'>'.\Localization\SongEditorPage\PreviousDisc.'</button>
						</section>
					</section>
					<section class="has-tooltip" tooltip-id="2">
						<h2>'.\Localization\SongEditorPage\OriginalName.'<span class="required-input"> *</span></h2>
						<input type="text" name="original-name" placeholder="星たちの歌" value="'.$originalName.'" required/>
					</section>
					<section class="has-tooltip" tooltip-id="3">
						<h2>'.\Localization\SongEditorPage\TransliteratedName.'<span class="required-input"> *</span></h2>
						<input type="text" name="transliterated-name" placeholder="Hoshitachi no Uta" pattern="[ -~]+" value="'.$transliteratedName.'" required />
					</section>
					<section class="has-tooltip" tooltip-id="4">
						<h2>'.\Localization\SongEditorPage\LocalizedName.'</h2>
						<input type="text" name="localized-name" placeholder="Song of the Stars" value="'.$localizedName.'"/>
					</section>
					<section class="has-tooltip" tooltip-id="5">
						<h2>'.\Localization\SongEditorPage\HasVocal.'<span class="required-input"> *</span></h2>
						'.$select.'
					</section>
					<section class="has-tooltip" tooltip-id="6">
						<section class="page-controls">
							'.$this->createButton(\Localization\Controls\Cancel, $cancelLink).'
							<section></section>
							<input type="submit" value="'.$submitButtonValue.'" />
						</section>
					</section>
				</form>
			</section>
			<section>
			</section>
		</article>
		';
		
		$tooltipWindow = $this->createTooltipWindow();
		$tooltipHeadings = $this->createTooltipHeadingDatalist
		(
			\Localization\TooltipWindow\DefaultHeading,
			\Localization\SongEditorPage\TooltipHeading\DiscAndTrack,
			\Localization\SongEditorPage\TooltipHeading\OriginalName,
			\Localization\SongEditorPage\TooltipHeading\TransliteratedName,
			\Localization\SongEditorPage\TooltipHeading\LocalizedName,
			\Localization\SongEditorPage\TooltipHeading\HasVocal,
			\Localization\SongEditorPage\TooltipHeading\Controls
		);
		$tooltipContents = $this->createTooltipContentDatalist
		(
			\Localization\TooltipWindow\DefaultContent,
			\Localization\SongEditorPage\TooltipContent\DiscAndTrack,
			\Localization\SongEditorPage\TooltipContent\OriginalName,
			\Localization\SongEditorPage\TooltipContent\TransliteratedName,
			\Localization\SongEditorPage\TooltipContent\LocalizedName,
			\Localization\SongEditorPage\TooltipContent\HasVocal,
			\Localization\SongEditorPage\TooltipContent\Controls
		);
		
		$html .= 
		'
		'.$tooltipWindow.'
		'.$tooltipHeadings.'
		'.$tooltipContents.'
		';
		
		$html .= $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/add-delete-row-buttons.js',
				'/js/shared/tooltip-window.js',
				'/js/song-editor-page.js'
			]
		);
		
		echo $html;
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
			// Create a fake to avoid duplicating code when creating <selects>
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
		
		$html = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/editor-page.css']
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section class="has-tooltip" tooltip-id="1">
						<h2>'.\Localization\LyricsEditorPage\ArtistAndCharacter.'<span class="required-input"> *</span></h2>
		';
		
		// Unfortunately, I can not find another way to push these arrays into $this->createSelect
		// Performers and these two have different keys
		
		for ($i = 0; $i < count($artists); $i++)
		{
			$artists[$i]['artist_id'] = $artists[$i]['id'];
			$artists[$i]['artist_transliterated_name'] = $artists[$i]['transliterated_name'];
		}
		
		for ($i = 0; $i < count($characters); $i++)
		{
			$characters[$i]['character_id'] = $characters[$i]['id'];
			$characters[$i]['character_transliterated_name'] = $characters[$i]['transliterated_name'];
		}
		
		foreach ($relatedPerformers as $relatedPerformer)
		{
			$artistInput = $this->createSelect
			(
				'artist-ids[]',
				null,
				null,
				$relatedPerformer['song_artist_character_relation_status'] === 'unchecked',
				false,
				true,
				true,
				$artists,
				$relatedPerformer,
				'artist_transliterated_name',
				'artist_id'
			);
			
			$characterInput = $this->createSelect
			(
				'character-ids[]',
				null,
				null,
				$relatedPerformer['song_artist_character_relation_status'] === 'unchecked',
				false,
				false,
				true,
				$characters,
				$relatedPerformer,
				'character_transliterated_name',
				'character_id'
			);
			
			$addRowButton = $this->createAddRowButton
			(
				'artist-select character-select'
			);
			
			$deleteRowButton = $this->createDeleteRowButton
			(
				'artist-select character-select',
				$relatedPerformer['song_artist_character_relation_status'] === 'unchecked'
			);
			
			$html .= 
			'
					<section class="related-entity-controls">
						'.$artistInput.'
						<span>'.\Localization\LyricsEditorPage\PerformsAs.'</span>
						'.$characterInput.'
						'.$addRowButton.'
						'.$deleteRowButton.'
					</section>
			';
		}
		
		$html .= 
		'
					</section>
		';
		
		$originalSong = array_find
		(
			$originalSongs,
			function(array $original) use ($song)
			{
				return $song['original_song_id'] === $original['id'];
			}
		);
		
		$originalSongSelect = $this->createSelect
		(
			'original-song-id',
			'original-song-select',
			null,
			true,
			false,
			false,
			true,
			$originalSongs,
			$originalSong,
			'transliterated_name',
			'id'
		);
		
		$html .= 
		'
					<section class="has-tooltip" tooltip-id="2">
						<h2>'.Localization\LyricsEditorPage\OriginalSong.'</h2>
						'.$originalSongSelect.'
					</section>
		';
		
		for ($i = 0; $i < count($languages); $i++)
			$languages[$i]['language_id'] = $languages[$i]['id'];
		
		$languageSelect = $this->createSelect
		(
			'language-id',
			'language-select',
			null,
			true,
			false,
			true,
			true,
			$languages,
			$song,
			\Localization\Functions\localizeLanguageKey(),
			'language_id'
		);
		
		$cancelLink = Http::buildInternalPath($this->language, 'album', $album['uri'], 'song', $song['uri']);
		
		$html .= 
		'
					<section class="has-tooltip" tooltip-id="3">
						<h2>'.\Localization\LyricsEditorPage\Language.'<span class="required-input"> *</h2>
						'.$languageSelect.'
					</section>
					<section class="has-tooltip lyrics-textarea" tooltip-id="4">
						<h2>'.\Localization\LyricsEditorPage\Lyrics.'<span class="required-input"> *</span></h2>
						<textarea name="lyrics" placeholder="'.\Localization\Controls\Textarea.'" id="lyrics-area" required>'.htmlspecialchars($song['lyrics'] ?? '').'</textarea>
					</section>
					<section class="has-tooltip notes-textarea" tooltip-id="5">
						<h2>'.\Localization\LyricsEditorPage\Notes.'</h2>
						<textarea name="notes" placeholder="'.\Localization\Controls\Textarea.'" id="notes-area">'.htmlspecialchars($song['notes'] ?? '').'</textarea>
					</section>
					<section class="has-tooltip" tooltip-id="6">
						<section class="page-controls">
							'.$this->createButton(\Localization\Controls\Cancel, $cancelLink).'
							<section></section>
							<input type="submit" value="'.\Localization\Controls\Submit.'"/>
						</section>
					</section>
				</form>
			</section>
			<section>
			</section>
		</article>
		';
		
		$tooltipWindow = $this->createTooltipWindow();
		$tooltipHeadings = $this->createTooltipHeadingDatalist
		(
			\Localization\TooltipWindow\DefaultHeading,
			\Localization\LyricsEditorPage\TooltipHeading\ArtistAndCharacter,
			\Localization\LyricsEditorPage\TooltipHeading\OriginalSong,
			\Localization\LyricsEditorPage\TooltipHeading\Language,
			\Localization\LyricsEditorPage\TooltipHeading\Lyrics,
			\Localization\LyricsEditorPage\TooltipHeading\Notes,
			\Localization\LyricsEditorPage\TooltipHeading\Controls
		);
		$tooltipContents = $this->createTooltipContentDatalist
		(
			\Localization\TooltipWindow\DefaultContent,
			\Localization\LyricsEditorPage\TooltipContent\ArtistAndCharacter,
			\Localization\LyricsEditorPage\TooltipContent\OriginalSong,
			\Localization\LyricsEditorPage\TooltipContent\Language,
			\Localization\LyricsEditorPage\TooltipContent\Lyrics,
			\Localization\LyricsEditorPage\TooltipContent\Notes,
			\Localization\LyricsEditorPage\TooltipContent\Controls
		);
		
		$html .= 
		'
		'.$tooltipWindow.'
		'.$tooltipHeadings.'
		'.$tooltipContents.'
		';
		
		$html .= $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/add-delete-row-buttons.js',
				'/js/shared/tooltip-window.js',
				'/js/lyrics-editor-page.js'
			]
		);
		
		echo $html;
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
			$languageInput = '<input type="text" value="'.\Localization\Functions\localizeLanguageName($translation).'"disabled/>';
			
			$translationName   = htmlspecialchars($translation['name']);
			$translationLyrics = htmlspecialchars($translation['lyrics']);
			$translationNotes  = htmlspecialchars($translation['notes'] ?? '');
			
			$cancelLink = Http::buildInternalPath($this->language, 'album', $album['uri'], 'song', $song['uri'], 'translation', $translation['uri']);
		}
		else
		{
			$forbiddenLanguages = array_column($translationsByCurrentUser, 'language_id');
			
			$languageInput = '<select name="translation-language-id" required><option value=""></option>';
			
			foreach ($languages as $language)
			{
				if (in_array($language['id'], $forbiddenLanguages, true))
					$languageInput .= '<option value="'.$language['id'].'" disabled>'.htmlspecialchars(\Localization\Functions\localizeLanguageName($language)).'</option>';
				else if ($song['language_id'] === $language['id'])
					$languageInput .= '<option value="'.$language['id'].'" disabled>'.htmlspecialchars(\Localization\Functions\localizeLanguageName($language)).'</option>';
				else
					$languageInput .= '<option value="'.$language['id'].'">'.htmlspecialchars(\Localization\Functions\localizeLanguageName($language)).'</option>';
			}
			
			$languageInput .= '</select><section class="select-fake-filler"></section>';
			
			$translationName   = '';
			$translationLyrics = '';
			$translationNotes  = '';
			
			$cancelLink = Http::buildInternalPath($this->language, 'album', $album['uri'], 'song', $song['uri']);
		}
		
		$html = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/editor-page.css']
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section class="has-tooltip" tooltip-id="1">
						<h2>'.\Localization\TranslationEditorPage\TargetLanguage.'<span class="required-input"> *</span></h2>
						'.$languageInput.'
					</section>
					<section class="has-tooltip" tooltip-id="2">
						<h2>'.\Localization\TranslationEditorPage\TranslationName.'<span class="required-input"> *</span></h2>
						<input type="text" name="translation-name" value="'.$translationName.'" required/>
					</section>
					<section class="has-tooltip" tooltip-id="3">
						<h2>'.\Localization\TranslationEditorPage\TranslationLyrics.'<span class="required-input"> *</span></h2>
						<textarea name="translation-lyrics" placeholder="'.\Localization\Controls\Textarea.'" required>'.$translationLyrics.'</textarea>
					</section>
					<section class="has-tooltip" tooltip-id="4">
						<h2>'.\Localization\TranslationEditorPage\TranslationNotes.'</h2>
						<textarea name="translation-notes" placeholder="'.\Localization\Controls\Textarea.'">'.$translationNotes.'</textarea>
					</section>
					<section class="has-tooltip" tooltip-id="5">
						<section class="page-controls">
							'.$this->createButton(\Localization\Controls\Cancel, $cancelLink).'
							<section></section>
							<input type="submit" value="'.\Localization\Controls\Submit.'"/>
						</section>
					</section>
				</form>
			</section>
			<section>
				'.$this->createHeading($song['transliterated_name'], 1).'
				<section class="form-replacement">
					<section>
						<h2>'.\Localization\TranslationEditorPage\SourceLanguage.'</h2>
						<input type="text" value="'.\Localization\Functions\localizeLanguageName($song).'" disabled/>
					</section>
					<section>
						<h2>'.\Localization\TranslationEditorPage\SongName.'</h2>
						<input type="text" value="'.$song['original_name'].'" disabled/>
					</section>
					<section>
						<h2>'.\Localization\TranslationEditorPage\SongLyrics.'</h2>
						<textarea readonly>'.htmlspecialchars($song['lyrics']).'</textarea>
					</section>
					<section>
						<h2>'.\Localization\TranslationEditorPage\SongNotes.'</h2>
						<textarea readonly>'.htmlspecialchars($song['notes'] ?? '').'</textarea>
					</section>
				</section>
			</section>
		</article>
		';
		
		$tooltipWindow = $this->createTooltipWindow();
		$tooltipHeadings = $this->createTooltipHeadingDatalist
		(
			\Localization\TooltipWindow\DefaultHeading,
			\Localization\TranslationEditorPage\TooltipHeading\TranslationLanguage,
			\Localization\TranslationEditorPage\TooltipHeading\TranslationName,
			\Localization\TranslationEditorPage\TooltipHeading\TranslationLyrics,
			\Localization\TranslationEditorPage\TooltipHeading\TranslationNotes,
			\Localization\TranslationEditorPage\TooltipHeading\Controls
		);
		$tooltipContents = $this->createTooltipContentDatalist
		(
			\Localization\TooltipWindow\DefaultContent,
			\Localization\TranslationEditorPage\TooltipContent\TranslationLanguage,
			\Localization\TranslationEditorPage\TooltipContent\TranslationName,
			\Localization\TranslationEditorPage\TooltipContent\TranslationLyrics,
			\Localization\TranslationEditorPage\TooltipContent\TranslationNotes,
			\Localization\TranslationEditorPage\TooltipContent\Controls
		);
		
		$html .= 
		'
		'.$tooltipWindow.'
		'.$tooltipHeadings.'
		'.$tooltipContents.'
		';
		
		$html .= $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/add-delete-row-buttons.js',
				'/js/shared/tooltip-window.js',
				'/js/translation-editor-page.js'
			]
		);
		
		echo $html;
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
	
	final public function renderDeletePage(string $heading, string $cancelLink)
	{
		$html = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/window-in-center-page.css']
		);
		
		$cancelButton = $this->createButton
		(
			\Localization\Controls\Cancel,
			$cancelLink
		);
		
		$checkbox = $this->createCheckbox
		(
			'confirmation',
			'confirmation-button',
			null,
			true,
			false,
			true,
			1,
			\Localization\Controls\Confirmation
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				'.$this->createParagraph(\Localization\DeleteEntityPage\Introduction).'
		';
		
		$html .= 
		'
				'.$this->createParagraph(\Localization\DeleteEntityPage\Warning).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section>
						'.$cancelButton.'
						<section class="filler"></section>
						'.$checkbox.'
						<input type="submit" id="submission-button" value="'.\Localization\Controls\Submit.'" disabled/>
					</section>
				</form>
			</section>
		</article>
		';
		
		$html .= $this->endRender
		(
			jsScriptUris:
			[
				'/js/shared/switch-element-availability.js',
				'/js/entity-deletion-page.js'
			]
		);
		
		echo $html;
	}
	
	final public function renderDeleteGamePage(array $game): void
	{
		$heading = \Localization\DeleteEntityPage\DeleteGame.$game['transliterated_name'];
		$entityInfo =
		[
			\Localization\DeleteEntityPage\Game  => $game['transliterated_name']
		];
		$cancelLink = Http::buildInternalPath($this->language, 'game', $game['uri']);
		
		$this->renderDeletePage($heading, $cancelLink);
}
	
	final public function renderDeleteAlbumPage(array $album): void
	{
		$heading = \Localization\DeleteEntityPage\DeleteAlbum.$album['transliterated_name'];
		$cancelLink = Http::buildInternalPath($this->language, 'album', $album['uri']);
		
		$this->renderDeletePage($heading, $cancelLink);
	}
	
	final public function renderDeleteArtistPage(array $artist): void
	{
		$heading = \Localization\DeleteEntityPage\DeleteArtist.$artist['transliterated_name'];
		$cancelLink = Http::buildInternalPath($this->language, 'artist', $artist['uri']);
		
		$this->renderDeletePage($heading, $cancelLink);
	}
	
	final public function renderDeleteCharacterPage(array $character): void
	{
		$heading = \Localization\DeleteEntityPage\DeleteCharacter.$character['transliterated_name'];
		$cancelLink = Http::buildInternalPath($this->language, 'character', $character['uri']);
		
		$this->renderDeletePage($heading, $cancelLink);
	}
	
	final public function renderDeleteLyricsPage(array $album, array $song): void
	{
		$heading = \Localization\DeleteEntityPage\DeleteLyrics.$song['transliterated_name'];
		$cancelLink = Http::buildInternalPath($this->language, 'album', $album['uri'], 'song', $song['uri']);
		
		$this->renderDeletePage($heading, $cancelLink);
	}
	
	final public function renderDeleteTranslationPage(array $album, array $song, array $translation): void
	{
		$heading = \Localization\DeleteEntityPage\DeleteTranslation.$song['transliterated_name'];
		$cancelLink = Http::buildInternalPath($this->language, 'album', $album['uri'], 'song', $song['uri'], 'translation', $translation['uri']);
		
		$this->renderDeletePage($heading, $cancelLink);
	}
	
	final public function renderChangeAccountDataPage(array $user, InputError $error = InputError::None)
	{
		$heading = $user['user_username'].\Localization\UserAccountDataPage\Edit;
		$cancelLink = Http::buildInternalPath($this->language, 'user', $user['user_username']);
		
		switch ($error)
		{
			case InputError::IncorrectPassword:
				$errorMessage = \Localization\InputError\IncorrectPassword;
				break;
			
			case InputError::UsernameTrimmable:
				$errorMessage = \Localization\InputError\UsernameTrimmable;
				break;
			
			case InputError::UsernameForbiddenSymbols:
				$errorMessage = \Localization\InputError\UsernameForbiddenSymbols;
				break;
			
			case InputError::UsernameLengthIncorrect:
				$errorMessage = \Localization\InputError\UsernameLengthIncorrect;
				break;
			
			case InputError::UsernameTaken:
				$errorMessage = \Localization\InputError\UsernameTaken;
				break;
			
			case InputError::PasswordTrimmable:
				$errorMessage = \Localization\InputError\PasswordTrimmable;
				break;
				
			case InputError::PasswordForbiddenSymbols:
				$errorMessage = \Localization\InputError\PasswordForbiddenSymbols;
				break;
			
			case InputError::UsernameLengthIncorrect:
				$errorMessage = \Localization\InputError\UsernameLengthIncorrect;
				break;
			
			case InputError::EmailTaken:
				$errorMessage = \Localization\InputError\EmailTaken;
				break;
			
			case InputError::EmailInvalid:
				$errorMessage = \Localization\InputError\EmailInvalid;
				break;
			
			case InputError::EmailNotExists:
				$errorMessage = \Localization\InputError\EmailNotExists;
				break;
			
			default:
				$errorMessage = '';
				break;
		}
		
		$html = $this->startRender
		(
			title: $heading,
			cssSheetUris: ['/css/window-in-center-page.css']
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
			</section>
			<section>
				<h2>'.\Localization\UserAccountDataPage\AccountData.'</h2>
				<p>'.$errorMessage.'</p>
				<form method="POST">
					<section>
						<h3>'.\Localization\UserAccountDataPage\Username.'<span class="required-input"> *</span></h3>
						<input type="text" name="username" pattern="[a-zA-Z0-9]+" minlength="4" maxlength="32" value="'.$user['user_username'].'" placeholder="'.\Localization\SignUpPage\HintUsername.'" required/>
					</section>
					<section>
						<h3>'.\Localization\SignUpPage\Email.'<span class="required-input"> *</span></h3>
						<input type="email" name="email" minlength="4" maxlength="32" value="'.$user['user_email'].'" placeholder="'.\Localization\SignUpPage\HintEmail.'" required/>
					</section>
					<section>
						<h3>'.\Localization\UserAccountDataPage\NewPassword.'</h3>
						<input type="password" name="new-password" pattern="[a-zA-Z0-9]+" minlength="4" maxlength="32" placeholder="'.\Localization\UserAccountDataPage\NewPasswordNote.'"/>
					</section>
					<section>
						<h3>'.\Localization\UserAccountDataPage\OldPassword.'<span class="required-input"> *</span></h3>
						<input type="password" name="old-password" pattern="[a-zA-Z0-9]+" minlength="4" maxlength="32" placeholder="'.\Localization\SignUpPage\HintPassword.'" required/>
					</section>
					<section>
						'.$this->createButton(\Localization\Controls\Cancel, $cancelLink, true, '').'
						<section class="filler"></section>
						<input type="submit" value="'.\Localization\Controls\Submit.'"/>
					</section>
				</form>
			</section>
		</article>
		';
		
		$html .= $this->endRender();
		
		echo $html;
	}
	
	final public function renderDeleteAccountPage(array $user, InputError $error = InputError::None)
	{
		$heading = $user['user_username'].\Localization\UserAccountDeletePage\Delete;
		$cancelLink = Http::buildInternalPath($this->language, 'user', $user['user_username']);
		
		switch ($error)
		{
			case InputError::IncorrectPassword:
				$errorMessage = \Localization\InputError\IncorrectPassword;
				break;
			
			default:
				$errorMessage = '';
				break;
		}
		
		$html = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/window-in-center-page.css']
		);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
			</section>
			<section>
				<h2>'.\Localization\UserAccountDeletePage\AccountData.'</h2>
				<p>'.\Localization\UserAccountDeletePage\Warning1.'</p>
				<p>'.\Localization\UserAccountDeletePage\Warning2.'</p>
				<p>'.\Localization\UserAccountDeletePage\Warning3.'</p>
				<p>'.\Localization\UserAccountDeletePage\Warning4.'</p>
				<p>'.\Localization\UserAccountDeletePage\Confirmation.'</p>
				<p>'.$errorMessage.'</p>
				<form method="POST">
					<section>
						<h3>'.\Localization\UserAccountDeletePage\Password.'<span class="required-input"> *</span></h3>
						<input type="password" name="password"  pattern="[a-zA-Z0-9]+" minlength="4" maxlength="32" required/>
					</section>
					<section>
						'.$this->createButton(\Localization\Controls\Cancel, $cancelLink, true, '').'
						<section class="filler"></section>
						<input type="submit" value="'.\Localization\Controls\Submit.'"/>
					</section>
				</form>
			</section>
		</article>
		';
		
		$html .= $this->endRender();
		
		echo $html;
	}
}
