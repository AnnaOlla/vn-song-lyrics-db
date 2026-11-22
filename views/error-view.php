<?php

class ErrorView extends BaseView
{
	public function __construct(string $language)
	{
		parent::__construct($language);
	}
	
	private function renderError(string $codename, string $reason, string $hint): void
	{
		$html[] = $this->startRender
		(
			title:        $codename,
			cssSheetUris: ['/css/window-in-center-page.css']
		);
		
		$html[] =
		'
		<article>
			<section>
				<h1>'.htmlspecialchars($codename).'</h1>
				<p>'.htmlspecialchars($reason).'</p>
				<p>'.htmlspecialchars($hint).'</p>
			</section>
		</article>
		';
		
		$html[] = $this->endRender();
		
		$this->echoHtml($html);
	}
	
	final public function renderBadRequest400(): void
	{
		$this->renderError
		(
			'400 Bad Request',
			__('error.400.reason'),
			__('error.400.hint')
		);
	}
	
	final public function renderUnauthorized401(): void
	{
		$this->renderError
		(
			'401 Unauthorized',
			__('error.401.reason'),
			__('error.401.hint')
		);
	}
	
	final public function renderPaymentRequired402(): void
	{
		$this->renderError
		(
			'402 Payment Required',
			__('error.402.reason'),
			__('error.402.hint')
		);
	}
	
	final public function renderForbidden403(): void
	{
		$this->renderError
		(
			'403 Forbidden',
			__('error.403.reason'),
			__('error.403.hint')
		);
	}
	
	final public function renderNotFound404(): void
	{
		$this->renderError
		(
			'404 Not Found',
			__('error.404.reason'),
			__('error.404.hint')
		);
	}
	
	final public function renderMethodNotAllowed405(): void
	{
		$this->renderError
		(
			'405 Method Not Allowed',
			__('error.405.reason'),
			__('error.405.hint')
		);
	}
	
	final public function renderNotAcceptable406(): void
	{
		$this->renderError
		(
			'406 Not Acceptable',
			__('error.406.reason'),
			__('error.406.hint')
		);
	}
	
	final public function renderConflict409(): void
	{
		$this->renderError
		(
			'409 Conflict',
			__('error.409.reason'),
			__('error.409.hint')
		);
	}
	
	final public function renderContentTooLarge413(): void
	{
		$this->renderError
		(
			'413 Content Too Large',
			__('error.413.reason'),
			__('error.413.hint')
		);
	}
	
	final public function renderUnsupportedMediaType415(): void
	{
		$this->renderError
		(
			'415 Unsupported Media Type',
			__('error.415.reason'),
			__('error.415.hint')
		);
	}
	
	final public function renderUnprocessableEntity422(): void
	{
		$this->renderError
		(
			'422 Unprocessable Entity',
			__('error.422.reason'),
			__('error.422.hint')
		);
	}
	
	final public function renderTooManyRequests429(): void
	{
		$this->renderError
		(
			'429 Too Many Requests',
			__('error.429.reason'),
			__('error.429.hint')
		);
	}
	
	final public function renderUnavailableForLegalReasons451(): void
	{
		$this->renderError
		(
			'451 Unavailable For Legal Reasons',
			__('error.451.reason'),
			__('error.451.hint')
		);
	}
	
	final public function renderInternalServerError500(): void
	{
		$this->renderError
		(
			'500 Internal Server Error',
			__('error.500.reason'),
			__('error.500.hint')
		);
	}
	
	final public function renderNotImplemented501(): void
	{
		$this->renderError
		(
			'501 Not Implemented',
			__('error.501.reason'),
			__('error.501.hint')
		);
	}
	
	final public function renderBadGateway502(): void
	{
		$this->renderError
		(
			'502 Bad Gateway',
			__('error.502.reason'),
			__('error.502.hint')
		);
	}
	
	final public function renderServiceUnavailable503(): void
	{
		$this->renderError
		(
			'503 Service Unavailable',
			__('error.503.reason'),
			__('error.503.hint')
		);
	}
	
	final public function renderPermanentlyBlocked418(): void
	{
		echo
		'
<!DOCTYPE html>
<html>
	<head>
		<title>Access Denied</title>
	</head>
	<style>
		html, body {
			height: 100%;
			margin: 0;
			padding: 0;
		}
		body {
			display: flex;
			align-items: center;
			justify-content: center;
		}
		section {
			border: 1px solid black;
			border-radius: 10px;
			display: flex;
			align-items: center;
			justify-content: center;
			flex-direction: column;
		}
		section > * {
			padding: 0px;
			margin: 10px;
		}
	</style>
	<body>
		<section>
			<h1>Access Denied</h1>
			<p>You have been permanently blocked for malicious behavior.</p>
			<p>If you see this message in error, contact us at support@vn-song-lyrics-db.ru.</p>
			<p>In this case, include the information below.</p>
			<p>Current time: '.date("Y-m-d H:i:s").', UTC+3.</p>
			<p>Your IP address: '.$_SERVER['REMOTE_ADDR'].'.</p>
		</section>
	</body>
</html>
		';
	}
}
