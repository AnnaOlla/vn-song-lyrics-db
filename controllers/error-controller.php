<?php

require_once 'controllers/controller.php';

class ErrorController extends Controller
{
	public function __construct(string $language)
	{
		parent::__construct($language);
		
		require_once 'models/error-model.php';
		require_once 'views/error-view.php';

		$this->model = new ErrorModel;
		$this->view = new ErrorView($language);
	}
	
	final public function handleRedirect(string $location): void
	{
		http_response_code(303);
		header('Location: '.$location);
	}
	
	final public function handleBadRequest400(): void
	{
		http_response_code(400);
		$this->view->renderBadRequest400();
	}
	
	final public function handleUnauthorized401(): void
	{
		http_response_code(401);
		$this->view->renderUnauthorized401();
	}
	
	final public function handlePaymentRequired402(): void
	{
		http_response_code(402);
		$this->view->renderPaymentRequired402();
	}
	
	final public function handleForbidden403(): void
	{
		http_response_code(403);
		$this->view->renderForbidden403();
	}
	
	final public function handleNotFound404(): void
	{
		http_response_code(404);
		$this->view->renderNotFound404();
	}
	
	final public function handleMethodNotAllowed405(): void
	{
		http_response_code(405);
		$this->view->renderMethodNotAllowed405();
	}
	
	final public function handleNotAcceptable406(): void
	{
		http_response_code(406);
		$this->view->renderNotAcceptable406();
	}
	
	final public function handleUnavailableForLegalReasons451(): void
	{
		http_response_code(451);
		$this->view->renderUnavailableForLegalReasons451();
	}
	
	final public function handleInternalServerError500(): void
	{
		http_response_code(500);
		$this->view->renderInternalServerError500();
	}
	
	final public function handleNotImplemented501(): void
	{
		http_response_code(501);
		$this->view->renderNotImplemented501();
	}
	
	final public function handleBadGateway502(): void
	{
		http_response_code(502);
		$this->view->renderBadGateway502();
	}
	
	final public function handleServiceUnavailable503(): void
	{
		http_response_code(503);
		$this->view->renderServiceUnavailable503();
	}
}
