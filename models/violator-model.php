<?php

require_once 'models/visitor-model.php';

class ViolatorModel extends VisitorModel
{
	public function __construct()
	{
		$this->pdo = getPdo('violator');
	}
	
	// Nothing here.
	// The class is used as an authorized alternative to 'visitor'.
	// Violators have read-only access.
}
