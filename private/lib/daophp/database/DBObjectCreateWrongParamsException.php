<?php

namespace daophp\database ;


class DBObjectCreateWrongParamsException extends DBException {
	public function __construct( $params )
	{
		$str = print_r( $params , true );
		$msg = <<<EOM
Wrong Params : 
{$str}
EOM;
		parent::__construct($msg);
	}
}
?>