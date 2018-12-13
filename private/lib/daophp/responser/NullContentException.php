<?php

namespace daophp\responser;



class NullContentException extends \Exception {
	
	public function __construct() {
$msg =<<< EOM
Null content to be parser
EOM;

	parent::__construct( $msg );
	}
}
?>