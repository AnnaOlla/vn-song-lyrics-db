<?php

final class JuliamoCaptcha
{
	private const FONT_PATH = 'fonts/juliamo.ttf';
	
	private static function convertHsvaToRgba(array $hsva): array
	{
		$h = $hsva['h'] / 360;
		$s = $hsva['s'] / 100;
		$v = $hsva['v'] / 100;
		$a = $hsva['a'];

		$i = floor($h * 6);
		$f = $h * 6 - $i;
		$p = $v * (1 - $s);
		$q = $v * (1 - $f * $s);
		$t = $v * (1 - (1 - $f) * $s);

		switch ($i % 6)
		{
			case 0: $r = $v; $g = $t; $b = $p; break;
			case 1: $r = $q; $g = $v; $b = $p; break;
			case 2: $r = $p; $g = $v; $b = $t; break;
			case 3: $r = $p; $g = $q; $b = $v; break;
			case 4: $r = $t; $g = $p; $b = $v; break;
			case 5: $r = $v; $g = $p; $b = $q; break;
		}

		return
		[
			'r' => round($r * 255),
			'g' => round($g * 255),
			'b' => round($b * 255),
			'a' => round($a * 255)
		];
	}
	
	private static function generateCharacters(int $length, int $strength): array
	{
		$strongCharacters = mb_str_split('BFGRVWZ');
		$weakCharacters   = mb_str_split('ACDEHIJKLMNOPQSTUXY');
		
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
		$hsva = 
		[
			'h' => rand(30, 180),
			's' => rand(90, 100),
			'v' => rand(90, 100),
			'a' => 1
		];
		
		$rgba = self::convertHsvaToRgba($hsva);
		
		$color = imagecolorallocatealpha
		(
			$image,
			$rgba['r'],
			$rgba['g'],
			$rgba['b'],
			(int)((255 - $rgba['a']) / 2)
		);
		
		return $color;
	}
	
	public static function generateBase64Captcha
	(
		int    $length,
		int    $strength,
		int    $widthPx      = 192,
		int    $heightPx     = 48,
		string $outputFormat = 'webp',
		array  $outputParams = ['quality' => 80]
	): array
	{
		// How it works:
		// 1. Layer of noise
		// 2. Juliamo Letters (strong = misdetected letters, weak = common letters)
		// 3. Half-transparent layer of noise
		
		// All constants are calculated empirically for the Juliamo font
		// Intensity and Transparence for generated noise are also chosen empirically
		
		// 1. Generate noise
		$image = imagecreatetruecolor($widthPx, $heightPx);
		self::generateNoise($image, 64, 192, 0);
		
		// 2. Generate letters
		$characters = self::generateCharacters($length, $strength);
		
		// --A--A--A--A--A--A--
		// Gap between letters       = (widthPx / count + 1)
		// Gap before first letter   = (widthPx / count + 1)
		//
		// widthPx of a Juliamo letter = 192/418 * heightPx
		// To align with the center  = (192/418 * heightPx) / 2
		
		$xOffset  = $widthPx / (count($characters) + 1);
		$xStart   = $xOffset - $heightPx * 192 / 418 / 2;
		$yOffset  = 0;
		$yStart   = $heightPx * 5 / 6;
		$fontSize = round($heightPx * 2 / 3);
		
		for ($i = 0; $i < count($characters); $i++)
		{
			// A little randomization to make it harder
			$x     = round($xStart + $xOffset * $i) + rand(-2, 2);
			$y     = round($yStart + $yOffset * $i) + rand(-2, 2);
			$angle = rand(-10, +10);
			$color = self::generateColor($image);
			
			imagettftext($image, $fontSize, $angle, $x, $y, $color, self::FONT_PATH, $characters[$i]);
		}
		
		// 3. Generate half-transparent noise
		self::generateNoise($image, 64, 192, 104);
		
		// 4. Select format
		switch ($outputFormat)
		{
			case 'avif': $convert = 'imageavif'; $type = image_type_to_mime_type(IMAGETYPE_AVIF); break;
			case 'bmp':  $convert = 'imagebmp';  $type = image_type_to_mime_type(IMAGETYPE_BMP);  break;
			case 'gif':  $convert = 'imagegif';  $type = image_type_to_mime_type(IMAGETYPE_GIF);  break;
			case 'jpeg': $convert = 'imagejpeg'; $type = image_type_to_mime_type(IMAGETYPE_JPEG); break;
			case 'png':  $convert = 'imagepng';  $type = image_type_to_mime_type(IMAGETYPE_PNG);  break;
			case 'wbmp': $convert = 'imagewbmp'; $type = image_type_to_mime_type(IMAGETYPE_WBMP); break;
			case 'webp': $convert = 'imagewebp'; $type = image_type_to_mime_type(IMAGETYPE_WEBP); break;
			case 'xbm':  $convert = 'imagexbm';  $type = image_type_to_mime_type(IMAGETYPE_XBM);  break;
		}
		
		// 5. Save image
		ob_start();
		$convert($image, null, ...$outputParams);
		$binaryData = ob_get_clean();
		
		// 6. Clean memory
		imagedestroy($image);
		
		// 7. Result
		$solution = implode('', $characters);
		$encodedImage = 'data:'.$type.';base64,'.base64_encode($binaryData);
		
		return [$solution, $encodedImage];
	}
}
