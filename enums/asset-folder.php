<?php

enum AssetFolder: string
{
	case Base       = 'assets';
	case Games      = 'game-logos';
	case Albums     = 'album-covers';
	case Artists    = 'artist-photos';
	case Characters = 'character-images';
	case Static     = 'static-images';
}
