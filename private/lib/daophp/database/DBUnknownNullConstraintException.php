<?php

namespace daophp\database ;

class DBUnknownNullConstraintException extends DBException {
	public function __construct($tableName , $filedName , $constraintValue )
	{
		$msg = <<<EOM
Table Name: {$tableName} 
Field Name: {$filedName}
Constraint Value: {$constraintValue}		
EOM;
		parent::__construct($msg);
	}
}
?>