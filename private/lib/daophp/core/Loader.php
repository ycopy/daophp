<?php

namespace daophp\core ;

use daophp\file\FileNotFoundException;
use daophp\file\FileIncludeIoException ;
use daophp\core\Debug;

include_once DP_LIB_DAOPHP_FILE_DIR . 'FileNotFoundException.php' ;
include_once DP_LIB_DAOPHP_FILE_DIR . 'FileIncludeIoException.php' ;

abstract class Loader {
	abstract public function load( $className, $fileSuffix = '.php' ) ;
	
	public function __construct( $folder ) {
		$this->setNamespaceFolder($folder);
	}
	
	private $namespaceFolder = DP_LIB_DIR ;
	
	public function setNamespaceFolder( $folder ) {
		$this->namespaceFolder = $folder ;
	}
	
	protected function loadClassFile( $classNameFile ) {
		
 		//echo 'locd class file: '. $classNameFile . ',include_path: '. get_include_path() ."\n" ;
		
//		/**
//		 * just want to ignore this error in error_get_last() ;
//		 */
//		set_error_handler( array( $this, '_includeErrorHandler') );
		if( @include_once $classNameFile ) {
//			echo 'try to include '. $classNameFile ;
			return true;
		}
//		restore_error_handler() ;
		
		$filePath = $this->namespaceFolder . $classNameFile ;
		
		return $this->includeFile($filePath) ;
	}
	
	public function _includeErrorHandler($errno, $errstr, $errfile, $errline ) {
		Debug::addCoreLog('include file error: '. $errno .':'.$errstr . "\n". '('. $errline .')' . $errfile );
	}
	
	private function includeFile( $filePath ) {
		//echo 'include class file: '. $filePath . " begin\n" ;
		
		if( !file_exists( $filePath )) {
			return false ;
		}
		
		if( ! (include_once $filePath) ) {
			throw new FileIncludeIoException( $filePath );
		}
		
//		echo 'include class file: '. $filePath . " end\n" ;
		return true;
	}
}