<?php
namespace daophp\core;

use daophp\core\DaoPHPAutoLoader ;

include_once DP_LIB_DAOPHP_CORE_DIR . DS . 'Singleton.php' ;
include_once DP_LIB_DAOPHP_CORE_DIR . DS . 'DaoPHPAutoLoader.php' ;
include_once DP_LIB_DAOPHP_CORE_DIR . DS . 'ClassNotFoundException.php' ;

class AutoloaderManager implements Singleton {
	
	private static $self = null;
	public static function getInstance( $options = array() ) {
		
		if( self::$self === null ) {
			self::$self = new AutoloaderManager() ;
		}
		
		return self::$self;
	}	
	
	/**
	 * 
	 * key , object pair
	 * daophp 	=> DaophpAutoloader
	 * xxx		=> XxxAutoloader
	 * 
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	
	private $autoloaders = array() ;
	
	public function __construct() {
		$this->init() ;
	}
	
	public function init() {
		$this->addAutoloader('daophp', new DaoPHPAutoLoader( DP_LIB_DIR ) );
	}
	
	public function autoload( $className ) {

	    //echo "autoload: ". $className. '.php'. "\n";
	    
	    
		/**
		 * just want to ignore this error in error_get_last() ;
		 */
		if( @include_once $className . '.php' ) {
			return true;
		}
		
	
		foreach( $this->autoloaders as $autoloader ) {
			foreach( $autoloader as $ns_prefix => $loader ) {
				
				if($ns_prefix != '' && strpos($className, $ns_prefix) !== false ) {
					if( $loader->load($className) ) {
						return true;
					}
				}
			}
		}
		
		throw new ClassNotFoundException( $className );
	}
	
	public function addAutoloader( $ns_prefix,  $loader ) {
		
		if( !method_exists( $loader, 'load') ) {
			throw new \InvalidArgumentException( 'Load must implement load method');
		}
		
		array_push( $this->autoloaders, array($ns_prefix => $loader) ) ;
	}
}