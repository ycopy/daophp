<?php

namespace daophp\database ;


class DBInvalidEnumFieldException extends DBException {
	public function __construct( $tableName, $fieldName ) {
$msg = <<<EOM
Invalid enum Field {$tableName}.{$fieldName} 
EOM;
		parent::__construct ( $msg );
	}
}


?>