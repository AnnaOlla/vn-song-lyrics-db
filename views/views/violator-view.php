<?php

require_once 'views/visitor-view.php';

class ViolatorView extends VisitorView
{
	public function __construct(string $language)
	{
		parent::__construct($language);
	}
	
	// Nothing here.
	// The class is used as an authorized alternative to 'visitor'.
	// Violators have read-only access.
}
