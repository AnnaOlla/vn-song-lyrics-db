<?php

final class JuliamoCaptcha
{
	private const FONT_PATH = 'fonts/juliamo.ttf';
	
	private static function generateCharacters(int $length, int $strength): array
	{
		$strongCharacters = mb_str_split('bfgrvwzBFGRVWZ');
		$weakCharacters   = mb_str_split('acdehijklmnopqstuxyACDEHIJKLMNOPQSTUXY');
		
		$randomCharacters = [];
		
		for ($i = 0; $i < $strength; $i++)
		{
			$randomIndex        = random_int(0, count($strongCharacters) - 1);
			$randomCharacters[] = $strongCharacters[$randomIndex];
		}
		
		for ($i = 0; $i < $length - $strength; $i++)
		{
			$randomIndex        = random_int(0, count($weakCharacters) - 1);
			$randomCharacters[] = $weakCharacters[$randomIndex];
		}
		
		shuffle($randomCharacters);
		return $randomCharacters;
	}
	
	private static function generateNoise
	(
		GDImage &$image,
		int     $minIntensity,
		int     $maxIntensity,
		int     $alpha
	): void
	{
		$width  = imagesx($image);
		$height = imagesy($image);
		
		for ($i = 0; $i < $width; $i++)
		{
			for ($j = 0; $j < $height; $j++)
			{
				$r = rand($minIntensity, $maxIntensity);
				$g = rand($minIntensity, $maxIntensity);
				$b = rand($minIntensity, $maxIntensity);
				
				$color = imagecolorallocatealpha($image, $r, $g, $b, $alpha);
				
				imagesetpixel($image, $i, $j, $color);
			}
		}
	}
	
	private static function generateColor(GDImage &$image)
	{
		$colors = 
		[
			[000, 000, 000, 000],
			[000, 000, 255, 000],
			[000, 255, 000, 000],
			[000, 255, 255, 000],
			[255, 000, 000, 000],
			[255, 000, 255, 000],
			[255, 255, 000, 000],
			[255, 255, 255, 000]
		];
		
		$index = random_int(0, count($colors) - 1);
		$color = imagecolorallocatealpha($image, ...$colors[$index]);
		
		return $color;
	}
	
	public static function generateBase64Captcha
	(
		int $length,
		int $strength,
		int $widthPx = 192,
		int $heightPx = 48,
	): array
	{
		// How it works:
		// 1. Layer of noise
		// 2. Juliamo Letters (strong = misdetected letters, weak = usual letter)
		// 3. Half-transparent layer of noise
		
		// All constants are calculated empirically for the Juliamo font
		// Intensity and Transparence for generated noise are also chosen empirically
		
		// 1. Generate noise
		$image = imagecreatetruecolor($widthPx, $heightPx);
		self::generateNoise($image, 64, 192, 0);
		
		// 2. Generate letters
		$string = self::generateCharacters($length, $strength);
		
		// --A--A--A--A--A--A--
		// Gap between letters       = (widthPx / count + 1)
		// Gap before first letter   = (widthPx / count + 1)
		//
		// widthPx of a Juliamo letter = 192/418 * heightPx
		// To align with the center  = (192/418 * heightPx) / 2
		
		$xOffset  = $widthPx / (count($string) + 1);
		$xStart   = $xOffset - $heightPx * 192 / 418 / 2;
		$yOffset  = 0;
		$yStart   = $heightPx * 5 / 6;
		$fontSize = round($heightPx * 2 / 3);
		
		for ($i = 0; $i < count($string); $i++)
		{
			// A little randomization to make it harder
			$x     = round($xStart + $xOffset * $i) + random_int(-2, 2);
			$y     = round($yStart + $yOffset * $i) + random_int(-2, 2);
			$angle = random_int(-10, +10);
			$color = self::generateColor($image);
			
			imagettftext($image, $fontSize, $angle, $x, $y, $color, self::FONT_PATH, $string[$i]);
		}
		
		// 3. Generate half-transparent noise
		self::generateNoise($image, 64, 192, 96);
		
		// 4. Save as PNG
		ob_start();
		imagepng($image);
		$binaryData = ob_get_clean();
		
		$encodedImage = 'data:image/png;base64,'.base64_encode($binaryData);
		imagedestroy($image);
		
		// 5. Join the generated symbols into a string
		$solution = implode('', $string);
		
		return [$solution, $encodedImage];
	}
}
