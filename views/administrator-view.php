<?php

class AdministratorView extends ContributorView
{
	public function __construct(string $language)
	{
		parent::__construct($language);
	}
	
	final public function renderControlPanelPage(): void
	{
		if (Authorizer::isMaintenanceModeActive())
			$maintenance = __('administrator.control_panel.maintenance.switch_off');
		else
			$maintenance = __('administrator.control_panel.maintenance.switch_on');
		
		$html[] = $this->startRender(title: 'Control Panel');
		
		$html[] = 
		'
		<article>
			<section>
				<h1>'.__('administrator.control_panel.heading').'</h1>
				
				<h2>'.__('administrator.control_panel.maintenance.heading').'</h2>
				<p><a href="/en/control-panel/switch-maintenance-mode">'.$maintenance.'</a></p>
				<br/>
				
				<h2>'.__('administrator.control_panel.add.heading').'</h2>
				<p><a href="/en/control-panel/add-language">'.__('administrator.control_panel.add.language').'</a></p>
				<br/>
				
				<h2>'.__('administrator.control_panel.view.heading').'</h2>
				<p><a href="/en/control-panel/report-list">'.__('administrator.control_panel.view.report_list').'</a></p>
				<p><a href="/en/control-panel/user-list">'.__('administrator.control_panel.view.user_list').'</a></p>
				<br/>
				
				<h2>'.__('administrator.control_panel.check.heading').'</h2>
				<p><a href="/en/control-panel/unchecked-game-list">'.__('administrator.control_panel.check.game_list').'</a></p>
				<p><a href="/en/control-panel/unchecked-album-list">'.__('administrator.control_panel.check.album_list').'</a></p>
				<p><a href="/en/control-panel/unchecked-artist-list">'.__('administrator.control_panel.check.artist_list').'</a></p>
				<p><a href="/en/control-panel/unchecked-character-list">'.__('administrator.control_panel.check.character_list').'</a></p>
				<p><a href="/en/control-panel/unchecked-song-list">'.__('administrator.control_panel.check.song_list').'</a></p>
				<p><a href="/en/control-panel/unchecked-translation-list">'.__('administrator.control_panel.check.translation_list').'</a></p>
				<br/>
				
				<h2>'.__('administrator.control_panel.update_sitemap.heading').'</h2>
				<p><a href="/en/control-panel/update-static-pages-sitemap">'.__('administrator.control_panel.update_sitemap.static_list').'</a></p>
				<p><a href="/en/control-panel/update-game-list-sitemap">'.__('administrator.control_panel.update_sitemap.game_list').'</a></p>
				<p><a href="/en/control-panel/update-album-list-sitemap">'.__('administrator.control_panel.update_sitemap.album_list').'</a></p>
				<p><a href="/en/control-panel/update-artist-list-sitemap">'.__('administrator.control_panel.update_sitemap.artist_list').'</a></p>
				<p><a href="/en/control-panel/update-character-list-sitemap">'.__('administrator.control_panel.update_sitemap.character_list').'</a></p>
				<p><a href="/en/control-panel/update-song-list-sitemap">'.__('administrator.control_panel.update_sitemap.song_list').'</a></p>
				<p><a href="/en/control-panel/update-translation-list-sitemap">'.__('administrator.control_panel.update_sitemap.translation_list').'</a></p>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	private function renderLanguageEditor(array|null $language, string $heading): void
	{
		if ($language)
		{
			$ownName = $language['own_name'];
			$ruName  = $language['ru_name'];
			$enName  = $language['en_name'];
			$jaName  = $language['ja_name'];
		}
		else
		{
			$ownName = null;
			$ruName  = null;
			$enName  = null;
			$jaName  = null;
		}
		
		$defaultReturnLink = Http::buildPath($this->language, 'control-panel');
		
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
					<section>
						'.$this->createHeadingForInput('Own Name', 2, true).'
						'.$this->createTextInput(['name' => 'own_name', 'value' => $ownName, 'required' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput('Name in Russian', 2, true).'
						'.$this->createTextInput(['name' => 'ru_name', 'value' => $ruName, 'required' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput('Name in English', 2, true).'
						'.$this->createTextInput(['name' => 'en_name', 'value' => $enName, 'required' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput('Name in Japanese', 2, true).'
						'.$this->createTextInput(['name' => 'ja_name', 'value' => $jaName, 'required' => true]).'
					</section>
					<section>
						<section class="page-controls">
							'.$this->createReturnButton($defaultReturnLink, __('base.controls.button.return')).'
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
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	final public function renderAddLanguagePage(): void
	{
		$this->renderLanguageEditor(null, 'Add Language');
	}
	
	final public function renderEditLanguagePage(array $language): void
	{
		$this->renderLanguageEditor($language, 'Edit Language');
	}
	
	final public function renderReportListPage(array $reports): void
	{
		$defaultReturnLink = Http::buildPath($this->language, 'control-panel');
		
		$html[] = $this->startRender
		(
			title: __('administrator.report_list.heading'),
			cssSheetUris: ['/css/moderation/table-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading(__('administrator.report_list.heading'), 1).'
				<section class="table-wrapper">
					<table>
						<tbody>
							<tr>
								<th>'.__('administrator.report_list.user_id').'</th>
								<th>'.__('administrator.report_list.user_ip').'</th>
								<th>'.__('administrator.report_list.username').'</th>
								<th>'.__('administrator.report_list.report_text').'</th>
								<th>'.__('administrator.report_list.entity_uri').'</th>
								<th>'.__('administrator.report_list.user_agent').'</th>
								<th>'.__('administrator.report_list.timestamp').'</th>
								<th>'.__('administrator.report_list.status').'</th>
							</tr>
		';
		
		foreach ($reports as $report)
		{
			$ipAddress = Cryptography::decryptData($report['ip_address']);
			
			$html[] = 
			'
							<tr>
								<td>'.htmlspecialchars($report['id']).'</td>
								<td>'.htmlspecialchars($ipAddress).'</td>
			';
			
			if ($report['username'] !== null)
			{
				$link = Http::buildPath($this->language, 'user', $report['user_uri']);
				
				$html[] =
				'
								<td><a href="'.$link.'">'.htmlspecialchars($report['username']).'</a></td>
								<td>
				';
			}
			else
			{
				$html[] =
				'
								<td>'.__('administrator.report_list.anonymous').'</td>
								<td>
				';
			}
			
			$lines = explode("\n", $report['message']);
			foreach ($lines as $line)
				$html[] = htmlspecialchars($line).'<br/>';
			
			$html[] =
			'
								</td>
								<td><a href="'.htmlspecialchars($report['request_uri']).'">'.htmlspecialchars($report['request_uri']).'</a></td>
								<td>'.htmlspecialchars($report['user_agent']).'</td>
								<td>'.htmlspecialchars($report['timestamp_sent']).'</td>
								<td>'.$this->createStatusSelect($report).'</td>
							</tr>
			';
		}
		
		$html[] = 
		'
						</tbody>
					</table>
				</section>
				<section>
					'.$this->createReturnButton($defaultReturnLink, __('base.controls.button.return')).'
				</section>
			</section>
		</article>
		';
		
		$html[] = $this->endRender
		(
			jsScriptUris:
			[
				'/js/moderation/change-report-status.js'
			]
		);
		
		$this->echoHtml($html);
	}
	
	final public function renderUserListPage(array $users): void
	{
		$defaultReturnLink = Http::buildPath($this->language, 'control-panel');
		
		$html[] = $this->startRender
		(
			title: __('administrator.user_list.heading'),
			cssSheetUris: ['/css/moderation/table-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading(__('administrator.user_list.heading'), 1).'
				<section class="table-wrapper">
					<table>
						<tbody>
							<tr>
								<th>'.__('administrator.user_list.id').'</th>
								<th>'.__('administrator.user_list.role').'</th>
								<th>'.__('administrator.user_list.username').'</th>
								<th>'.__('administrator.user_list.email').'</th>
								<th>'.__('administrator.user_list.signed_up').'</th>
								<th>'.__('administrator.user_list.logged_in').'</th>
								<th>'.__('administrator.user_list.fingerprints').'</th>
							</tr>
		';
		
		foreach ($users as $user)
		{
			$separator    = str_repeat(chr(0xFF), 8);
			$fingerprints = explode($separator, $user['fingerprints'] ?? '');
			
			for ($i = 0; $i < count($fingerprints); $i++)
				$fingerprints[$i] = Cryptography::decryptData($fingerprints[$i]);
			$fingerprints = implode(', ', $fingerprints);
			
			$link = Http::buildPath($this->language, 'user', $user['uri']);
			
			$html[] = 
			'
							<tr>
								<td>'.htmlspecialchars($user['id']).'</td>
								<td>'.htmlspecialchars($user['en_name']).'</td>
								<td><a href="'.$link.'">'.htmlspecialchars($user['username']).'</a></td>
								<td>'.htmlspecialchars(Cryptography::decryptData($user['email'])).'</td>
								<td>'.htmlspecialchars($user['timestamp_created']).'</td>
								<td>'.htmlspecialchars($user['timestamp_last_log_in']).'</td>
								<td>'.htmlspecialchars($fingerprints).'</td>
							</tr>
			';
		}
		
		$html[] = 
		'
						</tbody>
					</table>
				</section>
				<section>
					'.$this->createReturnButton($defaultReturnLink, __('base.controls.button.return')).'
				</section>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	final public function renderFillAlbumEditorPage(array $album, array $discography, string $heading): void
	{
		$defaultReturnLink = Http::buildPath($this->language, 'album', $album['uri']);
		
		$vocalOptions =
		[
			['id' => 0, 'value' => __('administrator.fill_album.has_lyrics_false')],
			['id' => 1, 'value' => __('administrator.fill_album.has_lyrics_true')],
		];
		
		$selectVocal = $this->createSelect
		(
			iteratedOptions: $vocalOptions,
			selectedOption:  $vocalOptions[0],
			keyToShownValue: 'value',
			keyToSentValue:  'id',
			attributes:      ['name' => 'has_vocal[]', 'required' => true]
		);
		
		$html[] = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/moderation/fill-album-page.css']
		);
		
		// My site supports 3 names: original, transliteration, localization
		$LOCALIZATION_MIN_COUNT = 3;
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<table class="has-tooltip" tooltip-id="1">
						<tbody>
							<tr>
								<th>'.__('administrator.fill_album.disc_number').'</th>
								<th>'.__('administrator.fill_album.track_number').'</th>
		';
		
		for ($i = 0; $i < count($discography[0][0]) || $i < $LOCALIZATION_MIN_COUNT; $i++)
		{
			// Could not come up with idea how to use $this->createSelect, so made this time manually
			// It'll definitely break if something in $this->createSelect changes
			
			$html[] = 
			'
								<th>
									<select class="name-type-select custom-select" data-column="'.$i.'">
										<option data-type=""                      class="custom-select-option" data-required="false"></option>
										<option data-type="original_name[]"       class="custom-select-option" data-required="true" >'.__('administrator.fill_album.original_name').'</option>
										<option data-type="transliterated_name[]" class="custom-select-option" data-required="true" >'.__('administrator.fill_album.transliterated_name').'</option>
										<option data-type="localized_name[]"      class="custom-select-option" data-required="false">'.__('administrator.fill_album.localized_title').'</option>
									</select>
									<section class="custom-select-filler"></section>
								</th>
			';
		}
		
		$html[] = 
		'
								<th>'.__('administrator.fill_album.has_lyrics').'</th>
							</tr>
		';
		
		for ($i = 0; $i < count($discography); $i++)
		{
			for ($j = 0; $j < count($discography[$i]); $j++)
			{
				$html[] = 
				'
							<tr>
								<td><input class="custom-input" type="text" name="disc_number[]" value="'.($i + 1).'" readonly/></td>
								<td><input class="custom-input" type="text" name="track_number[]" value="'.($j + 1).'" readonly/></td>
				';
				
				for ($k = 0; $k < count($discography[$i][$j]) || $k < $LOCALIZATION_MIN_COUNT; $k++)
				{
					$value = htmlspecialchars($discography[$i][$j][$k] ?? '');
					$html[] = 
					'
								<td><input class="custom-input" type="text" data-column-id="'.$k.'" value="'.$value.'"/></td>
					';
				}
				
				$html[] = 
				'
								<td>
									'.$selectVocal.'
								</td>
							</tr>
				';
			}
		}
		
		$html[] = 
		'
						</tbody>
					</table>
					<section class="has-tooltip" tooltip-id="2">
						<section class="page-controls">
							'.$this->createReturnButton($defaultReturnLink, __('base.controls.button.return')).'
							'.$this->createFillerSection().'
							'.$this->createSubmitButton(__('base.controls.button.submit')).'
						</section>
					</section>
				</form>
			</section>
		</article>
		';
		
		/*
		$tooltipWindow = $this->createTooltipWindow();
		$tooltipHeadings = $this->createTooltipHeadingDatalist
		(
			\Localization\TooltipWindow\DefaultHeading,
			\Localization\FillAlbumPage\TooltipHeading\TrackTable,
			\Localization\FillAlbumPage\TooltipHeading\Controls
		);
		$tooltipContents = $this->createTooltipContentDatalist
		(
			\Localization\TooltipWindow\DefaultContent,
			\Localization\FillAlbumPage\TooltipContent\TrackTable,
			\Localization\FillAlbumPage\TooltipContent\Controls
		);
		
		$html[] = 
		'
		'.$tooltipWindow.'
		'.$tooltipHeadings.'
		'.$tooltipContents.'
		';
		*/
		
		$html[] = $this->endRender
		(
			[
				'/js/shared/tooltip-window.js',
				'/js/moderation/fill-album-editor-page.js'
			]
		);
		
		$this->echoHtml($html);
	}
	
	final public function renderFillAlbumPage
	(
		array $album,
		array $discography
	): void
	{
		$this->renderFillAlbumEditorPage
		(
			$album,
			$discography,
			__('administrator.fill_album.heading', ['name' => $album['transliterated_name']])
		);
	}
}
