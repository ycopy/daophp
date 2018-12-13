<?php

namespace daophp\database ;

class DBInvalidPrimaryKeyException extends DBException {
	public function __construct( $key ) {
		$msg = <<<EOM
Invalid Id : {$key}
EOM;
		parent::__construct ( $msg );
	}
}

?>