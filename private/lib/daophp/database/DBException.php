<?php

namespace daophp\database ;

class DBException extends \Exception
{
	public function __construct($msg, $code = 0)
	{
		parent::__construct($msg, $code);
	}
}
?>