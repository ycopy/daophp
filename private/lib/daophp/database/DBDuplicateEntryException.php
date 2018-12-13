<?php

namespace daophp\database ;


class DBDuplicateEntryException extends DBException {
	public function __construct($sql,$msg) {
$msg = <<<EOM
Duplicate entry occur !!
msg: {$msg}
sql: {$sql}
EOM;
		parent::__construct($msg, 1062);
	}
}

?>