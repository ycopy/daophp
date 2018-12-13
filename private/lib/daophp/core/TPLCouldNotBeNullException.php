<?php

namespace daophp\core;

class TPLCouldNotBeNullException extends \Exception {
	
	public function __construct()
	{
	$msg = <<<EOM
TPL could not be null
EOM;
	parent::__construct( $msg );
	}
}
?>