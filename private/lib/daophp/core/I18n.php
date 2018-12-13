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
use daophp\core\object\DPObject ;

class I18n extends DPObject {

	private static $i18nText = array();


	public static function add( $i18nTextArray ) {
		if( !is_array( $i18nTextArray )) {
			Debug::addWarning('# pls provide a array to init I18n Text' );
			return false;
		}

		//Debug::addCoreLog( Common::pr($i18nTextArray, true,'add i18n'));
		self::$i18nText = array_merge(self::$i18nText , $i18nTextArray);
		return true ;
	}

	public static function get( $key, $defaultValue = '' ) {
		
		if( count(self::$i18nText) ) {
			return (isset(self::$i18nText[$key]) && !empty(self::$i18nText[$key]) ) ? self::$i18nText[$key] : $defaultValue ;
		}
	}

	public static function set( $key , $value ) {
		self::$i18nText[$key]=$value;
	}

	public static function remove($key) {
		self::$i18nText[$key] = null ;
	}

	public static function removeAll(){
		self::$i18nText = array();
	}

	public static function getAll() {
		return self::$i18nText ;
	}
}

?>