<?php

namespace daophp\file ;

class FileNotFoundException extends \Exception {
	
	public function __construct( $file )
	{

		$msg =<<<EOM
The following file could not be found 
{$file}		
EOM;
		parent::__construct( $msg );
	}
}
?>