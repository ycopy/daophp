<?php

namespace daophp\database ;

class DBInvalidResourceLinkException extends DBException  {
	public function __construct()
	{
		parent::__construct( 'invalid dblink handle' );
	}
}
?>