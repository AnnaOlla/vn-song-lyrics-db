<?php

final class Cryptography
{
	public static function isPasswordCorrect(string $password, string $hash): bool
	{
		throw new DatabaseLogicException(__METHOD__.' not implemented');
	}
	
	public static function generatePasswordHash(string $password): string
	{
		throw new DatabaseLogicException(__METHOD__.' not implemented');
	}
	
	public static function generateSimpleHash(string $data): string
	{
		throw new DatabaseLogicException(__METHOD__.' not implemented');
	}
	
	public static function encryptData(string $data): string
	{
		throw new DatabaseLogicException(__METHOD__.' not implemented');
	}
	
	public static function decryptData(string $bytes): string
	{
		throw new DatabaseLogicException(__METHOD__.' not implemented');
	}
	
	public static function createToken(string $data): string
	{
		throw new DatabaseLogicException(__METHOD__.' not implemented');
	}
}
