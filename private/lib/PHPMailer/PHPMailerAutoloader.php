<?php

namespace PHPMailer;

if ( !defined('__PHPMAILER__') ) {
	die('invalid request at php mailer. ') ;
}

use PHPMailer\Loader ;

include_once 'Loader.php';

class PHPMailerAutoloader extends Loader {
	
	public function load ( $className , $fileSuffix = '.php' ) {
		
		if( class_exists($className) ) {
			return true;
		}
		
		return $this->loadClassFile( str_replace('\\', DIRECTORY_SEPARATOR ,$className) . $fileSuffix ) ;
	}
}
