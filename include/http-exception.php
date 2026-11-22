<?php

class HttpException extends Exception
{
	private $variables;
	private $status;
	
	public function __construct
	(
		HttpStatus     $status,
		string         $message   = '',
		array          $variables = [],
		Throwable|null $previous  = null
	)
	{
		$this->status    = $status;
		$this->variables = $variables;
		
		parent::__construct($message, $status->value, $previous);
	}
	
	public function getVariables(): array
	{
		return $this->variables;
	}
	
	public function getStatus(): HttpStatus
	{
		return $this->status;
	}
}
