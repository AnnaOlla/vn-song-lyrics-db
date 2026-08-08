<?php

final class ImageProcession
{
	private const IMAGE_MAX_WIDTH  = 384;
	private const IMAGE_MAX_HEIGHT = 384;
	private const IMAGE_GAUSSIAN_BLUR_ITERATION_COUNT = 25;
	
	public static function readUploadedImage(array& $file): bool
	{
		$file = imagecreatefromstring(file_get_contents($file['tmp_name']));
		
		if (!$file)
			return false;
		
		return true;
	}
	
	public static function convertImageToTrueColor(GdImage& $image): bool
	{
		if (imageistruecolor($image))
			return true;
		
		$image = imagepalettetotruecolor($image);
		
		if (!$image)
			return false;
		
		return true;
	}
	
	public static function scaleDownImage(GdImage& $image): bool
	{
		$sourceWidth  = imagesx($image);
		$sourceHeight = imagesy($image);
		
		if ($sourceWidth <= self::IMAGE_MAX_WIDTH && $sourceHeight <= self::IMAGE_MAX_HEIGHT)
			return true;
		
		if ($sourceWidth < $sourceHeight)
		{
			$ratio = $sourceHeight / self::IMAGE_MAX_HEIGHT;
			
			$targetWidth  = (int)round($sourceWidth / $ratio);
			$targetHeight = self::IMAGE_MAX_HEIGHT;
		}
		else if ($sourceWidth > $sourceHeight)
		{
			$ratio = $sourceWidth / self::IMAGE_MAX_WIDTH;
			
			$targetWidth  = self::IMAGE_MAX_WIDTH;
			$targetHeight = (int)round($sourceHeight / $ratio);
		}
		else
		{
			$targetWidth  = self::IMAGE_MAX_WIDTH;
			$targetHeight = self::IMAGE_MAX_HEIGHT;
		}
		
		$image = imagescale($image, $targetWidth, $targetHeight, IMG_BICUBIC);
		
		if (!$image)
			return false;
		
		return true;
	}
	
	public static function applyBlurredBackdropToImage(GdImage& $image): bool
	{
		// If the image is not square, then use blurred backdrop to make it square
		
		$sourceWidth  = imagesx($image);
		$sourceHeight = imagesy($image);
		
		if ($sourceWidth === $sourceHeight)
			return true;
		
		if ($sourceWidth < $sourceHeight)
		{
			$ratio        = $sourceHeight / $sourceWidth;
			$targetWidth  = $sourceHeight;
			$targetHeight = (int)round($sourceHeight * $ratio);
			
			$backdrop = imagescale($image, $targetWidth, $targetHeight, IMG_BICUBIC);
			
			$x            = 0;
			$y            = (int)round(($targetHeight - $sourceHeight) / 2);
			$targetWidth  = $targetWidth;
			$targetHeight = $targetWidth;
			$rectangle    = ['x' => $x, 'y' => $y, 'width' => $targetWidth, 'height' => $targetHeight];
			
			$backdrop = imagecrop($backdrop, $rectangle);
			
			for ($i = 0; $i < self::IMAGE_GAUSSIAN_BLUR_ITERATION_COUNT; $i++)
				imagefilter($backdrop, IMG_FILTER_GAUSSIAN_BLUR);
			
			$x = (int)round(($targetWidth - $sourceWidth) / 2);
			$y = 0;
			
			imagecopy($backdrop, $image, $x, $y, 0, 0, $sourceWidth, $sourceHeight);
		}
		else
		{
			$ratio        = $sourceWidth / $sourceHeight;
			$targetWidth  = (int)round($sourceWidth * $ratio);
			$targetHeight = $sourceWidth;
			
			$backdrop = imagescale($image, $targetWidth, $targetHeight, IMG_BICUBIC);
			
			$x            = (int)round(($targetWidth - $sourceWidth) / 2);
			$y            = 0;
			$targetWidth  = $targetHeight;
			$targetHeight = $targetHeight;
			$rectangle    = ['x' => $x, 'y' => $y, 'width' => $targetWidth, 'height' => $targetHeight];
			
			$backdrop = imagecrop($backdrop, $rectangle);
			
			for ($i = 0; $i < self::IMAGE_GAUSSIAN_BLUR_ITERATION_COUNT; $i++)
				imagefilter($backdrop, IMG_FILTER_GAUSSIAN_BLUR);
			
			$x = 0;
			$y = (int)round(($targetHeight - $sourceHeight) / 2);
			
			imagecopy($backdrop, $image, $x, $y, 0, 0, $sourceWidth, $sourceHeight);
		}
		
		$image = $backdrop;
		
		if (!$image)
			return false;
		
		return true;
	}
	
	public static function saveUploadedImage(GdImage& $image, string $fullPath): bool
	{
		return imagewebp($image, $fullPath, 100);
	}
}
