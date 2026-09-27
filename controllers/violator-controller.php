<?php

require_once 'controllers/visitor-controller.php';

class ViolatorController extends VisitorController
{
	public function __construct(string $language)
	{
		parent::__construct($language);
		
		require_once 'models/violator-model.php';
		require_once 'views/violator-view.php';

		$this->model = new ViolatorModel;
		$this->view = new ViolatorView($language);
	}
	
	public function handleAddGamePage(): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleAddAlbumPage(): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleAddArtistPage(): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleAddCharacterPage(): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleAddSongPage(string $albumUri): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleAddLyricsPage(string $albumUri, string $songUri): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleAddTranslationPage(string $albumUri, string $songUri): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleEditGamePage(string $gameUri): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleEditAlbumPage(string $albumUri): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleEditArtistPage(string $artistUri): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleEditCharacterPage(string $characterUri): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleEditSongPage(string $albumUri, string $songUri): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleEditLyricsPage(string $albumUri, string $songUri): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleEditTranslationPage(string $albumUri, string $songUri, string $translationUri): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleDeleteGamePage(string $gameUri): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleDeleteAlbumPage(string $albumUri): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleDeleteArtistPage(string $artistUri): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleDeleteCharacterPage(string $characterUri): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleDeleteSongPage(string $albumUri, string $songUri): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleDeleteLyricsPage(string $albumUri, string $songUri): void
	{
		throw new HttpForbidden403();
	}
	
	public function handleDeleteTranslationPage(string $albumUri, string $songUri, string $translationUri): void
	{
		throw new HttpForbidden403();
	}
}
