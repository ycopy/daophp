<?php

namespace daophp\database ;


class DBQueryException extends DBException
{
	public function __construct($sql , $message = '')
	{
		$msg = <<<EOM
The following sql seems has some problem: 
{$sql}
{$message}		
EOM;
		parent::__construct($msg);
	}
}
?>