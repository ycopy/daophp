<?php 

namespace daophp\database ;


class DBRecordNotFoundException extends DBException {
	public function __construct( $pk , $table ) {

$msg =<<<EOM
The following record could not be found in db
Primary Key : $pk 
Table: $table 
EOM;
		parent::__construct( $msg );
	}
}

?>