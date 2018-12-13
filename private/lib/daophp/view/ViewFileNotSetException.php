<?php

namespace daophp\view;


use daophp\file\FileNotFoundException ;

class ViewFileNotSetException extends FileNotFoundException {
	public function __construct() {

		$msg = <<< EOM
View File Not Set
EOM;
		parent::__construct( $msg );
	}
}
?>