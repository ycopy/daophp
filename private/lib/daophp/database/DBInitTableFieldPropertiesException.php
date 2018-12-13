<?php

namespace daophp\database ;

class DBInitTableFieldPropertiesException extends \Exception {
	public function __construct( $tableName ) {

$msg = <<<EOM
INIT TABLE PROPERTIES FAILED FOR:
TABLE NAME: {$tableName}
EOM;
	parent::__construct( $msg );
	}
}