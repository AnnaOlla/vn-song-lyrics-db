<?php

abstract class Controller
{
	protected const ENTITY_LIST_DEFAULT_QUERY = ['limit' => 10, 'page' => 1];
	
    protected $model;
    protected $view;
	protected $language;
	
	public function __construct(string $language)
	{
		$this->language = $language;
	}
}
