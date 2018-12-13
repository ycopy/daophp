<?php
namespace daophp\database ;

class DBObjectSaveException extends \Exception {
	public function __construct( $objectName, Exception $e ) {

		$exceptionMessage = $e->getMessage();
		//$exceptionStack = $e->getTraceAsString();
		$type  = get_class($e);
$msg = <<< EOM
DBObject save error
Exception Type: {$type}
DBObject name: {$objectName}
message: $exceptionMessage
EOM;
	parent::__construct( $msg );
	}
}
?>