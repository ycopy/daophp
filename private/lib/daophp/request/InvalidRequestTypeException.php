<?php

namespace daophp\request;

class InvalidRequestTypeException extends \Exception {
	public function __construct( $type ) {

$msg = <<< EOM
Invalid Request Type: {$type}
Could Be (get,post,file) only right now 
EOM;
		parent::__construct( $msg );
	}
}