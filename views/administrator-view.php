<?php

require_once 'views/user-view.php';

class AdministratorView extends UserView
{
	public function __construct(string $language)
	{
		parent::__construct($language);
	}
	
	final public function renderControlPanelPage(): void
	{
		$html[] = $this->startRender(title: 'Control Panel');
		
		// Add new links as new pages appear
		
		$html[] = 
		'
		<article>
			<section>
				<h1>Control Panel</h1>
				
				<h2>Add Data</h2>
				<p><a href="/en/control-panel/add-language">Add Language</a></p>
				<br/>
				
				<h2>View Data</h2>
				<p><a href="/en/control-panel/report-list">Report List</a></p>
				<p><a href="/en/control-panel/user-list">User List</a></p>
				<br/>
				
				<h2>Check Data</h2>
				<p><a href="/en/control-panel/unchecked-game-list">Unchecked Game List</a></p>
				<p><a href="/en/control-panel/unchecked-album-list">Unchecked Album List</a></p>
				<p><a href="/en/control-panel/unchecked-artist-list">Unchecked Artist List</a></p>
				<p><a href="/en/control-panel/unchecked-character-list">Unchecked Character List</a></p>
				<p><a href="/en/control-panel/unchecked-song-list">Unchecked Song List</a></p>
				<p><a href="/en/control-panel/unchecked-translation-list">Unchecked Translation List</a></p>
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
		
		$defaultReturnLink = Http::buildInternalPath($this->language, 'control-panel');
		
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
						'.$this->createTextInput(['name' => 'own-name', 'value' => $ownName, 'required' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput('Name in Russian', 2, true).'
						'.$this->createTextInput(['name' => 'ru-name', 'value' => $ruName, 'required' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput('Name in English', 2, true).'
						'.$this->createTextInput(['name' => 'en-name', 'value' => $enName, 'required' => true]).'
					</section>
					<section>
						'.$this->createHeadingForInput('Name in Japanese', 2, true).'
						'.$this->createTextInput(['name' => 'ja-name', 'value' => $jaName, 'required' => true]).'
					</section>
					<section>
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
		$heading = 'Report List';
		$defaultReturnLink = Http::buildInternalPath($this->language, 'control-panel');
		
		$html[] = $this->startRender
		(
			title: $heading,
			cssSheetUris: ['/css/moderation/table-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<section class="table-wrapper">
					<table>
						<tbody>
							<tr>
								<th>Id</th>
								<th>Sender Ip</th>
								<th>Sender Username</th>
								<th>Message</th>
								<th>Request Uri</th>
								<th>User Agent</th>
								<th>Timestamp Sent</th>
								<th>Status</th>
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
			
			if (!is_null($report['username']))
			{
				$link = Http::buildInternalPath($this->language, 'user', $report['user_uri']);
				
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
								<td>[Anonymous]</td>
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
					'.$this->createReturnButton($defaultReturnLink).'
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
		$heading = 'User List';
		$defaultReturnLink = Http::buildInternalPath($this->language, 'control-panel');
		
		$html[] = $this->startRender
		(
			title: $heading,
			cssSheetUris: ['/css/moderation/table-page.css']
		);
		
		$html[] = 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<section class="table-wrapper">
					<table>
						<tbody>
							<tr>
								<th>Id</th>
								<th>Role</th>
								<th>Username</th>
								<th>Email</th>
								<th>Created</th>
								<th>Last Log-In</th>
								<th>Fingerprints</th>
							</tr>
		';
		
		foreach ($users as $user)
		{
			$separator    = str_repeat(chr(0xFF), 8);
			$fingerprints = explode($separator, $user['fingerprints'] ?? '');
			
			for ($i = 0; $i < count($fingerprints); $i++)
				$fingerprints[$i] = Cryptography::decryptData($fingerprints[$i]);
			$fingerprints = implode(', ', $fingerprints);
			
			$link = Http::buildInternalPath($this->language, 'user', $user['uri']);
			
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
					'.$this->createReturnButton($defaultReturnLink).'
				</section>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	final public function renderFillAlbumEditorPage(array $album, array $discography, string $heading): void
	{
		$defaultReturnLink = Http::buildInternalPath($this->language, 'album', $album['uri']);
		
		$vocalOptions =
		[
			['id' => 0, 'value' => \Localization\SongEditorPage\HasVocalFalse],
			['id' => 1, 'value' => \Localization\SongEditorPage\HasVocalTrue],
		];
		
		$selectVocal = $this->createSelect
		(
			iteratedOptions: $vocalOptions,
			selectedOption:  ['id' => 0, 'value' => \Localization\SongEditorPage\HasVocalFalse],
			keyToShownValue: 'value',
			keyToSentValue:  'id',
			attributes:      ['name' => 'has-vocal[]', 'required' => true]
		);
		
		$html[] = $this->startRender
		(
			title:        $heading,
			cssSheetUris: ['/css/editor-page.css', '/css/fill-album-page.css']
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
								<th>'.\Localization\FillAlbumEditorPage\DiscNumber.'</th>
								<th>'.\Localization\FillAlbumEditorPage\TrackNumber.'</th>
		';
		
		for ($i = 0; $i < count($discography[0][0]) || $i < $LOCALIZATION_MIN_COUNT; $i++)
		{
			// Could not come up with idea how to use $this->createSelect, so made this time manually
			// It'll definitely break if something in $this->createSelect changes
			
			$html[] = 
			'
								<th>
									<select class="name-type-select" data-column="'.$i.'">
										<option data-type="" data-required="false"></option>
										<option data-type="original-name[]" data-required="true">'.\Localization\FillAlbumEditorPage\OriginalName.'</option>
										<option data-type="transliterated-name[]" data-required="true">'.\Localization\FillAlbumEditorPage\TransliteratedName.'</option>
										<option data-type="localized-name[]" data-required="false">'.\Localization\FillAlbumEditorPage\LocalizedName.'</option>
									</select>
									<section class="select-fake-filler"></section>
								</th>
			';
		}
		
		$html[] = 
		'
								<th>'.\Localization\FillAlbumEditorPage\HasVocal.'</th>
							</tr>
		';
		
		for ($i = 0; $i < count($discography); $i++)
		{
			for ($j = 0; $j < count($discography[$i]); $j++)
			{
				$html[] = 
				'
							<tr>
								<td><input type="text" name="disc-number[]" value="'.($i + 1).'" readonly/></td>
								<td><input type="text" name="track-number[]" value="'.($j + 1).'" readonly/></td>
				';
				
				for ($k = 0; $k < count($discography[$i][$j]) || $k < $LOCALIZATION_MIN_COUNT; $k++)
				{
					$value = htmlspecialchars($discography[$i][$j][$k] ?? '');
					$html[] = 
					'
								<td><input type="text" data-column-id="'.$k.'" value="'.$value.'"/></td>
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
							'.$this->createReturnButton($defaultReturnLink).'
							'.$this->createFillerSection().'
							'.$this->createSubmitButton().'
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
				'/js/fill-album-editor-page.js'
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
			\Localization\FillAlbumEditorPage\Heading.$album['transliterated_name']
		);
	}
}
