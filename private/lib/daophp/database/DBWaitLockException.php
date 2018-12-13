<?php

namespace daophp\database ;


class DBWaitLockException extends DBException {

	public function __construct($dbHost,$sql) {
$msg = <<<EOM
DBI wait lock timeout, please restart the transaction !!
host: ${dbHost}
sql: ${$sql}
EOM;
		parent::__construct($msg, 1205);
	}
}
?>