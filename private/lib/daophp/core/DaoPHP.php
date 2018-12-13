<?php

/*************************************************

DaoPHP - the PHP Web Framework
Author: cpingg@gmail.com
Copyright (c): 2008-2010 DaoPHP Group, all rights reserved
Version: 1.0.0

 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

You may contact the author of DaoPHP by e-mail at:
cpingg@gmail.com

The latest version of DaoPHP can be obtained from:
https://cp-daophp.googlecode.com/svn/trunk/

*************************************************/

namespace daophp\core ;

use daophp\plugins\AbstractPlugin;

use daophp\log\Logger;
use daophp\log\LoggerManager;
use daophp\log\FileLogger;

use daophp\responser\Responser;
use daophp\request\CGIRequest;
use daophp\request\CLIRequest;

use daophp\core\object\SingletonObject;


final class DaoPHP extends SingletonObject {
	
	private static $SID = '' ;
	
	public static function getSID() {
		return self::$SID;
	}
	
	public static function setSID($SID) {
		self::$SID = $SID;
	}

	public function initUser() {
// 		User::init ();
	}

	/**
	 * Get the User Agent information
	 * @return stdClass
	 */
	public static function getUserAgent($options = '') {
		$uaString = strtolower ( @$_SERVER ['HTTP_USER_AGENT'] );

		if (strpos ( $uaString, 'iphone' ) 
			|| strpos ( $uaString, 'ipod' )
			|| strpos ( $uaString, 'ipad' )
			|| strpos ( $uaString, 'itouch' )
			)
		{
			return 'imobile';
		} else if (strpos ( $uaString, 'baiduspider' ) !== false ) {
			return 'baiduspider';
		} else if( strpos ( $uaString, 'googlebot' ) !== false) {
			return 'googlespider';
		} else if( strpos( $uaString , 'sogou web spider') !== false ) {
			return 'sogouspider';
		}
		
		//var_dump( strpos ( $uaString, 'baiduspider' ) ) ;
		
		//die();
		return @$_SERVER ['HTTP_USER_AGENT'];
	}
	
	public static function isBaiduSpider() {
		return (self::getUserAgent() == 'baiduspider') ;
	}
	
	public static function isGoogleSpider() {
		return (self::getUserAgent() == 'googlespider') ;
	}
	
	public static function isSogouSpider() {
		return (self::getUserAgent() == 'sogouspider' );
	}
	
	public static function isImobile() {
		return (self::getUserAgent() == 'imobile' );
	}
	
	public static function isSpider() {
		if( self::isBaiduSpider() 
			|| self::isGoogleSpider()
			|| self::isSogouSpider()
		) {
			return true;
		}
		
		return false;
	}
	
	
	public function getLoggerManager() {
		return $this->_loggerManager ;
	}

	public $_loggerManager = null; //record the normal log
	//public $error = null; //record the error msg

	private $_logCache = array() ;
	/**
	 * @return return write bytes, return null for error
	 * Enter description here ...
	 * @param unknown_type $msg
	 */
	public function log( $msg , $type = Logger::INFO ) {
		if( $this->_loggerManager === null ) {
			array_push( $this->_logCache, array($msg,$type) );
			return strlen($msg); //number of bytes
		} else {
			if( count( $this->_logCache )) {
				$totalWriteBytes = 0;
				
				foreach( $this->_logCache as $singleLine ) {
					$totalWriteBytes += $this->_loggerManager->log( $singleLine[0] , $singleLine[1] );
				}
		
				$this->_logCache = array();
			}
			
			return $this->_loggerManager->log ( $msg, $type );
		}
	}

	public function flushLog() {
		if( $this->_loggerManager !== null )
			$this->_loggerManager->flush ();
	}
	
	public function closeLog() {
		if( $this->_loggerManager !== null ) {
			$this->_loggerManager = null;
		}
	}

	private function initSystemLog() {
		
		//if( !WebEnv::isDevelopment() ) {
			//disable for a temporary solution
		//	return ;
		//}
		
//		$date = date ( 'YmdHis' );
		$date = '';
		
		$dir = '';
		$action = $this->getModule () . '_' .$this->getController().'_'. $this->getAction ();

		$logFileName = '' ;
		if( DP_EXEC_MODE === EXEC_MODE_CLI ) {
			
			$argv = $_SERVER['argv'] ;
			array_shift($argv);
			$action = implode('_',$argv );
			
			$dir = DP_CLI_LOG_DIR ;

		} else {
			$dir = DP_LOG_DIR ;
		}
		
		
		if( !file_exists($dir) ) {
			if( !mkdir( $dir, 0700, true) ) {
				echo 'mkdir cli_log dir error: '. $dir ;
				DaoPHP::exitSite();
			}
		}
		
		$logFileName = $dir . 'log_' . $action ;
		
		$logMode = 'w+';

		$logFileName = $logFileName .'.log'; 
		
		$systemFileLogger = new FileLogger( $logFileName, $logMode ) ;
		
		$systemFileLogger->addLogFlag( Logger::CORE | Logger::ERROR | Logger::WARN | logger::INFO );
		
		//continue to init for cli ,
		if( $this->_loggerManager == null && !empty($logFileName) ) {
			$this->_loggerManager = new LoggerManager() ;
		}
		
		$this->_loggerManager->registerLogger( 'system', $systemFileLogger );
	}
	

	private function initSystemI18n() {
		/**
		 * init system i18n
		 */
		$this->initI18n( self::I18N_SYSTEM );
	}
	
	
	private function initModuleI18n() {
		$this->initI18n( self::I18N_MODULE );
	}
	
	const I18N_SYSTEM = 1;
	const I18N_MODULE = 2;
	
	private function initI18n( $i18nType ) {
		
		$i18nFile = null;
		
		if( $i18nType == self::I18N_MODULE ) {
			$i18nFile = DP_MODULES_DIR . $this->getModule() . DS . 'i18n' . DS . DP_LANG . '.ini' ;
		} else if( $i18nType == self::I18N_SYSTEM ) {
			$i18nFile = DP_I18N_DIR . DS .DP_LANG.'.ini';
		} else {
			throw new \InvalidArgumentException('invalid i18n type');
		}
		/*
		$cacheProvider = CacheManager::getCacheProvider(DP_CACHE_TYPE);
		$cacheKey = 'dp_text_i18n_'. $i18nType ;
		
		$hit = false;
		if($cacheProvider) {
			$hit = $cacheProvider->get($cacheKey);
		
			if($hit !== false) {
				I18n::add( $hit );
				$hit = null ;
				return;
			}
		}
		
		if( file_exists( $i18nFile ) ) {
			
			$dp_text_i18n = parse_ini_file( $i18nFile );
			
//			echo '<pre>';
//			print_r( $dp_text_i18n );
//			echo '</pre>';
//			exit();
		
			if( $cacheProvider ) {
				$cacheProvider->set($cacheKey, $dp_text_i18n );
			}
		
			if(count( $dp_text_i18n) ) {
				I18n::add($dp_text_i18n );
			}
		
			$dp_text_i18n = null ;
			unset( $dp_text_i18n ) ;
		}
		*/
	}

	private $_dp_start_time ;

	//public function getDPExecTime() {
	//	return self::$execTimeArray ;
	//}


	public static $dpConfig = array();
	
	/**
	 * prepare request information
	 *
	 */
	
	public function init( $initArray = array() ) {
		
		if( !defined('_ZG_FLAG_') ) {
			DaoPHP::exitSite('500 - Internal server error, please install php with correct version!!');
		}
		
		if(isset($initArray['dp_config'])) {
			self::$dpConfig = $initArray['dp_config'];
		}
		
		if( array_key_exists('dp_start_time', $initArray)) {
			$this->_dp_start_time = $initArray['dp_start_time'] ;
		} else {
		    $this->_dp_start_time = microtime(true);
		}
		
		//self::setDBConfig(DP_DEFAULT_DB_CONFIG);
		
		/**
		 * INIT DEBUG
		 */
		//Debug::init() ; //nothing to do right now, for performance , comment 
		
		
		if ( DP_EXEC_MODE === EXEC_MODE_CLI ) {
			$this->initSystemLog() ;
		}
		
		$this->initSystemI18n();		
		$this->initRequest();		
		$this->initModule();		
		
		$this->bootstrap();	
	}
	
	private function initModule() {
		$this->registerModule($this->getModule()) ;
		$this->initModuleI18n();
	}

	private $_bootstrap = null;
	private function bootstrap() {
		
		$bootstrapClass = DP_BOOTSTRAP_DIR . 'Bootstrap.php' ;
		
		if( file_exists( $bootstrapClass) ) {
			include_once $bootstrapClass ;
			
			$bootstrap = new \Bootstrap();
			
			$bootstrap->startup();
		}
	}
	
	/**
	 * Exec the component and response the result into $site->
	 * move to controller::exec() 2011.11.27
	 */
	
	
	private $request = null;
	
	public function initRequest() {
		if( DP_EXEC_MODE == EXEC_MODE_CGI ) {
			$this->request = new CGIRequest();
		} else {
			$this->request = new CLIRequest() ;
		}
	}
	
	public function getRequest() {
		
		if( $this->request === null ) {
			$this->initRequest() ;
		}

		return  $this->request ;
	}
	
	private $responser = null;
	private $responser_type = '';
	private $responser_layout = 'index';
	private $header_line = array();
	
	public function setResponserType( $type ) {
	    $this->responser_type = $type;	   
	    if( $this->responser != null) {
	        if( $this->responser->getType() != $type ) {	            
	            $this->responser =null;
	        }
	    }
	    return $this;
	}	
	public function disableResponser() {
	    $this->setResponserType( Responser::TYPE_NULL);
	    return $this;
	}
	
	public function getResponser() {
	    if( $this->responser == null ) {	        
	        $type = $this->responser_type === '' ? $this->getRequest()->getResponseType() : $this->responser_type;
	        $this->responser = Responser::createResponser( $type );	        
	    }
	    return $this->responser;
	}
	
	public function addHeaderLine($line) {	    
	    array_push( $this->header_line, $line );
	}
	
	public function setLayout( $layout ) {	    
	    $this->responser_layout = $layout;
	}
	
	public function registerModule( $module ) {
		AutoloaderManager::getInstance()->addAutoloader( $module , new DaoPHPAutoLoader( DP_MODULES_DIR ) );
	}
	
	public function dispatch() {
		Debug::core ( __METHOD__ . ' BEGIN' );
		Debug::core ( __METHOD__ . ' END' );
	}
	
	private $_controller_instance = null;
	public function initControllerInstance() {
		
		$moduleName = $this->getModule();
		$controllerName = ucfirst($this->getController()) ;
		$actionName = $this->getAction () . 'Action';
		
		if( $controllerName === null ) {
			throw new \Exception('controller not set : '. $moduleName ) ;
		}
		
		if( DP_EXEC_MODE == EXEC_MODE_CLI ) {
			$controllerName = 'Cli_'. ucfirst( $controllerName );
		}		
	
		$controller_class_name_fmt = "\\%s\\controllers\\%s";
		$controller_class_name = sprintf($controller_class_name_fmt, $moduleName, $controllerName);			
	
		try {
		    $controller = new $controller_class_name();		    
		    $this->_controller_instance = $controller;
		} catch( \Exception $e ) {
			throw $e;
		}
	
		return $this;
	}
	

	public function runAction() {
		try {

			$moduleName = $this->getModule();
			$actionName = $this->getAction () ;
			
			//echo get_class( $controller );			
			
		
			if (! empty ( $actionName )) {
		
			    if (! method_exists ( $this->_controller_instance, $actionName )) {
					$str = 'action not implemented '. "\n" ;
					$str .= 'name: '.$actionName . "\n";
					$str .= 'File: '. DP_MODULES_DIR . $moduleName . DS . 'controllers'. DS . get_class($this->_controller_instance) . '.php' ."\n" ;
					
					throw new ActionNotImplementedException( $str );
				}
				
				$controllerName = $this->getController();				
				Debug::core( 'EXEC ACTION: '. $controllerName .'::'.$actionName ) ;
				
				if( method_exists( $this->_controller_instance, 'init') ) {
				    $this->_controller_instance->init() ;
				}
				
				//$_pre_action = '_'. $actionName;
				//$_post_action = $actionName . '_';
				
				//if( method_exists( $this->_controller_instance, $_pre_action)) {
				//    $this->_controller_instance->{$_pre_action}() ;				    
				//}
				ob_start();		
				$this->_controller_instance->{$actionName}() ;	
				$content = ob_get_contents();
				ob_end_clean();
				if(strlen($content)) {
					$this->getResponser()->appendContent( $content );			
				}
				
				$content = $this->_controller_instance->render();		
				if(strlen($content)) {
					$this->getResponser()->appendContent( $content );
				}
				
				//if( method_exists( $this->_controller_instance, $_post_action)) {
				//    $this->_controller_instance->{$_post_action}() ;
				//}
			} else {
				if( DP_EXEC_MODE == EXEC_MODE_CLI ) {
					Debug::error("NO TASK ASSIGNED");
				} else if( DP_EXEC_MODE == EXEC_MODE_CGI ) {
					Controller::error('/index.php', I18n::get('return_home'), 'uri not found') ;
				} else {
				}
			}
		} catch ( \Exception $e ) {
			throw $e ;
		}
	}
	
	public function getModule() {
		return $this->getRequest()->getModule()  ;
	}
	
	public function getController() {
		return $this->getRequest()->getController()  ;
	}

	public function getAction() {
		return $this->getRequest()->getAction()  ;
	}
	
	
	private  $_host_url = '';
	public function getHostUrl() {    
	    return DP_HOST;
	}	

	public function exec() {
		Debug::core ( 'SITE EXEC BEGIN' );
		
		$this->dispatch();		
		$this->initControllerInstance();		
		
		foreach( $this->_plugins as $plugin) {
			$plugin->preRun( $this->getRequest() ) ;
		}
		$this->runAction();
		
		foreach( $this->_plugins as $plugin) {
			$plugin->postRun( $this->getRequest() ) ;
		}
		
		foreach($this->_plugins as $plugin ) {
		    $plugin->preDisplay();		    
		}		
		$this->display();		
		foreach($this->_plugins as $plugin ) {
		    $plugin->postDisplay();
		}
		
		Debug::core ( 'SITE EXEC END' );
	}
	
	public function display() {
	    Debug::core('display start') ;
		//$content = $this->_controller_instance->render();		
		//Debug::info("content:". $content);

		foreach ( $this->header_line as $line) {
		      $this->getResponser()->addHeaderLine($line);		    
		}
		
		if( !empty( $this->responser_layout) ) {
		    $this->getResponser()->setLayout($this->responser_layout);
		}
		
		Debug::core('responser_type: '. $this->getResponser()->getType());
	    $result = $this->getResponser()->render ();			
		
		//for text responser charset bug
		//header('Content-Type: text/html; charset="'.$responser->getCharset().'"'); 
		echo $result;
		
		Debug::core( "html: ". "\n". $result );
		
		Debug::core('display end') ;		
	}
	
	
	private $_plugins = array() ;
	
	public function registerPlugin( $pluginName, AbstractPlugin $plugin ) {
		$this->_plugins[$pluginName] = $plugin ;
	}
	
	
	

//	public static $useCache = array('')  ; //sql cache
//	private static $cacheProvider = array() ;
	
	public static function exitSite($str = '', $clearResourceConnection = true ) {
		//close db connecion
//		if( $clearResourceConnection ) {
//			//close db connecion
//			$dbi = DBManager::GetDBI();
//			$dbi->free() ;
//			$dbi = null;
//		}
		
	    //DaoPHP::getInstance()->tttt();
		exit($str);
	}	
	
	//public function tttt() {
	//   exit('fdsafsad');	    
	//}	
	
	public function __destruct() {
    
		Debug::core ( "DaoPHP::__desctruct() start" );
		
		if( DP_EXEC_MODE === EXEC_MODE_CLI ) {
			self::flushLog();
		} else 	{
				$this->initSystemLog() ;	
				
				$logServerInfo = Common::pr($_SERVER, true, 'serverInfo') ;
				$logs = $logServerInfo . "\n------------------------------------------------\n" ;
				
				try {
					$this->log( $logs );
				} catch( \Exception $e ) {
					;
				}
			}
			
			$this->flushLog();
		
		Debug::core( "DaoPHP::__desctruct() end" );
		
		$now = microtime(true);
		$timediff = $now - $this->_dp_start_time;		
		Debug::core( 'page exec time: '. $timediff );
	}
}
?>