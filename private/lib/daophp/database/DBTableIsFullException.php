<?php

namespace daophp\database ;

class DBTableIsFullException extends DBException {
	public function __construct($msg) {
		parent::__construct($msg, 1114);
	}
}
?>