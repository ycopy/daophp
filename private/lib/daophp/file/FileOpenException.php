<?php

namespace daophp\file ;


class FileOpenException extends \Exception {
	public function __construct( $fileName ) {
		
$msg = <<< EOM
The Followwing File Could Not Be Open
FileName: {$fileName}
EOM;

	parent::__construct( $msg );
	}
}
?>