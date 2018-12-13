<?php

namespace daophp\core;


class ModuleNotFoundException extends \Exception {
	public function __construct( $moduleName , $path ) {

$msg = <<< EOM
Module <b>{$moduleName}</b> could not be found
It shoud be expected in the following location:
<b>{ $path }</b>
EOM;
		parent::__construct( $msg );
}
}
?>