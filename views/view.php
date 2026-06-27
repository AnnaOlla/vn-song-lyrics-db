<?php

abstract class View
{
	protected const ENTITY_LIST_DEFAULT_QUERY = 'limit=10&page=1';
	
	protected const ACCOUNT_DATA_MIN_LENGTH = 4;
	protected const ACCOUNT_DATA_MAX_LENGTH = 32;
	protected const FEEDBACK_MAX_LENGTH     = 500;
	protected const REPORT_MAX_LENGTH       = 500;
	
	protected $language;
	
	public function __construct(string $language)
	{
		$localizationFilePath = 'localization/'.$language.'-localization.php';
		
		if (!file_exists($localizationFilePath))
			throw new HttpNotAcceptable406();
		
		require_once $localizationFilePath;
		
		$this->language = $language;
	}
	
	final protected function getTimestamp(string $path)
	{
		/* The file should be delivered with the forwarding slash
		   to tell the browser that the path to the file starts at the root
		   
		   PHP fails to find the folder in this case,
		   so the slash must be stripped */
		
		return filemtime(mb_ltrim($path, '/'));
	}
	
	final protected function echoHtml(string|array $html): void
	{
		if (is_array($html))
		{
			foreach ($html as $part)
				$this->echoHtml($part);
		}
		else
			echo $html;
	}
	
	private function changeUriLocale(string $uri, string $newCode): string
	{
		$parts = explode('/', $uri, 3);
		$parts[1] = $newCode;
		return implode('/', $parts);
	}
	
	private function buildAttributes(array|null $attributes): string
	{
		if (is_null($attributes))
			return '';
		
		$parts = [];
		
		foreach ($attributes as $key => $value)
		{
			if ($value === false || is_null($value))
				continue;
			
			if (is_array($value))
				$value = implode(' ', $value);
			
			if ($value === true || $value === '')
				$parts[] = htmlspecialchars($key ?? '');
			else
				$parts[] = htmlspecialchars($key ?? '').'="'.htmlspecialchars($value ?? '').'"';
		}
		
		return implode(' ', $parts);
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
		$title          = htmlspecialchars($title.' | vn-song-lyrics-db');
		$description    = htmlspecialchars($description ?? 'The database of lyrics for songs introduced in visual novels.');
		
		$requestUri     = htmlspecialchars(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		$currentUrl     = htmlspecialchars('https://'.$_SERVER['HTTP_HOST'].$requestUri);
		$canonicalUrl   = htmlspecialchars('https://'.$_SERVER['HTTP_HOST'].($canonicalUri ?? $requestUri));
		$alternateRefEn = htmlspecialchars('https://'.$_SERVER['HTTP_HOST'].$this->changeUriLocale($requestUri, 'en'));
		$alternateRefRu = htmlspecialchars('https://'.$_SERVER['HTTP_HOST'].$this->changeUriLocale($requestUri, 'ru'));
		$alternateRefJa = htmlspecialchars('https://'.$_SERVER['HTTP_HOST'].$this->changeUriLocale($requestUri, 'ja'));
		
		$ogUrl          = htmlspecialchars($currentUrl);
		$ogImageUrl     = htmlspecialchars('https://'.$_SERVER['HTTP_HOST'].($ogImageUri ?? '/assets/static-images/wee-hagana-og.webp'));
		
		$html[] =
<<<HTML
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
	
	<link type="text/css" rel="stylesheet" href="/css/core/font-hanazono-mincho-type-a.css?v={$this->getTimestamp('/css/core/font-hanazono-mincho-type-a.css')}" />
	<link type="text/css" rel="stylesheet" href="/css/core/font-juliamo-ampleksa.css?v={$this->getTimestamp('/css/core/font-juliamo-ampleksa.css')}" />
	<link type="text/css" rel="stylesheet" href="/css/core/sizes.css?v={$this->getTimestamp('/css/core/sizes.css')}" />
	<link type="text/css" rel="stylesheet" href="/css/core/dark-theme.css?v={$this->getTimestamp('/css/core/dark-theme.css')}" />
	<link type="text/css" rel="stylesheet" href="/css/core/general.css?v={$this->getTimestamp('/css/core/general.css')}" />
	
	<link type="text/css" rel="stylesheet" href="/css/custom-inputs/button.css?v={$this->getTimestamp('/css/custom-inputs/button.css')}" />
	<link type="text/css" rel="stylesheet" href="/css/custom-inputs/checkbox.css?v={$this->getTimestamp('/css/custom-inputs/checkbox.css')}" />
	<link type="text/css" rel="stylesheet" href="/css/custom-inputs/fileupload.css?v={$this->getTimestamp('/css/custom-inputs/fileupload.css')}" />
	<link type="text/css" rel="stylesheet" href="/css/custom-inputs/searchable-select.css?v={$this->getTimestamp('/css/custom-inputs/searchable-select.css')}" />
	<link type="text/css" rel="stylesheet" href="/css/custom-inputs/select.css?v={$this->getTimestamp('/css/custom-inputs/select.css')}" />
	<link type="text/css" rel="stylesheet" href="/css/custom-inputs/textarea.css?v={$this->getTimestamp('/css/custom-inputs/textarea.css')}" />
	<link type="text/css" rel="stylesheet" href="/css/custom-inputs/textinput.css?v={$this->getTimestamp('/css/custom-inputs/textinput.css')}" />

HTML;
		
		foreach ($cssSheetUris as $cssSheetUri)
		{
			$html[] =
<<<HTML
	<link type="text/css" rel="stylesheet" href="{$cssSheetUri}?v={$this->getTimestamp($cssSheetUri)}"/>

HTML;
		}
		
        foreach ($jsScriptUris as $jsScriptUri)
		{
			$html[] =
<<<HTML
	<script src="{$jsScriptUri}?v={$this->getTimestamp($jsScriptUri)}"/></script>

HTML;
		}
		
        $html[] =
<<<HTML
</head>

HTML;
		
		return implode($html);
	}
	
	private function createHeader(): string
	{
		$enLink = $this->changeUriLocale($_SERVER['REQUEST_URI'], 'en');
		$ruLink = $this->changeUriLocale($_SERVER['REQUEST_URI'], 'ru');
		$jaLink = $this->changeUriLocale($_SERVER['REQUEST_URI'], 'ja');
		
		if (Session::agentIsVisitor())
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
			$username = '<a href="/'.$this->language.'/user/'.htmlspecialchars($_SESSION['user']['uri']).'">'.htmlspecialchars($_SESSION['user']['username']).'</a>';
		}
		
		return
		'
	<header>
		<nav>
			<section class="home-page">
				<a href="/'.$this->language.'">'.\Localization\Header\HomePage.'</a>
			</section>
			<section class="main-pages">
				<a href="/'.$this->language.'/game-list?limit=10&page=1">'.\Localization\Header\GameList.'</a>
				<a href="/'.$this->language.'/album-list?limit=10&page=1">'.\Localization\Header\AlbumList.'</a>
				<a href="/'.$this->language.'/artist-list?limit=10&page=1">'.\Localization\Header\ArtistList.'</a>
				<a href="/'.$this->language.'/character-list?limit=10&page=1">'.\Localization\Header\CharacterList.'</a>
				<a href="/'.$this->language.'/song-list?limit=10&page=1">'.\Localization\Header\SongList.'</a>
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
		return
<<<HTML
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
	
	final protected function endRender(array $jsScriptUris = []): string
	{
		$html[] =
<<<HTML

	</main>
	{$this->createFooter()}
	
	<script src="/js/core/emulate-event.js?v={$this->getTimestamp('/js/core/emulate-event.js')}"/></script>
	<script src="/js/core/prepare-entity-name-for-filtering.js?v={$this->getTimestamp('/js/core/prepare-entity-name-for-filtering.js')}"/></script>
	
	<script src="/js/custom-inputs/captcha-input.js?v={$this->getTimestamp('/js/custom-inputs/captcha-input.js')}"/></script>
	<script src="/js/custom-inputs/checkbox.js?v={$this->getTimestamp('/js/custom-inputs/checkbox.js')}"/></script>
	<script src="/js/custom-inputs/fileupload.js?v={$this->getTimestamp('/js/custom-inputs/fileupload.js')}"/></script>
	<script src="/js/custom-inputs/searchable-select.js?v={$this->getTimestamp('/js/custom-inputs/searchable-select.js')}"/></script>
	<script src="/js/custom-inputs/select.js?v={$this->getTimestamp('/js/custom-inputs/select.js')}"/></script>
	<script src="/js/custom-inputs/textarea.js?v={$this->getTimestamp('/js/custom-inputs/textarea.js')}"/></script>

HTML;
		
        foreach ($jsScriptUris as $jsScriptUri)
		{
			$html[] =
<<<HTML
	<script src="{$jsScriptUri}?v={$this->getTimestamp($jsScriptUri)}"/></script>

HTML;
		}
		
		$html[] =
<<<HTML
</body>
</html>
HTML;
		
		return implode($html);
	}
	
	//-----------------------------------------------------------//
	//      Content Pages: Small Blocks To Build Pages With      //
	//-----------------------------------------------------------//
	
	final protected function createLink
	(
		string     $href,
		string     $content,
		array|null $attributes = null
	): string
	{
		$attributes['href'] = $href;
		
		return '<a '.$this->buildAttributes($attributes).'>'.htmlspecialchars($content).'</a>';
	}
	
	final protected function createVndbLinkParagraph
	(
		string $contentBefore,
		array  $entity,
		string $entityFirstLetter
	): string
	{
		if (is_null($entity['vndb_id']))
			return '';
		
		$href = htmlspecialchars('https://vndb.org/'.$entityFirstLetter.$entity['vndb_id']);
		$link = '<a href="'.$href.'" target="_blank">'.$href.'</a>';
		
		return '<p>'.htmlspecialchars($contentBefore).$link.'</p>';
	}
	
	final protected function createVgmdbLinkParagraph
	(
		string $contentBefore,
		array  $entity,
		string $entityName
	): string
	{
		if (is_null($entity['vgmdb_id']))
			return '';
		
		$href = htmlspecialchars('https://vgmdb.net/'.$entityName.'/'.$entity['vgmdb_id']);
		$link = '<a href="'.$href.'" target="_blank">'.$href.'</a>';
		
		return '<p>'.htmlspecialchars($contentBefore).$link.'</p>';
	}
	
	final protected function createHeading
	(
		string     $heading,
		int        $level,
		array|null $attributes = null
	): string
	{
		return '<h'.$level.' '.$this->buildAttributes($attributes).'>'.htmlspecialchars($heading).'</h'.$level.'>';
	}
	
	final protected function createHeadingAsLink
	(
		string     $heading,
		int        $level,
		string     $href,
		array|null $attributes = null
	): string
	{
		return '<h'.$level.' '.$this->buildAttributes($attributes).'>'.$this->createLink($href, $heading, $attributes).'</h'.$level.'>';
	}
	
	final protected function createHeadingForInput
	(
		string     $string,
		int        $level,
		bool       $isRequired,
		array|null $attributes = null
	): string
	{
		if ($isRequired)
			return '<h'.$level.' '.$this->buildAttributes($attributes).'>'.htmlspecialchars($string).'<span class="required-input"> *</span></h'.$level.'>';
		else
			return '<h'.$level.' '.$this->buildAttributes($attributes).'>'.htmlspecialchars($string).'</h'.$level.'>';
	}
	
	final protected function createParagraph
	(
		string|null $string,
		array|null  $attributes = null
	): string
	{
		return '<p '.$this->buildAttributes($attributes).'>'.htmlspecialchars($string ?? '').'</p>';
	}
	
	final protected function createParagraphAsLink
	(
		string     $string,
		string     $href,
		array|null $attributes = null
	): string
	{
		return '<p '.$this->buildAttributes($attributes).'>'.$this->createLink($href, $string).'</p>';
	}
	
	private function createTimestamp
	(
		string|null $timestamp,
		string|null $username,
		string|null $uri,
		string      $status
	): string
	{
		if (is_null($timestamp))
			return '';
		
		if (is_null($username))
			$username = \Localization\TimestampString\DeletedUser;
		else
			$username = $this->createLink(Http::buildInternalPath($this->language, 'user', $uri), $username);
		
		return '<p>'.htmlspecialchars($status.\Localization\TimestampString\Delimeter.$timestamp.\Localization\TimestampString\By).$username.'</p>';
	}
	
	final protected function createTimestampAdded
	(
		string|null $timestamp,
		string|null $username,
		string|null $uri
	): string
	{
		return $this->createTimestamp($timestamp, $username, $uri, \Localization\TimestampString\Added);
	}
	
	final protected function createTimestampUpdated
	(
		string|null $timestamp,
		string|null $username,
		string|null $uri
	): string
	{
		return $this->createTimestamp($timestamp, $username, $uri, \Localization\TimestampString\Updated);
	}
	
	final protected function createTimestampReviewed
	(
		string|null $timestamp,
		string|null $username,
		string|null $uri
	): string
	{
		return $this->createTimestamp($timestamp, $username, $uri, \Localization\TimestampString\Reviewed);
	}
	
	final protected function createStatus
	(
		string $status,
		bool   $isRelation = false
	): string
	{
		switch ($status)
		{
			case 'unchecked':
				$status = \Localization\ModerationStatus\Unchecked;
				break;
			
			case 'checked':
				$status = \Localization\ModerationStatus\Checked;
				break;
			
			case 'hidden':
				$status = \Localization\ModerationStatus\Hidden;
				break;
				
			default:
				throw new HttpInternalServerError500(__METHOD__.' received unknown status');
		}
		
		if ($isRelation)
			$prefix = \Localization\ModerationStatus\RelationStatus;
		else
			$prefix = \Localization\ModerationStatus\Status;
		
		return $this->createParagraph($prefix.$status);
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
			$dataEntityUri    = $relationHref;
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
			['toSend' => 'unchecked', 'toShow' => \Localization\ModerationStatus\Unchecked],
			['toSend' => 'checked',   'toShow' => \Localization\ModerationStatus\Checked],
			['toSend' => 'hidden',    'toShow' => \Localization\ModerationStatus\Hidden]
		];
		
		$currentStatusIndex = array_search($entity[$keyToStatus], array_column($statuses, 'toSend'));
		$currentStatus      = $statuses[$currentStatusIndex];
		
		return
		'
			<section>
				<p>'.$prefix.'</p>
				'.$select = $this->createSearchableSelect
				(
					iteratedOptions:         $statuses,
					selectedOption:          $currentStatus,
					addEmptyOption:          false,
					keyToShownValue:         'toShow',
					keyToSentValue:          'toSend',
					attributesForSentInput:  ['class' => ['status-select'], 'data-entity-uri' => $dataEntityUri],
					attributesForShownInput: ['readonly' => true]
				).
			'</section>
		';
	}
	
	final protected function createButtonAsLink
	(
		string|null $label      = null,
		array|null  $attributes = null
	): string
	{
		$attributes['class'][] = 'custom-button';
		
		return '<a '.$this->buildAttributes($attributes).'>'.htmlspecialchars($label ?? '').'</a>';
	}
	
	final protected function createButtonAsRestrictedLink
	(
		string|null $label      = null,
		AccessState $access     = AccessState::Ok,
		array|null  $attributes = null
	): string
	{
		if ($access !== AccessState::Ok)
		{
			if (isset($attributes['href']))
				unset($attributes['href']);
			
			$attributes['class'][] = 'disabled';
			$attributes['title']   = \Localization\Functions\localizeAccessState($access);
		}
		
		return $this->createButtonAsLink($label, $attributes);
	}
	
	final protected function createImage
	(
		string $src,
		string $alt = ""
	): string
	{
		return '<img src="'.htmlspecialchars($src).'?v='.$this->getTimestamp($src).'" alt="'.htmlspecialchars($alt).'"/>';
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
			$source = Http::buildInternalPath
			(
				AssetFolder::Base->value,
				$dynamicAssetFolder->value,
				$dynamicAssetFilename.'.webp'
			);
			return $this->createImage($source, $dynamicAssetAlternativeText);
		}
		else
		{
			$source = Http::buildInternalPath
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
	
	final protected function createInfoBlockWithImage
	(
		string $htmlImage,
		array  $htmlValues,
		string $entityClass
	): string
	{
		$html[] =
		'
			<section class="'.$entityClass.'">
				'.$htmlImage.'
				<section>
		';
		
		foreach ($htmlValues as $htmlValue)
			$html[] = $htmlValue;
		
		$html[] =
		'
				</section>
			</section>
		';
		
		return implode($html);
	}
	
	final protected function createEntityControlBlock
	(
		array  $entities,
		array  $entitiesNames,
		array  $currentEntity,
		string $currentEntityName,
		string $editEntityPathPart   = 'edit',
		string $deleteEntityPathPart = 'delete',
		string $reportEntityPathPart = 'report'
	): string
	{
		$entityCount     = count($entities);
		$entityNameCount = count($entitiesNames);
		
		if ($entityCount !== $entityNameCount || $entityCount === 0 || $entityNameCount === 0)
			throw new HttpInternalServerError500(__METHOD__.' was called incorrectly', get_defined_vars());
		
		for ($i = 0; $i < $entityCount; $i++)
		{
			$path[] = $entitiesNames[$i];
			$path[] = $entities[$i]['uri'];
		}
		
		$editHref   = Http::buildInternalPath($this->language, ...$path, ...[$editEntityPathPart]);
		$deleteHref = Http::buildInternalPath($this->language, ...$path, ...[$deleteEntityPathPart]);
		$reportHref = Http::buildInternalPath($this->language, ...$path, ...[$reportEntityPathPart]);
		
		$editAttributes   = ['href' => $editHref];
		$deleteAttributes = ['href' => $deleteHref];
		$reportAttributes = ['href' => $reportHref];
		
		$editAccess   = ('Session::agentHasRightToEdit'  .$currentEntityName)($currentEntity);
		$deleteAccess = ('Session::agentHasRightToDelete'.$currentEntityName)($currentEntity);
		$reportAccess = ('Session::agentHasRightToReport'.$currentEntityName)($currentEntity);
		
		$editButton   = $this->createButtonAsRestrictedLink(\Localization\Controls\Edit,   $editAccess,   $editAttributes);
		$deleteButton = $this->createButtonAsRestrictedLink(\Localization\Controls\Delete, $deleteAccess, $deleteAttributes);
		$reportButton = $this->createButtonAsRestrictedLink(\Localization\Controls\Report, $reportAccess, $reportAttributes);
		
		return
		'
			<section>
				'.$editButton.'
				'.$deleteButton.'
				'.$reportButton.'
			</section>
		';
	}
	
	private function createLyricsReferenceToAlbum(array $album): string
	{
		$href      = Http::buildInternalPath($this->language, 'album', $album['uri']);
		$link      = $this->createLink($href, $album['transliterated_name']);
		$paragraph = '<p>'.\Localization\LyricsPage\Album.$link.'</p>';
		
		return $paragraph;
	}
	
	private function createLyricsReferenceToCurrentSong(array $song): string
	{
		$href      = Http::buildInternalPath($this->language, 'album', $song['album_uri'], 'song', $song['uri']);
		$link      = $this->createLink($href, $song['transliterated_name']);
		$paragraph = '<p>'.\Localization\LyricsPage\ShowLyricsOnly.$link.'</p>';
		
		return $paragraph;
	}
	
	private function createLyricsReferenceToOriginalSong(array $originalSong): string
	{
		$href      = Http::buildInternalPath($this->language, 'album', $originalSong['album_uri'], 'song', $originalSong['uri']);
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
				$translationCount[$language] = ['currentCount' => 0, 'totalCount' => 1];
		}
		
		$links = [];
		
		foreach ($translations as $translation)
		{
			$language = \Localization\Functions\localizeLanguageName($translation);
			
			if ($translationCount[$language]['totalCount'] !== 1)
			{
				$translationCount[$language]['currentCount']++;
				
				$number = \Localization\Functions\localizeTranslationNumber($translationCount[$language]['currentCount']);
				$language = $language.$number;
			}
			
			if (!is_null($currentTranslation) && $translation['uri'] === $currentTranslation['uri'])
			{
				// If the current translation is shown, then no need to do a link to it
				$links[] = $language;
			}
			else
			{
				$href = Http::buildInternalPath
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
		
		if ((Session::agentIsUser() || Session::agentIsAdministrator()) && is_null($song['original_song_id']))
		{
			$href = Http::buildInternalPath
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
				$hrefCharacter = Http::buildInternalPath($this->language, 'character', $performers[$i]['character_uri']);
				$linkCharacter = $this->createLink($hrefCharacter, $performers[$i]['character_transliterated_name']);
				
				$hrefArtist = Http::buildInternalPath($this->language, 'artist', $performers[$i]['artist_uri']);
				$linkArtist = $this->createLink($hrefArtist, $performers[$i]['artist_transliterated_name']);
				
				$cvOpeningBracket = \Localization\LyricsPage\CvOpeningBracket;
				$cvClosingBracket = \Localization\LyricsPage\CvClosingBracket;
				
				$parts[] = $linkCharacter.$cvOpeningBracket.$linkArtist.$cvClosingBracket;
			}
			else
			{
				$hrefArtist = Http::buildInternalPath($this->language, 'artist', $performers[$i]['artist_uri']);
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
		$html[] = '<section class="main-entity">';
		$html[] = $this->createAlbumImage($album);
		
		$html[] = '<section>';
		$html[] = $this->createHeading($headingText, 1);
		$html[] = $this->createLyricsReferenceToAlbum($album);
		
		if (!is_null($translation))
			$html[] = $this->createLyricsReferenceToCurrentSong($song);
		
		if (!is_null($performers))
			$html[] = $this->createLyricsPerformerList($performers);
		
		if (!is_null($originalSong))
			$html[] = $this->createLyricsReferenceToOriginalSong($originalSong);
		
		if (!is_null($translations))
			$html[] = $this->createLyricsTranslationList($album, $song, $translation, $translations);
		
		$html[] = '</section></section>';
		
		return implode($html);
	}
	
	final protected function createLyricsControls(): string
	{
		return
		'
		<section class="lyrics-controls">
			<section>
				<section>
					<span>'.\Localization\LyricsPage\PageSettingsFontSize.'</span>
				</section>
					<section>
					'.$this->createSearchableSelect
					(
						iteratedOptions:         [
											          [0 => \Localization\LyricsPage\PageSettingsFontSize1, 1 => -4],
											          [0 => \Localization\LyricsPage\PageSettingsFontSize2, 1 => -2],
											          [0 => \Localization\LyricsPage\PageSettingsFontSize3, 1 =>  0],
											          [0 => \Localization\LyricsPage\PageSettingsFontSize4, 1 => +2],
											          [0 => \Localization\LyricsPage\PageSettingsFontSize5, 1 => +4]
										         ],
						selectedOption:          [0 => \Localization\LyricsPage\PageSettingsFontSize3, 1 => 0],
						addEmptyOption:          false,
						keyToShownValue:         0,
						keyToSentValue:          1,
						attributesForSentInput:  ['id' => 'font-size-select'],
						attributesForShownInput: [
						                             'readonly' => true,
													 'data-placeholder-filter' => \Localization\Controls\SelectOption
												 ]
					).'
				</section>
			</section>
			<section>
				<section>
					<span>'.\Localization\LyricsPage\PageSettingsFurigana.'</span>
				</section>
				<section>
					'.$this->createSearchableSelect
					(
						iteratedOptions:         [
											          [0 => \Localization\LyricsPage\PageSettingsShowFurigana, 1 => 1],
											          [0 => \Localization\LyricsPage\PageSettingsHideFurigana, 1 => 0]
										         ],
						selectedOption:          [0 => \Localization\LyricsPage\PageSettingsShowFurigana, 1 => 1],
						addEmptyOption:          false,
						keyToShownValue:         0,
						keyToSentValue:          1,
						attributesForSentInput:  ['id' => 'show-furigana-select'],
						attributesForShownInput: [
						                              'readonly' => true,
													  'data-placeholder-filter' => \Localization\Controls\SelectOption
												 ]
					).'
				</section>
			</section>
			<section>
				<section>
					<span>'.\Localization\LyricsPage\PageSettingsNotes.'</span>
				</section>
				<section>
					'.$this->createSearchableSelect
					(
						iteratedOptions:         [
											          [0 => \Localization\LyricsPage\PageSettingsShowNotes, 1 => 1],
											          [0 => \Localization\LyricsPage\PageSettingsHideNotes, 1 => 0]
										         ],
						selectedOption:          [0 => \Localization\LyricsPage\PageSettingsShowNotes, 1 => 1],
						addEmptyOption:          false,
						keyToShownValue:         0,
						keyToSentValue:          1,
						attributesForSentInput:  ['id' => 'show-notes-select'],
						attributesForShownInput: [
						                              'readonly' => true,
													  'data-placeholder-filter' => \Localization\Controls\SelectOption
												 ]
					).'
				</section>
			</section>
			<section>
				<section>
					<span>'.\Localization\LyricsPage\PageSettingsColors.'</span>
				</section>
				<section>
					'.$this->createSearchableSelect
					(
						iteratedOptions:         [
											         [0 => \Localization\LyricsPage\PageSettingsShowColors, 1 => 1],
											         [0 => \Localization\LyricsPage\PageSettingsHideColors, 1 => 0]
										         ],
						selectedOption:          [0 => \Localization\LyricsPage\PageSettingsShowColors, 1 => 1],
						addEmptyOption:          false,
						keyToShownValue:         0,
						keyToSentValue:          1,
						attributesForSentInput:  ['id' => 'show-colors-select'],
					    attributesForShownInput: [
						                             'readonly' => true,
													 'data-placeholder-filter' => \Localization\Controls\SelectOption
												 ]
					).'
				</section>
			</section>
		</section>
		';
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
	
	final protected function createSongLyrics(array $song): string
	{
		return
		'
			<section>
				'.$this->createHeading($song['original_name'], 2).'
				'.$this->createHeading(\Localization\Functions\localizeLanguageName($song), 3).'
				'.$this->createMarkupText($song['lyrics'], 'lyrics-reference-', 'lyrics-note-', 'note-small').'
			</section>
		';
	}
	
	final protected function createSongNotes(array $song): string
	{
		return
		'
			<section>
				'.$this->createHeading(\Localization\LyricsPage\LyricsNotes, 2).'
				'.$this->createMarkupText($song['notes'] ?? \Localization\LyricsPage\LyricsNoNotes, 'lyrics-note-', 'lyrics-reference-', 'note-big').'
			</section>
		';
	}
	
	final protected function createTranslationLyrics(array $translation): string
	{
		return
		'
			<section>
				'.$this->createHeading($translation['name'], 2).'
				'.$this->createHeading(\Localization\Functions\localizeLanguageName($translation), 3).'
				'.$this->createMarkupText($translation['lyrics'], 'translation-reference-', 'translation-note-', 'note-small').'
			</section>
		';
	}
	
	final protected function createTranslationNotes(array $translation): string
	{
		return
		'
			<section>
				'.$this->createHeading(\Localization\LyricsPage\TranslationNotes, 2).'
				'.$this->createMarkupText($translation['notes'] ?? \Localization\LyricsPage\TranslationNoNotes, 'translation-note-', 'translation-reference-', 'note-big').'
			</section>
		';
	}
	
	final protected function createTimestampBlock(array $entity): string
	{
		if (Session::agentIsAdministrator())
			$statusRow = $this->createStatusSelect($entity);
		else
			$statusRow = $this->createStatus($entity['status']);
		
		return
		'
		<section>
			'.$this->createTimestampAdded($entity['timestamp_added'], $entity['user_username_added'], $entity['user_uri_added']).'
			'.$this->createTimestampUpdated($entity['timestamp_updated'], $entity['user_username_updated'], $entity['user_uri_updated']).'
			'.$this->createTimestampReviewed($entity['timestamp_reviewed'], $entity['user_username_reviewed'], $entity['user_uri_reviewed']).'
			'.$statusRow.'
		</section>
		';
	}
	
	final protected function createCheckbox
	(
		string|null $label         = null,
		bool        $isLabelBefore = true,
		array|null  $attributes    = null,
	): string
	{
		$attributes['type']     = 'checkbox';
		$attributes['class'][]  = 'custom-checkbox-input';
		
		if (!Validation::isNullOrEmpty($label))
			$span = '<span class="custom-checkbox-label">'.htmlspecialchars($label ?? '').'</span>';
		else
			$span = '';
		
		$input = '<span class="custom-checkbox-button">　</span><input '.$this->buildAttributes($attributes).' />';
		
		if ($isLabelBefore)
			return '<label class="custom-checkbox">'.$span.$input.'</label>';
		else
			return '<label class="custom-checkbox">'.$input.$span.'</label>';
	}
	
	final protected function createFilterBar(): string
	{
		$attributes =
		[
			'class'       => 'custom-input',
			'type'        => 'search',
			'id'          => 'filter-bar',
			'placeholder' => \Localization\Controls\FilterPage
		];
		
		return '<input '.$this->buildAttributes($attributes).' />';
	}
	
	final protected function createFileupload(array|null $attributes = null): string
	{
		$attributes['type']     = 'file';
		$attributes['class'][]  = 'custom-fileupload-input';
		$attributes['tabindex'] = '-1';
		
		$attributes2 =
		[
			'class'                  => 'custom-fileupload-button',
			'text-file-not-selected' => \Localization\Controls\ChooseFile,
			'text-file-too-big'      => \Localization\Controls\FileTooBig
		];
		
		return
		'
		<label class="custom-fileupload">
			<input '.$this->buildAttributes($attributes).' />
			<section '.$this->buildAttributes($attributes2).' tabindex="0">'.\Localization\Controls\ChooseFile.'</section>
		</label>
		';
	}
	
	final protected function createSelect
	(
		array|null $iteratedOptions = null,
		array|null $disabledOptions = null,
		array|null $selectedOption  = null,
		bool       $addEmptyOption  = true,
		string     $keyToShownValue = '',
		string     $keyToSentValue  = '',
		array|null $attributes      = null
	): string
	{
		if (is_null($iteratedOptions))
			$iteratedOptions = [];
		
		if (is_null($disabledOptions))
			$disabledOptions = [];
		
		$attributes['class'][] = 'custom-select';
		
		$html[] = '<select '.$this->buildAttributes($attributes).'>';
		
		$iterated = array_column($iteratedOptions, $keyToSentValue, $keyToShownValue);
		$disabled = array_column($disabledOptions, $keyToSentValue);
		$selected = is_null($selectedOption) ? null : $selectedOption[$keyToSentValue];
		
		if ($addEmptyOption)
			$iterated = ['' => ''] + $iterated;
		
		foreach ($iterated as $shown => $sent)
		{
			$attributes = [];
			
			if (in_array($sent, $disabled, true))
				$attributes['disabled'] = true;
			
			if ($sent === $selected)
				$attributes['selected'] = true;
			
			$attributes['value'] = $sent;
			
			$html[] = '<option class="custom-select-option" '.$this->buildAttributes($attributes).'>'.htmlspecialchars($shown ?? '').'</option>';
		}
		
		$html[] = '</select><section class="custom-select-filler"></section>';
		
		return implode($html);
	}
	
	final protected function createSearchableSelect
	(
		array|null  $iteratedOptions         = null,
		array|null  $disabledOptions         = null,
		array|null  $selectedOption          = null,
		bool        $addEmptyOption          = true,
		string      $keyToShownValue         = 'description',
		array|null  $keysToShownHints        = null,
		string      $keyToSentValue          = 'id',
		array|null  $attributesForShownInput = [
		                                           'placeholder'             => \Localization\Controls\SelectOption,
												   'data-placeholder-select' => \Localization\Controls\SelectOption,
												   'data-placeholder-filter' => \Localization\Controls\SelectFilter
											   ],
		array|null  $attributesForSentInput  = null
	): string
	{
		if (is_null($iteratedOptions))
			$iteratedOptions = [];
		
		if (is_null($disabledOptions))
			$disabledOptions = [];
		
		if (is_null($selectedOption))
		{
			$selectedOption = [];
			$selectedOption[$keyToShownValue] = '';
			$selectedOption[$keyToSentValue] = '';
		}
		
		if (is_null($keysToShownHints))
			$keysToShownHints = [];
		
		if ($addEmptyOption)
		{
			$emptyOption = [];
			$emptyOption[$keyToShownValue] = '';
			$emptyOption[$keyToSentValue] = '';
			
			foreach ($keysToShownHints as $keyToShownHint)
				$emptyOption[$keyToShownHint] = '';
			
			array_unshift($iteratedOptions, $emptyOption);
		}
		
		$attributesForShownInput['class'][]  = 'custom-searchable-select-shown-value';
		$attributesForShownInput['type']     = 'text';
		$attributesForShownInput['value']    = $selectedOption[$keyToShownValue];
		
		$attributesForSentInput['class'][]  = 'custom-searchable-select-sent-value';
		$attributesForSentInput['type']     = 'text';
		$attributesForSentInput['value']    = $selectedOption[$keyToSentValue];
		$attributesForSentInput['tabindex'] = '-1';
		
		$html[] = 
		'
			<label class="custom-searchable-select">
				<input '.$this->buildAttributes($attributesForSentInput).' />
				<input '.$this->buildAttributes($attributesForShownInput).' />
				<ul class="custom-searchable-select-option-container" tabindex="-1">
		';
		
		foreach ($iteratedOptions as $iteratedOption)
		{
			$disabled = in_array($iteratedOption, $disabledOptions) ? 'disabled' : '';
			
			$html[] =
			'
					<li class="custom-searchable-select-option" value="'.htmlspecialchars($iteratedOption[$keyToSentValue] ?? '').'" '.$disabled.' tabindex="-1">
						<section class="custom-searchable-select-option-shown-value">'.htmlspecialchars($iteratedOption[$keyToShownValue] ?? '').'</section>
			';
			
			foreach ($keysToShownHints as $keyToShownHint)
			{
				$html[] =
				'
						<section class="custom-searchable-select-option-shown-hint">'.htmlspecialchars($iteratedOption[$keyToShownHint] ?? '').'</section>
				';
			}
			
			$html[] =
			'
					</li>
			';
		}
		
		$html[] = 
		'
				</ul>
			</label>
		';
		
		return implode($html);
	}
	
	final protected function createButton
	(
		string|null $label      = null,
		array|null  $attributes = null
	): string
	{
		$attributes['type']    = 'button';
		$attributes['class'][] = 'custom-button';
		
		return '<button '.$this->buildAttributes($attributes).'>'.htmlspecialchars($label).'</button>';
	}
	
	final protected function createAddRowButton
	(
		array|null $attributes = null
	): string
	{
		$attributes['class'][] = 'add-input-row';
		
		return $this->createButton('＋', $attributes);
	}
	
	final protected function createDeleteRowButton
	(
		array|null $attributes = null
	): string
	{
		$attributes['class'][] = 'delete-input-row';
		
		return $this->createButton('ー', $attributes);
	}
	
	final protected function createReturnButton
	(
		string|null $fallbackHref = null,
		string      $label        = \Localization\Controls\Cancel,
		array|null  $attributes   = null
	): string
	{
		$attributes['href']    = Http::getLastVisitedPage($fallbackHref);
		$attributes['class'][] = 'custom-button';
		
		return $this->createButtonAsLink($label, $attributes);
	}
	
	final protected function createSubmitButton
	(
		string     $label      = \localization\Controls\Submit,
		array|null $attributes = null
	): string
	{
		$attributes['type']    = 'submit';
		$attributes['value']   = $label;
		$attributes['class'][] = 'custom-button';
		
		return '<input '.$this->buildAttributes($attributes).'/>';
	}
	
	final protected function createHiddenInput
	(
		array|null $attributes = null
	): string
	{
		$attributes['type'] = 'hidden';
		
		return '<input '.$this->buildAttributes($attributes).'/>';
	}
	
	final protected function createTextarea
	(
		string|null $value      = null,
		array|null  $attributes = null
	): string
	{
		$attributes['class'][] = 'custom-textarea';
		
		return '<textarea '.$this->buildAttributes($attributes).'>'.htmlspecialchars($value ?? '').'</textarea>';
	}
	
	final protected function createCaptchaInput
	(
		array|null $attributes = null
	): string
	{
		$attributes['type']        = 'text';
		$attributes['name'][]      = 'captcha-code';
		$attributes['class'][]     = 'custom-input';
		$attributes['class'][]     = 'captcha-input';
		$attributes['placeholder'] = 'code:';
		
		return '<input '.$this->buildAttributes($attributes).' />';
	}
	
	final protected function createCaptchaImage
	(
		string|null $base64,
		array|null  $attributes = null
	): string
	{
		$attributes['src']     = htmlspecialchars($base64 ?? '');
		$attributes['alt']     = 'captcha';
		$attributes['class'][] = 'captcha-image';
		
		return '<img '.$this->buildAttributes($attributes).' />';
	}
	
	final protected function createTextInput
	(
		array|null $attributes = null
	): string
	{
		$attributes['type']    = 'text';
		$attributes['class'][] = 'custom-input';
		
		return '<input '.$this->buildAttributes($attributes).' />';
	}
	
	final protected function createUsernameInput
	(
		array|null $attributes = null
	): string
	{
		$attributes['type']      = 'text';
		$attributes['pattern']   = '[a-zA-Z0-9]+';
		$attributes['minlength'] = self::ACCOUNT_DATA_MIN_LENGTH;
		$attributes['maxlength'] = self::ACCOUNT_DATA_MAX_LENGTH;
		$attributes['class'][]   = 'custom-input';
		
		return '<input '.$this->buildAttributes($attributes).' />';
	}
	
	final protected function createPasswordInput
	(
		array|null $attributes = null
	): string
	{
		$attributes['type']      = 'password';
		$attributes['pattern']   = '[a-zA-Z0-9]+';
		$attributes['minlength'] = self::ACCOUNT_DATA_MIN_LENGTH;
		$attributes['maxlength'] = self::ACCOUNT_DATA_MAX_LENGTH;
		$attributes['class'][]   = 'custom-input';
		
		return '<input '.$this->buildAttributes($attributes).' />';
	}
	
	final protected function createEmailInput
	(
		array|null $attributes = null
	): string
	{
		$attributes['type']      = 'email';
		$attributes['minlength'] = self::ACCOUNT_DATA_MIN_LENGTH;
		$attributes['maxlength'] = self::ACCOUNT_DATA_MAX_LENGTH;
		$attributes['class'][]   = 'custom-input';
		
		return '<input '.$this->buildAttributes($attributes).' />';
	}
	
	final protected function createUrlInput
	(
		array|null $attributes = null
	): string
	{
		$attributes['type']    = 'url';
		$attributes['class'][] = 'custom-input';
		
		return '<input '.$this->buildAttributes($attributes).' />';
	}
	
	final protected function createFillerSection(): string
	{
		return '<section class="filler"></section>';
	}
	
	final protected function createTooltipWindow(): string
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
	
	final protected function createDatalist
	(
		array|null $options    = null,
		array|null $attributes = null
	): string
	{
		$html[] = '<datalist '.$this->buildAttributes($attributes).'>';
		
		foreach ($options as $option)
			$html[] = '<option>'.$option.'</option>';
		
		$html[] = '</datalist>';
		
		return implode($html);
	}
	
	//---------------------------------------//
	//       Content Pages: Pagination       //
	//---------------------------------------//
	
	final protected function createResultsLimitBlock(int|null $limit): string
	{
		if (is_null($limit))
			$selectedOption = ['toShow' => \Localization\Controls\NoLimit, 'toSend' => null];
		else
			$selectedOption = ['toShow' => $limit, 'toSend' => $limit];
		
		$options =
		[
			['toShow' => 10,  'toSend' => 10],
			['toShow' => 25,  'toSend' => 25],
			['toShow' => 50,  'toSend' => 50],
			['toShow' => 100, 'toSend' => 100],
			['toShow' => \Localization\Controls\NoLimit, 'toSend' => null]
		];
		
		// sort nulls last
		if (!in_array($selectedOption, $options) && !is_null($limit))
		{
			$options[] = $selectedOption;
			usort
			(
				$options,
				function($a, $b)
				{
					if ($a === null) return +1;
					if ($b === null) return -1;
					return $a <=> $b;
				}
			);
		}
		
		$select = $this->createSearchableSelect
		(
			iteratedOptions:         $options,
			selectedOption:          $selectedOption,
			addEmptyOption:          false,
			keyToShownValue:         'toShow',
			keyToSentValue:          'toSend',
			attributesForSentInput:  ['id' => 'limit-result-count-bar'],
			attributesForShownInput: [
										 'data-placeholder-filter' => \Localization\Controls\SelectOption,
										 'readonly' => true
									 ]
		);
		
		return
		'
			<section>'.\Localization\Controls\LimitHeading.'</section>
			<section class="results-limit">
				'.$select.'
			</section>
		';
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
		// then change the divisor of pagination.button in search-filter-section.css
		$mostLeftPage       = 1;
		$pageCountFromLeft  = 3;
		$pageCountfromRight = 3;
		$mostRightPage      = $pageCount;
		
		$fromLeft = $currentPageIndex - $pageCountFromLeft;
		$fromLeft = $fromLeft > $mostLeftPage ? $fromLeft : $mostLeftPage;
		
		$fromRight = $currentPageIndex + $pageCountfromRight;
		$fromRight = $fromRight < $mostRightPage ? $fromRight : $mostRightPage;
		
		$html[] =
		'
			<section>'.\Localization\Controls\PageHeading.'</section>
			<section class="pagination">
		';
		
		if ($fromLeft > $mostLeftPage)
		{
			$href = $pageLink.Http::buildPaginationParameters($limit, $mostLeftPage, $search);
			$html[] = $this->createPaginationButton($mostLeftPage, $href, 'enabled');
		}
		
		if ($fromLeft > $mostLeftPage + 1)
			$html[] = $this->createPaginationButton('…', null, 'disabled');
		
		for ($i = $fromLeft; $i <= $fromRight; $i++)
		{
			$href  = $pageLink.Http::buildPaginationParameters($limit, $i, $search);
			$state = ($i === $currentPageIndex) ? 'current' : 'enabled';
			
			$html[] = $this->createPaginationButton($i, $href, $state);
		}
		
		if ($fromRight < $mostRightPage - 1)
			$html[] = $this->createPaginationButton('…', null, 'disabled');
		
		if ($fromRight < $mostRightPage)
		{
			$href = $pageLink.Http::buildPaginationParameters($limit, $mostRightPage, $search);
			$html[] = $this->createPaginationButton($mostRightPage, $href, 'enabled');
		}
		
		$html[] = 
		'
			</section>
		';
		
		return implode($html);
	}
	
	final protected function createSearchBarBlock
	(
		int|null    $limit,
		int|null    $page,
		string|null $search
	): string
	{
		if (!is_null($limit))
		{
			$limitInput  = '<input name="limit" type="hidden" value="'.htmlspecialchars($limit).'" />';
			$pageInput   = '<input name="page" type="hidden" value="1" />';
			
		}
		else
		{
			$limitInput = '';
			$pageInput  = '';
		}
		
		$searchInput = '<input class="custom-input" name="search" type="search" id="search-bar" value="'.htmlspecialchars($search ?? '').'" placeholder="'.\Localization\Controls\SearchPlaceholder.'" required />';
		
		return
		'
		<section>'.\Localization\Controls\SearchHeading.'</section>
		<section class="search-elements">
			'.$limitInput.'
			'.$pageInput.'
			'.$searchInput.'
			<button class="custom-button" id="search-bar-button">'.\Localization\Controls\SearchButton.'</button>
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
		string|null $relationKey         = null,
		bool        $statusChangeAllowed = true
	): array
	{
		$html = [];
		
		foreach ($games as $game)
		{
			$href = Http::buildInternalPath($this->language, 'game', $game['uri']);
			
			$image        = $this->createGameImage($game);
			$textEntities = [];
			
			$textEntities[] = $this->createHeadingAsLink($game['transliterated_name'], $headingLevel, $href, ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($game['original_name'], ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($game['localized_name'], ['class' => ['entity-name']]);
			
			if ($relationKey)
			{
				if (Session::agentIsAdministrator() && $statusChangeAllowed)
					$textEntities[] = $this->createStatusSelect($game, $relationKey, $href);
				else
					$textEntities[] = $this->createStatus($game[$relationKey], true);
			}
			
			$html[] = $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
		return $html;
	}
	
	final protected function createAlbumList
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
			$href = Http::buildInternalPath($this->language, 'album', $album['uri']);
			
			$image        = $this->createAlbumImage($album);
			$textEntities = [];
			
			$textEntities[] = $this->createHeadingAsLink($album['transliterated_name'], $headingLevel, $href, ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($album['original_name'], ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($album['localized_name'], ['class' => ['entity-name']]);
			
			if ($relationKey)
			{
				if (Session::agentIsAdministrator()&& $statusChangeAllowed)
					$textEntities[] = $this->createStatusSelect($album, $relationKey, $href);
				else
					$textEntities[] = $this->createStatus($album[$relationKey], true);
			}
			
			$html[] = $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
		return $html;
	}
	
	final protected function createArtistList
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
			$href = Http::buildInternalPath($this->language, 'artist', $artist['uri']);
			
			$image        = $this->createArtistImage($artist);
			$textEntities = [];
			
			$textEntities[] = $this->createHeadingAsLink($artist['transliterated_name'], $headingLevel, $href, ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($artist['original_name'], ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($artist['localized_name'], ['class' => ['entity-name']]);
			
			if ($relationKey)
			{
				if (Session::agentIsAdministrator())
					$textEntities[] = $this->createStatusSelect($artist, $relationKey, $href);
				else
					$textEntities[] = $this->createStatus($artist[$relationKey], true);
			}
			
			$html[] = $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
		return $html;
	}
	
	final protected function createCharacterList
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
			$href = Http::buildInternalPath($this->language, 'character', $character['uri']);
			
			$image        = $this->createCharacterImage($character);
			$textEntities = [];
			
			$textEntities[] = $this->createHeadingAsLink($character['transliterated_name'], $headingLevel, $href, ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($character['original_name'], ['class' => ['entity-name']]);
			$textEntities[] = $this->createParagraph($character['localized_name'], ['class' => ['entity-name']]);
			
			if ($relationKey)
			{
				if (Session::agentIsAdministrator() && $statusChangeAllowed)
					$textEntities[] = $this->createStatusSelect($character, $relationKey, $href);
				else
					$textEntities[] = $this->createStatus($character[$relationKey], true);
			}
			
			$html[] = $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
		return $html;
	}
	
	final protected function createAlbumSongList
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
				$href = Http::buildInternalPath($this->language, 'album', $album['uri'], 'song', $songs[$i]['uri']);
				$transliteratedName = $this->createParagraphAsLink($songs[$i]['transliterated_name'], $href);
			}
			else
				$transliteratedName = $this->createParagraph($songs[$i]['transliterated_name']);
			
			$editLabel    = \Localization\AlbumPage\EditSong;
			$editAccess   = Session::agentHasRightToEditAlbum($album);
			$editAccess   = ($editAccess === AccessState::Ok) ? Session::agentHasRightToEditSong($songs[$i]) : $editAccess;
			$editHref     = Http::buildInternalPath($this->language, 'album', $album['uri'], 'song', $songs[$i]['uri'], 'edit');
			$editButton   = $this->createButtonAsRestrictedLink($editLabel, $editAccess, ['href' => $editHref]);
			
			$deleteLabel  = \Localization\AlbumPage\DeleteSong;
			$deleteAccess = Session::agentHasRightToEditAlbum($album);
			$deleteAccess = ($deleteAccess === AccessState::Ok) ? Session::agentHasRightToDeleteSong($songs[$i]) : $deleteAccess;
			$deleteHref   = Http::buildInternalPath($this->language, 'album', $album['uri'], 'song', $songs[$i]['uri'], 'delete');
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
			'.$this->createHeading(\Localization\AlbumPage\SongList, 2).'
			<table>
				<tbody>
					<tr>
						<th>'.\Localization\AlbumPage\DiscNumber.'</th>
						<th>'.\Localization\AlbumPage\TrackNumber.'</th>
						<th>'.\Localization\AlbumPage\SongName.'</th>
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
			$addLabel  = \Localization\AlbumPage\AddSong;
			$addAccess = Session::agentHasRightToEditAlbum($album);
			$addHref   = Http::buildInternalPath($this->language, 'album', $album['uri'], 'add-song');
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
		
		if (count($songs) === 0 && Session::agentIsAdministrator())
		{
			$title = \Localization\AlbumPage\FillAlbum;
			$href  = Http::buildInternalPath($this->language, 'album', $album['uri'], 'fill-album');
			
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
	
	final protected function createSongList
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
				$href = Http::buildInternalPath($this->language, 'album', $song['album_uri'], 'song', $song['uri']);
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
	
	final protected function createTranslationList
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
			
			$href = Http::buildInternalPath
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
			$textEntities[] = $this->createParagraph(\Localization\Functions\localizeLanguageName($translation));
			
			$html[] = $this->createInfoBlockWithImage($image, $textEntities, $entityClass);
		}
		
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
		$textEntities[] = $this->createVndbLinkParagraph(\Localization\GamePage\Details, $game, 'v');
		
		return $this->createInfoBlockWithImage($image, $textEntities, 'main-entity');
	}
	
	final protected function createAlbum(array $album, int $headingLevel): string
	{
		$image        = $this->createAlbumImage($album);
		$textEntities = [];
		
		$textEntities[] = $this->createHeading($album['transliterated_name'], $headingLevel);
		$textEntities[] = $this->createParagraph($album['original_name']);
		$textEntities[] = $this->createParagraph($album['localized_name']);
		$textEntities[] = $this->createVgmdbLinkParagraph(\Localization\AlbumPage\Details, $album, 'album');
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
		$textEntities[] = $this->createVgmdbLinkParagraph(\Localization\ArtistPage\Details, $artist, 'artist');
		
		if ($artist['alias_of_transliterated_name'] && $artist['alias_of_uri'])
		{
			$href = Http::buildInternalPath($this->language, 'artist', $artist['alias_of_uri']);
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
		$textEntities[] = $this->createVndbLinkParagraph(\Localization\CharacterPage\Details, $character, 'c');
		
		return $this->createInfoBlockWithImage($image, $textEntities, 'main-entity');
	}
}
