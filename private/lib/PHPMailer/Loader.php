<?php

namespace PHPMailer;

include_once 'FileIncludeIoException.php';

abstract class Loader {
	abstract public function load( $className, $fileSuffix = '.php' ) ;
	
	public function __construct( $folder ) {
		$this->setNamespaceFolder($folder);
	}
	
	private $namespaceFolder = '' ;
	
	public function setNamespaceFolder( $folder ) {
		$this->namespaceFolder = $folder ;
	}
	
	protected function loadClassFile( $classNameFile ) {
		
// 		echo 'class: '. $classNameFile . ',include_path: '. get_include_path() ."\n" ;
		
		/**
		 * just want to ignore this error in error_get_last() ;
		 */
		if( @include_once $classNameFile ) {
			return true;
		}
		
		$filePath = $this->namespaceFolder . $classNameFile ;
		
		return $this->includeFile($filePath) ;
	}
	
	private function includeFile( $filePath ) {
		
// 		echo 'include class file: '. $filePath . " begin\n" ;
		
		if( !file_exists( $filePath )) {
			//echo 'file not exit: '. $filePath . " begin\n" ;
			return false ;
		}
		
		if( ! (include_once $filePath) ) {
			throw new FileIncludeIoException( $filePath );
		}
		
//		echo 'include class file: '. $filePath . " end\n" ;
		return true;
	}
}