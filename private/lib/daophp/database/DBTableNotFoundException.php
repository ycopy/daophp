<?php

namespace daophp\database ;

class DBTableNotFoundException extends DBException {
	public function __construct($msg) {
		parent::__construct($msg, 1146);
	}
}
?>