<?php

namespace daophp\core ;

class InvalidModelNameException extends \Exception {
	public function __construct( $modelName ) {

$msg = <<< EOM
Invalid Model Name <"{$modelName}">
must ovveride this name in sub controller
EOM;
		parent::__construct( $msg );
	}
}
?>