<?php

namespace daophp\database ;


class DBNullTableNameException extends DBException {
	public function __construct( $dbObjectName ) {
		parent::__construct("(". $dbObjectName .")Table Name Could Not Be Null, please implement it in sub class");
	}
}
?>