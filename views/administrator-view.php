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
		$html = $this->startRender('Control Panel');
		
		// Add new links as new pages appear
		
		$html .= 
		'
		<article>
			<h1>Control Panel</h1>
			<section>
				<h2><a href="/en/control-panel/add-language">Add Language</a></h2>
				<h2><a href="/en/control-panel/edit-language">Edit Language</a></h2>
				<h2><a href="/en/control-panel/report-list">Report List</a></h2>
				<h2><a href="/en/control-panel/user-list">User List</a></h2>
			</section>
		</article>
		';
		
		$html .= $this->endRender();
		
		echo $html;
	}
	
	private function renderLanguageEditor(array|null $language, string $heading): void
	{
		if ($language)
		{
			$ownName = htmlspecialchars($language['own_name']);
			$ruName  = htmlspecialchars($language['ru_name']);
			$enName  = htmlspecialchars($language['en_name']);
			$jaName  = htmlspecialchars($language['ja_name']);
		}
		else
		{
			$ownName = '';
			$ruName  = '';
			$enName  = '';
			$jaName  = '';
		}
		
		$cancelLink = buildInternalLink($this->language, 'control-panel');
		
		$html = $this->startRender($heading, ['/css/editor-page.css']);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<form method="POST" enctype="multipart/form-data" autocomplete="off">
					<section>
						<h2>Own Name<span class="required-input"> *</span></h2>
						<input name="own-name" value="'.$ownName.'" required />
					</section>
					<section>
						<h2>Name in Russian<span class="required-input"> *</span></h2>
						<input name="ru-name" value="'.$ruName.'" required />
					</section>
					<section>
						<h2>Name in English<span class="required-input"> *</span></h2>
						<input name="en-name" value="'.$enName.'" required />
					</section>
					<section>
						<h2>Name in Japanese<span class="required-input"> *</span></h2>
						<input name="ja-name" value="'.$jaName.'" required />
					</section>
					<section>
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
		
		$html .= $this->endRender();
		
		echo $html;
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
		$cancelLink = buildInternalLink($this->language, 'control-panel');
		
		$html = $this->startRender($heading);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<table>
					<tbody>
						<tr>
							<th>Id</th>
							<th>Sender Id</th>
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
			$html .= 
			'
						<tr>
							<td>'.htmlspecialchars($report['id']).'</td>
							<td>'.htmlspecialchars($report['sender_id'] ?? 'null').'</td>
							<td>'.htmlspecialchars($report['username'] ?? 'null').'</td>
							<td>'.htmlspecialchars($report['message']).'</td>
							<td>'.htmlspecialchars($report['request_uri']).'</td>
							<td>'.htmlspecialchars($report['user_agent']).'</td>
							<td>'.htmlspecialchars($report['timestamp_sent']).'</td>
							<td>'.$this->createStatusSelect($report).'</td>
						</tr>
			';
		}
		
		$html .= 
		'
					</tbody>
				</table>
				<section>
					'.$this->createButton(\Localization\Controls\Cancel, $cancelLink).'
				</section>
			</section>
		</article>
		';
		
		$html .= $this->endRender
		(
			[
				'/js/moderation/change-report-status.js'
			]
		);
		
		echo $html;
	}
	
	final public function renderUserListPage(array $users): void
	{
		$heading = 'User List';
		$cancelLink = buildInternalLink($this->language, 'control-panel');
		
		$html = $this->startRender($heading);
		
		$html .= 
		'
		<article>
			<section>
				'.$this->createHeading($heading, 1).'
				<table>
					<tbody>
						<tr>
							<th>Id</th>
							<th>Role</th>
							<th>Username</th>
							<th>Email</th>
							<th>IP Address</th>
							<th>Created</th>
							<th>Last Log-In</th>
						</tr>
		';
		
		foreach ($users as $user)
		{
			$html .= 
			'
						<tr>
							<td>'.htmlspecialchars($user['id']).'</td>
							<td>'.htmlspecialchars($user['en_name']).'</td>
							<td>'.htmlspecialchars($user['username']).'</td>
							<td>'.htmlspecialchars($user['email']).'</td>
							<td>'.htmlspecialchars($user['ip_address']).'</td>
							<td>'.htmlspecialchars($user['timestamp_created']).'</td>
							<td>'.htmlspecialchars($user['timestamp_last_log_in']).'</td>
						</tr>
			';
		}
		
		$html .= 
		'
					</tbody>
				</table>
				<section>
					'.$this->createButton(\Localization\Controls\Cancel, $cancelLink).'
				</section>
			</section>
		</article>
		';
		
		$html .= $this->endRender();
		
		echo $html;
	}
	
	final public function renderFillAlbumEditorPage(array $album, array $discography, string $heading): void
	{
		$cancelLink = buildInternalLink($this->language, 'album', $album['uri']);
		
		$vocalOptions =
		[
			// The null option is added automatically
			['id' => 0, 'value' => \Localization\SongEditorPage\HasVocalFalse],
			['id' => 1, 'value' => \Localization\SongEditorPage\HasVocalTrue],
		];
		
		$selectVocal = $this->createSelect
		(
			'has-vocal[]',
			'',
			true,
			true,
			$vocalOptions,
			'value',
			'id',
			null
		);
		
		$html = $this->startRender($heading, ['/css/editor-page.css', '/css/fill-album-page.css']);
		
		// My site supports 3 names: original, transliteration, localization
		$LOCALIZATION_MIN_COUNT = 3;
		
		$html .= 
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
			
			$html .= 
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
		
		$html .= 
		'
								<th>'.\Localization\FillAlbumEditorPage\HasVocal.'</th>
							</tr>
		';
		
		for ($i = 0; $i < count($discography); $i++)
		{
			for ($j = 0; $j < count($discography[$i]); $j++)
			{
				$html .= 
				'
							<tr>
								<td><input name="disc-number[]" value="'.($i + 1).'" readonly/></td>
								<td><input name="track-number[]" value="'.($j + 1).'" readonly/></td>
				';
				
				for ($k = 0; $k < count($discography[$i][$j]) || $k < $LOCALIZATION_MIN_COUNT; $k++)
				{
					$value = htmlspecialchars($discography[$i][$j][$k] ?? '');
					$html .= 
					'
								<td><input data-column-id="'.$k.'" value="'.$value.'"/></td>
					';
				}
				
				$html .= 
				'
								<td>
									'.$selectVocal.'
								</td>
							</tr>
				';
			}
		}
		
		$html .= 
		'
						</tbody>
					</table>
					<section class="has-tooltip" tooltip-id="2">
						<section class="page-controls">
							'.$this->createButton(\Localization\Controls\Cancel, $cancelLink).'
							<section></section>
							<input type="submit" value="'.\Localization\Controls\Submit.'"/>
						</section>
					</section>
				</form>
			</section>
		</article>
		';
		
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
		
		$html .= 
		'
		'.$tooltipWindow.'
		'.$tooltipHeadings.'
		'.$tooltipContents.'
		';
		
		$html .= $this->endRender
		(
			[
				'/js/shared/custom-select.js',
				'/js/shared/tooltip-window.js',
				'/js/fill-album-editor.js'
			]
		);
		
		echo $html;
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
