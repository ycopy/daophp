<?php

namespace daophp\database ;


class DBConnectException extends DBException
{
	public function __construct( $dbHost,$dbUserName,$dbUserpass)
	{
		$msg = <<<EOM
DBI connect failed :
DB Host: {$dbHost}
DB User: {$dbUserName}
EOM;
		parent::__construct($msg);
	}
}
?>