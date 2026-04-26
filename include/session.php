<?php

final class Session
{
	/*
	
	Here are the functions to restrict access to various parts of the website.
	
	Most of them repeat each other now. This behavior may change in the future.
	
	*/
	
	// ---------------- //
	// Function-Helpers //
	// ---------------- //
	
	public static function entityIsUnchecked(array $entity): bool
	{
		return $entity['status'] === 'unchecked';
	}
	
	public static function entityIsChecked(array $entity): bool
	{
		return $entity['status'] === 'checked';
	}
	
	public static function entityIsHidden(array $entity): bool
	{
		return $entity['status'] === 'hidden';
	}
	
	public static function agentIs(int|null $id): bool
	{
		if (isset($_SESSION['user']['id']) && isset($id))
			return $_SESSION['user']['id'] === $id;
		
		return false;
	}
	
	public static function agentIsVisitor(): bool
	{
		return $_SESSION['user']['role'] === 'visitor';
	}
	
	public static function agentIsViolator(): bool
	{
		return $_SESSION['user']['role'] === 'violator';
	}
	
	public static function agentIsUser(): bool
	{
		return $_SESSION['user']['role'] === 'user';
	}
	
	public static function agentIsAdministrator(): bool
	{
		return $_SESSION['user']['role'] === 'administrator';
	}
	
	// -------------------------------------------------------------- //
	// Viewing: if the entry is hidden, block its button or throw 451 //
	// -------------------------------------------------------------- //
	
	private static function agentHasRightToViewEntity(array $entity): AccessState
	{
		if (Session::agentIsAdministrator())
			return AccessState::Ok;
		
		if (Session::entityIsHidden($entity))
			return AccessState::EntityIsHiddenError;
		
		return AccessState::Ok;
	}
	
	public static function agentHasRightToViewGame(array $game): AccessState
	{
		return Session::agentHasRightToViewEntity($game);
	}
	
	public static function agentHasRightToViewAlbum(array $album): AccessState
	{
		return Session::agentHasRightToViewEntity($album);
	}
	
	public static function agentHasRightToViewArtist(array $artist): AccessState
	{
		return Session::agentHasRightToViewEntity($artist);
	}
	
	public static function agentHasRightToViewCharacter(array $character): AccessState
	{
		return Session::agentHasRightToViewEntity($character);
	}
	
	public static function agentHasRightToViewSong(array $song): AccessState
	{
		return Session::agentHasRightToViewEntity($song);
	}
	
	public static function agentHasRightToViewLyrics(array $song): AccessState
	{
		return Session::agentHasRightToViewEntity($song);
	}
	
	public static function agentHasRightToViewTranslation(array $translation): AccessState
	{
		return Session::agentHasRightToViewEntity($translation);
	}
	
	// ----------------------------------------------------------------- //
	// Reporting: if the entry is hidden, block its button or throw 451  //
	// ----------------------------------------------------------------- //
	
	private static function agentHasRightToReportEntity(array $entity): AccessState
	{
		return Session::agentHasRightToViewEntity($entity);
	}
	
	public static function agentHasRightToReportGame(array $game)
	{
		return Session::agentHasRightToReportEntity($game);
	}
	
	public static function agentHasRightToReportAlbum(array $album)
	{
		return Session::agentHasRightToReportEntity($album);
	}
	
	public static function agentHasRightToReportArtist(array $artist)
	{
		return Session::agentHasRightToReportEntity($artist);
	}
	
	public static function agentHasRightToReportCharacter(array $character)
	{
		return Session::agentHasRightToReportEntity($character);
	}
	
	public static function agentHasRightToReportSong(array $song)
	{
		return Session::agentHasRightToReportEntity($song);
	}
	
	public static function agentHasRightToReportLyrics(array $song)
	{
		return Session::agentHasRightToReportEntity($song);
	}
	
	public static function agentHasRightToReportTranslation(array $translation)
	{
		return Session::agentHasRightToReportEntity($translation);
	}
	
	// ---------------------------------------------------------------------- //
	// Adding: if the user is not allowed, then block the button or throw 403 //
	// ---------------------------------------------------------------------- //
	
	private static function agentHasRightToAddEntity(): AccessState
	{
		if (Session::agentIsAdministrator())
			return AccessState::Ok;
		
		if (Session::agentIsVisitor())
			return AccessState::AgentIsVisitorError;
		
		if (Session::agentIsViolator())
			return AccessState::AgentIsViolatorError;
		
		return AccessState::Ok;
	}
	
	public static function agentHasRightToAddGame(): AccessState
	{
		return Session::agentHasRightToAddEntity();
	}
	
	public static function agentHasRightToAddAlbum(): AccessState
	{
		return Session::agentHasRightToAddEntity();
	}
	
	public static function agentHasRightToAddArtist(): AccessState
	{
		return Session::agentHasRightToAddEntity();
	}
	
	public static function agentHasRightToAddCharacter(): AccessState
	{
		return Session::agentHasRightToAddEntity();
	}
	
	public static function agentHasRightToAddSong(): AccessState
	{
		return Session::agentHasRightToAddEntity();
	}
	
	public static function agentHasRightToAddLyrics(): AccessState
	{
		return Session::agentHasRightToAddEntity();
	}
	
	public static function agentHasRightToAddTranslation(): AccessState
	{
		return Session::agentHasRightToAddEntity();
	}
	
	// -------------------------------------------------------------------------- //
	// Editing: if the user is not the author, then block the button or throw 403 //
	// -------------------------------------------------------------------------- //
	
	private static function agentHasRightToEditEntity(array $entity): AccessState
	{
		if (Session::agentIsAdministrator())
			return AccessState::Ok;
		
		if (Session::entityIsChecked($entity))
			return AccessState::EntityIsCheckedError;
		
		if (!Session::agentIs($entity['user_added_id']))
			return AccessState::AgentIsNotAuthorError;
		
		if (Session::agentIs($entity['user_added_id']) && Session::agentIsViolator())
			return AccessState::AgentIsViolatorError;
		
		return AccessState::Ok;
	}
	
	public static function agentHasRightToEditGame(array $game): AccessState
	{
		return Session::agentHasRightToEditEntity($game);
	}
	
	public static function agentHasRightToEditAlbum(array $album): AccessState
	{
		return Session::agentHasRightToEditEntity($album);
	}
	
	public static function agentHasRightToEditArtist(array $artist): AccessState
	{
		return Session::agentHasRightToEditEntity($artist);
	}
	
	public static function agentHasRightToEditCharacter(array $character): AccessState
	{
		return Session::agentHasRightToEditEntity($character);
	}
	
	public static function agentHasRightToEditSong(array $song): AccessState
	{
		return Session::agentHasRightToEditEntity($song);
	}
	
	public static function agentHasRightToEditLyrics(array $song): AccessState
	{
		return Session::agentHasRightToEditEntity($song);
	}
	
	public static function agentHasRightToEditTranslation(array $translation): AccessState
	{
		if (Session::agentIsAdministrator())
			return AccessState::Ok;
		
		if (!Session::agentIs($translation['user_added_id']))
			return AccessState::AgentIsNotAuthorError;
		
		if (Session::agentIs($translation['user_added_id']) && Session::agentIsViolator())
			return AccessState::AgentIsViolatorError;
		
		return AccessState::Ok;
	}
	
	// ---------------------------------------------------------------------------- //
	// Deleting: if the user is not the author, then block the button or throw 403  //
	// ---------------------------------------------------------------------------- //
	
	private static function agentHasRightToDeleteEntity(array $entity): AccessState
	{
		return Session::agentHasRightToEditEntity($entity);
	}
	
	public static function agentHasRightToDeleteGame(array $game): AccessState
	{
		return Session::agentHasRightToDeleteEntity($game);
	}
	
	public static function agentHasRightToDeleteAlbum(array $album): AccessState
	{
		return Session::agentHasRightToDeleteEntity($album);
	}
	
	public static function agentHasRightToDeleteArtist(array $artist): AccessState
	{
		return Session::agentHasRightToDeleteEntity($artist);
	}
	
	public static function agentHasRightToDeleteCharacter(array $character): AccessState
	{
		return Session::agentHasRightToDeleteEntity($character);
	}
	
	public static function agentHasRightToDeleteSong(array $song): AccessState
	{
		return Session::agentHasRightToDeleteEntity($song);
	}
	
	public static function agentHasRightToDeleteLyrics(array $song): AccessState
	{
		return Session::agentHasRightToDeleteEntity($song);
	}
	
	public static function agentHasRightToDeleteTranslation(array $translation): AccessState
	{
		return Session::agentHasRightToEditTranslation($translation);
	}
}
