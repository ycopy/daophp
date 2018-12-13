<?php


namespace daophp\core ;

include_once 'Loader.php';

class DaoPHPAutoLoader extends Loader {
	
	public function load ( $className , $fileSuffix = '.php' ) {
		
		if( class_exists($className) ) {
			return true;
		}
		
		return $this->loadClassFile( str_replace('\\', DS ,$className) . $fileSuffix ) ;
	}
}

?>