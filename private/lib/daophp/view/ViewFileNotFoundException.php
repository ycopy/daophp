<?php

namespace daophp\view;

use daophp\file\FileNotFoundException ;

class ViewFileNotFoundException extends FileNotFoundException {

	public function __construct( $viewName ) {

		$msg = <<< EOM
view: <b>{$viewName}</b>
EOM;
		parent::__construct( $msg );
	}
}
?>