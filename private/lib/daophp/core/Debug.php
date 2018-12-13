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
use daophp\log\Logger;

use daophp\mailer\Gmailer;

use daophp\core\object\DPObject ;

final class Debug extends DPObject {


	/*
	array(
		'str info',
		);
	*/
	private static $debugArray = array ();

// 	const DEBUG_EXCEPTION		= DP_DEBUG_EXCEPTION;
// 	const DEBUG_WARNING			= Logger::WARN;
// 	const DEBUG_NOTICE			= Logger::INFO;
// 	const DEBUG_TRACE			= Logger::INFO;
// 	const DEBUG_LOG_FILE		= Logger::INFO_FILE;
// 	const DEBUG_LOG				= Logger::INFO;
// 	const DEBUG_CORE			= Logger::CORE;


// 	private static $debugMode = 0 ;
// 	public static function addDebugMode( $mode ) {
// 		self::$debugMode |= $mode ;
// 	}

// 	public static function removeDebugMode( $mode ) {
// 		self::$debugMode &= ~$mode ;
// 	}


// 	public static function init() {
// 	}

// 	public static function setDebugMode( $mode ) {
// 		self::$debugMode = $mode;
// 	}

// 	public static function getDebugMode(){
// 		return self::$debugMode ;
// 	}

	public static function dumpMemInfo( $tagName ='mem usage info' ) {
		$curUsage = memory_get_usage() / 1024 . ' KB' ;
		$peakUsage = memory_get_peak_usage() / 1024 . ' KB' ;

		$msg = DP_NEW_LINE. $tagName .DP_NEW_LINE ;
		$msg .= 'MEM CURRENT USAGE: ' . $curUsage . DP_NEW_LINE ;
		$msg .= 'MEM PEAK USAGE: ' . $peakUsage . DP_NEW_LINE ;

		Debug::trace( $msg , false ) ;
	}

	public static function recordTime( $tag = '', $addToTrace = true ) {
		if(!empty( $tag )) {
			$tag = $tag .' ';
		}

		if( $addToTrace ) {
			Debug::trace($tag. 'record time: '. strval(time() + microtime()), false);
		} else {
			Debug::info( $tag. 'record time: '. strval(time() + microtime()), false );
		}
	}

	public static function getCallStack( $removeLevel = 1 ) {
		$traceArray = array() ;
		
		$traceArray = debug_backtrace(0);
		$traceArray = array_slice($traceArray, $removeLevel) ;

		//var_dump( $traceArray );
		//exit();
		
		$traceString = '' ;
		$index = 1 ;
		foreach( $traceArray as $trace ) {

			$traceString .= '#' . $index++ . '	' ;

			if( isset( $trace['file']) ) {
				$traceString .= $trace['file'] .'('. $trace['line'] .')' ;
				$traceString .= "	";
			}


			if( isset( $trace['class'] ) ) {
				$traceString .= $trace['class'] ;
			}

			if( isset( $trace['object']) && ( $trace['object'] instanceof DPObject ) ) {
				$traceString .= '@['. $trace['object']->getDPObjectKey() .']' ;
			}

			if( isset($trace['type']) ) {
				$traceString .= $trace['type'] ;
			}

			if( isset( $trace['function']) ) {
				$traceString .= $trace['function'] . '(' ;
			}

			if( isset( $trace['args']) && count( $trace['args']) ) {

				$t_argArray = array() ;
				foreach( $trace['args'] as $arg ) {
					if( is_object( $arg ) ) {
						array_push( $t_argArray, get_class( $arg ) );
					} else if( is_array($arg)) {
						array_push( $t_argArray, 'Array' );
					} else if( is_string($arg )) {

						if( mb_strlen( $arg) > 100 ) {
							array_push( $t_argArray, mb_substr( $arg, 0, 300 ) .'...' );
						} else {
							array_push( $t_argArray, $arg );
						}
					} else {
						array_push( $t_argArray, $arg );
					}
				}
				$traceString .= implode( ',', $t_argArray );

				$t_argArray = null ;
			}

			if( isset( $trace['function'] ) ) {
				$traceString .= ')' ;
			}

			$traceString .= DP_NEW_LINE ;
		}

		return $traceString ;
	}


	/**
	 *
	 * @param unknown_type $msg
	 * @param unknown_type $title
	 */

	private static function log(&$msg, $type , $printTrace = false ) {
		/**
 		 * 2010.06.17
 		 * add for performance
 		 * @cpingg@gmail.com
 		 */

		if( is_array( $msg ) ) {
			$msg = Common::pr($msg, true, 'ArrayToString'  );
		}

		if( is_object( $msg ) ) {
		    
		    if( method_exists( $msg, '__toString')) {
		     $msg = $msg->__toString();
		    } else {
			 $msg = Common::pr($msg, true, 'ObjectToString'  );
		    }
		}

		//$var = debug_backtrace(true);
		//var_dump( $var);
		
		if( $printTrace ) {
			$callStack = self::getCallStack(2);
			$msg .= DP_NEW_LINE . $callStack ;
		}

		// for cli, just insert into disk
		if ( DP_EXEC_MODE === EXEC_MODE_CLI ) {
			$str_replaceSrc = array('&lt;','&gt;','&nbsp;', '<br />');
			$str_replaceTar = array('<','>',' ', "\r\n" );
			$msg = str_replace($str_replaceSrc, $str_replaceTar, $msg );

			if(
				$type == Logger::ERROR
			||	$type == Logger::WARN
			) {
				echo $msg .''. DP_NEW_LINE ;
			}

			DaoPHP::getInstance()->log( $msg , $type );
		} else if (DP_EXEC_MODE == EXEC_MODE_CGI ) {
			//for cgi, keep in mem , will flush to file by Daophp_desctruct()
			DaoPHP::getInstance()->log( $msg , $type );
		} else {
			DaoPHP::exitSite('invlaid exec mode');
			return ;
		}
	}

	public static function error( $msg, $printTrace = true ) {
		self::log( $msg, Logger::ERROR, $printTrace);
	}

	public static function warning( $msg,$printTrace = true ) {
		self::log( $msg, Logger::WARN , $printTrace);
	}

	public static function notice( $msg,$printTrace = false ) {
		self::log( $msg, Logger::INFO, $printTrace );
	}

	public static function trace( $msg, $printTrace = true ) {
		self::log( $msg, Logger::INFO , $printTrace);
	}

	public static function core( $msg, $printTrace = false) {
		self::log( $msg, Logger::CORE, $printTrace );
	}
	public static function info( $msg,$printTrace = false ) {
		self::log( $msg, Logger::INFO, $printTrace );
	}

	public static function exception( $e ) {
		$str = '['.get_class($e).']['. $e->getCode() .']'.' '.$e->getMessage() . DP_NEW_LINE ;
		$str .= $e->getTraceAsString() ;

		self::error( $str );
	}

	public static function get() {
		if( DP_EXEC_MODE === 'CLI' ) {
			return implode("\n", self::$debugArray );
		}

		return implode('<br />', self::$debugArray );
	}

	public static function clear() {
		self::$debugArray = null;
	}
}