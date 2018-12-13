<?php

namespace daophp\file ;

class FileIncludeIoException extends \Exception {
	
	public function __construct( $classFilePathName ) {

$msg = <<<EOM
The followwing file could not be included
{$classFilePathName}		
EOM;
	}
}
?>