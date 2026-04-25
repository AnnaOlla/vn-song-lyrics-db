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
			\Localization\ErrorPage\BadRequest400\Reason,
			\Localization\ErrorPage\BadRequest400\Hint
		);
	}
	
	final public function renderUnauthorized401(): void
	{
		$this->renderError
		(
			'401 Unauthorized',
			\Localization\ErrorPage\Unauthorized401\Reason,
			\Localization\ErrorPage\Unauthorized401\Hint
		);
	}
	
	final public function renderPaymentRequired402(): void
	{
		$this->renderError
		(
			'402 Payment Required',
			\Localization\ErrorPage\PaymentRequired402\Reason,
			\Localization\ErrorPage\PaymentRequired402\Hint
		);
	}
	
	final public function renderForbidden403(): void
	{
		$this->renderError
		(
			'403 Forbidden',
			\Localization\ErrorPage\Forbidden403\Reason,
			\Localization\ErrorPage\Forbidden403\Hint
		);
	}
	
	final public function renderNotFound404(): void
	{
		$this->renderError
		(
			'404 Not Found',
			\Localization\ErrorPage\NotFound404\Reason,
			\Localization\ErrorPage\NotFound404\Hint
		);
	}
	
	final public function renderMethodNotAllowed405(): void
	{
		$this->renderError
		(
			'405 Method Not Allowed',
			\Localization\ErrorPage\MethodNotAllowed405\Reason,
			\Localization\ErrorPage\MethodNotAllowed405\Hint
		);
	}
	
	final public function renderNotAcceptable406(): void
	{
		$this->renderError
		(
			'406 Not Acceptable',
			\Localization\ErrorPage\NotAcceptable406\Reason,
			\Localization\ErrorPage\NotAcceptable406\Hint
		);
	}
	
	final public function renderConflict409(): void
	{
		$this->renderError
		(
			'409 Conflict',
			\Localization\ErrorPage\Conflict409\Reason,
			\Localization\ErrorPage\Conflict409\Hint
		);
	}
	
	final public function renderContentTooLarge413(): void
	{
		$this->renderError
		(
			'413 Content Too Large',
			\Localization\ErrorPage\ContentTooLarge413\Reason,
			\Localization\ErrorPage\ContentTooLarge413\Hint
		);
	}
	
	final public function renderUnsupportedMediaType415(): void
	{
		$this->renderError
		(
			'415 Unsupported Media Type',
			\Localization\ErrorPage\UnsupportedMediaType415\Reason,
			\Localization\ErrorPage\UnsupportedMediaType415\Hint
		);
	}
	
	final public function renderUnprocessableEntity422(): void
	{
		$this->renderError
		(
			'422 Unprocessable Entity',
			\Localization\ErrorPage\UnprocessableEntity422\Reason,
			\Localization\ErrorPage\UnprocessableEntity422\Hint
		);
	}
	
	final public function renderUnavailableForLegalReasons451(): void
	{
		$this->renderError
		(
			'451 Unavailable For Legal Reasons',
			\Localization\ErrorPage\UnavailableForLegalReasons451\Reason,
			\Localization\ErrorPage\UnavailableForLegalReasons451\Hint
		);
	}
	
	final public function renderInternalServerError500(): void
	{
		$this->renderError
		(
			'500 Internal Server Error',
			\Localization\ErrorPage\InternalServerError500\Reason,
			\Localization\ErrorPage\InternalServerError500\Hint
		);
	}
	
	final public function renderNotImplemented501(): void
	{
		$this->renderError
		(
			'501 Not Implemented',
			\Localization\ErrorPage\NotImplemented501\Reason,
			\Localization\ErrorPage\NotImplemented501\Hint
		);
	}
	
	final public function renderBadGateway502(): void
	{
		$this->renderError
		(
			'502 Bad Gateway',
			\Localization\ErrorPage\BadGateway502\Reason,
			\Localization\ErrorPage\BadGateway502\Hint
		);
	}
	
	final public function renderServiceUnavailable503(): void
	{
		$this->renderError
		(
			'503 Service Unavailable',
			\Localization\ErrorPage\ServiceUnavailable503\Reason,
			\Localization\ErrorPage\ServiceUnavailable503\Hint
		);
	}
}
