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
}
