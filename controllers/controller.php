<?php

abstract class Controller
{
    protected $model;
    protected $view;
	protected $language;
	
	public function __construct(string $language)
	{
		$this->language = $language;
	}
}
