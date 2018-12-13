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

namespace daophp\request;
use daophp\responser\Responser;

use daophp\core\object\DPObject ;
use daophp\database\DBManager ;

final class CGIRequest extends DPObject implements Request {

    const REQUEST_METHOD_GET        = 'get';
    const REQUEST_METHOD_POST       = 'post';
    const REQUEST_METHOD_PUT        = 'put';
    const REQUEST_METHOD_DELETE     = 'delete';

	private $post = array();
	private $get  = array();
	private $file = array();

	private $_requestMethod;
	private $_clientIP;
	private $_isHttps;
	private $_requestTime;


	public function __construct() {
		$this->init();
	}

	public function init() {
		/**
		 * Initialize $_POST $_GET parameters
		 *
		 */
		$this->post = $_POST ;
		$this->get = $_GET;
		$this->file = $_FILES;

		$this -> _requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
		if (empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    $this -> _clientIP = empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR'];
		} else {
		    $this -> _clientIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		$this -> _isHttps = !empty($_SERVER['HTTPS']);
		$this -> _requestTime = $_SERVER['REQUEST_TIME'];
	}

	public function getRequestMethod()
	{
		return $this -> _requestMethod;
	}

	public function getClientIP()
	{
		return $this -> _clientIP;
	}

	public function isHttps()
	{
		return $this -> _isHttps;
	}

	public function getRequestTime()
	{
	    return $this -> _requestTime;
	}

	public function getAllPostVar()
	{
	    return $this -> post;
	}

	public function getAllGetVar()
	{
	    return $this -> get;
	}

	public function getAllGetVarWithoutSysVar()
	{
	    $getParms = $this -> get;
	    unset($getParms[DP_REQUEST_MODULE_KEY], $getParms[DP_REQUEST_CONTROLLER_KEY], $getParms[DP_REQUEST_ACTION_KEY]);
	    return $getParms;
	}

	public function getAllFileVar()
	{
	    return $this -> file;
	}

	public function getAllVar()
	{
	    $vars = array();
	    $getVars = $this -> getAllGetVarWithoutSysVar();
	    if(count($getVars) > 0){
	        $vars['get'] = $getVars;
	    }
	    if(count($this -> post) > 0){
	        $vars['post'] = $this -> post;
	    }
	    if(count($this -> file) > 0){
	        $vars['file'] = $this -> file;
	    }
	    return $vars;
	}

	/**
	 * Generic method used to get requst var
	 * @param $key
	 * @param $default , if not found, return this default value
	 * @param $type
	 * @return unknown_type
	 */
	public function getRequestVar( $key , $default = '' ,$type = 'all' ) {

		$key = trim( $key );
		$type = trim( $type );

		switch ( $type ) {
			case 'post' : return $this->getPostVar( $key, $default );
			case 'get' : return $this->getGetVar( $key, $default );
			case 'file' : return $this->getFileVar( $key ,$default );
			case 'all' :
				return 	isset( $this->get[$key] ) ? $this->get[$key] : (isset( $this->post[$key] ) ? $this->post[$key] : (isset( $this->file[$key] ) ? $this->file[$key] : $default));
			default:
				throw new InvalidRequestTypeException( $type );
		}
	}

	public function getParam( $key ) {
		return $this->get( $key );
	}

	/**
	 * alias to $this->request->getRequestVar
	 * @param unknown_type $key
	 * @param unknown_type $default
	 * @param unknown_type $type
	 */
	public function get( $key , $default = '' ,$type = 'all' ) {
		$tmp = $this->getRequestVar( $key , $default ,$type ) ;

		if( is_array($tmp) || is_object($tmp) ) {
			return $tmp;
		}

//		$tmp=str_replace("_","\_",$tmp);
//		$tmp=str_replace("%","\%",$tmp);
//		$tmp = nl2br($tmp);
		$tmp = htmlspecialchars($tmp,ENT_COMPAT, 'UTF-8');
//		$tmp = mysql_real_escape_string($tmp, DBManager::GetDBI()->getDBlink() );

		return $tmp;
	}

	public function getCookie( $key, $default = '' ) {	    

		if(!isset($_COOKIE[$key]) ) {
			return $default ;
		}

		$tmp = $_COOKIE[$key] ;

		if( is_array($tmp) || is_object($tmp) ) {
			return $tmp;
		}

//		$tmp=str_replace("_","\_",$tmp);
//		$tmp=str_replace("%","\%",$tmp);
//		$tmp = nl2br($tmp);
		$tmp = htmlspecialchars($tmp,ENT_COMPAT, 'UTF-8');
		return $tmp;
	}
	
	public function setCookie( $key, $value, $expire = 0, $path = '', $domain = '', $secure = false, $httponly = false) {    
	    return setcookie($key, $value, $expire, $path, $domain, $secure, $httponly );	    
	}

	public function getPostVar( $key, $default = '' ) {
		return isset($this->post[$key]) ? $this->post[$key] : $default ;
	}

	public function getGetVar( $key ,$default = '') {
		return isset($this->get[$key]) ? $this->get[$key] : $default ;
	}

	public function getFileVar( $key ,$default ) {
		return isset($this->file[$key]) ? $this->file[$key] : $default ;
	}

    public function getModule()
    {
        $module = $this -> get(DP_REQUEST_MODULE_KEY, DP_DEFAULT_MODULE);
        if ($module === '') {
            $module = DP_DEFAULT_MODULE;
        }
        return $module;
    }

    public function getController()
    {
        $controller = $this -> get(DP_REQUEST_CONTROLLER_KEY, DP_DEFAULT_CONTROLLER);
        if ($controller === '') {
            $controller = DP_DEFAULT_CONTROLLER;
        }
        return $controller;
    }

    public function getAction()
    {
        $action = $this -> get(DP_REQUEST_ACTION_KEY, DP_DEFAULT_ACTION);
        if ($action === '') {
            $action = DP_DEFAULT_ACTION;
        }
        return $action;
    }

    public function getResponseType()
    {
        return $this -> get(DP_REQUEST_RESPONSE_TYPE_KEY, Responser::TYPE_XHTML);
    }
}
?>