<?php

namespace daophp\view; 

use daophp\core\DaoPHP;
use daophp\core\Debug;
use daophp\file\FileIncludeIoException;
use daophp\core\ClassNotFoundException ;
use daophp\view\AbstractView;

class View extends AbstractView {	
	private $helperPath = array() ;

	
//	const HELPER_MODULE = 1;
//	const HELPER_DAOPHP = 2;
	
	public function addHelperDir( $dir ) {
	  array_push($this->helperPath, $dir);
	}

	public function initHelper() { 
	    $this->addHelperDir( DP_MODULES_DIR . DaoPHP::getInstance()->getModule() . DS . 'views' . DS . 'helpers' . DS );
	    $this->addHelperDir( DP_LIB_DAOPHP_VIEW_DIR . 'helpers' . DS);
	}	
	
	private static $helpers = array() ;
	
	public function getHelper( $name ) {
		
		if( !isset(self::$helpers[$name]) ) {
		    $this->loadHelper($name);
		}
		
		return self::$helpers[$name];
	}
	
	/**
	 * 
	 * lookup in daophp\view\helpers first, 
	 * then modules\moduleName\views\helpers ;
	 * 
	 * @example
	 * 		in view.phtml
	 * 		$this->CSSUrl( $cssFileName );
	 * 
	 * @param unknown_type $name
	 * @throws FileIncludeIoException
	 * @throws ClassNotFoundException
	 */
	public function loadHelper($name) {
			
		$viewHelper = null;
		
		
		foreach( $this->helperPath as $path ) {  
		    
			$helperFilePath =  $path . $name .'.php' ;
			
			if( !file_exists( $path . $name .'.php') ) {
				continue;
			}			
	
			if( !include_once $helperFilePath) {
				throw new FileIncludeIoException($helperFilePath) ;
			}
			
			$viewHelper = new $name();
			break;
		}		
		
		if( $viewHelper === null ) {
		    throw new ClassNotFoundException( $name);
		}
		
		self::$helpers[$name] = $viewHelper ;		
	}
	
	
	public function __call( $method, $args ) {		
		$helper = $this->getHelper($method) ;		
    	return call_user_func_array(
			array( $helper, 'call' ),
			$args
		);
	}
	
	
	private $viewSuffix = '.phtml';

	public function setViewName( $viewName ) {
		$this->viewName = $viewName ;
	}
	public function setViewSuffix( $suffix ) {
		$this->viewSuffix = $suffix ;
		return $this;
	}
	
	private $_path = null;
	public function setPath( $path ) {
	    $this->_path = $path;
	    return $this;
	}

	/**
	 * when render called, this field will set to true
	 * @var unknown_type
	 */
	private $viewName = '';
	
	/**
	 * The view file name
	 * @var string
	 */
	//private $viewFileName = '';
	

	public function __construct($viewName) {
		parent::__construct();
		
		if(empty($viewName)) {		    
		    throw new ViewTemplateEmptyException();
		}
		
		$this->viewName = $viewName;	
		$this->initHelper() ;
	}

	/**
	 *
	 * @return string
	 */
	public function render() {
		if( $this->rendered ) {
			return $this->result ;
		}
		
		if(empty($this->viewName)) {
		    throw new ViewTemplateEmptyException();
		}
		
		if( $this->_path ) {
		    $viewFilePath = $this->_path. $this->viewName  . $this->viewSuffix ;
		} else {
			$viewFilePath = DP_MODULES_DIR . Daophp::getInstance()->getModule() . DS . 'views' . DS . 'scripts' . DS . Daophp::getInstance()->getController(). DS . $this->viewName  . $this->viewSuffix ;
		}

		Debug::core('include scripts: '.$viewFilePath . ' begin' );
		
		if( !file_exists( $viewFilePath ) ) {
			throw new ViewFileNotFoundException($viewFilePath) ;
		}
		
		//var_dump( $this->vars );
		//echo '1111'. $this->email_hint;
		
		ob_start();
		if( !include $viewFilePath ) {
			throw new FileIncludeIoException($viewFilePath) ;
		}
		$this->result = ob_get_contents();
		//Debug::core( $this->viewName . ': '. $this->result );		
		ob_end_clean();
		
		Debug::core('include scripts: '.$viewFilePath . ' end' );
		//Debug::core( 'before reset: '. $this->viewName . ': '. $this->result );
		
		//Debug::core( 'after reset: '. $this->viewName . ': '. $this->result );
		$this->rendered = true;		
		
		return $this->result;
	}
}
?>