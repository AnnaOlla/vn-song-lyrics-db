<?php

final class EntityManager
{
	public static function isEntityUnchecked(array $entity): bool
	{
		return $entity['status'] === 'unchecked';
	}
	
	public static function isEntityChecked(array $entity): bool
	{
		return $entity['status'] === 'checked';
	}
	
	public static function isEntityHidden(array $entity): bool
	{
		return $entity['status'] === 'hidden';
	}
}
