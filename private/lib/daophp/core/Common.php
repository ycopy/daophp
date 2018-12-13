<?php
/*************************************************

DaoPHP - the PHP Web Framewrok
Author: cpingg@gmail.com
Copyright (c): 2008-2010 New Digital Group, all rights reserved
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
use daophp\core\object\DPObject ;

class Common extends DPObject {
	public static function isResource($var) {
		return is_resource ( $var );
	}

	/**
	 *
	 * @param $name
	 * @param $type ,class, interface,abstract, controller, model, view
	 * @return array , file['path'] , file['name']
	 */
	public static function getFileNameByClassName($name, $classNameExtensionName = DP_CLASS_EXTENSION_NAME) {
		return $name . $classNameExtensionName ;
	}

	/**
	 *
	 * In test_function_name.class.php
	 * out TestFunctionName
	 *
	 * @param $fileName string
	 * @return string
	 */

	public static function getClassNameByFileName($fileName, $classFileExtensionName = DP_CLASS_EXTENSION_NAME) {

		list( $class, $ext) = explode('.', $fileName) ;
		
		return  $class;
	}

	/**
	 * use to print some information
	 * substitute print_r
	 *
	 * @param array()
	 */
	public static function pr( $var, $returnString = false, $info = 'Info:') {

		$body = '';
		if (DP_EXEC_MODE !== EXEC_MODE_CLI ) {
			$body = '<div class="debug-array">' . $info . '</div>';
			$body .= '<pre>';
		} else  {
			$body = $info . DP_NEW_LINE ;
		}

		$body .= print_r ( $var, true );

		if (DP_EXEC_MODE !== EXEC_MODE_CLI ) {
			$body .= '</pre>';
		}

		if ($returnString === true)
			return $body;
		else
			echo $body;
	}

	/**
	 * judge whether it's a multi-array
	 * @param $array
	 * @param $deepth
	 * @params $strict, if set true, array() will return false
	 * @return unknown_type
	 */
	public static function isMultiArray($array, $deepth = 2, $strict = false) {
		$i = 0;

		if (is_array ( $array )) {
			if ($deepth === 1) {
				if ($strict === true) {
					return (! empty ( $array )) && is_array ( $array );
				}
				return true;
			}

			foreach ( $array as $array_tmp ) {
				if (self::isMultiArray ( $array_tmp, $deepth - 1, $strict ) === true) {
					return true;
				} else {
					if ($strict === true) {
						return false;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Get the deepth of the Array
	 * @param $array
	 * @return int, Deepth
	 */
	public static function getDeepthOfArray($array) {

		$deepth = 0;
		if (is_array ( $array )) {
			$deepth ++;
			$deepthArray = array ();
			foreach ( $array as $value ) {
				$deepthArray [] = self::getDeepthOfArray ( $value );
			}
			if (count ( $deepthArray )) {
				rsort ( $deepthArray );
				$deepth += $deepthArray [0];
			}
		}
		return $deepth;
	}

	public static function objectToArray($object) {
		$array_tmp = array ();
		foreach ( $object as $key => $value ) {
			$array_tmp [$key] = $value;
		}

		return $array_tmp;
	}

	/**
	 * $array = array('key' => 'value', 'key2' => 'value2');
	 *
	 * to
	 *
	 * array(obj1, obj2 );
	 *
	 * obj1 = {key:value}
	 * obj2 = {key2: value2 }
	 * @param unknown_type $arr
	 */
	public static function assocArrayToObjectArray($arr) {
		$arr_tmp = array ();

		foreach ( $arr as $key => $value ) {
			$stdObj = new \stdClass ();
			$stdObj->$key = $value;
			$arr_tmp [] = $stdObj;
		}

		return $arr_tmp;
	}
	/**
	 * only support assoc array
	 * @param unknown_type $arr
	 */
	public static function arrayToObject( $arr ) {
		return self::assocArrayToObjectArray( $arr ) ;
	}

	public static function detectEncoding($str) {
		$rs = mb_detect_encoding ( $str, array ('ASCII', 'GB2312', 'GBK', 'UTF-8', 'BIG5' ) );
		if( $rs == 'GB2312' || $rs == 'CP936' ) {
			return 'GBK';
		}

		return $rs ;
	}

	public static function convertToUTF8( $str ) {
		$srcEncoding = self::detectEncoding ( $str );
		
		if( $srcEncoding == 'UTF-8') {
			return trim( $str );
		}
		
		$rs = trim ( @iconv ( $srcEncoding, 'UTF-8//IGNORE', $str ) );

		return $rs;
	}


	public static function filterArrayNumberKey( $arr ) {
		if( !(is_array($arr)) ) {
			Debug::addNotice('PROVIDED PARAMS IS NOT A ARRAY' );
			return $arr;
		}

		foreach( $arr as $key => $value ) {
			if( !is_int($key) ) {
				continue;
			}

			$arr[$key] = null ;
			unset( $arr[$key] );
		}

		return $arr ;
	}

	public static function ArrayEqual( $arrOne, $arrTwo ) {
		if( count( $arrOne) !== count( $arrTwo ) ) {
			return false;
		}

		foreach( $arrOne as $tmp ) {
			if( !in_array($tmp, $arrTwo) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @author cpingg@gmail.com
	 * @date 2010-1-13
	 * call example, Common::cleanHtmlString( $str, '<p><a>') ;
	 * Enter description here ...
	 * @param unknown_type $str
	 * @param unknown_type $allowTags
	 */
	public static function cleanHtmlString( $str , $replaceInnerSpace = false , $allowTags = ''  )
	{
		if ( !empty( $allowTags ) ) {
			$str = strip_tags( $str );
		} else {
			$str = strip_tags($str, $allowTags );
		}
		
		if( $replaceInnerSpace ) {
			$replace_array = array('&nbsp;',' ','	','　');
			$target_array = array('','','','');
			$str = str_replace($replace_array, $target_array, $str );
		}

		return trim($str);
	}
	
	public static function trimArray( $array ) {
		$t_rsArray = array() ;
		foreach($array as $key => $value ) {
			
			$t_rsArray[$key] = trim($value) ;
		}
		
		return $t_rsArray ;
	}

	public static function mkdirRecursive( $dirName, $rights = 0777 ) {
	    $dirs = explode('/', $dirName);
	    $dir='';
	    foreach ($dirs as $part) {
	        $dir.=$part.'/';
	        if (!is_dir($dir) && strlen($dir)>0) {
	            $rs = mkdir($dir, $rights);

	            if( !$rs ) {
	            	Debug::addError('MKDIR ERROR: '. $dir . ' right: '. $rights );
	            	return false;
	            }
	        }
	    }
	    return true;
	}
}
?>