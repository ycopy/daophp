<?php

namespace daophp\database ;


class DBFieldValueCheckException extends DBException {
	
    public function __construct( $fieldName, $tableName)
	{
		$msg = <<<EOM
{$tableName}.{$fieldName} value check failed
EOM;
		parent::__construct($msg);
	}
}
?>