<?php

require_once 'controllers/visitor-controller.php';

class ViolatorController extends VisitorController
{
	public function __construct(string $language)
	{
		parent::__construct($language);
		
		require_once 'models/violator-model.php';
		require_once 'views/violator-view.php';

		$this->model = new VisitorModel;
		$this->view = new VisitorView($language);
	}
	
	final public function handleLogInPage(): void
	{
		$this->handleRedirect(buildInternalLink($this->language));
	}
	
	final public function handleSignUpPage(): void
	{
		$this->handleRedirect(buildInternalLink($this->language));
	}
	
	final public function handleLogOutPage(): void
	{
		$this->deleteUserSession();
		$this->handleRedirect($_SERVER['HTTP_REFERER']);
	}
	
	final protected function deleteUserSession(): void
	{
		session_unset();
		setcookie(session_name(), session_id(), time() - 60 * 60 * 60 * 24);
		session_destroy();
	}
	
	public function handleAddGamePage(): void
	{
		$this->handleForbidden();
	}
	
	public function handleAddAlbumPage(): void
	{
		$this->handleForbidden();
	}
	
	public function handleAddArtistPage(): void
	{
		$this->handleForbidden();
	}
	
	public function handleAddCharacterPage(): void
	{
		$this->handleForbidden();
	}
	
	public function handleAddSongPage(string $albumUri): void
	{
		$this->handleForbidden();
	}
	
	public function handleAddLyricsPage(string $albumUri, string $songUri): void
	{
		$this->handleForbidden();
	}
	
	public function handleAddTranslationPage(string $albumUri, string $songUri): void
	{
		$this->handleForbidden();
	}
	
	public function handleEditGamePage(string $gameUri): void
	{
		$this->handleForbidden();
	}
	
	public function handleEditAlbumPage(string $albumUri): void
	{
		$this->handleForbidden();
	}
	
	public function handleEditArtistPage(string $artistUri): void
	{
		$this->handleForbidden();
	}
	
	public function handleEditCharacterPage(string $characterUri): void
	{
		$this->handleForbidden();
	}
	
	public function handleEditSongPage(string $albumUri, string $songUri): void
	{
		$this->handleForbidden();
	}
	
	public function handleEditLyricsPage(string $albumUri, string $songUri): void
	{
		$this->handleForbidden();
	}
	
	public function handleEditTranslationPage(string $albumUri, string $songUri, string $translationUri): void
	{
		$this->handleForbidden();
	}
	
	public function handleDeleteGamePage(string $gameUri): void
	{
		$this->handleForbidden();
	}
	
	public function handleDeleteAlbumPage(string $albumUri): void
	{
		$this->handleForbidden();
	}
	
	public function handleDeleteArtistPage(string $artistUri): void
	{
		$this->handleForbidden();
	}
	
	public function handleDeleteCharacterPage(string $characterUri): void
	{
		$this->handleForbidden();
	}
	
	public function handleDeleteSongPage(string $albumUri, string $songUri): void
	{
		$this->handleForbidden();
	}
	
	public function handleDeleteLyricsPage(string $albumUri, string $songUri): void
	{
		$this->handleForbidden();
	}
	
	public function handleDeleteTranslationPage(string $albumUri, string $songUri, string $translationUri): void
	{
		$this->handleForbidden();
	}
}
