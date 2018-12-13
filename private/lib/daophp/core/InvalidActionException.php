<?php 

namespace daophp\core;


class InvalidActionException extends \Exception {
	
	public function __construct( $taskName, $className ) {
$msg=<<<EOM
The following method could not be found in class {$className}
action: {$taskName}
EOM;

	parent::__construct( $msg );
		
	}
}

?>