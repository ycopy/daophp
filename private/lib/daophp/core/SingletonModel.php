<?php
/*************************************************

DaoPHP - the PHP Web Frame
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
use daophp\core\Singleton ;

abstract class SingletonModel extends Model implements Singleton {

	private static $self = array();
	/**
	 * @param unknown_type unknown_type $options
	 */
	public static function getInstance($options = array(), $restriction = false ) {
		if( $restriction ) {
			$instanceKey = get_called_class() .'_'. md5(implode(':', $options )) ;
		} else {
			$instanceKey = get_called_class() ;
		}
		
		$className = get_called_class() ;
		
		if( array_key_exists($instanceKey, self::$self ) ) {
			if( get_class( self::$self[$instanceKey]) == $className ) {
				return self::$self[$instanceKey] ;
			}
		}
		
		self::$self[$instanceKey] = new $className( $options );
		return self::$self[$instanceKey];
	}
}
?>