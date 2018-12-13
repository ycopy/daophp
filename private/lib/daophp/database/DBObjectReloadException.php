<?php

namespace daophp\database ;


class DBObjectReloadException extends \Exception {
	public function __construct( $object, \Exception $e = null ) {

		$exceptionMessage = '' ;
		if( $e != null ) {
			$exceptionMessage = $e->getMessage();
		}

		$objectPrimaryKeyString = $object->getPrimaryKeyAsString() ;
		//$exceptionStack = $e->getTraceAsString();
		$type  = get_class($e);
$msg = <<< EOM
DBObject reload error
objectPrimaryKeyString: {$objectPrimaryKeyString}
message: $exceptionMessage
EOM;
	parent::__construct( $msg );
	}
}
?>