<?php

spl_autoload_register
(
	function (string $className): void
	{
		// PascalCase to kebab-case
		$fileName = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $className));
		
		$directories =
		[
			'controllers',
			'core',
			'enums',
			'include',
			'models',
			'views'
		];
		
		foreach ($directories as $directory)
		{
			$file = ROOT_DIR.'/'.$directory.'/'.$fileName.'.php';
			
			if (file_exists($file))
			{
				require_once $file;
				return;
			}
		}
	}
);

function __($key, $replacements = []): string
{
	return Localizer::localize($key, $replacements);
}
