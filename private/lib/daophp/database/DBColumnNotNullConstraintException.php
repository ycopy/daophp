<?php

namespace daophp\database ;

class DBColumnNotNullConstraintException extends DBException {
	
	public function __construct($tableName, $columnName , $p )
	{
		$p = print_r( $p, true ) ;
		$msg = <<<EOM
Column Not Null Constraint Failed
field: {$tableName}.{$columnName}		
fields info: 
$p
EOM;
		parent::__construct($msg);
	}
}