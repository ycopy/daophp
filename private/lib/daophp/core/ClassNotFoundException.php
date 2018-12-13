<?php

namespace daophp\core ;

class ClassNotFoundException extends \Exception {

	public function __construct( $className) {

		$includePath = get_include_path() ;
$msg = <<< EOM
Class {$className} Not Found 
Include Path: $includePath

EOM;

	parent::__construct( $msg );
	}
}
?>