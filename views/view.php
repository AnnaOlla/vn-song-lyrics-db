<?php

abstract class View
{
	protected $language;
	
	/* These values help browsers auto-update css and js. Set them in __construct manually */
	private $cssVersion;
	private $jsVersion;
	
	public function __construct(string $language)
	{
		$this->language = $language;
		require_once 'localization/'.$language.'-localization.php';
		
		$this->cssVersion = '1.3';
		$this->jsVersion  = '1.0';
	}
	
	private function changeUriLocale(string $uri, string $newCode): string
	{
		$parts = explode('/', $uri, 3);
		$parts[1] = $newCode;
		return implode('/', $parts);
	}
	
	//----------------------------//
	//      Page Base Blocks      //
	//----------------------------//
	
	private function createHead
	(
		string      $title,
		string|null $description  = null,
		string|null $canonicalUri = null,
		string|null $ogImageUri   = null,
		array       $cssSheetUris = [],
		array       $jsScriptUris = []
	): string
    {
		$title          = htmlspecialchars($title).' | vn-song-lyrics-db';
		$description    = $description ?? 'The database of lyrics for songs introduced in visual novels.';
		
		$currentUrl     = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$canonicalUrl   = 'https://'.$_SERVER['HTTP_HOST'].($canonicalUri ?? $_SERVER['REQUEST_URI']);
		$alternateRefEn = 'https://'.$_SERVER['HTTP_HOST'].$this->changeUriLocale($_SERVER['REQUEST_URI'], 'en');
		$alternateRefRu = 'https://'.$_SERVER['HTTP_HOST'].$this->changeUriLocale($_SERVER['REQUEST_URI'], 'ru');
		$alternateRefJa = 'https://'.$_SERVER['HTTP_HOST'].$this->changeUriLocale($_SERVER['REQUEST_URI'], 'ja');
		
		$ogUrl          = $currentUrl;
		$ogImageUrl     = 'https://'.$_SERVER['HTTP_HOST'].($ogImageUri ?? '/assets/static-images/wee-hagana-og.webp');
		
		$html = <<<HTML
		<head>
			<meta charset="UTF-8" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1" />
			
			<title>{$title}</title>
			<meta name="description" content="{$description}" />
			<meta name="keywords" content="Visual Novels, Music, Soundtrack, Song, OST, Lyrics, Translations" />
			
			<meta property="og:title"            content="{$title}" />
			<meta property="og:description"      content="{$description}" />
			<meta property="og:site_name"        content="vn-song-lyrics-db" />
			<meta property="og:type"             content="website" />
			<meta property="og:url"              content="{$ogUrl}" />
			<meta property="og:image"            content="{$ogImageUrl}" />
			<meta property="og:image:width"      content="1200" />
			<meta property="og:image:height"     content="630" />
			<meta property="og:image:alt"        content="Hagana from World End Economica" />
			<meta property="og:locale"           content="en_US" />
			<meta property="og:locale:alternate" content="ru_RU" />
			<meta property="og:locale:alternate" content="ja_JP" />
			
			<meta name="robots"          content="noai, noimageai" />
			<meta name="CCBot"           content="nofollow" />
			<meta name="tdm-reservation" content="1" />
			
			<link rel="canonical"                      href="{$canonicalUrl}" />
			
			<link rel="alternate" hreflang="en"        href="{$alternateRefEn}" />
			<link rel="alternate" hreflang="ru"        href="{$alternateRefRu}" />
			<link rel="alternate" hreflang="ja"        href="{$alternateRefJa}" />
			<link rel="alternate" hreflang="x-default" href="{$alternateRefEn}" />
			
			<link rel="icon"             sizes="96x96"   type="image/png"     href="/favicon-96x96.png"  />
			<link rel="icon"                             type="image/svg+xml" href="/favicon.svg" />
			<link rel="shortcut icon"                                         href="/favicon.ico" />
			<link rel="apple-touch-icon" sizes="180x180"                      href="/apple-touch-icon.png" />
			<link rel="manifest"                                              href="/site.webmanifest" />
			
			<link type="text/css" rel="stylesheet" href="/css/core/font-hanazono-mincho-type-a.css?v={$this->cssVersion}" />
			<link type="text/css" rel="stylesheet" href="/css/core/sizes.css?v={$this->cssVersion}" />
			<link type="text/css" rel="stylesheet" href="/css/core/dark-theme.css?v={$this->cssVersion}" />
			<link type="text/css" rel="stylesheet" href="/css/core/general.css?v={$this->cssVersion}" />

		HTML;
		
		foreach ($cssSheetUris as $cssSheetUri)
		{
			$html .= <<<HTML
			<link type="text/css" rel="stylesheet" href="{$cssSheetUri}?v={$this->cssVersion}"/>

		HTML;
		}
		
        foreach ($jsScriptUris as $jsScriptUri)
		{
			$html .= <<<HTML
			<script src="{$jsScriptUri}?v={$this->jsVersion}"/></script>

		HTML;
		}
		
        $html .= <<<HTML
		</head>
		HTML;
		
		return $html;
	}
	
	private function createHeader(): string
	{
		$enLink = $this->changeUriLocale($_SERVER['REQUEST_URI'], 'en');
		$ruLink = $this->changeUriLocale($_SERVER['REQUEST_URI'], 'ru');
		$jaLink = $this->changeUriLocale($_SERVER['REQUEST_URI'], 'ja');
		
		if (isCurrentUserVisitor())
		{
			$logIn    = '<a href="/'.$this->language.'/log-in">'.\Localization\Header\LogIn.'</a>';
			$signUp   = '<a href="/'.$this->language.'/sign-up">'.\Localization\Header\SignUp.'</a>';
			$logOut   = '';
			$username = '';
		}
		else
		{
			$logIn    = '';
			$signUp   = '';
			$logOut   = '<a href="/'.$this->language.'/log-out">'.\Localization\Header\LogOut.'</a>';
			$username = '<a href="/'.$this->language.'/user/'.$_SESSION['user']['username'].'">'.$_SESSION['user']['username'].'</a>';
		}
		
		return
		'
	<header>
		<nav>
			<section class="home-page">
				<a href="/'.$this->language.'">'.\Localization\Header\HomePage.'</a>
			</section>
			<section class="main-pages">
				<a href="/'.$this->language.'/game-list">'.\Localization\Header\GameList.'</a>
				<a href="/'.$this->language.'/album-list">'.\Localization\Header\AlbumList.'</a>
				<a href="/'.$this->language.'/artist-list">'.\Localization\Header\ArtistList.'</a>
				<a href="/'.$this->language.'/character-list">'.\Localization\Header\CharacterList.'</a>
				<a href="/'.$this->language.'/song-list">'.\Localization\Header\SongList.'</a>
				<a href="/'.$this->language.'/feedback">'.\Localization\Header\Feedback.'</a>
			</section>
			<section class="filler"><span></span></section>
			<section class="localization">
				<a href="'.$enLink.'">EN</a>
				<a href="'.$ruLink.'">RU</a>
				<a href="'.$jaLink.'">JA</a>
			</section>
			<section class="filler"><span></span></section>
			<section class="authorization">
				'.$logIn.'
				'.$signUp.'
				'.$username.'
				'.$logOut.'
			</section>
		</nav>
	</header>
		';
	}
	
	final protected function startRender
	(
		string      $title,
		string|null $description  = null,
		string|null $canonicalUri = null,
		string|null $ogImageUri   = null,
		array       $cssSheetUris = [],
		array       $jsScriptUris = []
	): string
	{
		return <<<HTML
<!DOCTYPE html>
<html lang="{$this->language}">
{$this->createHead($title, $description, $canonicalUri, $ogImageUri, $cssSheetUris, $jsScriptUris)}
<body>
	{$this->createHeader()}
	<main>

HTML;
	}
	
	private function createFooter(): string
	{
		return
		'
	<footer>
		<nav>
			<section class="filler"><span></span></section>
			<section class="contacts">
				<span>AnnaOlla, 2026</span>
			</section>
			<section class="links">
				<a href="/'.$this->language.'/about">'.\Localization\Footer\About.'</a>
				<a href="/'.$this->language.'/policy">'.\Localization\Footer\Policy.'</a>
				<a href="/'.$this->language.'/rules">'.\Localization\Footer\BehaviorRules.'</a>
				<a href="/'.$this->language.'/writing-guide">'.\Localization\Footer\WritingGuide.'</a>
			</section>
			<section class="contacts">
				<span>support@vn-song-lyrics-db.ru</span>
			</section>
			<section class="filler"><span></span></section>
		</nav>
	</footer>
		';
	}
	
	final protected function endRender(array $jsScripts = []): string
	{
		$html = <<<HTML

			</main>
			{$this->createFooter()}
		HTML;
		
        foreach ($jsScripts as $jsScript)
		{
			$html .= <<<HTML
			<script src="{$jsScript}?v={$this->jsVersion}"/></script>

		HTML;
		}
		
		$html .= <<<HTML

		</body>
		</html>
		HTML;
		
		return $html;
	}
	
	/* 
	 * Trick against PHP
	 * Const values are not allowed in PHP's heredoc
	 * But we can hack it by using a function
	*/
	final protected function getConst(string $constStringFromLocalizationFile): string
	{
		return $constStringFromLocalizationFile;
	}
	
	//-----------------------------------------------------------//
	//      Content Pages: Small Blocks To Build Pages With      //
	//-----------------------------------------------------------//
	
	final protected function createLink(string $href, string $content, string ...$cssClasses): string
	{
		$class = $cssClasses ? ' class="'.implode(' ', $cssClasses).'" ' : ' ';
		return '<a'.$class.'href="'.htmlspecialchars($href).'">'.htmlspecialchars($content).'</a>';
	}
	
	final protected function createVndbLink(string $contentBefore, array $entity, string $entityFirstLetter): string
	{
		if (is_null($entity['vndb_id']))
			return '';
		
		$href = htmlspecialchars('https://vndb.org/'.$entityFirstLetter.$entity['vndb_id']);
		$link = '<a href="'.$href.'" target="_blank">'.$href.'</a>';
		
		return '<p>'.htmlspecialchars($contentBefore).$link.'</p>';
	}
	
	final protected function createVgmdbLink(string $contentBefore, array $entity, string $entityName): string
	{
		if (is_null($entity['vgmdb_id']))
			return '';
		
		$href = htmlspecialchars('https://vgmdb.net/'.$entityName.'/'.$entity['vgmdb_id']);
		$link = '<a href="'.$href.'" target="_blank">'.$href.'</a>';
		
		return '<p>'.htmlspecialchars($contentBefore).$link.'</p>';
	}
	
	final protected function createHeading(string $header, int $level): string
	{
		return '<h'.$level.'>'.htmlspecialchars($header).'</h'.$level.'>';
	}
	
	final protected function createHeadingAsLink(string $header, int $level, string $href, string ...$cssClasses): string
	{
		return '<h'.$level.'>'.$this->createLink($href, $header, ...$cssClasses).'</h'.$level.'>';
	}
	
	final protected function createParagraph(string|null $content, string ...$cssClasses): string
	{
		if (is_null($content))
			return '';
		
		$class = $cssClasses ? ' class="'.implode(' ', $cssClasses).'"' : '';
		return '<p'.$class.'>'.htmlspecialchars($content).'</p>';
	}
	
	final protected function createParagraphAsLink(string $content, string $href, string ...$cssClasses): string
	{
		$class = $cssClasses ? ' class="'.implode(' ', $cssClasses).'"' : '';
		return '<p'.$class.'>'.$this->createLink($href, $content).'</p>';
	}
	
	private function createTimestamp(string|null $timestamp, string|null $username, string $status): string
	{
		if (is_null($timestamp))
			return '';
		
		if (is_null($username))
			$username = \Localization\TimestampString\DeletedUser;
		else
			$username = $this->createLink(buildInternalLink($this->language, 'user', $username), $username);
		
		return
			'<p>'.
			htmlspecialchars($status).
			\Localization\TimestampString\Delimeter.
			htmlspecialchars($timestamp).
			\Localization\TimestampString\By.
			$username.
			'</p>';
	}
	
	final protected function createTimestampAdded(string|null $timestamp, string|null $username): string
	{
		return $this->createTimestamp($timestamp, $username, \Localization\TimestampString\Added);
	}
	
	final protected function createTimestampUpdated(string|null $timestamp, string|null $username): string
	{
		return $this->createTimestamp($timestamp, $username, \Localization\TimestampString\Updated);
	}
	
	final protected function createTimestampReviewed(string|null $timestamp, string|null $username): string
	{
		return $this->createTimestamp($timestamp, $username, \Localization\TimestampString\Reviewed);
	}
	
	final protected function createStatus(string $status, bool $isRelation = false): string
	{
		switch ($status)
		{
			case 'unchecked':
				$status = \Localization\ModerationStatus\Unchecked;
				$cssClasses = ['important-status'];
				break;
			
			case 'checked':
				$status = \Localization\ModerationStatus\Checked;
				$cssClasses = [];
				break;
			
			case 'hidden':
				$status = \Localization\ModerationStatus\Hidden;
				$cssClasses = ['important-status'];
				break;
				
			default:
				$status = \Localization\ModerationStatus\Unknown;
				$cssClasses = ['important-status'];
				break;
		}
		
		if ($isRelation)
			$prefix = \Localization\ModerationStatus\RelationStatus;
		else
			$prefix = \Localization\ModerationStatus\Status;
		
		return $this->createParagraph($prefix.$status, ...$cssClasses);
	}
	
	final protected function createStatusSelect
	(
		array       $entity,
		string|null $relationKey  = null,
		string|null $relationHref = null
	): string
	{
		if ($relationKey && $relationHref)
		{
			$relationName = mb_substr($relationKey, 0, -mb_strlen('_status'));
			$relationName = str_replace('_', '-', $relationName);
			
			$keyToStatus      = $relationKey;
			$dataEntityUri    = 'data-entity-uri="'.$relationHref.'"';
			$prefix           = \Localization\ModerationStatus\RelationStatus;
		}
		else
		{
			$keyToStatus      = 'status';
			$dataEntityUri    = '';
			$prefix           = \Localization\ModerationStatus\Status;
		}
		
		$statuses =
		[
			'unchecked' => \Localization\ModerationStatus\Unchecked,
			'checked'   => \Localization\ModerationStatus\Checked,
			'hidden'    => \Localization\ModerationStatus\Hidden
		];
		
		$html = '<p>'.$prefix.'<select class="status-select" '.$dataEntityUri.'>';
		
		foreach ($statuses as $status => $name)
		{
			if ($status === $entity[$keyToStatus])
				$html .= '<option value="'.$status.'" selected>'.$name.'</option>';
			else
				$html .= '<option value="'.$status.'">'.$name.'</option>';
		}
		
		$html .= '</select><section class="select-fake-filler"></section></p>';
		
		return $html;
	}
	
	final protected function createButton
	(
		string $content,
		string $href,
		bool   $isEnabled = true,
		string $tooltipIfDisabled = ''
	): string
	{
		if ($isEnabled)
			return '<a href="'.htmlspecialchars($href).'" class="custom-button">'.htmlspecialchars($content).'</a>';
		else
			return '<a class="custom-button disabled" title="'.$tooltipIfDisabled.'">'.htmlspecialchars($content).'</a>';
	}
	
	final protected function createImage(string $src, string $alt = ""): string
	{
		return '<img src="'.$src.'" alt="'.htmlspecialchars($alt).'"/>';
	}
	
	private function createEntityImage
	(
		array       $entity,
		AssetFolder $dynamicAssetFolder,
		string      $dynamicAssetFilename,
		string      $dynamicAssetAlternativeText,
		AssetFolder $staticReplacementFolder,
		string      $staticReplacementFilename,
		string      $staticReplacementAlternativeText
	): string
	{
		if ($entity['is_image_uploaded'])
		{
			$source = buildInternalLink
			(
				AssetFolder::Base->value,
				$dynamicAssetFolder->value,
				$dynamicAssetFilename.'.webp'
			);
			return $this->createImage($source, $dynamicAssetAlternativeText);
		}
		else
		{
			$source = buildInternalLink
			(
				AssetFolder::Base->value,
				$staticReplacementFolder->value,
				$staticReplacementFilename.'.png'
			);
			return $this->createImage($source, $staticReplacementAlternativeText);
		}
	}
	
	final protected function createGameImage(array $game): string
	{
		return $this->createEntityImage
		(
			$game,
			AssetFolder::Games,
			$game['uri'],
			\Localization\ImageAltText\GameLogoOf.$game['transliterated_name'],
			AssetFolder::Static,
			StaticAsset::NoGame->value,
			\Localization\ImageAltText\NoGameLogo
		);
	}
	
	final protected function createAlbumImage(array $album): string
	{
		return $this->createEntityImage
		(
			$album,
			AssetFolder::Albums,
			$album['uri'],
			\Localization\ImageAltText\AlbumCoverOf.$album['transliterated_name'],
			AssetFolder::Static,
			StaticAsset::NoAlbum->value,
			\Localization\ImageAltText\NoAlbumCover
		);
	}
	
	final protected function createArtistImage(array $artist): string
	{
		return $this->createEntityImage
		(
			$artist,
			AssetFolder::Artists,
			$artist['uri'],
			\Localization\ImageAltText\ArtistPhotoOf.$artist['transliterated_name'],
			AssetFolder::Static,
			StaticAsset::NoArtist->value,
			\Localization\ImageAltText\NoArtistPhoto
		);
	}
	
	final protected function createCharacterImage(array $character): string
	{
		return $this->createEntityImage
		(
			$character,
			AssetFolder::Characters,
			$character['uri'],
			\Localization\ImageAltText\CharacterImageOf.$character['transliterated_name'],
			AssetFolder::Static,
			StaticAsset::NoCharacter->value,
			\Localization\ImageAltText\NoCharacterImage
		);
	}
	
	final protected function createSearchBar(): string
	{
		return '<input type="search" id="search-bar" placeholder="'.\Localization\Controls\Search.'" />';
	}
	
	final protected function createInfoBlockWithImage
	(
		string $htmlImage,
		array  $htmlValues,
		string $entityClass
	): string
	{
		$html =
		'
			<section class="'.$entityClass.'">
				'.$htmlImage.'
				<section>
		';
		
		foreach ($htmlValues as $htmlValue)
		{
			$html .= 
			'
					'.$htmlValue.'
			';
		}
		
		$html .=
		'
				</section>
			</section>
		';
		
		return $html;
	}
	
	/* The function is not suitable for lyrics and translations because they have different logic */
	final protected function createControlButtonsBlock(array $entity, string $entityName): string
	{
		$editHref   = buildInternalLink($this->language, $entityName, $entity['uri'], 'edit');
		$deleteHref = buildInternalLink($this->language, $entityName, $entity['uri'], 'delete');
		$reportHref = buildInternalLink($this->language, $entityName, $entity['uri'], 'report');
		
		[$editEnabled,   $editTooltipIfDisabled]   = canCurrentUserEditEntity  ($entity['user_added_id'], $entity['status']);
		[$deleteEnabled, $deleteTooltipIfDisabled] = canCurrentUserDeleteEntity($entity['user_added_id'], $entity['status']);
		[$reportEnabled, $reportTooltipIfDisabled] = canCurrentUserReportEntity($entity['status']);
		
		$editButton   = $this->createButton(\Localization\Controls\Edit,   $editHref,   $editEnabled,   $editTooltipIfDisabled);
		$deleteButton = $this->createButton(\Localization\Controls\Delete, $deleteHref, $deleteEnabled, $deleteTooltipIfDisabled);
		$reportButton = $this->createButton(\Localization\Controls\Report, $reportHref, $reportEnabled, $reportTooltipIfDisabled);
		
		$html =
		'
			<section>
				'.$editButton.'
				'.$deleteButton.'
				'.$reportButton.'
			</section>
		';
		
		return $html;
	}
	
	/* It was easier to copy-paste than writing a new wrapper*/
	final protected function createControlButtonsBlockForSong(array $album, array $song, array $translations): string
	{
		$editLink   = buildInternalLink($this->language, 'album', $album['uri'], 'song', $song['uri'], 'edit-lyrics');
		$deleteLink = buildInternalLink($this->language, 'album', $album['uri'], 'song', $song['uri'], 'delete-lyrics');
		$reportLink = buildInternalLink($this->language, 'album', $album['uri'], 'song', $song['uri'], 'report-lyrics');
		
		[$isButtonEnabled, $tooltipIfDisabled] = canCurrentUserEditLyrics
		(
			$song['user_added_id'],
			$song['status'],
			!empty($translations)
		);
		
		$editButton = $this->createButton
		(
			\Localization\LyricsPage\EditLyrics,
			$editLink,
			$isButtonEnabled,
			$tooltipIfDisabled
		);
		
		$deleteButton = $this->createButton
		(
			\Localization\LyricsPage\DeleteLyrics,
			$deleteLink,
			$isButtonEnabled,
			$tooltipIfDisabled
		);
		
		[$isButtonEnabled, $tooltipIfDisabled] = canCurrentUserReportEntity($song['status']);
		
		$reportButton = $this->createButton
		(
			\Localization\LyricsPage\ReportLyrics,
			$reportLink,
			$isButtonEnabled,
			$tooltipIfDisabled
		);
		
		$html =
		'
			<section>
				'.$editButton.'
				'.$deleteButton.'
				'.$reportButton.'
			</section>
		';
		
		return $html;
	}
	
	/* It was easier to copy-paste than writing a new wrapper*/
	final protected function createControlButtonsBlockForTranslation(array $album, array $song, array $translation): string
	{
		$editLink   = buildInternalLink($this->language, 'album', $album['uri'], 'song', $song['uri'], 'translation', $translation['uri'], 'edit');
		$deleteLink = buildInternalLink($this->language, 'album', $album['uri'], 'song', $song['uri'], 'translation', $translation['uri'], 'delete');
		$reportLink = buildInternalLink($this->language, 'album', $album['uri'], 'song', $song['uri'], 'translation', $translation['uri'], 'report');
		
		[$isButtonEnabled, $tooltipIfDisabled] = canCurrentUserEditTranslation
		(
			$translation['user_added_id'],
			$translation['status'],
			is_null($song['original_song_id'])
		);
		
		$editButton = $this->createButton
		(
			\Localization\LyricsPage\EditTranslation,
			$editLink,
			$isButtonEnabled,
			$tooltipIfDisabled
		);
		
		$deleteButton = $this->createButton
		(
			\Localization\LyricsPage\DeleteTranslation,
			$deleteLink,
			$isButtonEnabled,
			$tooltipIfDisabled
		);
		
		[$isButtonEnabled, $tooltipIfDisabled] = canCurrentUserReportEntity($translation['status']);
	
		$reportButton = $this->createButton
		(
			\Localization\LyricsPage\ReportTranslation,
			$reportLink,
			$isButtonEnabled,
			$tooltipIfDisabled
		);
		
		$html =
		'
			<section>
				'.$editButton.'
				'.$deleteButton.'
				'.$reportButton.'
			</section>
		';
		
		return $html;
	}
	
	final protected function createMarkupLine
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
			function(array $x) use($noteName, $noteId, $noteClass)
			{
				return '<a href="#'.$noteName.$x[1].'" class="'.$noteClass.'" id="'.$noteId.$x[1].'">['.$x[1].']</a>';
			},
			
			'/{kj}(.+){fg}(.+){\/fg}/uU' =>
			function(array $x)
			{
				return '<ruby>'.$x[1].'<rt>'.$x[2].'</rt></ruby>';
			},
			
			'/{cl (#[0-9A-Fa-f]{6})}(.+){\/cl}/uU' =>
			function(array $x)
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
	
	private function createLyricsReferenceToAlbum(array $album): string
	{
		$href      = buildInternalLink($this->language, 'album', $album['uri']);
		$link      = $this->createLink($href, $album['transliterated_name']);
		$paragraph = '<p>'.\Localization\LyricsPage\Album.$link.'</p>';
		
		return $paragraph;
	}
	
	private function createLyricsReferenceToCurrentSong(array $song): string
	{
		$href      = buildInternalLink($this->language, 'album', $song['album_uri'], 'song', $song['uri']);
		$link      = $this->createLink($href, $song['transliterated_name']);
		$paragraph = '<p>'.\Localization\LyricsPage\ShowLyricsOnly.$link.'</p>';
		
		return $paragraph;
	}
	
	private function createLyricsReferenceToOriginalSong(array $originalSong): string
	{
		$href      = buildInternalLink($this->language, 'album', $originalSong['album_uri'], 'song', $originalSong['uri']);
		$link      = $this->createLink($href, $originalSong['transliterated_name']);
		$paragraph = '<p>'.\Localization\LyricsPage\OriginalSong.$link.'</p>';
		
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
			$language = \Localization\Functions\localizeLanguageName($translation);
			
			if (array_key_exists($language, $translationCount))
				$translationCount[$language]['totalCount']++;
			else
				$translationCount[$language] = ['currentCount' => 1, 'totalCount' => 1];
		}
		
		$links = [];
		
		foreach ($translations as $translation)
		{
			$language = \Localization\Functions\localizeLanguageName($translation);
			
			if ($translationCount[$language]['totalCount'] !== 1)
			{
				$number = \Localization\Functions\localizeTranslationNumber($translationCount[$language]['currentCount']);
				$language = $language.$number;
				
				$translationCount[$language]['currentCount']++;
			}
			
			if (!is_null($currentTranslation) && $translation['uri'] === $currentTranslation['uri'])
			{
				// If the current translation is shown, then no need to do a link to it
				$links[] = $language;
			}
			else
			{
				$href = buildInternalLink
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
		
		if (canCurrentUserPost() && is_null($song['original_song_id']))
		{
			$href = buildInternalLink
			(
				$this->language,
				'album',
				$song['album_uri'],
				'song',
				$song['uri'],
				'add-translation'
			);
			
			$links[] = $this->createLink($href, \Localization\LyricsPage\AddTranslation);
		}
		
		$links     = implode(\Localization\LyricsPage\ListElementSeparator, $links);
		$paragraph = '<p>'.\Localization\LyricsPage\TranslationList.$links.'</p>';
		
		return $paragraph;
	}
	
	private function createLyricsPerformerList(array $performers): string
	{
		$parts = [];
		
		for ($i = 0; $i < count($performers); $i++)
		{
			if (!is_null($performers[$i]['character_uri']))
			{
				$hrefCharacter = buildInternalLink($this->language, 'character', $performers[$i]['character_uri']);
				$linkCharacter = $this->createLink($hrefCharacter, $performers[$i]['character_transliterated_name']);
				
				$hrefArtist = buildInternalLink($this->language, 'artist', $performers[$i]['artist_uri']);
				$linkArtist = $this->createLink($hrefArtist, $performers[$i]['artist_transliterated_name']);
				
				$cvOpeningBracket = \Localization\LyricsPage\CvOpeningBracket;
				$cvClosingBracket = \Localization\LyricsPage\CvClosingBracket;
				
				$parts[] = $linkCharacter.$cvOpeningBracket.$linkArtist.$cvClosingBracket;
			}
			else
			{
				$hrefArtist = buildInternalLink($this->language, 'artist', $performers[$i]['artist_uri']);
				$linkArtist = $this->createLink($hrefArtist, $performers[$i]['artist_transliterated_name']);
				
				$parts[] = $linkArtist;
			}
		}
		
		$links     = implode(\Localization\LyricsPage\ListElementSeparator, $parts);
		$paragraph = '<p>'.\Localization\LyricsPage\PerformerList.$links.'</p>';
		
		return $paragraph;
	}
	
	final protected function createLyricsPageHeading
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
		$html = '<section class="main-entity">';
		$html .= $this->createAlbumImage($album);
		
		$html .= '<section>';
		$html .= $this->createHeading($headingText, 1);
		$html .= $this->createLyricsReferenceToAlbum($album);
		
		if (!is_null($translation))
			$html .= $this->createLyricsReferenceToCurrentSong($song);
		
		if (!is_null($performers))
			$html .= $this->createLyricsPerformerList($performers);
		
		if (!is_null($originalSong))
			$html .= $this->createLyricsReferenceToOriginalSong($originalSong);
		
		if (!is_null($translations))
			$html .= $this->createLyricsTranslationList($album, $song, $translation, $translations);
		
		$html .= '</section></section>';
		
		return $html;
	}
	
	private function createMarkupText
	(
		string $lyrics,
		string $noteFrom,
		string $noteTo,
		string $noteClass
	): string
	{
		$html = '';
		
		$lines = explode("\n", $lyrics);
		
		foreach ($lines as $line)
		{
			if ($line !== '')
				$line = '<span class="text-line">'.$this->createMarkupLine($line, $noteFrom, $noteTo, $noteClass).'</span>';
			else
				$line = '<br/>';
			
			$html .= $line;
		}
		
		return $html;
	}
	
	final protected function createSongLyrics(array $song): string
	{
		$html = '';
		
		$html .= '<section>';
		$html .= $this->createHeading($song['original_name'], 2);
		$html .= $this->createHeading(\Localization\Functions\localizeLanguageName($song), 3);
		$html .= $this->createMarkupText($song['lyrics'], 'lyrics-reference-', 'lyrics-note-', 'note-small');
		$html .= '</section>';
		
		return $html;
	}
	
	final protected function createSongNotes(array $song): string
	{
		if (is_null($song['notes']))
		{
			return
			'
			<section>
				'.$this->createHeading(\Localization\LyricsPage\LyricsNotes, 2).'
				<span>'.\Localization\LyricsPage\LyricsNoNotes.'</span>
			</section>
			';
		}
		
		$html = '';
		
		$html .= '<section>';
		$html .= $this->createHeading(\Localization\LyricsPage\LyricsNotes, 2);
		$html .= $this->createMarkupText($song['notes'], 'lyrics-note-', 'lyrics-reference-', 'note-big');
		$html .= '</section>';
		
		return $html;
	}
	
	final protected function createTranslationLyrics(array $translation): string
	{
		$html = '';
		
		$html .= '<section>';
		$html .= $this->createHeading($translation['name'], 2);
		$html .= $this->createHeading(\Localization\Functions\localizeLanguageName($translation), 3);
		$html .= $this->createMarkupText($translation['lyrics'], 'translation-reference-', 'translation-note-', 'note-small');
		$html .= '</section>';
		
		return $html;
	}
	
	final protected function createTranslationNotes(array $translation): string
	{
		if (is_null($translation['notes']))
		{
			return
			'
			<section>
				'.$this->createHeading(\Localization\LyricsPage\TranslationNotes, 2).'
				<span>'.\Localization\LyricsPage\TranslationNoNotes.'</span>
			</section>
			';
		}
		
		$html = '';
		
		$html .= '<section>';
		$html .= $this->createHeading(\Localization\LyricsPage\TranslationNotes, 2);
		$html .= $this->createMarkupText($translation['notes'], 'translation-note-', 'translation-reference-', 'note-big');
		$html .= '</section>';
		
		return $html;
	}
	
	final protected function createTimestampBlock(array $entity): string
	{
		if (isCurrentUserModerator())
			$statusRow = $this->createStatusSelect($entity);
		else
			$statusRow = $this->createStatus($entity['status']);
		
		return
		'
		<section>
			'.$this->createTimestampAdded($entity['timestamp_added'], $entity['user_added']).'
			'.$this->createTimestampUpdated($entity['timestamp_updated'], $entity['user_updated']).'
			'.$this->createTimestampReviewed($entity['timestamp_reviewed'], $entity['user_reviewed']).'
			'.$statusRow.'
		</section>
		';
	}
	
	final protected function createTranslationTimestamps(array $translation): string
	{
		return $this->createSongTimestamps($translation);
	}
	
	final protected function createSelect
	(
		string     $name,
		string     $class,
		bool       $isEnabled,
		bool       $isRequired,
		array      $entities,
		string     $keyToShow,
		string     $keyToSend,
		array|null $currentEntity
	): string
	{
		if (!$isEnabled)
			$disabled = 'disabled';
		else
			$disabled = '';
		
		if ($isRequired)
			$required = 'required';
		else
			$required = '';
		
		$html = '<select name="'.$name.'" class="'.$class.'" '.$disabled.' '.$required.'>';
		
		// empty option
		$html .= '<option></option>';
		
		foreach ($entities as $entity)
		{
			if ($currentEntity && $entity[$keyToSend] === $currentEntity[$keyToSend])
				$html .= '<option value="'.htmlspecialchars($entity[$keyToSend]).'" selected>'.htmlspecialchars($entity[$keyToShow]).'</option>';
			else
				$html .= '<option value="'.htmlspecialchars($entity[$keyToSend]).'">'.htmlspecialchars($entity[$keyToShow]).'</option>';
		}
		
		$html .= '</select><section class="select-fake-filler"></section>';
		
		return $html;
	}
	
	final protected function createDatalist(string $name, string $key, array $entities): string
	{
		$html = '<datalist id="'.$name.'">';
		
		foreach ($entities as $entity)
			$html .= '<option value="'.$entity[$key].'"></option>';
		
		$html .= '</datalist>';
		
		return $html;
	}
	
	final protected function createDatalistInput(string $name, string $listName, string|null $value): string
	{
		if (is_null($value))
			return '<input name="'.$name.'" list="'.$listName.'" />';
		
		return '<input name="'.$name.'" list="'.$listName.'" value="'.$value.'" />';
	}
	
	final protected function createAddRowButton(string $selectClass): string
	{
		return '<button type="button" class="'.$selectClass.' add-input-row">＋</button>';
	}
	
	final protected function createDeleteRowButton(string $class, bool $isEnabled): string
	{
		if ($isEnabled)
			return '<button type="button" class="'.$class.' delete-input-row">ー</button>';
		
		return '<button type="button" class="'.$class.' delete-input-row" disabled>ー</button>';
	}
	
	final protected function createTooltipWindow()
	{
		return
		'
		<aside>
			<section id="tooltip-window">
				<h3>'.\Localization\TooltipWindow\DefaultHeading.'</h3>
				<section>'.\Localization\TooltipWindow\DefaultContent.'</section>
			</section>
		</aside>
		';
	}
	
	final protected function createTooltipHeadingDatalist(string ...$headings): string
	{
		$html = '<datalist id="tooltip-headings">';
		foreach ($headings as $heading)
			$html .= '<option>'.$heading.'</option>';
		$html .= '</datalist>';
		
		return $html;
	}
	
	final protected function createTooltipContentDatalist(string ...$contents): string
	{
		$html = '<datalist id="tooltip-contents">';
		foreach ($contents as $content)
			$html .= '<option>'.$content.'</option>';
		$html .= '</datalist>';
		
		return $html;
	}
	
	//-----------------------//
	//      Error Pages      //
	//-----------------------//
	
	private function renderError(string $codename, string $reason, string $hint): void
	{
		echo $this->startRender
		(
			title:        $codename,
			cssSheetUris: ['/css/window-in-center.css']
		);
		
		echo
		'
		<article>
			<section>
				<h1>'.htmlspecialchars($codename).'</h1>
				<p>'.htmlspecialchars($reason).'</p>
				<p>'.htmlspecialchars($hint).'</p>
			</section>
		</article>
		';
		
		echo $this->endRender();
	}
	
	final public function renderBadRequest(): void
	{
		$this->renderError
		(
			'400 Bad Request',
			\Localization\ErrorPage\textBadRequest1,
			\Localization\ErrorPage\textBadRequest2
		);
	}
	
	final public function renderUnauthorized(): void
	{
		$this->renderError
		(
			'401 Unauthorized',
			\Localization\ErrorPage\textUnauthorized1,
			\Localization\ErrorPage\textUnauthorized2
		);
	}
	
	final public function renderPaymentRequired(): void
	{
		$this->renderError
		(
			'402 Payment Required',
			\Localization\ErrorPage\textPaymentRequired1,
			\Localization\ErrorPage\textPaymentRequired2
		);
	}
	
	final public function renderForbidden(): void
	{
		$this->renderError
		(
			'403 Forbidden',
			\Localization\ErrorPage\textForbidden1,
			\Localization\ErrorPage\textForbidden2
		);
	}
	
	final public function renderNotFound(): void
	{
		$this->renderError
		(
			'404 Not Found',
			\Localization\ErrorPage\textNotFound1,
			\Localization\ErrorPage\textNotFound2
		);
	}
	
	final public function renderMethodNotAllowed(): void
	{
		$this->renderError
		(
			'405 Method Not Allowed',
			\Localization\ErrorPage\textMethodNotAllowed1,
			\Localization\ErrorPage\textMethodNotAllowed2
		);
	}
	
	final public function renderNotAcceptable(): void
	{
		$this->renderError
		(
			'406 Not Acceptable',
			\Localization\ErrorPage\textNotAcceptable1,
			\Localization\ErrorPage\textNotAcceptable2
		);
	}
	
	final public function renderUnavailableForLegalReasons(): void
	{
		$this->renderError
		(
			'451 Unavailable For Legal Reasons',
			\Localization\ErrorPage\textUnavailableForLegalReasons1,
			\Localization\ErrorPage\textUnavailableForLegalReasons2
		);
	}
	
	final public function renderInternalServerError(): void
	{
		$this->renderError
		(
			'500 Internal Server Error',
			\Localization\ErrorPage\textInternalServerError1,
			\Localization\ErrorPage\textInternalServerError2
		);
	}
}
