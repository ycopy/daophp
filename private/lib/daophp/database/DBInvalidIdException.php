<?php

namespace daophp\database ;


class DBInvalidIdException extends \Exception {
	
	/**
	 * @var $tableName , table name
	 *
	 * @param unknown_type $tableName
	 */
	public function __construct()
	{
$msg =<<< EOM
Invalid ID OR ID null while load a DBObject , <could be int only>
EOM;
	parent::__construct( $msg );
	}
}
?>