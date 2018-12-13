<?php

namespace daophp\view;

use daophp\file\FileNotFoundException ;

class ViewFileNotFoundException extends FileNotFoundException {

	public function __construct( $viewName ) {

		$msg = <<< EOM
View <b>{$viewName}</b> could not be found
EOM;
		parent::__construct( $msg );
	}
}
?>