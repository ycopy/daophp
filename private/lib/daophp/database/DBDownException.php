<?php

namespace daophp\database ;


class DBDownException extends DBException {
	public function __construct($dbHost)
	{
		$msg = <<<EOM
DBI has gone away !!
EOM;
		parent::__construct($msg, 2006);
	}
}
?>