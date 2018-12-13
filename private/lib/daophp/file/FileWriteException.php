<?php

namespace daophp\file ;


class FileWriteException extends \Exception {
	public function __construct( $fileName ) {

$msg = <<< EOM
The Followwing File write errro
FileName: {$fileName}
EOM;

	parent::__construct( $msg );
	}
}

?>