<?php

namespace daophp\database ;


class DBInvalidObjectStatusException extends DBException {
	public function __construct( $msg ) {
		parent::__construct ( $msg );
	}
}