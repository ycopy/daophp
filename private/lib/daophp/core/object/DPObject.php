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

namespace daophp\core\object;
use daophp\database\DBObject;

abstract class DPObject {
	
	private static $totalDPObjectInstanceNumber = 0;
	
	public static function getTotalDPObjectInstanceNumber() {
		return self::$totalDPObjectInstanceNumber ;
	}

	/**
	 * @date 2010.9.5 add these properties to indentify the instance for the whole system
	 *
	 * for static class , the it's '', currently.
	 * for instance , the key is className_uniqueNumber
	 * Enter description here ...
	 * @var unknown_type
	 */
	private static $uniDPObjectKeyCurIndex = 0 ;

	private static function getUniCurKeyIndex( $increase = false) {
		if( $increase ) {
			return self::$uniDPObjectKeyCurIndex++;
		} else {
			return self::$uniDPObjectKeyCurIndex ;
		}
	}

	//compose format : className_index
	private $DPObjectkey = '' ;

	/*
	 * generate the a global unique key for the obj
	 */
	private function initDPObjectKey() {
		$this->DPObjectkey = get_called_class() . '#'. self::getUniCurKeyIndex(true);
	}

	public function getDPObjectKey() {

		if($this instanceof DBObject) {
			return '['.$this->DPObjectkey.'_pk_'.$this->getPK().']';
		}
		
		return '['.$this->DPObjectkey.']' ;
	}
	
	public function __toString() {
		return $this->getDPObjectKey();
	}

	/**
	 * If the access is private ,you must call it by your self, or they won't be called
	 * @param $options
	 * @return unknown_type
	 */
	public function __construct() {
		$this->initDPObjectKey() ;
		self::$totalDPObjectInstanceNumber++;
	}

	public function __destruct() {
	}
}
?>