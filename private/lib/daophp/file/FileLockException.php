<?php

namespace daophp\file ;

class FileLockException extends \Exception {
	public function __construct( $fileName ) {

$msg = <<< EOM
The Followwing File Could Not Be locked
FileName: {$fileName}
EOM;

	parent::__construct( $msg );
	}
}

?>