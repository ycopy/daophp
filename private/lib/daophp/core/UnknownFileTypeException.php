<?php

namespace daophp\core;


class UnknownFileTypeException extends \Exception {
	public function __construct( $fileName ) {
$msg = <<< EOM
The Followwing File Could Not Be Recogized By DP System
FileName: {$fileName}
EOM;
	
	parent::__construct( $msg );
	}
}
?>