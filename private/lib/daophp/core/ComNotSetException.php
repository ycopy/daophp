<?php
namespace daophp\core ;


class ComNotSetException extends \Exception {
	public function __construct( $className ) {
$msg = <<< EOM
You must set a com for class {$className} before load it's model
you can explicitly set by override in field or call method setCom
EOM;

	parent::__construct( $msg );
	}
}
?>