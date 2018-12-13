<?php

namespace daophp\database ;


class DBDuplicateEntryException extends DBException {
	public function __construct($msg,$sql) {
		parent::__construct($msg, 1062);
	}
}

?>