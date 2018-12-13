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
namespace daophp\core;
use daophp\responser\Responser;

use daophp\core\object\DPObject; 
use daophp\request\Request ;
use daophp\view\View;
use daophp\view\AbstractView;
use daophp\view\JsonView;
use daophp\view\NullView;

abstract class Controller extends DPObject {	
    
    
    private $_view = null;
    public function render() {
        if( $this->_view == null ) { return ''; }
        $this->_view->reset( $this->_vars);
        return $this->_view->render() ;
    }
    
    public function setView(AbstractView $view) {
        $this->_view = $view;
       
        if( ($view instanceof JsonView)) {
            $this->setResponseType( Responser::TYPE_JSON);
        }
        return $this;
    }

    public function setJsonView() {$this->setView( new JsonView()); $this->setNoLayout();return $this;}
    public function setNoView() {$this->setView( new NullView()); return $this;}
    public function setNoLayout() {$this->disableResponser();return $this ;}
    
    public function setResponseType($type) {
        DaoPHP::getInstance()->setResponserType($type);        
    }
    
    public function disableResponser() {
        DaoPHP::getInstance()->disableResponser();
    }
    
	public function setLayout( $layout ) {
	    DaoPHP::getInstance()->setLayout( $layout );
		return $this;
	}	
	
	public function addHeaderLine($line) {
	    DaoPHP::getInstance()->addHeaderLine( $line );    
	}
	
	public function addJs( $js_url ) {	    
	    $line = '<script src="'. $js_url .'" type="text/javascript"></script>';	   
	    $this->addHeaderLine( $line);
	}
	
	public function addCss( $css_url ) {
	   $css_line = '<link rel="stylesheet" type="text/css" href="'. $css_url .'" />';
	   $this->addHeaderLine( $css_line);	    
	}
	

	public function __construct() {
		parent::__construct();		
	
		if( DP_EXEC_MODE == EXEC_MODE_CGI ) {
		    
		    $request = DaoPHP::getInstance()->getRequest();
		    
			//$response_type =  $request->getResponseType();
			//$this->responser = Responser::createResponser($response_type);
			
			$action = $request->getAction();
			$this->_view = new View( $action ) ;
		}
	}
	
	private $_vars = array();
	
	public function assign( $key,$value ) {
	    $this->_vars[$key] = $value;		
		return $this;
	}	

	private static $redirectUrl = '';
	private static $redirectTitle = '';
	private static $redirectPageTitle = '';

	public static function setRedirectUrl($url) {
		self::$redirectUrl = $url;
	}

	public static function setRedirectTitle( $title) {
		self::$redirectTitle = $title;
	}
	public static function getRedirectTitle() {
		return self::$redirectTitle ;
	}
	public static function setRedirectPageTitle( $title) {
		self::$redirectPageTitle = $title;
	}
	public static function getRedirectPageTitle() {
		return self::$redirectPageTitle ;
	}
	public static function getRedirectUrl() {
		return self::$redirectUrl;
	}

	const DEFAULT_WAIT_TIME = 30;
	private static $redirectWaitTime = self::DEFAULT_WAIT_TIME;

	public function setRedirectWaitTime($time) {
		self::$redirectWaitTime = $time;
	}

	public function getRedirectWaitTime() {
		return self::$redirectWaitTime;
	}

	private static $redirectMessage = '';

	public static function setRedirectMessage($message) {
		self::$redirectMessage = $message;
	}
	public static function getRedirectMessage() {
		return self::$redirectMessage;
	}

	public static function resetRedirectInfo() {
		self::setRedirectUrl ( '' );
		self::setRedirectMessage ( '' );
		self::setRedirectWaitTime ( self::DEFAULT_WAIT_TIME );
	}
	
	public static function error( $redirectURL, $title, $message , $errorPageTitle ) {
		ob_clean();
		
		$responser = DaoPHP::getInstance()->getResponser ();
		
		$responser->setTitle ( $errorPageTitle );
		$errorView = View::getSystemView('error') ;
		
		$errorView->set('redirect_url', $redirectURL );
		$errorView->set('error_title', $title );
		$errorView->set('error_message', $message );
		
		DaoPHP::getInstance()->setRenderData('content', $errorView->render());
		DaoPHP::getInstance()->display();
		
		Debug::addError( 'error: '. $title . ',message: '.$message .'errPageTitle: '.$errorPageTitle  );
		DaoPHP::exitSite();
	}
	
	public static function redirect($url , $title = '',$message = '' , $redirectPageTitle = '', $waitTime = self::DEFAULT_WAIT_TIME ) {

		$site = DaoPHP::getInstance ();
		$responser = $site->getResponser ();

		if ( $url != '' ) {
			self::setRedirectUrl ( $url );

			if( !empty($title)) {
				self::setRedirectTitle($title) ;
			} else {
				self::setRedirectTitle( I18n::get("redirect_title") );
			}
			
			if( !empty($redirectPageTitle)) {
				self::setRedirectPageTitle($redirectPageTitle) ;
			} else {
				self::setRedirectPageTitle( '閲嶅畾鍚�-璺宠浆-301' );
			}
			

			if (trim ( $message ) != '') {
				self::setRedirectMessage ( $message );
			} else if(empty( $title)) {
				self::setRedirectMessage( $title ) ;
			}else{}

			if (intval ( $waitTime ) != 0 && !empty($message) ) {
				self::setRedirectWaitTime ( $waitTime );

				$headerLine = '<meta http-equiv="refresh" content="' . self::$redirectWaitTime . '; url=' . self::$redirectUrl . '"/>';
				$responser->addHeaderLine ( $headerLine );
			}
		}

		/**
		 * change theme to redirect
		 * set meta info
		 * @var unknown_type
		 */

		$responser->setTitle ( self::getRedirectPageTitle() );

		if (! empty ( self::$redirectUrl )) {

			if (! empty ( self::$redirectMessage )) {

				$view = View::getSystemView ( 'redirect' );
				$view->set ( 'redirect_message', self::$redirectMessage );
				$view->set ( 'redirect_waitTime', self::$redirectWaitTime );
				$view->set ( 'redirect_url', self::$redirectUrl );
				$view->set ( 'redirect_title', self::$redirectTitle );

				ob_clean ();
				DaoPHP::getInstance()->setRenderData('content', $view->render());
				self::setRedirectUrl ( '' );
				self::setRedirectTitle ( '' );
				self::setRedirectMessage ( '' );
				self::setRedirectWaitTime ( self::DEFAULT_WAIT_TIME );
				DaoPHP::getInstance()->display();
				DaoPHP::exitSite();
			} else {
				//$this->redirectUrl = '';
				self::setRedirectMessage ( '' );
				self::setRedirectWaitTime ( self::DEFAULT_WAIT_TIME );

				Debug::trace ( '#redirect: ' . self::$redirectUrl );
				header ( 'Location: ' . self::$redirectUrl );
				DaoPHP::exitSite ();
			}
		}
	}
	
	
	public function getRequest() {	    
	    return DaoPHP::getInstance()->getRequest();
	}

	/**
	 *
	 *
	 */
	public function indexAction(){
		throw new \Exception('index not implemented');
	}
}
?>