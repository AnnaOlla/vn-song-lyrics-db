<?php

final class RoleManager
{
	// -------------------------------------------------------------- //
	// Viewing: if the entry is hidden, block its button or throw 451 //
	// -------------------------------------------------------------- //
	
	private static function getStateToViewEntity(array $entity): AccessState
	{
		if (Authorizer::isCurrentUserAdministrator())
			return AccessState::Ok;
		
		if (EntityManager::isEntityHidden($entity))
			return AccessState::EntityIsHiddenError;
		
		return AccessState::Ok;
	}
	
	public static function getStateToViewGame(array $game): AccessState
	{
		return self::getStateToViewEntity($game);
	}
	
	public static function getStateToViewAlbum(array $album): AccessState
	{
		return self::getStateToViewEntity($album);
	}
	
	public static function getStateToViewArtist(array $artist): AccessState
	{
		return self::getStateToViewEntity($artist);
	}
	
	public static function getStateToViewCharacter(array $character): AccessState
	{
		return self::getStateToViewEntity($character);
	}
	
	public static function getStateToViewSong(array $song): AccessState
	{
		return self::getStateToViewEntity($song);
	}
	
	public static function getStateToViewLyrics(array $song): AccessState
	{
		return self::getStateToViewEntity($song);
	}
	
	public static function getStateToViewTranslation(array $translation): AccessState
	{
		return self::getStateToViewEntity($translation);
	}
	
	// ----------------------------------------------------------------- //
	// Reporting: if the entry is hidden, block its button or throw 451  //
	// ----------------------------------------------------------------- //
	
	private static function getStateToReportEntity(array $entity): AccessState
	{
		return self::getStateToViewEntity($entity);
	}
	
	public static function getStateToReportGame(array $game)
	{
		return self::getStateToReportEntity($game);
	}
	
	public static function getStateToReportAlbum(array $album)
	{
		return self::getStateToReportEntity($album);
	}
	
	public static function getStateToReportArtist(array $artist)
	{
		return self::getStateToReportEntity($artist);
	}
	
	public static function getStateToReportCharacter(array $character)
	{
		return self::getStateToReportEntity($character);
	}
	
	public static function getStateToReportSong(array $song)
	{
		return self::getStateToReportEntity($song);
	}
	
	public static function getStateToReportLyrics(array $song)
	{
		return self::getStateToReportEntity($song);
	}
	
	public static function getStateToReportTranslation(array $translation)
	{
		return self::getStateToReportEntity($translation);
	}
	
	// ---------------------------------------------------------------------- //
	// Adding: if the user is not allowed, then block the button or throw 403 //
	// ---------------------------------------------------------------------- //
	
	private static function getStateToAddEntity(): AccessState
	{
		if (Authorizer::isCurrentUserAdministrator())
			return AccessState::Ok;
		
		if (Authorizer::isCurrentUserVisitor())
			return AccessState::AgentIsVisitorError;
		
		if (Authorizer::isCurrentUserViolator())
			return AccessState::AgentIsViolatorError;
		
		return AccessState::Ok;
	}
	
	public static function getStateToAddGame(): AccessState
	{
		return self::getStateToAddEntity();
	}
	
	public static function getStateToAddAlbum(): AccessState
	{
		return self::getStateToAddEntity();
	}
	
	public static function getStateToAddArtist(): AccessState
	{
		return self::getStateToAddEntity();
	}
	
	public static function getStateToAddCharacter(): AccessState
	{
		return self::getStateToAddEntity();
	}
	
	public static function getStateToAddSong(): AccessState
	{
		return self::getStateToAddEntity();
	}
	
	public static function getStateToAddLyrics(): AccessState
	{
		return self::getStateToAddEntity();
	}
	
	public static function getStateToAddTranslation(): AccessState
	{
		return self::getStateToAddEntity();
	}
	
	// -------------------------------------------------------------------------- //
	// Editing: if the user is not the author, then block the button or throw 403 //
	// -------------------------------------------------------------------------- //
	
	private static function getStateToEditEntity(array $entity): AccessState
	{
		if (Authorizer::isCurrentUserAdministrator())
			return AccessState::Ok;
		
		if (EntityManager::isEntityChecked($entity))
			return AccessState::EntityIsCheckedError;
		
		if (!Authorizer::isCurrentUser($entity['user_added_id']))
			return AccessState::AgentIsNotAuthorError;
		
		if (Authorizer::isCurrentUser($entity['user_added_id']) && Authorizer::isCurrentUserViolator())
			return AccessState::AgentIsViolatorError;
		
		return AccessState::Ok;
	}
	
	public static function getStateToEditGame(array $game): AccessState
	{
		return self::getStateToEditEntity($game);
	}
	
	public static function getStateToEditAlbum(array $album): AccessState
	{
		return self::getStateToEditEntity($album);
	}
	
	public static function getStateToEditArtist(array $artist): AccessState
	{
		return self::getStateToEditEntity($artist);
	}
	
	public static function getStateToEditCharacter(array $character): AccessState
	{
		return self::getStateToEditEntity($character);
	}
	
	public static function getStateToEditSong(array $song): AccessState
	{
		return self::getStateToEditEntity($song);
	}
	
	public static function getStateToEditLyrics(array $song): AccessState
	{
		return self::getStateToEditEntity($song);
	}
	
	public static function getStateToEditTranslation(array $translation): AccessState
	{
		if (Authorizer::isCurrentUserAdministrator())
			return AccessState::Ok;
		
		if (!Authorizer::isCurrentUser($translation['user_added_id']))
			return AccessState::AgentIsNotAuthorError;
		
		if (Authorizer::isCurrentUser($translation['user_added_id']) && Authorizer::isCurrentUserViolator())
			return AccessState::AgentIsViolatorError;
		
		return AccessState::Ok;
	}
	
	// ---------------------------------------------------------------------------- //
	// Deleting: if the user is not the author, then block the button or throw 403  //
	// ---------------------------------------------------------------------------- //
	
	private static function getStateToDeleteEntity(array $entity): AccessState
	{
		return self::getStateToEditEntity($entity);
	}
	
	public static function getStateToDeleteGame(array $game): AccessState
	{
		return self::getStateToDeleteEntity($game);
	}
	
	public static function getStateToDeleteAlbum(array $album): AccessState
	{
		return self::getStateToDeleteEntity($album);
	}
	
	public static function getStateToDeleteArtist(array $artist): AccessState
	{
		return self::getStateToDeleteEntity($artist);
	}
	
	public static function getStateToDeleteCharacter(array $character): AccessState
	{
		return self::getStateToDeleteEntity($character);
	}
	
	public static function getStateToDeleteSong(array $song): AccessState
	{
		return self::getStateToDeleteEntity($song);
	}
	
	public static function getStateToDeleteLyrics(array $song): AccessState
	{
		return self::getStateToDeleteEntity($song);
	}
	
	public static function getStateToDeleteTranslation(array $translation): AccessState
	{
		return self::getStateToEditTranslation($translation);
	}
}
