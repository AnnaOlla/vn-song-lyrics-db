<?php

require_once 'views/view.php';

class ErrorView extends View
{
	public function __construct(string $language)
	{
		parent::__construct($language);
	}
	
	private function renderError(string $codename, string $reason, string $hint): void
	{
		$html = $this->startRender
		(
			title:        $codename,
			cssSheetUris: ['/css/window-in-center-page.css']
		);
		
		$html .=
		'
		<article>
			<section>
				<h1>'.htmlspecialchars($codename).'</h1>
				<p>'.htmlspecialchars($reason).'</p>
				<p>'.htmlspecialchars($hint).'</p>
			</section>
		</article>
		';
		
		$html .= $this->endRender();
		
		echo $html;
	}
	
	final public function renderBadRequest400(): void
	{
		$this->renderError
		(
			'400 Bad Request',
			\Localization\ErrorPage\textBadRequest1,
			\Localization\ErrorPage\textBadRequest2
		);
	}
	
	final public function renderUnauthorized401(): void
	{
		$this->renderError
		(
			'401 Unauthorized',
			\Localization\ErrorPage\textUnauthorized1,
			\Localization\ErrorPage\textUnauthorized2
		);
	}
	
	final public function renderPaymentRequired402(): void
	{
		$this->renderError
		(
			'402 Payment Required',
			\Localization\ErrorPage\textPaymentRequired1,
			\Localization\ErrorPage\textPaymentRequired2
		);
	}
	
	final public function renderForbidden403(): void
	{
		$this->renderError
		(
			'403 Forbidden',
			\Localization\ErrorPage\textForbidden1,
			\Localization\ErrorPage\textForbidden2
		);
	}
	
	final public function renderNotFound404(): void
	{
		$this->renderError
		(
			'404 Not Found',
			\Localization\ErrorPage\textNotFound1,
			\Localization\ErrorPage\textNotFound2
		);
	}
	
	final public function renderMethodNotAllowed405(): void
	{
		$this->renderError
		(
			'405 Method Not Allowed',
			\Localization\ErrorPage\textMethodNotAllowed1,
			\Localization\ErrorPage\textMethodNotAllowed2
		);
	}
	
	final public function renderNotAcceptable406(): void
	{
		$this->renderError
		(
			'406 Not Acceptable',
			\Localization\ErrorPage\textNotAcceptable1,
			\Localization\ErrorPage\textNotAcceptable2
		);
	}
	
	final public function renderUnavailableForLegalReasons451(): void
	{
		$this->renderError
		(
			'451 Unavailable For Legal Reasons',
			\Localization\ErrorPage\textUnavailableForLegalReasons1,
			\Localization\ErrorPage\textUnavailableForLegalReasons2
		);
	}
	
	final public function renderInternalServerError500(): void
	{
		$this->renderError
		(
			'500 Internal Server Error',
			\Localization\ErrorPage\textInternalServerError1,
			\Localization\ErrorPage\textInternalServerError2
		);
	}
	
	final public function renderNotImplemented501(): void
	{
		$this->renderError
		(
			'501 Not Implemented',
			\Localization\ErrorPage\textNotImplemented1,
			\Localization\ErrorPage\textNotImplemented2
		);
	}
	
	final public function renderBadGateway502(): void
	{
		$this->renderError
		(
			'502 Bad Gateway',
			\Localization\ErrorPage\textBadGateway1,
			\Localization\ErrorPage\textBadGateway2
		);
	}
	
	final public function renderServiceUnavailable503(): void
	{
		$this->renderError
		(
			'503 Service Unavailable',
			\Localization\ErrorPage\textServiceUnavailable1,
			\Localization\ErrorPage\textServiceUnavailable2
		);
	}
}
