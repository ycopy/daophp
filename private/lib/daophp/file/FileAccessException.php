<?php

namespace daophp\file ;


class FileAccessException extends \Exception {
	
	public function __construct( $classFilePathName, $info = 'Access Error') {

$msg = <<<EOM
Access Error On the following file
{$classFilePathName}	
<b>Info</b>: {$info}	
EOM;
	}
}
?>