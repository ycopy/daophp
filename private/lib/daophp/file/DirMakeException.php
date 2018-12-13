<?php
namespace daophp\file ;


class DirMakeException extends \Exception {
	public function __construct( $path ) {

$msg = <<< EOM
Mkdir error
FileName: {$path}
EOM;

	parent::__construct( $msg );
	}
}
?>