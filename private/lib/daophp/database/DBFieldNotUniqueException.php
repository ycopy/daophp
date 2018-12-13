<?php

namespace daophp\database ;


class DBFieldNotUniqueException extends DBException {
	
	public function __construct($tableName,$fieldName)
	{
		$msg = <<<EOM
{$tableName}.{$fieldName} is not unique constraint
EOM;
		parent::__construct($msg);
	}
}
?>