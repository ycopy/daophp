<?php
namespace daophp\database ;


class DBInvalidResultException extends DBException  {
	public function __construct() {
		parent::__construct( 'db invalid result' );
	}
}
?>