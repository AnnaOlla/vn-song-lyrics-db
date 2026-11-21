<?php

abstract class BaseView
{
	protected const ENTITY_LIST_DEFAULT_QUERY = ['limit' => 10, 'page' => 1];
	
	protected const ACCOUNT_DATA_MIN_LENGTH = 4;
	protected const ACCOUNT_DATA_MAX_LENGTH = 32;
	protected const FEEDBACK_MAX_LENGTH     = 500;
	protected const REPORT_MAX_LENGTH       = 500;
	
	protected $language;
	private   $nonce;
	
	public function __construct(string $language)
	{
		$this->language = $language;
		$this->nonce    = Cryptography::generateNonce();
		
		Localizer::setLocale($language);
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
	
	private function getTimestamp(string $path): int
	{
		// Heredoc does not support operator ::
		return Http::getTimestamp($path);
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
		bool        $allowIndex   = true,
		bool        $allowFollow  = true,
		array       $cssSheetUris = [],
		array       $jsScriptUris = []
	): string
    {
		$title          = htmlspecialchars($title.' | vn-song-lyrics-db');
		$description    = htmlspecialchars($description ?? 'The database of lyrics for songs introduced in visual novels.');
		
		$requestUri     = htmlspecialchars(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		$currentUrl     = htmlspecialchars('https://'.$_SERVER['HTTP_HOST'].$requestUri);
		$canonicalUrl   = htmlspecialchars('https://'.$_SERVER['HTTP_HOST'].($canonicalUri ?? $requestUri));
		$alternateRefEn = htmlspecialchars('https://'.$_SERVER['HTTP_HOST'].Http::changeUriLocale(($canonicalUri ?? $requestUri), 'en'));
		$alternateRefRu = htmlspecialchars('https://'.$_SERVER['HTTP_HOST'].Http::changeUriLocale(($canonicalUri ?? $requestUri), 'ru'));
		$alternateRefJa = htmlspecialchars('https://'.$_SERVER['HTTP_HOST'].Http::changeUriLocale(($canonicalUri ?? $requestUri), 'ja'));
		
		$ogUrl          = htmlspecialchars($currentUrl);
		$ogImageUrl     = htmlspecialchars('https://'.$_SERVER['HTTP_HOST'].($ogImageUri ?? '/assets/static-images/wee-hagana-og.webp'));
		
		$index          = htmlspecialchars($allowIndex ? 'index' : 'noindex');
		$follow         = htmlspecialchars($allowFollow ? 'follow' : 'nofollow');
		
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
	<meta property="og:image:alt"        content="Hagana of World End Economica" />
	<meta property="og:locale"           content="en_US" />
	<meta property="og:locale:alternate" content="ru_RU" />
	<meta property="og:locale:alternate" content="ja_JP" />
	
	<meta name="robots"          content="{$index}" />
	<meta name="robots"          content="{$follow}" />
	<meta name="robots"          content="noai" />
	<meta name="robots"          content="noimageai" />
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


HTML;
		array_unshift
		(
			$cssSheetUris,
			'/css/core/font-hanazono-mincho-type-a.css',
			'/css/core/font-juliamo-ampleksa.css',
			'/css/core/sizes.css',
			'/css/core/dark-theme.css',
			'/css/core/general.css',
			'/css/custom-inputs/button.css',
			'/css/custom-inputs/checkbox.css',
			'/css/custom-inputs/fileupload.css',
			'/css/custom-inputs/searchable-select.css',
			'/css/custom-inputs/select.css',
			'/css/custom-inputs/textarea.css',
			'/css/custom-inputs/textinput.css'
		);
		
		foreach ($cssSheetUris as $cssSheetUri)
		{
			$html[] =
<<<HTML
	<link type="text/css" rel="stylesheet" href="{$cssSheetUri}?v={$this->getTimestamp($cssSheetUri)}" nonce="{$this->nonce}"/>

HTML;
		}
		
        foreach ($jsScriptUris as $jsScriptUri)
		{
			$html[] =
<<<HTML
	<script src="{$jsScriptUri}?v={$this->getTimestamp($jsScriptUri)}" nonce="{$this->nonce}"/></script>

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
		$enLink = Http::changeUriLocale($_SERVER['REQUEST_URI'], 'en');
		$ruLink = Http::changeUriLocale($_SERVER['REQUEST_URI'], 'ru');
		$jaLink = Http::changeUriLocale($_SERVER['REQUEST_URI'], 'ja');
		
		if (Authorizer::isCurrentUserVisitor())
		{
			$logIn    = '<a href="/'.$this->language.'/log-in">'.__('base.header.log_in').'</a>';
			$signUp   = '<a href="/'.$this->language.'/sign-up">'.__('base.header.sign_up').'</a>';
			$logOut   = '';
			$username = '';
		}
		else
		{
			$logIn    = '';
			$signUp   = '';
			$logOut   = '<a href="/'.$this->language.'/log-out">'.__('base.header.log_out').'</a>';
			$username = '<a href="/'.$this->language.'/user/'.htmlspecialchars($_SESSION['user']['uri']).'">'.htmlspecialchars($_SESSION['user']['username']).'</a>';
		}
		
		return
		'
	<header>
		<nav>
			<section class="home-page">
				<a href="/'.$this->language.'">'.__('base.header.home_page').'</a>
			</section>
			<section class="main-pages">
				<a href="/'.$this->language.'/game-list?limit=10&page=1">'.__('base.header.game_list').'</a>
				<a href="/'.$this->language.'/album-list?limit=10&page=1">'.__('base.header.album_list').'</a>
				<a href="/'.$this->language.'/artist-list?limit=10&page=1">'.__('base.header.artist_list').'</a>
				<a href="/'.$this->language.'/character-list?limit=10&page=1">'.__('base.header.character_list').'</a>
				<a href="/'.$this->language.'/song-list?limit=10&page=1">'.__('base.header.song_list').'</a>
				<a href="/'.$this->language.'/feedback">'.__('base.header.feedback').'</a>
			</section>
			<section class="filler"><span></span></section>
			<section class="localization">
				<a href="'.$enLink.'">'.__('base.header.language_en').'</a>
				<a href="'.$ruLink.'">'.__('base.header.language_ru').'</a>
				<a href="'.$jaLink.'">'.__('base.header.language_ja').'</a>
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
		bool        $allowIndex   = true,
		bool        $allowFollow  = true,
		array       $cssSheetUris = [],
		array       $jsScriptUris = []
	): string
	{
		header("Content-Security-Policy: script-src 'nonce-{$this->nonce}';");
		
		// The problem now is that inline-styles on lyrics and translation pages are blocked
		// header("Content-Security-Policy: style-src 'nonce-{$this->nonce}';");
		
		return
<<<HTML
<!DOCTYPE html>
<html lang="{$this->language}">
{$this->createHead($title, $description, $canonicalUri, $ogImageUri, $allowIndex, $allowFollow, $cssSheetUris, $jsScriptUris)}
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
				<span>'.__('base.footer.author').'</span>
			</section>
			<section class="links">
				<a href="/'.$this->language.'/about">'.__('base.footer.about').'</a>
				<a href="/'.$this->language.'/policy">'.__('base.footer.policy').'</a>
				<a href="/'.$this->language.'/rules">'.__('base.footer.rules').'</a>
				<a href="/'.$this->language.'/writing-guide">'.__('base.footer.guide').'</a>
			</section>
			<section class="contacts">
				<span>'.__('base.footer.email').'</span>
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

HTML;
		
		array_unshift
		(
			$jsScriptUris,
			'/js/core/emulate-event.js',
			'/js/core/prepare-entity-name-for-filtering.js',
			'/js/custom-inputs/checkbox.js',
			'/js/custom-inputs/captcha-input.js',
			'/js/custom-inputs/fileupload.js',
			'/js/custom-inputs/searchable-select.js',
			'/js/custom-inputs/select.js',
			'/js/custom-inputs/textarea.js'
		);
		
        foreach ($jsScriptUris as $jsScriptUri)
		{
			$html[] =
<<<HTML
	<script src="{$jsScriptUri}?v={$this->getTimestamp($jsScriptUri)}" nonce="{$this->nonce}"/></script>

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
		
		return '<a '.Http::buildAttributes($attributes).'>'.htmlspecialchars($content).'</a>';
	}
	
	final protected function createVndbLinkParagraph
	(
		string $contentBefore,
		array  $entity,
		string $entityFirstLetter
	): string
	{
		if ($entity['vndb_id'] === null)
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
		if ($entity['vgmdb_id'] === null)
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
		return '<h'.$level.' '.Http::buildAttributes($attributes).'>'.htmlspecialchars($heading).'</h'.$level.'>';
	}
	
	final protected function createHeadingAsLink
	(
		string     $heading,
		int        $level,
		string     $href,
		array|null $attributes = null
	): string
	{
		return '<h'.$level.' '.Http::buildAttributes($attributes).'>'.$this->createLink($href, $heading, $attributes).'</h'.$level.'>';
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
			return '<h'.$level.' '.Http::buildAttributes($attributes).'>'.htmlspecialchars($string).'<span class="required-input"> *</span></h'.$level.'>';
		else
			return '<h'.$level.' '.Http::buildAttributes($attributes).'>'.htmlspecialchars($string).'</h'.$level.'>';
	}
	
	final protected function createParagraph
	(
		string|null $string,
		array|null  $attributes = null
	): string
	{
		return '<p '.Http::buildAttributes($attributes).'>'.htmlspecialchars($string ?? '').'</p>';
	}
	
	final protected function createParagraphAsLink
	(
		string     $string,
		string     $href,
		array|null $attributes = null
	): string
	{
		return '<p '.Http::buildAttributes($attributes).'>'.$this->createLink($href, $string).'</p>';
	}
	
	private function createTimestamp
	(
		string|null $timestamp,
		string|null $username,
		string|null $uri,
		string      $action
	): string
	{
		if ($timestamp === null)
			return '';
		
		if ($username === null)
			$username = __('base.timestamp.deleted_user');
		else
			$username = $this->createLink(Http::buildPath($this->language, 'user', $uri), $username);
		
		return '<p>'.__('base.timestamp.string', ['action' => $action, 'timestamp' => $timestamp, 'username' => $username]).'</p>';
	}
	
	final protected function createTimestampAdded
	(
		string|null $timestamp,
		string|null $username,
		string|null $uri
	): string
	{
		return $this->createTimestamp($timestamp, $username, $uri, __('base.timestamp.added'));
	}
	
	final protected function createTimestampUpdated
	(
		string|null $timestamp,
		string|null $username,
		string|null $uri
	): string
	{
		return $this->createTimestamp($timestamp, $username, $uri, __('base.timestamp.updated'));
	}
	
	final protected function createTimestampReviewed
	(
		string|null $timestamp,
		string|null $username,
		string|null $uri
	): string
	{
		return $this->createTimestamp($timestamp, $username, $uri, __('base.timestamp.reviewed'));
	}
	
	final protected function createStatus
	(
		string $status,
		bool   $isRelation = false
	): string
	{
		$status = __('base.moderation_status.'.$status);
		
		if ($isRelation)
			$prefix = __('base.moderation_status.relation_status', ['status' => $status]);
		else
			$prefix = __('base.moderation_status.status', ['status' => $status]);
		
		return $this->createParagraph($prefix);
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
			$status           = __('base.moderation_status.'.$entity[$keyToStatus]);
			$prefix           = __('base.moderation_status.relation_status', ['status' => $status]);
		}
		else
		{
			$keyToStatus      = 'status';
			$dataEntityUri    = '';
			$status           = __('base.moderation_status.'.$entity[$keyToStatus]);
			$prefix           = __('base.moderation_status.status', ['status' => $status]);
		}
		
		$statuses =
		[
			['to_send' => 'unchecked', 'to_show' => __('base.moderation_status.unchecked')],
			['to_send' => 'checked',   'to_show' => __('base.moderation_status.checked')],
			['to_send' => 'hidden',    'to_show' => __('base.moderation_status.hidden')]
		];
		
		$currentStatusIndex = array_search($entity[$keyToStatus], array_column($statuses, 'to_send'));
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
					keyToShownValue:         'to_show',
					keyToSentValue:          'to_send',
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
		
		return '<a '.Http::buildAttributes($attributes).'>'.htmlspecialchars($label ?? '').'</a>';
	}
	
	final protected function createButtonAsRestrictedLink
	(
		string|null $label       = null,
		AccessState $accessState = AccessState::Ok,
		array|null  $attributes  = null
	): string
	{
		if ($accessState !== AccessState::Ok)
		{
			if (isset($attributes['href']))
				unset($attributes['href']);
			
			$attributes['class'][] = 'disabled';
			$attributes['title']   = __("base.access_state.{$accessState->value}");
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
			$source = Http::buildPath
			(
				AssetFolder::Base->value,
				$dynamicAssetFolder->value,
				$dynamicAssetFilename.'.webp'
			);
			return $this->createImage($source, $dynamicAssetAlternativeText);
		}
		else
		{
			$source = Http::buildPath
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
			__('base.image_alt_text.game', ['game' => $game['transliterated_name']]),
			AssetFolder::Static,
			StaticAsset::NoGame->value,
			__('base.image_alt_text.no_game')
		);
	}
	
	final protected function createAlbumImage(array $album): string
	{
		return $this->createEntityImage
		(
			$album,
			AssetFolder::Albums,
			$album['uri'],
			__('base.image_alt_text.album', ['album' => $album['transliterated_name']]),
			AssetFolder::Static,
			StaticAsset::NoAlbum->value,
			__('base.image_alt_text.no_album')
		);
	}
	
	final protected function createArtistImage(array $artist): string
	{
		return $this->createEntityImage
		(
			$artist,
			AssetFolder::Artists,
			$artist['uri'],
			__('base.image_alt_text.artist', ['artist' => $artist['transliterated_name']]),
			AssetFolder::Static,
			StaticAsset::NoArtist->value,
			__('base.image_alt_text.no_artist')
		);
	}
	
	final protected function createCharacterImage(array $character): string
	{
		return $this->createEntityImage
		(
			$character,
			AssetFolder::Characters,
			$character['uri'],
			__('base.image_alt_text.character', ['character' => $character['transliterated_name']]),
			AssetFolder::Static,
			StaticAsset::NoCharacter->value,
			__('base.image_alt_text.no_character')
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
		string $editEntityPathPart,
		string $deleteEntityPathPart,
		string $reportEntityPathPart,
		string $editEntityLocalization,
		string $deleteEntityLocalization,
		string $reportEntityLocalization
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
		
		$editHref   = Http::buildPath($this->language, ...$path, ...[$editEntityPathPart]);
		$deleteHref = Http::buildPath($this->language, ...$path, ...[$deleteEntityPathPart]);
		$reportHref = Http::buildPath($this->language, ...$path, ...[$reportEntityPathPart]);
		
		$editAttributes   = ['href' => $editHref];
		$deleteAttributes = ['href' => $deleteHref];
		$reportAttributes = ['href' => $reportHref];
		
		$editAccess   = ('RoleManager::getStateToEdit'  .$currentEntityName)($currentEntity);
		$deleteAccess = ('RoleManager::getStateToDelete'.$currentEntityName)($currentEntity);
		$reportAccess = ('RoleManager::getStateToReport'.$currentEntityName)($currentEntity);
		
		$editButton   = $this->createButtonAsRestrictedLink($editEntityLocalization,   $editAccess,   $editAttributes);
		$deleteButton = $this->createButtonAsRestrictedLink($deleteEntityLocalization, $deleteAccess, $deleteAttributes);
		$reportButton = $this->createButtonAsRestrictedLink($reportEntityLocalization, $reportAccess, $reportAttributes);
		
		return
		'
			<section>
				'.$editButton.'
				'.$deleteButton.'
				'.$reportButton.'
			</section>
		';
	}
	
	final protected function createTimestampBlock(array $entity): string
	{
		if (Authorizer::isCurrentUserAdministrator())
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
		
		$input = '<span class="custom-checkbox-button">　</span><input '.Http::buildAttributes($attributes).' />';
		
		if ($isLabelBefore)
			return '<label class="custom-checkbox">'.$span.$input.'</label>';
		else
			return '<label class="custom-checkbox">'.$input.$span.'</label>';
	}
	
	/*
	final protected function createFilterBar(): string
	{
		$attributes =
		[
			'class'       => 'custom-input',
			'type'        => 'search',
			'id'          => 'filter-bar',
			'placeholder' => \Localization\Controls\FilterPage
		];
		
		return '<input '.Http::buildAttributes($attributes).' />';
	}
	*/
	
	final protected function createFileupload
	(
		array|null $attributes = null
	): string
	{
		$attributes['type']     = 'file';
		$attributes['class'][]  = 'custom-fileupload-input';
		$attributes['tabindex'] = '-1';
		
		$attributes2 =
		[
			'class'                  => 'custom-fileupload-button',
			'text-file-not-selected' => __('base.controls.fileupload.hint_idle'),
			'text-file-too-big'      => __('base.controls.fileupload.hint_size_exceeded'),
			'tabindex'               => '0'
		];
		
		return
		'
		<label class="custom-fileupload">
			<input '.Http::buildAttributes($attributes).' />
			<section '.Http::buildAttributes($attributes2).'>'.__('base.controls.fileupload.hint_idle').'</section>
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
		if ($iteratedOptions === null)
			$iteratedOptions = [];
		
		if ($disabledOptions === null)
			$disabledOptions = [];
		
		$attributes['class'][] = 'custom-select';
		
		$html[] = '<select '.Http::buildAttributes($attributes).'>';
		
		$iterated = array_column($iteratedOptions, $keyToSentValue, $keyToShownValue);
		$disabled = array_column($disabledOptions, $keyToSentValue);
		$selected = $selectedOption !== null ? $selectedOption[$keyToSentValue] : null;
		
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
			
			$html[] = '<option class="custom-select-option" '.Http::buildAttributes($attributes).'>'.htmlspecialchars($shown ?? '').'</option>';
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
		string      $keyToShownValue         = '',
		array|null  $keysToShownHints        = null,
		string      $keyToSentValue          = '',
		array|null  $attributesForShownInput = null,
		array|null  $attributesForSentInput  = null
	): string
	{
		if ($iteratedOptions === null)
			$iteratedOptions = [];
		
		if ($disabledOptions === null)
			$disabledOptions = [];
		
		if ($selectedOption === null)
		{
			$selectedOption = [];
			$selectedOption[$keyToShownValue] = '';
			$selectedOption[$keyToSentValue] = '';
		}
		
		if ($keysToShownHints === null)
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
				<input '.Http::buildAttributes($attributesForSentInput).' />
				<input '.Http::buildAttributes($attributesForShownInput).' />
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
		
		return '<button '.Http::buildAttributes($attributes).'>'.htmlspecialchars($label).'</button>';
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
		string|null $fallbackHref,
		string      $label,
		array|null  $attributes   = null
	): string
	{
		$attributes['href']    = Http::getLastVisitedPage($fallbackHref);
		$attributes['class'][] = 'custom-button';
		
		return $this->createButtonAsLink(__($label), $attributes);
	}
	
	final protected function createSubmitButton
	(
		string     $label,
		array|null $attributes = null
	): string
	{
		$attributes['type']    = 'submit';
		$attributes['value']   = __($label);
		$attributes['class'][] = 'custom-button';
		
		return '<input '.Http::buildAttributes($attributes).'/>';
	}
	
	final protected function createHiddenInput
	(
		array|null $attributes = null
	): string
	{
		$attributes['type'] = 'hidden';
		
		return '<input '.Http::buildAttributes($attributes).'/>';
	}
	
	final protected function createTextarea
	(
		string|null $value      = null,
		array|null  $attributes = null
	): string
	{
		$attributes['class'][] = 'custom-textarea';
		
		return '<textarea '.Http::buildAttributes($attributes).'>'.htmlspecialchars($value ?? '').'</textarea>';
	}
	
	final protected function createCaptchaInput
	(
		array|null $attributes = null
	): string
	{
		$attributes['type']        = 'text';
		$attributes['name'][]      = 'captcha';
		$attributes['class'][]     = 'custom-input';
		$attributes['class'][]     = 'captcha-input';
		$attributes['placeholder'] = 'code:';
		
		return '<input '.Http::buildAttributes($attributes).' />';
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
		
		return '<img '.Http::buildAttributes($attributes).' />';
	}
	
	final protected function createTextInput
	(
		array|null $attributes = null
	): string
	{
		$attributes['type']    = 'text';
		$attributes['class'][] = 'custom-input';
		
		return '<input '.Http::buildAttributes($attributes).' />';
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
		
		return '<input '.Http::buildAttributes($attributes).' />';
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
		
		return '<input '.Http::buildAttributes($attributes).' />';
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
		
		return '<input '.Http::buildAttributes($attributes).' />';
	}
	
	final protected function createUrlInput
	(
		array|null $attributes = null
	): string
	{
		$attributes['type']    = 'url';
		$attributes['class'][] = 'custom-input';
		
		return '<input '.Http::buildAttributes($attributes).' />';
	}
	
	final protected function createFillerSection(): string
	{
		return '<section class="filler"></section>';
	}
	
	final protected function createDatalist
	(
		array|null $options    = null,
		array|null $attributes = null
	): string
	{
		$html[] = '<datalist '.Http::buildAttributes($attributes).'>';
		
		foreach ($options as $option)
			$html[] = '<option>'.$option.'</option>';
		
		$html[] = '</datalist>';
		
		return implode($html);
	}
}
