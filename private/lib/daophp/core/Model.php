<?php
/*************************************************

DaoPHP - the PHP Web Framewrok
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

use daophp\database\DBRecordNotFoundException;
use daophp\database\DBObject;
use daophp\database\DBManager;
use daophp\database\DBNullTableNameException;
use daophp\core\object\DPObject;

abstract class Model extends DPObject {
	public function __construct($options = array()) {
		parent::__construct ( $options );

		/**
		 * the table name can only be reset when we don't override the tableName in sub class by hard code
		 */

		if( static::$tableName != '') {
			Debug::addNotice ( 'Reset tableName for model: ' . get_called_class () );
			return ;
		}
		
		if (array_key_exists ( 'tableName', $options ) ) {
			static::$tableName = $options ['tableName'];
			return ;
		} 
		
		//if we don't fount a tableName spec , so we just set it com name
		if( array_key_exists('com', $options ) )  {
			static::$tableName = trim($options['com']) ;
		}
	}

	/**
	 * can be implement in sub class, or be appoint via __construct
	 *
	 * @modify
	 * @desc change to static type
	 * @date 2010-6-27
	 * @author cpingg@gmail.com
	 *
	 */
	protected static $tableName;

	/**
	 * @desc
	 * only supprot own table to which with condition that subjected
	 * condition
	 * array('xx' => $xx, 'xxII' => $xxII );
	 * inCondition
	 * array('yy' => array(1,2,3), 'yyII' => array(3,4,5) )
	 * if it's a empty array be default , return list with no restriction
	 * @param string $tableName
	 * @param unknown_type $condition
	 * @param unknown_type $inCondition
	 * @return stdClass obj
	 */
	public function getByCondition($tableName = '' , $condition = array(), $inCondition = array()) {

		if ($tableName == '') {
			$tableName = static::$tableName;
		}

		if (empty ( $tableName )) {
			throw new DBNullTableNameException();
		}

		// where condition initial value
		// chengjin.wang 2010.10.15
		$CDA = ' 1=1 ';

		//add empty condition support
		//cpingg@gmail.com 2010.7.4
		if (count ( $condition )) {
			foreach ( $condition as $key => $value ) {
				$CDA .= ' AND `' . trim ( $key ) . '`="' . trim ( $value ) . '" ';
			}
		}
		
		// "in" condition support
		// chengjin.wang 2013.10.15
		
		$ICDA = '';
		if (count ( $inCondition )) {
			foreach ( $inCondition as $k => $vArr ) {
				$ICDA .= ' AND `' . trim ( $k ) . '`in (' . implode(",", $vArr ) . ') ';
			}
		}

		$sql = 'SELECT * FROM `' . $tableName . '` WHERE ' . $CDA . $ICDA;
		
		$dbi = DBManager::GetDBI();

		$dbi->query ( $sql );

		return $dbi->getAllObject ();
	}
	
	/**
	 * Select by id
	 * @param unknown_type $tableName
	 * @param unknown_type $id
	 * @throws DBNullTableNameException
	 */
	public function getById($tableName, $id) {
		
		if ($tableName == '') {
			$tableName = static::$tableName;
		}

		if (empty ( $tableName )) {
			throw new DBNullTableNameException();
		}
		
		$sql = 'SELECT * FROM `' . $tableName . '` WHERE id = '.$id;
		$dbi = DBManager::GetDBI();

		$dbi->query ( $sql );

		return $dbi->getObject ();
	}

	public function getDojoStructureObj($tableName = '') {
		if ($tableName == '') {
			$tableName = static::$tableName;
		}

		$objClassName = ucfirst ( $tableName . 'Object' );

		$obj = new $objClassName ();
		$pArray = $obj->getProperties ();
		$obj = null;

		$structArray = array ();
		foreach ( $pArray as $fieldName => $fieldProperties ) {
			$stdObj = new \stdClass ();
			$stdObj->field = $fieldName;
			$stdObj->name = $fieldName;
			$stdObj->width = 'auto';

			if ($fieldName != 'id') {
				$stdObj->editable = true;
			}

			$structArray [] = $stdObj;
		}

		return $structArray;
	}

	/**
	 * return a empty stardand class object by table name
	 * Enter description here ...
	 * @param unknown_type $tableName
	 */
//	public function getEmptyObject($tableName = '') {
//		if ($tableName == '') {
//			$tableName = static::$tableName;
//		}
//
//		$objClassName = ucfirst ( $tableName . 'Object' );
//
//		$obj = new $objClassName ();
//
//		$pArray = $obj->getProperties ();
//
//		$obj = null;
//
//		foreach ( $pArray as $fieldName => $fieldProperties ) {
//			if ($fieldName === 'id') {
//				// if it's id , just assign -1
//				$obj->id = DBObject::ID_INITIAL ;
//				continue;
//			}
//
//			$obj->$fieldName = (! empty ( $fieldProperties ['default'] )) ? $fieldProperties ['default'] : '';
//		}
//
//		return $obj;
//	}

	public function updateRecordByGrid($objList) {

		$resObj = new \stdClass ();
		$resObj->success = array ();
		$resObj->failed = array ();

		if (is_array ( $objList ) && (count ( $objList ) >= 1)) {

			foreach ( $objList as $tmpObj ) {

				try {
					$tableName = $tmpObj->table;
					$objName = DBObject::GetDBIbjectNameByTableName ( $tableName );

					if (! is_object ( $tmpObj->obj )) {
						throw new \Exception ( '$obj->obj is not a obj' );
					}
					$obj = new $objName ( $tmpObj->obj );
					$saveResult = $obj->save ();
					$tmpObj->obj = $obj->getObject (); // fix feedback to update id bug 2010.8.14


					//var_dump( $saveResult ) ;
					if ($saveResult !== false) {
						array_push ( $resObj->success, $tmpObj );
					} else {
						$tmpObj->message = 'save failed';
						array_push ( $resObj->failed, $tmpObj );
					}
				} catch ( \Exception $e ) {
					//echo $e->getMessage();
					$tmpObj->message = $e->getMessage ();
					array_push ( $resObj->failed, $tmpObj );
				}
			}
		}
		return $resObj;
	}

	public function deleteRecordByGrid($objList) {
		$resObj = new \stdClass ();
		$resObj->success = array ();
		$resObj->failed = array ();

		if (is_array ( $objList ) && (count ( $objList ) >= 1)) {

			foreach ( $objList as $tmpObj ) {
				try {
					$tableName = $tmpObj->table;
					$objName = DBObject::GetDBIbjectNameByTableName ( $tableName );

					if (! is_object ( $tmpObj->obj )) {
						throw new \Exception ( '$obj->obj is not a obj' );
					}
					$obj = new $objName ( $tmpObj->obj );
					$saveResult = $obj->delete ();
					$tmpObj->obj = $obj->getObject (); // fix feedback to update id bug 2010.8.14

					//var_dump( $saveResult ) ;
					if ($saveResult !== false) {
						array_push ( $resObj->success, $tmpObj );
					} else {
						$tmpObj->message = 'save failed';
						array_push ( $resObj->failed, $tmpObj );
					}
				} catch( DBRecordNotFoundException $e) {
					array_push ( $resObj->success, $tmpObj );
				} catch ( \Exception $e ) {
					//echo $e->getMessage();
					$tmpObj->message = $e->getMessage ();
					array_push ( $resObj->failed, $tmpObj );
				}
			}
		}
		return $resObj;
	}
}

?>