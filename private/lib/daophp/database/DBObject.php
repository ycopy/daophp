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

/**
 * usage
 *
 * This is the basic data object used in this project ,
 * every objects map with a table's record, and identified by primary key
 *
 * we can initialize this object with a empty array, or a array with values or a data object that pull out from db
 * then ,we can call setField to change a filed value and call saveField to save it into db
 * after all done, we can call save,
 *
 * how to save a object into db
 * 1, $this->setAutoSaveFlag(true), then the modification will be automatically saved into db
 * 2, we can call $this->save() to save all the modifications into db
 *
 * useful funciotns
 *
 * 1, setField
 * 2, saveField
 * 3, save
 * 4, setTableName
 *
 * @version 0.1.16
 * @date 2009-07-16
 * @author ping.cao
 *
 * @date 2010-10-02
 * change pk to [union] primary key
 *
 */


namespace daophp\database ;

use daophp\core\Common;
use daophp\core\object\DPObject ;
use daophp\core\Debug ;


abstract class DBObject extends DPObject implements GetDBI, DBRecordOperation {


	/**
	 * We Can HardCode Change it to any others, such as idUser
	 * Enter description here ...
	 * @var unknown_type
	 */
	//protected static $pkName = 'id' ;
	/**
	 * table name must be assigned by hard code when implements a new DBObject
	 * then can be reassign by setTableName
	 * and DON'T CHANGE IT ANY MORE
	 *
	 * * @var string
	 */
	protected static $tableName;

	/**
	 *
	 * @desc , for some specific purposes, such as dynamic to change the table name, 
	 * you must know what you are doing when you call this methods
	 * @date 2010.10.28
	 * @param unknown_type $tableName
	 */
	public static function setTableName($tableName) {
		static::$tableName = $tableName;
	}
	
	/**
	 * return the object rel table name
	 * @date 2011.05.30
	 * Enter description here ...
	 */
	public static function getTableName() {
		return static::$tableName ;
	}
	
	const DISPLAY_NAME						= 0 ;
	const DISPLAY_FLAG_LIST_VIEW			= 1 ;
	const DISPLAY_FLAG_DETAIL_VIEW			= 2 ;
	const EDITABLE_FLAG						= 3 ;
	const FK_REF_DISPLAY_FIELD				= 4 ;
	const DISPLAY_CALLBACK					= 5 ;		//mainly for 
	const DISPLAY_TPL						= 6 ;
	
	const DISPLAY_FLAG_CREATE_VIEW			= 7 ; 	//FOR CREATE VIEW
	const CUSTOM_DISPLAY					= 8 ;
	
	/**
	 * @author cpingg@gmail.com
	 * @date 2011.06.07
	 * @desc
	 * config for property display name, display flag(whether should display in admin module)
	 * 
	 * array format
	 * 
	 * 	array(
	 * 	0	=> display name
	 *  1	=> list display flag
	 *  2	=> detail display flag
	 *  3	=> editable flag
	 *  4	=> fk ref column to display
	 * )
	 * Enter description here ...
	 * @var unknown_type
	 */
	protected static $propertyConfig = array() ;
	
	public static function getDisplayFlag( $fieldName , $viewType = self::DISPLAY_FLAG_LIST_VIEW ) {
		
		if( !self::fieldExists($fieldName) ) {
		    throw new DBFieldNotExistsException( $fieldName,static::$tableName);
		}
		
		//if static::$propertyConfig not set, return true 
		if( !count( static::$propertyConfig) ) {
			return true ;
		}
		
		if( !isset( static::$propertyConfig[$fieldName] ) ){
			Debug::addWarning(get_called_class().'::propertyConfig['.$fieldName.'] not set , please hardcode in '. get_called_class() .' before use this funciton');
			return true;
		}
		
		if( !isset( static::$propertyConfig[$fieldName][$viewType]) ) {
			return true ;
		}		
		return static::$propertyConfig[$fieldName][$viewType] ;
	}
	
	public static function getFieldDisplayName( $fieldName ){
		if( !self::fieldExists($fieldName) ) {
		    throw new DBFieldNotExistsException( $fieldName,static::$tableName);
		}
		
		//if static::$propertyConfig not set, return fieldname 
		if( !count( static::$propertyConfig) ) {
			return $fieldName ;
		}
		
		if( !isset( static::$propertyConfig[$fieldName] ) ){
			Debug::addWarning(get_called_class().'::propertyConfig['.$fieldName.'] not set , please hardcode in '. get_called_class() .' before use this function');
			return $fieldName ;
		}
		
		if( !isset( static::$propertyConfig[$fieldName][self::DISPLAY_NAME])) {
			Debug::addNotice(get_called_class().'::propertyConfig['.$fieldName.'] not set , please hardcode in '. get_called_class() .' before use this function');
			return $fieldName ;
		}
		
		return static::$propertyConfig[$fieldName][self::DISPLAY_NAME] ;
	}
	
	public static function isEditable( $fieldName ) {
		if( !self::fieldExists($fieldName) ) {
		    throw new DBFieldNotExistsException( $fieldName,static::$tableName);
		}
		
		//if static::$propertyConfig not set, return true 
		if( !count( static::$propertyConfig) || !isset( static::$propertyConfig[$fieldName][self::EDITABLE_FLAG]) ) {
			return false ;
		}
		
		return static::$propertyConfig[$fieldName][self::EDITABLE_FLAG] ;
	}

	public static function isCustomDisplay($fieldName) {
		if( !self::fieldExists($fieldName) ) {
		    throw new DBFieldNotExistsException( $fieldName,static::$tableName);
		}
		
		//if static::$propertyConfig not set, return true 
		if( !count( static::$propertyConfig) || !isset( static::$propertyConfig[$fieldName][self::CUSTOM_DISPLAY]) ) {
			return false ;
		}
		
		return static::$propertyConfig[$fieldName][self::EDITABLE_FLAG] ;
	}
	/**
	 * @date 2011.06.14 
	 */
	private static $emptyObjectCache = array() ;
	
	/**
	 * return a empty instance 
	 * Enter description here ...
	 */
	public static function getEmptyObject () {
		
		$className = get_called_class() ;
		
		if( isset( self::$emptyObjectCache[$className]) ) {
			return self::$emptyObjectCache[$className] ;
		}
		
//		$objName = self::GetDBIbjectNameByTableName(self::getTableName() );
		$instance = new $className();
		
		return self::$emptyObjectCache[$className] = $instance ;
	}
	
	/*
	public static function getDisplayCaptionArray() {
		
		$caption = array() ;
		if( isset( static::$propertyConfig) && count( static::$propertyConfig ) ){
			foreach( static::$propertyConfig as $fieldName => $value ) {
				if( self::getDisplayFlag($fieldName) ) {
					$caption[$fieldName] = $value[self::PROPERTY_DISPLAY_NAME];
				}
			}
		}
		
		return $caption ;
	}*/

	const STATUS_NORMAL =  1;// normal object
	const STATUS_DELETE =  2;//the object will be deleted from db
	const STATUS_ABNOMAL = 3;//NOT SAVE AUTOMATICALLY

	private static $_statusRange = array(
		self::STATUS_NORMAL,
		self::STATUS_DELETE,
		self::STATUS_ABNOMAL
	);
	
	private $status = self::STATUS_NORMAL;

	private function getStatus() {
		return $this->status;
	}

	//update the object status
	private function updateStatus($status) {
		if ( !in_array($status,self::$_statusRange) ) {
			throw new DBInvalidObjectStatusException ( '#Cound only be DBObject::STATUS_NORMAL OR DBObject::STATUS_DELETE OR DBObject::STATUS_ABNORMAL' );
		}
		
		$this->status = $status;
	}
	
	public function setAbnormal() {
		$this->updateStatus(self::STATUS_ABNOMAL);
	}
	
	public function setNormal() {
		$this->updateStatus(self::STATUS_NORMAL);
	}

	//for initial value
	const ID_INITIAL 	= -1;
	const ID_INVALID 	= -2;
	const ID_UNION 		= -3; //

	//the dbo object instance
	//private static $dbi = null;
	/**
	 * @see DBOperation::GetDBI()
	 * @ CAN ACTIVE THIS METHOD FOR MUL DBI SOURCE
	 */
	protected static $dbi = null;
	public static function GetDBI() {
		if ( static::$dbi == null || !(self::$dbi instanceof DBI) ) {
			static::$dbi = DBManager::GetDBI ();
		}

		return static::$dbi;
	}
	

	/**
	 * @see DBOperation::setDBI()
	 *
	 */
	public static function setDBI(DBI $dbi) {
		if ($dbi instanceof DBI) {
			$previous = self::GetDBI();
			static::$dbi = $dbi;
			return $this ;
		} else {
			Debug::error('invalid dbo provided for '. __METHOD__ );
			return false;
		}
	}



	/**
	 * hold the object real value
	 * Enter description here ...
	 * @var unknown_type
	 */
	private $p = array ();
	private function initP() {

		$this->p = self::getPFromPCache();

		if( is_array($this->p) ) {
			$this->setSaveFlag ( false );
			return true;
		}

		return false;
	}
	
	/**
	 * table properties cache
	 * @var array
	 */
	private static $pCache = array ();

	/**
	 * init the properties into cache
	 *
	 * Enter description here ...
	 * @param unknown_type $object, the object ,which should be init
	 * @throws InitTableFieldPropertiesException
	 */
	private static function initPCache(/*$object = null*/) {
		
	    $class = get_called_class ();
	    
		if( empty(static::$tableName)) {
			throw new DBNullTableNameException( $class );
		}

//		$cacheProvider = CacheManager::getCacheProvider(DP_CACHE_TYPE);
		$dbi = self::GetDBI();
		
		if ( !isset ( self::$pCache [$class] ) ) {
			
			$tableDesc = array() ;
			$tableDesc = $dbi->desc ( static::$tableName );
				
			if ($tableDesc === false) {
				throw new DBInitTableFieldPropertiesException ( static::$tableName );
			}			
			
			//var_dump( $tableDesc );			
			assert( is_array($tableDesc) );
	
			foreach ( $tableDesc as $field_info ) {				
			
			    $field_info = array_change_key_case ( $field_info, CASE_LOWER );
				
				//var_dump( $field_info);				
				self::_initFieldDetail( $field_info );
				
				$fieldName = $field_info['field'];				
				/*
			 	* initial id set to self::ID_INITIAL
			 	*/
				if( $fieldName == 'id' && $field_info['extra'] == 'auto_increment' ) {
				    $field_info['value'] = self::ID_INITIAL;
				} else {
				
				    if( $field_info[self::TABLE_FIELD_TYPE] == self::TABLE_FIELD_TYPE_NUMERIC ) {
				        $field_info['value'] = 0;
				    } else if($field_info[self::TABLE_FIELD_TYPE] == self::TABLE_FIELD_TYPE_JSON){
				        $field_info['value'] = array() ;				        
				    } else {
				    	$field_info['value'] = '';				    	 
				    }				
				}

				unset ( $field_info['field'] );

				self::$pCache [$class] [$fieldName] = $field_info;  
				
			}
			
			//var_dump( self::$pCache );
			
			//step 2			
            self::_initKeys();

			//step 3
			//for foreign key info
			// notice, only supported by mysql			
			self::_initForeignKey();	
		}
		
		return true;
	}
	
	
	const TABLE_FIELD_TYPE 		= 0;
	const TABLE_FIELD_PARAMS 	= 1;
	
	private static function _initFieldDetail( & $fieldRowStructure ) {
		$regexPregTypeAndParams = '/(?P<type_key>[^\(]+)(\((?P<type_params>.+)\))?/isu';
		preg_match ( $regexPregTypeAndParams, $fieldRowStructure ['type'], $matches );
		$type_key = trim ( $matches ['type_key'] );		
	
		if ( empty ( $type_key ) ) {
		    return self::TABLE_FIELD_TYPE_UNKNOWN;
		}

		if( !isset( $fieldRowStructure[self::TABLE_FIELD_TYPE]) ) {
		    $fieldRowStructure[self::TABLE_FIELD_TYPE] = self::TABLE_FIELD_TYPE_UNKNOWN;
		}
		
		switch ($type_key) {
			case 'int':
			case 'tinyint':
			case 'smallint':
			case 'mediumint':
			case 'bigint':
			case 'decimal':
			    $fieldRowStructure[self::TABLE_FIELD_TYPE]	= self::TABLE_FIELD_TYPE_NUMERIC;
				break;

			case 'varchar' :
			case 'char':
				$fieldRowStructure[self::TABLE_FIELD_TYPE]	= self::TABLE_FIELD_TYPE_STRING;
				break;

			case 'text':
				$fieldRowStructure[self::TABLE_FIELD_TYPE]	= self::TABLE_FIELD_TYPE_TEXT;
				break;
				
			case 'timestamp':
			case 'date':
			case 'datetime':
				$fieldRowStructure[self::TABLE_FIELD_TYPE]	= self::TABLE_FIELD_TYPE_DATETIME;
				break;
				
			case 'enum' :
				$fieldRowStructure[self::TABLE_FIELD_TYPE]	= self::TABLE_FIELD_TYPE_ENUM;
				break;

			case 'binary' :
				$fieldRowStructure[self::TABLE_FIELD_TYPE]	= self::TABLE_FIELD_TYPE_BINARY ;
				break;

			case 'json':
			    $fieldRowStructure[self::TABLE_FIELD_TYPE] = self::TABLE_FIELD_TYPE_JSON;
			    break;
			default :
				Debug::error("INVALID FILED TYPE FOUND FOR: ". self::getTableName(). ' type: '. $type_key );
				$fieldRowStructure[self::TABLE_FIELD_TYPE]	= self::TABLE_FIELD_TYPE_UNKNOWN;
				break;
		}
		
		if( isset( $matches['type_params']) ) {
		    $fieldRowStructure[self::TABLE_FIELD_PARAMS] = $matches['type_params'] ;
		} 
	}

	/**
	 * return a empty p for the new object 
	 * Enter description here ...
	 */
	private static function getPFromPCache() {
		$class = get_called_class() ;

		if ( !isset ( self::$pCache [$class]) ) {
			self::initPCache();
		}

		assert( is_array( self::$pCache[$class]) );		
		return self::$pCache[$class] ;
	}

	public function getPrimaryKeyAsString() {

		$class = get_called_class ();
		$columns = self::getPrimaryKeyColumns();
	
		$rsArray = array ();
		foreach ( $columns as $column ) {
		    $value = $this->get ( $column);
			$rsArray [] = $column. ':' . $value;
		}

		if ( count ( $rsArray )) {
		    return  '['.static::$tableName . implode ( ',', $rsArray ) .']';
		} else {
		    return '['.static::$tableName . 'empty_pk]';
		}
	}

	/**
	 * fileds that hold the keys
	 * could be a id , or a union primary key
	 *
	 * @var unknown_type
	 */
	private static $primaryKey = array  ();

	private static function _addPrimaryKey( $keys ) {
		$class = get_called_class ();

		if( !isset(self::$primaryKey[$class] ) ) {
			self::$primaryKey[$class] = array() ;
		}
		
		self::$primaryKey[$class] = $keys ;
	}


	public static function getPrimaryKeyColumns() {
		$class = get_called_class ();
		if( isset(self::$primaryKey[$class])) 
			return self::$primaryKey [$class];
		return array() ;
	}

	/**
	 *array(
	 *		$className => array(
	 *								$indexName => array( $field_1, $field_2 );
	 *							);
	 *
	 *)
	 */
	private static $uniqueKey = array() ;
	private static function _addUniqueKey( $unique_key_columns ) {
		$class = get_called_class() ;

		if( !isset(self::$uniqueKey[$class] ) ) {
			self::$uniqueKey[$class] = array() ;
		}

		array_push(self::$uniqueKey[$class], $unique_key_columns);
	}
	
	
	private static function _initKeys() {	    
	    $tableName = static::$tableName;
	    
	    $sql = <<<EOM
SHOW index FROM `{$tableName}`
WHERE `Non_unique`=0
EOM;
	
	    $dbi = DBManager::GetDBI();
        $dbi->query($sql) ;
        $rs = $dbi->getAll( MYSQLI_ASSOC ) ;
        
        assert( is_array($rs) && count($rs) );
        
        $keys = array();
        
        foreach( $rs as $singleRS ) {            
            if( !isset($keys [$singleRS['Key_name']]) ) {                
                $keys[ $singleRS['Key_name'] ] = array();
            }            
            array_push($keys[$singleRS['Key_name']], $singleRS['Column_name']);            
        }	    
	    
       //var_dump( $keys );        
        
        foreach ( $keys as $key => $key_columns ) {            
            if( $key == 'PRIMARY') {                
                self::_addPrimaryKey( $key_columns );
            } else {
                self::_addUniqueKey( $key_columns ) ;
            }
        }
        
        //var_dump( $keys );
	}	
	
	private static $foreignKey = array() ;	
	private static function _initForeignKey() {	    
	    
	    Debug::core('init foreign key');
	    
		$class = get_called_class() ;
		if( isset(self::$foreignKey[$class] ) ) {
			return ;
		}
		
		self::$foreignKey[$class] = array() ;
	
		$dbi = self::GetDBI();
		$tableName = self::getTableName();
		
		$allFields      = self::getAllFieldNames() ;
		$DBName 		= $dbi->getDBName() ;
		$tableName 		= self::getTableName();
		
		foreach( $allFields as $fieldName ) {
			$columnName = $fieldName ;
$sql = <<<EOM
SELECT
	`REFERENCED_TABLE_SCHEMA` 	as db_name,
	`REFERENCED_TABLE_NAME` 	as db_table,
	`REFERENCED_COLUMN_NAME`	as db_column
FROM 
	`information_schema`.`KEY_COLUMN_USAGE`
WHERE
	`COLUMN_NAME`='{$columnName}'
	AND `KEY_COLUMN_USAGE`.`TABLE_SCHEMA`='{$DBName}'
	AND `KEY_COLUMN_USAGE`.`TABLE_NAME`='{$tableName}'
	AND `REFERENCED_TABLE_SCHEMA`!=''
	AND `REFERENCED_TABLE_NAME`!=''
	AND `REFERENCED_COLUMN_NAME`!=''
EOM;

				try{
					$dbi->query( $sql );
					$rs = $dbi->getAll( MYSQLI_ASSOC );
				} catch ( \Exception $e ) {
					Debug::error('Init foreign key exception , fetch foreign key error, tableName: '. $tableName. ', filed name: '. $fieldName ) ;
					throw $e;
				}
				
				//one column can only refer to one column of the other table
				if( count( $rs ) === 1 ) {
					$rs = $rs[0] ;
					self::$foreignKey[$class][$columnName] = array(
						self::FK_REF_DB_NAME		=> $rs['db_name'] ,
						self::FK_REF_DB_TABLE_NAME	=> $rs['db_table'] ,
						self::FK_REF_DB_COLUMN_NAME	=> $rs['db_column'] 
					) ;
				} else if( count( $rs) > 1 ) {
					Debug::core('column: '. $tableName . '.' . $columnName .' has multi fk, skip') ;
				} 
				else {
					Debug::core('column: '. $tableName . '.' . $columnName .' is not a fk, skip') ;
				}
			}
		if( count( self::$foreignKey[$class] )) {
			Debug::core( Common::pr( self::$foreignKey[$class], true, 'fk for: '.  self::getTableName() ) ) ;
		} else {
			Debug::core('NO FK FOUND, Table: '. self::getTableName() ) ;
		}		
	}
	
	public static function getForeignKeyArray() {
		self::initForeignKey();
		return self::$foreignKey[get_called_class()] ;		
	}
	
	public static function isForeignKey( $fieldName ) {
		if (! self::fieldExists ( $fieldName )) {
		    throw new DBFieldNotExistsException( $fieldName , static::$tableName);
		}
		
		$foreignKeyArray = self::getForeignKeyArray();
		
		if( count( $foreignKeyArray) == 0 ) {
			return false;
		}
		
		
		return isset( $foreignKeyArray[$fieldName ] );		
	}
	
	
	const FK_REF_DB_NAME			= 0 ;
	const FK_REF_DB_TABLE_NAME		= 1 ;
	const FK_REF_DB_COLUMN_NAME		= 2 ;
	
	public static function getReferenceInfo( $fieldName, $type ) {
		
		if (! self::fieldExists ( $fieldName )) {
		    throw new DBFieldNotExistsException( $fieldName , static::$tableName);
		}
		
		$typeArray = array(
			self::FK_REF_DB_COLUMN_NAME,
			self::FK_REF_DB_NAME,
			self::FK_REF_DB_TABLE_NAME,
		);
		
		if( !in_array( $type, $typeArray ) ) {
			throw new \Exception( 'INVALID OPERATION, INVALID TYPE FOR '. _METHOD__) ;
		}
		
		if( !self::isForeignKey($fieldName)) {
			throw new \Exception( 'INVALID OPERATION,'. static::$tableName . $fieldName. ' is not a fk') ;
		}
		
		$foreignKeyArray = self::getForeignKeyArray();

		return $foreignKeyArray[$fieldName][$type] ;
	}
	
	public static function getForeignKeyRefDBName( $fieldName ) {
		return self::getReferenceInfo($fieldName, self::FK_REF_DB_NAME ) ;
	}
	
	
	public static function getForeignKeyRefTableName( $fieldName ) {
		return self::getReferenceInfo($fieldName, self::FK_REF_DB_TABLE_NAME ) ;
	}
	
	public static function getForeignKeyRefColumnName( $fieldName ) {
		
		if( isset( static::$propertyConfig[$fieldName][self::FK_REF_DISPLAY_FIELD]) ) {
			return static::$propertyConfig[$fieldName][self::FK_REF_DISPLAY_FIELD] ;
		}
		return self::getReferenceInfo($fieldName, self::FK_REF_DB_COLUMN_NAME ) ;
	}
	
	public static function getForeignKeyRefDBObjectName( $fieldName ) {
		return self::GetDBIbjectNameByTableName( self::getForeignKeyRefTableName($fieldName) );
	}

	public static function getUniqueKeys() {
		$class = get_called_class ();

		if( !isset(self::$uniqueKey[$class] ) ) {
			self::$uniqueKey[$class] = array() ;
			self::initPCache();
		}

		return self::$uniqueKey [$class];
	}

	const TABLE_FIELD_TYPE_NUMERIC	    = 'numeric' ;
	const TABLE_FIELD_TYPE_STRING 		= 'string' ;
	const TABLE_FIELD_TYPE_TEXT			= 'text' ;
	const TABLE_FIELD_TYPE_DATETIME 	= 'datetime' ;
	const TABLE_FIELD_TYPE_ENUM 		= 'enum' ;
	const TABLE_FIELD_TYPE_BINARY		= 'binary' ;
	const TABLE_FIELD_TYPE_JSON         = 'json';
	const TABLE_FIELD_TYPE_UNKNOWN 		= 'unknown';
	
	public static function getFieldParams( $fieldName ) {
		
		if (! self::fieldExists ( $fieldName )) {
		    Debug::warning( DP_LT . $fieldName . ' not exist in table: ' . static::$tableName . DP_GT );
			return self::TABLE_FIELD_TYPE_UNKNOWN;
		}
		
		$pCache = self::getPFromPCache() ;
		
		if( !isset($pCache[$fieldName][self::TABLE_FIELD_PARAMS]) ) {
			Debug::warning( $fieldName . DP_LT. self::getFieldType($fieldName) .DP_GT .' do not have a params property') ;
			return 0;
		}
		return $pCache[$fieldName][self::TABLE_FIELD_PARAMS] ;
	}
	
	public static function getFieldType($fieldName) {
		
		if (!self::fieldExists ( $fieldName )) {
			Debug::warning ( DP_LT . $fieldName . ' not exist in table: ' . static::$tableName . DP_GT );
			throw new DBFieldNotExistsException( $fieldName, static::$tableName );
		}
		
		$pCache = self::getPFromPCache() ;
		return $pCache[$fieldName][self::TABLE_FIELD_TYPE] ;
	}
	
	public static function isEnum( $fieldName ) {
		
		if (! self::fieldExists ( $fieldName )) {
		    throw new DBFieldNotExistsException( $fieldName, static::$tableName );
		}

		return self::getFieldType($fieldName) == self::TABLE_FIELD_TYPE_ENUM ;
	}
	
	public static function isDate( $fieldName ) {
		return self::getFieldType($fieldName) == self::TABLE_FIELD_TYPE_DATETIME ;
	}
	
	public static function isString( $fieldName ) {
		return self::getFieldType($fieldName) == self::TABLE_FIELD_TYPE_STRING ;
	}
	
	public static function isText( $fieldName ) {
		return self::getFieldType($fieldName) == self::TABLE_FIELD_TYPE_TEXT ;
	}
	
	public static function isNumeric( $fieldName ) {
	    return self::getFieldType($fieldName) == self::TABLE_FIELD_TYPE_NUMERIC;
	}

	/**
	 * if it's a enum type, return the enum list as a array
	 * Enter description here ...
	 * @param unknown_type $fieldName
	 * @throws DBInvalidFieldNameException
	 */
	public static function getEnumKeyList($fieldName, $reverseKey = false ) {
		$enumList = explode(',', str_replace ( '\'', '', self::getFieldParams($fieldName) ) );
		
		if( $reverseKey ) {
			return array_flip($enumList) ;
		}
		
		return $enumList;
	}

	public static function getFieldRowStructure($fieldName) {
		if (! self::fieldExists ( $fieldName )) {
		    throw new DBFieldNotExistsException( $fieldName, static::$tableName );
		}

		$pp = self::getPFromPCache() ;
		return $pp [$fieldName];
	}

	public static function fieldExists($fieldName) {
	    
	    if( empty( $fieldName) || !is_string( $fieldName ) ) {
	        throw new \InvalidArgumentException('filedname is string type');	        
	    }	    
		
		$class = get_called_class ();
		$pp = self::getPFromPCache();		
	
		return isset( $pp[$fieldName] );
	}

	public static function getAllFieldNames() {
		$pp = self::getPFromPCache();
		return array_keys( $pp ) ;
	}
	
	/**
	 * return the filed name if find any
	 * Enter description here ...
	 */
	private static function getAutoIncrementField() {
		
		$fields = self::getAllFieldNames();
		
		$find = '' ;
		foreach ($fields as $field ) {
			if( self::isAutoIncrement($field)) {
				$find = $field;
				break;
			}
		}
		
		return $find;
	}
	
	
	public static function isAutoIncrement( $fieldName ) {
		
		//echo 'class name :<isPrimaryKey>' . get_called_class() . DP_NEW_LINE ;
		if (! self::fieldExists ( $fieldName )) {
		    throw new DBFieldNotExistsException( $fieldName, static::$tableName );
		}
		
		$pp = self::getPFromPCache();
		
		return $pp[$fieldName]['extra'] == 'auto_increment' ;
		
//		Debug::addLog( Common::pr( $pp , true, 'pp') );		
	}

	/**
	 *
	 * Enter description here ...
	 * @param array or string $fieldName
	 * @throws DBInvalidFieldNameException
	 */
	public static function hasUnique( array $columns ) {

	    $class = get_called_class();
	    $unique_keys = self::$uniqueKey[$class];
	    
	    assert( is_array($unique_keys) );
	    
	    foreach ( $unique_keys as $unique_key_columns ) {	        
	        
	        $tmp = array();
	        foreach( $unique_key_columns as $column ) {	    
	            if( in_array($column, $columns)) {
	                array_push( $tmp, $column );
	            } else {
	                break;
	            }
	        }
	        
	        if( count( $tmp ) == count( $unique_key_columns)) {	            
	            return true;
	        }
	    }
	    
	    return false;
	}
	
	
	/**
	 * 
	 * 
	 *	
	 * get the Object Class name ,
	 *
	 * you must obey the basic name convention in EP,if you wanna to use this method
	 * Enter description here ...
	 * @param unknown_type $tableName
	 */
	public static function GetDBIbjectNameByTableName($tableName) {
		$tmp = explode ( '_', $tableName );
		
		foreach ( $tmp as & $value ) {
			$value = ucfirst ( $value );
		}
		
		return implode ( '', $tmp ) . 'Object';
	}

	/**
	 * @will be removed in future
	 * @param $fieldName
	 */
	public static function hasPrimary( array $columns ) {

	    $class = get_called_class();
	    $key_columns= self::$primaryKey[$class];
	    
	    assert( is_array($key_columns) );    
        
        $tmp = array();
        foreach( $key_columns as $column ) {
            if( in_array($column, $columns)) {
                array_push( $tmp, $column );
            } else {
                break;
            }
        }
        
        return count( $tmp ) == count($key_columns); 
	}

	public static function isNotNull($fieldName) {
		if (! self::fieldExists ( $fieldName )) {
		    throw new DBFieldNotExistsException( $fieldName, static::$tableName );
		}

		$pp = self::getPFromPCache();
		return $pp [$fieldName] ['null'] == 'NO';
	}

	public function hasDefault($fieldName) {
		if (! self::fieldExists ( $fieldName )) {
		    throw new DBFieldNotExistsException( $fieldName, static::$tableName );
		}

		$pp = self::getPFromPCache() ;
		return empty ( $pp [$fieldName] ['default'] );
	}

	public function getDefault($fieldName) {
		if (! self::hasDefault ( $fieldName )) {
			Debug::addWarning ( $fieldName . 'HAS NO DEFAULT VALUE' );
			return '';
		}

		$pp = self::getPFromPCache();
		return $pp [$fieldName] ['default'];
	}

	private function _get_primary_key_cda() {
	    
	    $columns= self::getPrimaryKeyColumns();
		
		if( !count($columns) ) {
			return array() ;
		}
		
		$_params = array();
		foreach ( $columns as $column ) {
		    $value = trim ( $this->get ( $column) );

			//echo $this->get(static::$pkName) ;
			if ( empty ( $value ) ) {
				//Debug::add ( "#WARNING: GENERATE PRIMARY KEY FAILED, EMPTY KEY FOUND " . DP_LT . 'fieldName:' . $fieldName . DP_GT;
				return array() ;
			}
			
			$_params[$column] = $value;
		}

		return $_params;
	}

	private function _get_primary_key_cda_string() {
	    return self::_cda_to_query_string( $this->_get_primary_key_cda() );
	}

	/**
	 * 
	 * support "<=,<,>=,>,=,!=,like, not between, between" right now,
	 * for range , array (
	 * 		0 	=> 
	 * 		1 	=> string|array('start','end') //for between
	 * )
	 * 
	 * 		$getCdl = array(
					'id' => array('not_in', array(1) )
				);
	 * Enter description here ...
	 * @param unknown_type $params
	 */

	
	private static $SQLOperation = array('<=' => 1,'<' => 1, '>=' => 1, '>' => 1, '=' => 1, '!=' => 1,'in'=>2,'not_in'=> 2,'like' => 1,'not_between' => 2 ,'between' => 2 /*2 tell two parameter*/ );
	private static function _cda_to_query_string($params) {
	    
	    assert( is_array($params) );
	    
		$params = self::_filterParams ( $params );
		$cda_string = '';


		//2011.07.13 @add range feature, cpingg@gmail.com
		foreach ( $params as $fieldName => $value ) {
			
			if( !is_array($value) ) {				
				if( self::isNumeric( $fieldName) ) {
					$value = $value + 0 ;
				} else {
					$value = self::_embrace_char( $value, '"' );
				}
				$cda_string.= ' AND `' . self::getTableName() . '`.`' . $fieldName . '`=' . $value ;
			} else {
				if( count($value) === 2 ) {
					if( !isset( self::$SQLOperation[$value[0]]) ) {
						throw new \Exception('INVALID PARAMETER(WRONG SQLOperation TYPE) FOR ' .__METHOD__ .' SHOULD BE: ('. implode( self::$SQLOperation, ',') .') ') ;
					}
				
					if( $value[0] == 'between' ) {
						if( !is_array($value[1]) || (count($value[1]) !==2 )) {
							throw new \Exception('INVALID PARAMETER(MUST BE ARRAY WITH START AND END FOR BETWEEN SQL CONDITION, SUCH AS [xx => array( "between", array("start", "end" )] '.__METHOD__);
						}
						
						$cda_string.= ' AND `'.self::getTableName() .'`.`' . $fieldName .'` BETWEEN "'. $value[1][0] .'" AND "'.$value[1][1].'" ';
					} else if( $value[0] == 'not_between') {
						if( !is_array($value[1]) || (count($value[1]) !==2 )) {
							throw new \Exception('INVALID PARAMETER(MUST BE ARRAY WITH START AND END FOR BETWEEN SQL CONDITION, SUCH AS [xx => array( "not_between", array("start", "end" )] ) '.__METHOD__);
						}
						
						$cda_string.= ' AND `'.self::getTableName() .'`.`' . $fieldName .'` NOT BETWEEN "'. $value[1][0] .'" AND "'.$value[1][1].'" ';
					} else if( $value[0] == 'like') {
						
						//SUCH AS [xx => array( "like" , "like_string" )] 
					    $cda_string.= ' AND `' . self::getTableName() . '`.`' . $fieldName . '` LIKE "%' . $value[1] . '%" ';
					} else if( $value[0] == 'in') {
						
						if( !is_array($value[1]) || count($value[1] ) == 0 ) {
							throw new \Exception('INVALID PARAMETER(MUST BE ARRAY FOR IN SQL CONDITION, SUCH AS [xx => array( "in" => array("one", "two","three" )] ) '.__METHOD__);
						}
						
						$value[1] = array_map( array( 'self', 'addQuote'), $value[1] ) ;
						$cda_string.= ' AND `'.self::getTableName() .'`.`' . $fieldName .'` IN (' . implode( $value[1] , ',' ) .')';
					} else if( $value[0] == 'not_in') { 
						if( !is_array($value[1])) {
							throw new \Exception('INVALID PARAMETER(MUST BE ARRAY FOR IN SQL CONDITION, SUCH AS [xx => array( "not_in" => array("one", "two","three" )] ) ) '.__METHOD__);
						}
						
						$value[1] = array_map( array( 'self', 'addQuote'), $value[1] ) ;
						$cda_string.= ' AND `'.self::getTableName() .'`.`' . $fieldName .'` NOT IN (' . implode( $value[1] , ',' ) .')';
					} else if( in_array($value[0], array('<=','>=','>','<','=','!=') ) ){
						// for normal sql condition operation, <=, <, >=, >, =, !=
					    $cda_string.= ' AND `' . self::getTableName() . '`.`' . $fieldName . '`'. $value[0] .'"' . $value[1] . '" ';
					} else {
						throw new \Exception('INVALID PARAMETER(WRONG FIELDS PARAMETER) FOR: ' .$fieldName.':'.  implode(',', $value ) .__METHOD__) ;
					}
				} else {
					throw new \Exception('INVALID PARAMETER(WRONG PARAMETER NUMBER) FOR ' .__METHOD__ . ', detailed parameter: '. print_r( $value, true ) ) ;
				}
			}
		}

		return $cda_string;
	}

	public static function _embrace_char( $src, $char ) {
	    return $char  . $src . $char;
	}
	

	/**
	 * @see DBOperation::exists()
	 * @desc
	 * exists in db check
	 * @notice
	 * @return boolean
	 */
	public function exists() {
	    $primary_cda = $this->_get_primary_key_cda() ;

		if ( !count ( $primary_cda) ) {
			return false;
		}

		return count ( self::find ( $primary_cda, array() ) ) === 1 ;
	}

	public static function getByID( $id ) {
		
		if(empty($id) || intval($id) < 0) {
			return null;
		}
		
		return self::findOne( array('id' => $id ), array() ,true );
	}
	
	public function reload( $reload_cda = array(), $reloadField = '' ) {

	    if( !count( $reload_cda) ) {
	        $reload_cda = $this->_get_primary_key_cda() ;
		}
		
		if( !count( $reload_cda )) {		    
		    $reload_cda = $this->getArray();
		    
		    foreach ( $reload_cda as $key => $value ) {		        
		        if( self::isAutoIncrement( $key ) || (self::isString($key) && empty($reload_cda[$key])) ) {		            
		            unset($reload_cda[$key]);
		        }		        
		    }
		}

		if( !count( $reload_cda) ) {
			$this->updateStatus( self::STATUS_ABNOMAL );			
			throw new DBObjectReloadException( $this , new \Exception('empty key found') ) ;
		}
		
		$tmp_obj = self::findOne( $reload_cda);

		if( !$tmp_obj ) {
			//flag the object abnormal
			$this->updateStatus( self::STATUS_ABNOMAL );
			throw new DBObjectReloadException($this, new \Exception('reload object failed') );
		}
		
		$this->beginPullFromDB ();		
		if( empty($reloadField) ) {
			$result = ( bool ) $this->initByObject ( $tmp_obj->getObject() );
		} else {
			$result = $this->set($reloadField, $tmp_obj->get($reloadField));
		}		
		$tmp_obj = null ;
		$this->endPullFromDB ();

		return $result;
	}

	public static function hasAny(array $CDAArray = array()) {

		if (! is_array ( $CDAArray ) ) {
			Debug::warning ( 'PROVIDED PARAMS MUST BE ARRAY OR OBJECT IN: ' . __METHOD__ );
			return false;
		}

		$count = count ( self::find ( $CDAArray ) );
		$rs = ($count > 0);

		Debug::core ( "Found " . $count . ": CDA: " . self::_cda_to_query_string($CDAArray) );
		return $rs;
	}

	/**
	 * to be saved by __destruct() flag
	 * @var unknown_type
	 */
	private $toBeSavedFlag = false;

	/**
	 * flag of whether is pulling data from db
	 * if yes, dont trigger the toBeSaveFlag
	 * @date 2010.6.18
	 * @cpingg@gmail.com
	 * @var $isPullFromDB string
	 */
	private $isPullFromDB = false;

	private function beginPullFromDB() {

		/**
		 * fix auto save bug ,
		 * this happen when we init by key
		 * @date 2010.10.19
		 */
		$this->setSaveFlag ( false );
		$this->setPullFromDBFlag ( true );
		
		return $this;
	}

	private function endPullFromDB() {
		$this->setPullFromDBFlag ( false );
		return $this;
	}

	private function setPullFromDBFlag($flag) {
		$this->isPullFromDB = ( bool ) $flag;
	}
	private function getPullFromDBFlag() {
		return $this->isPullFromDB == true;
	}

	/**
	 * only update the flag when we are not pull data from db
	 * @param unknown_type $flag
	 */
	private function setSaveFlag($flag) {
		if (! $this->getPullFromDBFlag ()) {
			$this->toBeSavedFlag = ( bool ) ($flag);
		}
	}

	private function getSaveFlag() {
		return $this->toBeSavedFlag == true;
	}

	/*
	 * mixed stdClass , array , int 
	 *
	 * if it's a id,
	 *
	 * call initById, this will pull from  db record to construct this obj
	 *
	 * if it's a array,
	 * first,  
	 * 
	 * a unique filed or primary key included in the array , do a exist check from db ,
	 * if it's exists , we will pull from db recrod to construt this obj also
	 * otherwise, just call initByArrayParams to initialize the obj, but it will not be save into db until you call save, or auto call save
	 *
	 * second , no unique filed found , just call initByArrayParams
	 *
	 *
	 * if it's stdObject, call initByObject to initilize the obj
	 *
	 * @2010.6.20
	 * @cpingg@gmail.com
	 *
	 * @2010.10.3
	 * change initById to key check
	 */
	public function __construct($params = array()) {
		parent::__construct ( $params );

		//self::GetDBI();
		$this->initP();
		
		assert( is_array($params) );
		
		//for performance 2011.08.01
		if( count($params) == 0 ) {
			Debug::core ( '#INIT EMPTY OBJ FOR '. get_called_class() ) ;
			return ;
		}
		
		$type = gettype ( $params );
		$initParams = array();
		
		if( $type == 'integer' || $type == 'string' ) {
			$initParams[static::$pkName] = $params ;
		} else if( $type == 'object') {
			$initParams =  Common::objectToArray($params) ;
		} else if( $type == 'array' ) {
			$initParams = $params; 
		}

		$t_obj = null ;
		if( is_array($initParams) && count( $initParams) ) {
			$t_obj = self::getFirstByCDA( $initParams, array(), true );
		}
		
		if( $t_obj ) {
			$this->beginPullFromDB ();
			$result = ( bool ) $this->initByArray($t_obj->getArray()) ;
			$this->endPullFromDB ();
			$t_obj = null;
			return ;
		}

		//restrict type, for empty array bug
		switch ($type) {
			case 'object':
				$params = Common::objectToArray($params) ;
			case 'array' :
					$this->initByArray ( $params );
				break;
			case 'string' :
			case 'integer' :
				$this->set( static::$pkName, $params ) ;
				break;
			default :
			Debug::addCoreLog ( '#INIT EMPTY OBJ FOR '. get_called_class() ) ;
				break;
		}
	}

	/**
	 * filter the params that not belong to the object
	 * @param unknown_type $params
	 */
	private static function _filterParams($params) {
		//Debug::add( get_called_class() );
		$params_rs = array ();
		foreach ( $params as $field => $value ) {
			if (self::fieldExists ( $field )) {
				$params_rs [$field] = $value;
				
				// optimization tip: if it's in ,and value only contain one member
				if( is_array($value) && count($value) == 2 && $value[0] == 'in' && count($value[1]) == 1 ) {
					$params_rs [$field] = $value[1][0] ;
				}
			}
		}

		return $params_rs;
	}


	private static function _filterParamsByPrimaryKey( array $params ) {	    
	    assert( is_array($params) );	    
	    $params_count = count($params);	    
	    $primary_key_columns = self::getPrimaryKeyColumns();	    
	    if( count( $primary_key_columns) != count( $params ) ) return array();
	    
	    $_params = array();
	    foreach( $primary_key_columns as $column ) {	        
	        if( isset( $params[$column]) ) {	            
	            $_params[$column] = $params[$column];
	        }
	    }	    
	    if( count( $primary_key_columns) == count( $_params) ) {	        
	        return $_params;
	    }
	    
	    return array();
	}

	/**
	 * IF GET ANY UNIQUE KEY, RETURN TRUE, AND SET TO $uniqueyKeyArray
	 * Enter description here ...
	 * @param unknown_type $srcParamsArray
	 */
	private static function _filterParamsByUniqueKey( array $params ) {

	    assert( is_array($params) );   

	    $unique_keys = self::getUniqueKeys();
	    
	    $params= self::_filterParams($params);
	    
	    foreach( $unique_keys as $key ) {	        
	        if( count( $key) > count( $params) ) continue;
	        
	        $_params = array();
	        foreach( $key as $column ) {
	            if( isset( $params[$column]) ) {
	                $_params[$column] = $params[$column];
	            }
	        }
	        if( count( $key) == count( $_params) ) {
	            return $_params;
	        }	        
	    }
	    
	    return array();
	}

	private function initByArray(array $array) {
		//Debug::add ( "#INIT BY ARRAY PARAMS");
		$array = self::_filterParams ( $array );
		
		//var_dump($array);

		if (count ( $array ) == 0) {
			//Debug::addError('invalid init params array');
			return false;
		}

		foreach ( $array as $key => $value ) {
			if(self::getFieldType($key) == self::TABLE_FIELD_TYPE_JSON) {
				if(is_string($value)) {
					$this->set($key, json_decode($value, true));	
				} else {
					$this->set($key, $value);
				}
			} else {
				$this->set ( $key, $value );
			}
		}
		return true;
	}

	private function initByObject($obj) {
		//Debug::add ( "#INIT BY OBJ ");
		if (! is_object ( $obj )) {
			Debug::addLog ( '#WARNING: PROVIDED IS NOT A OBJECT' );
			return false;
		}

		$paramsArray = get_object_vars ( $obj );
		return ( bool ) $this->initByArray ( $paramsArray );
	}

	/**
	 * this method can be used only if the name is unique
	 * @param unknown_type $name
	 */
	private function initByUniqueName($name) {
		return ( bool ) $this->initByUniqueField ( 'name', $name );
	}

	/**
	 * retrieve record line from db
	 * @var array $fields
	 *
	 * @return bool return false if failed
	 */


	/**
	 *  array( array('type'(desc|asc), fields(array( $fieldOne, $fieldsTwo, ... )) ), ... )
	 * Enter description here ...
	 * @param unknown_type $additionCDA
	 */
	private static function _order_info_to_string( $order_infos) {
	    if( !count($order_infos) ) {
			return '' ;
		}

		$orderBy = ' ORDER BY ' ;

		foreach( $order_infos as $order ) {		    
		    if( count($order) != 2 ) continue;
		    
		    assert( strtolower($order[0]) == 'asc' || strtolower($order[0]) == 'desc' );
		    assert( is_array($order[1]) && count($order[1]) );
		    
		    $embrace_fields = array();
		    foreach( $order[1] as $order_field ) {
		       array_push( $embrace_fields, self::_embrace_char( $order_field, '`'));		        
		    }
		    
		    $orderBy .= implode(',', $embrace_fields);
		    $orderBy .= ' '. $order[0];
		}
		
		return $orderBy;
	}	
	
	/**
	 * $limit_infos => array( 'begin', 'count' )
	 * Enter description here ...
	 * @param unknown_type $pageInfoArray
	 */
	private static function _limit_info_to_string( $limit_infos ) {	    
	    if( count( $limit_infos) == 1 ) {	        
	        return ' LIMIT '. $limit_infos[0];
	    }
	    
	    if( count( $limit_infos) == 2 ) {
    	    return ' LIMIT '. $limit_infos[0] . ', '. $limit_infos[1];
	    }
	    
	    throw new \InvalidArgumentException('limit info must array(count) or array(begin, count)');
	}
	
	private static function _tidy_cda_for_query( array $cda, bool & $has_unique ) {
	    
	    $has_unique = false;
	    
	    $CDA = array();
	    if( !count( $cda) ) {
	        Debug::warning( "#WARNING, PROVIDED CDA IS EMPTY, load all" );
	    } else {
	        $CDA = self::_filterParams ( $cda);
	        $CDA_FOR_PRIMARY = self::_filterParamsByPrimaryKey($CDA) ;
	        $CDA_FOR_UNI = array() ;
	        
	        //var_dump( $CDA_FOR_PRIMARY);
	        
	        //if no primary key found, try unique key
	        if( count( $CDA_FOR_PRIMARY) ) {
	            $CDA = $CDA_FOR_PRIMARY;
	            $has_unique = true;
	        } else {
	            //begin filter for unique, and use CDA BAK
	            $CDA_FOR_UNI = self::_filterParamsByUniqueKey( $CDA ) ;
	            if( count($CDA_FOR_UNI)) {
	                $CDA = $CDA_FOR_UNI;
	                $has_unique = true;
	            }
	        }
	    }
	    
	    assert ( is_array ( $CDA ) );	
	    
	    return $CDA;
	}
	
	/**
	 * @param array $CDAArray
	 * @$CDA example
	 *     $CDAArray = array (
	 *         'id' => 100  // means id=100
	 *     )
	 *     $CDAArray = array(
	 *         'id' => array ('>', 100)  // means id>100
	 *     )
	 *	   support "<=,<,>=,>,=,!=,like, not between, between" right now,
	 *
	 * @param array $additional
	 * @$additional example
	 *     array(
	 * 	       'order' => array( 
	 *                        array('type'[desc|asc], fields (array( $fieldOne, $fieldTwo, ... )) ), ... 
	 *                 )
	 *         'limit' => array( 'start[int]', 'count[int]' )
	 *     )
	 * @return Result set of querying by CDA
	 */
	public static function find( array $CDA = array(), array $additional_cda = array() ) {	    
    
	    if( !is_array( $CDA) ) {	        
	        throw new \InvalidArgumentException('$CDA must be array');
	    }
	    
	    $has_unique = false;	    
	    $CDA = self::_tidy_cda_for_query( $CDA , $has_unique);

   
	    $CDS = self::_cda_to_query_string( $CDA );		
		
		$sqlForQueryTPL = "SELECT * FROM `" . static::$tableName . "`" . " WHERE " . "1" ;		
		$sqlForQueryTPL .= $CDS;
	
		$orderByString 						= '' ;
		$limitString 						= '' ;
		
		if( !$has_unique && count( $additional_cda ) ) {
		    if( isset($additional_cda['order'] ) && count( $additional_cda['order'] ) ) {
		        $orderByString = self::_order_info_to_string( $additional_cda['order'] ) ;
		        
		        $sqlForQueryTPL .= $orderByString;
			}
			
			if( isset($additional_cda['limit']) && is_array($additional_cda['limit']) ) {
			    $limitString = self::_limit_info_to_string( $additional_cda['limit'] ) ;				
			}
		}
		
		if($has_unique) {
		    $limitString = ' LIMIT 1';
		}
		else {		    
		    if( $limitString == '') {
    		    $limitString = ' LIMIT 0, 30';
		    }
		}	
		
		$sqlForQueryTPL .= $limitString;		

		$rs = false;
		$allArray = array() ;
		
		$dbi = self::GetDBI();
		
		try {
			$rs = $dbi->query ( $sqlForQueryTPL );
			$allArray = $dbi->getAll( MYSQLI_ASSOC );
		} catch (\Exception $e ) {
			Debug::exception($e) ;
			throw $e;
		}		
		
		$all = array ();		
		$className = get_called_class ();
		try {
			foreach($allArray as &$sa ) {
				$t = new $className ();
				$t->beginPullFromDB ();
				$t->initByArray ( $sa );
				$t->endPullFromDB ();
				array_push ( $all, $t );
			}
		} catch ( \Exception $e ) {
			Debug::exception( $e );
			return array ();
		}

		return $all;
	}
	
	public function count(array $CDA = array()){
		return count(self::find($CDA));
	}
	
	public static function findAll() {
		$dbi = $dbi = self::GetDBI();
		$sql = "SELECT * FROM `" . static::$tableName . "`";
		$dbi -> query( $sql );
		return $dbi->getAllObject ();
	}

	//return the first obj by CDA
	//2011.01.19
	//cpingg@gmail.com
	public static function findOne( array $CDA = array() , $additional_cda = array() ) {		
        $rs = self::find( $CDA , $additional_cda ) ;
		if( count($rs) ) {
			return $rs[0] ;
		} else {
			return null ;
		}
	}
	
	public static function findById($id) {
		return self::findOne(array('id' => $id ));
	}
	
	//return a unique object identified by the key,value pair
	//2011.05.5
	//cpingg@gmail.com
	public static function findByUniqueKey( array $column_infos  ) {
	    if( !self::hasUnique( array_keys($column_infos)) ) {
			Debug::warning('Invalid unique key provided, must be in :' . implode(',', self::getUniqueKey() ) );
			return null;
		}
		return self::findOne( $column_infos );
	}

	public function set($fieldName, $value) {

		if (! self::fieldExists ( $fieldName )) {
		    throw new DBFieldNotExistsException( $fieldName, static::$tableName );
		}
		
		if( self::isAutoIncrement($fieldName) && intval($value) < 0 ) {
			Debug::notice('SET NUMBER LESS THAN ZERO FOR A AUTO_INCREMENT KEY, '. $fieldName . ':'. $value );
			return ;
		}
		
		if( self::getFieldType($fieldName) === self::TABLE_FIELD_TYPE_NUMERIC ) {
		    
		    if( !is_numeric($value) ) {
		          throw new DBFieldValueCheckException($fieldName, static::$tableName);
		    }
		
		    $value = $value + 0;
		}
		
		$_has_to_set = false;
		$_is_json = false;
		if(self::getFieldType($fieldName) === self::TABLE_FIELD_TYPE_JSON) {
			
			//var_dump($value);
			//var_dump($this->p [$fieldName] ['value']);
			
			$diff1 = array_diff($this->p [$fieldName] ['value'], $value );
			$diff2 = array_diff($value,$this->p [$fieldName] ['value'] );			
			$_has_to_set = count($diff1) !== count($diff2) ;
			$_is_json = true;
		} else {
			$_has_to_set = $this->p [$fieldName] ['value'] !== $value;
		}

		if ($_has_to_set) {
			if($_is_json) {
				Debug::info('SET FIELD, '. $fieldName . ': from: '. json_encode($this->p [$fieldName] ['value']) . ' to: '. json_encode($value) );
			} else {
				Debug::info('SET FIELD, '. $fieldName . ': from: '. $this->p [$fieldName] ['value'] . ' to: '. $value );				
			}
			$this->p [$fieldName] ['value'] = $value;
			$this->setSaveFlag ( true );
		}
	}
	
	const DB_FETCH_FLAG_CHECK_FK		= 0x01;
	const DB_FETCH_FLAG_FORCE_LOAD		= 0x02;
	
	/**
	 * @param $forceLoadFromDB , set true to re-fetch from db , by pk
	 * 
	 * @param $checkForeignKey, set true to reutrn real value of the fk-property, must set a fk ref column first,
	 * (non-PHPdoc)
	 * @see DBOperation::get()
	 */
	public function get($fieldName, $dbFetchFlag = 0 /*, $checkForeignKey = false, $forceLoadRecordFromDB = false*/) {
		if (! self::fieldExists($fieldName) ) {
		    throw new DBFieldNotExistsException( $fieldName, static::$tableName );
		}
		
		if( $dbFetchFlag & self::DB_FETCH_FLAG_FORCE_LOAD ) {
			$this->reload(array(),$fieldName);
		}
		
		if( $dbFetchFlag & self::DB_FETCH_FLAG_CHECK_FK && self::isForeignKey($fieldName) && !empty( $this->p[$fieldName]['value']) ) {
			
			$fkObjectName = self::getForeignKeyRefDBObjectName($fieldName) ;
			
			$objectExists = false;
			try {
				$objectExists = class_exists($fkObjectName) ;
			} catch( \Exception $e ) {
				Debug::addWarning($fkObjectName . ' NOT DEFINED' );
				$objectExists = false;
			}
			
			Debug::addCoreLog("class defined flag, class: ". $fkObjectName. '<>'  . ($objectExists? 'defined': 'not defined') ) ;
			
			if( $objectExists ) {
				if( !isset( $this->p[$fieldName]['fkObject'] ) || !is_object( $this->p[$fieldName]['fkObject'])  ) 
				{
					try {
						$this->p[$fieldName]['fkObject'] = $fkObjectName::getByID( $this->p[$fieldName]['value'] ) ;
					} catch ( \Exception $e ) {
						Debug::addError('LOAD FK OBJECT ERROR') ;
						throw $e;
					}
						
				} else if( $dbFetchFlag & self::DB_FETCH_FLAG_FORCE_LOAD ) {
					$this->p[$fieldName]['fkObject']->reload() ;				
				} else {
					// nothing to do
				}
				
				if( $this->p[$fieldName]['fkObject'] ) {
					return $this->p[$fieldName]['fkObject']->get( self::getForeignKeyRefColumnName($fieldName) );
				}
			}
			
			return $this->p [$fieldName] ['value'];
		} else {
			return $this->p [$fieldName] ['value'];
		}
	}

	/**
	 * This method return a basic properties arrays
	 * 
	 * 
	 * where hold the property name,and the it's value , and it's data type, etc
	 * 
	 * Enter description here ...
	 */
	public function getAll() {
		return $this->p;
	}

	//public function getPK() {
		//confirm the id is int
	//	return  $this->get ( static::$pkName ) ;
	//}
	
	//@add 2011.4.11
	//public function setPK( $value ) {
		//Debug::addNotice('add pk: '. $value .' for' . $this->getDPObjectKey() );
	//	return $this->set( static::$pkName, $value );
	//}
	
	public function getId() {
		return $this->get('id') ;
	}

	public function setId($id) {
		return $this->set ( 'id' , intval ( $id ) );
	}

	//for all failed and to be deleted obj,or do not need save, return false
	//for all save success, return true;
	
	const DB_OBJECT_SAVE_OK 		= 1;
	const DB_OBJECT_SAVE_FAILED		= 2;
	const DB_OBJECT_SAVE_NOT_CHANGE = 3;
	const DB_OBJECT_DELETE_OK 		= 4;
	const DB_OBJECT_DELETE_FAILED 	= 5;
	const DB_OBJECT_IDLE			= 6;
	
	private $DBActionStatus = self::DB_OBJECT_IDLE ;
	
	public function getDBActionStatus() {
		return $this->DBActionStatus;
	}
	
	/**
	 * 
	 * return true if save success, otherwise return false
	 * (non-PHPdoc)
	 * @see admin_v2/private/lib/daophp/database/daophp\database.DBRecordOperation::save()
	 */
	
	public function save() {
		if ($this->getStatus () !== self::STATUS_NORMAL) {
			Debug::warning ( 'pk: '. $this->getPrimaryKeyAsString(). '#CANCEL SAVE ACTION FOR STATUS NOT SELF::STATUS_NORMAL ', false );
			$this->setSaveFlag ( false );
			return false;
		}

		//Debug::add ( '#SAVE OBJ BEGIN ' . $this->getPrimaryKeyAsString ();
		$result = false;

		if(! $this->getSaveFlag() ) {
			Debug::warning( '['.$this->getDPObjectKey().'],object not changed, do not need update db, return false' );
			$this->DBActionStatus = self::DB_OBJECT_SAVE_NOT_CHANGE ;
			return false ;
		}
		// fix bug , if save failed , the 	$this->setAutoSaveFlag ( false ); will not be called, if you place it in the end of the function
		// the save function will be called in __destruct
		$this->setSaveFlag ( false );

		try {
			if ( $this->exists () ) {
				$result = $this->saveByUpdate ();
			} else {
				$result = $this->saveByCreate ();
			}
		} catch ( \Exception $e ) {
			$this->updateStatus( self::STATUS_ABNOMAL );
			throw $e ;
		}
		
		$rs = (bool) $result;
		
		if( $rs ) {
			$this->DBActionStatus = self::DB_OBJECT_SAVE_OK;
		} else {
			$this->DBActionStatus = self::DB_OBJECT_SAVE_FAILED;
		}

		//Debug::add ( '#SAVE OBJ END ' . $this->getPrimaryKeyAsString ();
		return $rs ;
	}
	
	public static function safeStringEscape($str) {
		if( is_int( $str) ) {
			return $str ;
		}
		
	   $len=strlen($str); 
	    $escapeCount=0; 
	    $targetString=''; 
	    for($offset=0;$offset<$len;$offset++) { 
	        switch($c=$str{$offset}) { 
	            case "'": 
	            // Escapes this quote only if its not preceded by an unescaped backslash 
	                    if($escapeCount % 2 == 0) $targetString.="\\"; 
	                    $escapeCount=0; 
	                    $targetString.=$c; 
	                    break; 
	            case '"': 
	            // Escapes this quote only if its not preceded by an unescaped backslash 
	                    if($escapeCount % 2 == 0) $targetString.="\\"; 
	                    $escapeCount=0; 
	                    $targetString.=$c; 
	                    break; 
	            case '\\': 
	                    $escapeCount++; 
	                    $targetString.=$c; 
	                    break; 
	            default: 
	                    $escapeCount=0; 
	                    $targetString.=$c; 
	        } 
	    } 
	    return $targetString; 
	}
	
	
	private function safeValue( $fieldName, $value ) {
		
	    if( self::isNumeric($fieldName)) {
			return $value + 0; 
		} else if( self::isText( $fieldName) || self::isString($fieldName) ) {
			return self::safeStringEscape( $value );
		} else {
		    return $value;
		}		
	}

	public function delete( $checkExists = false ) {
		$this->updateStatus ( self::STATUS_DELETE );
		$this->setSaveFlag ( false );
		
		$CDS = $this->_get_primary_key_cda_string() ;
		
		if( empty($CDS)) {
			Debug::error('empty key cds for delete') ;
			return false;
		}
		
		if( $checkExists ) {
			if( !$this->exists() ) {
				Debug::warning( $CDS . 'not exists in db, do not need to delete');
			}
			return false;
		}

		$dbi = self::GetDBI();
		$sql = 'DELETE FROM `' . static::$tableName . '`' . " WHERE " . "1 " . $CDS . " LIMIT 1;";

		$result = $dbi->query ( $sql );
		$affactedRow = $dbi->getAffectRows ();

		if (! $result) {
			$this->DBActionStatus = self::DB_OBJECT_DELETE_FAILED ;
			Debug::error ( '#Delete failed: ' . $this->getPrimaryKeyAsString () );
		} else {
			$this->DBActionStatus = self::DB_OBJECT_DELETE_OK ;
			Debug::core ( '#Delete success: ' . $this->getPrimaryKeyAsString () );
		}
		
		//if key not exists , return affacted row == 0, but this object will never has chance insert into db ,so return true
		return $result ;
		//return ($affactedRow == 1);
	}

	/**

	 * @2010.6.20
	 * @cpingg@gmail.com modified
	 *
	 * previous name is getStdClassFromP
	 *
	 * @principal
	 *
	 * we should take ( not null, unique ) in consideration
	 *
	 * for not null column,
	 *     on create: if no value found and no default value, throw exception
	 *     on update: if no value found there, just ignore them
	 *
	 * for unique
	 *     tobe implemented later
	 * @param boolean $createFlag
	 * true indicate prepare for create a record
	 * otherwise prepare for Update
	 *
	 * @return value false if failed, otherwise , return a stdClass obj
	 */
	private function prepareFieldValuePairForSave( $createFlag = true) {

		$pair = array() ;
		
		/**
		 * if we need support union primary key,we should keep going on this place,,,
		 * @TODO
		 */
		
		//var_dump($this->p);
		foreach ( $this->p as $fieldName => $fieldValueRef ) {
		    
		    if ( $createFlag && self::isAutoIncrement($fieldName) ) {
		        //skip this type, it is create action, db will assign a id for this field
		        continue;
		    }
		    
		    
		    if( empty($fieldValueRef['value']) && !empty($fieldValueRef['default'])) {
		        continue;
		    }
		    
		    if(self::getFieldType($fieldName) === self::TABLE_FIELD_TYPE_JSON) {
		    	$pair[$fieldName] = json_encode($fieldValueRef ['value'],JSON_FORCE_OBJECT);
		    } else {
			    if( !empty($fieldValueRef['value']) ) {
			        $pair[$fieldName] = $fieldValueRef ['value'];		        
			        continue;
			    }
		    }

			//exclued PRIMARY KEY

			if ($fieldValueRef ['null'] === 'NO') {
			    
			    if( self::getFieldType ( $fieldName ) === self::TABLE_FIELD_TYPE_NUMERIC) {				        
			        if( is_int( $fieldValueRef['value'] ) || is_numeric($fieldValueRef['value']) ) {
			            $pair[$fieldName] = $fieldValueRef ['value'];
			        } else {				        
			            throw new DBColumnNotNullConstraintException(static::$tableName, $fieldName, $this->p );
			        }
			        
			    } else if( self::getFieldType ( $fieldName ) === self::TABLE_FIELD_TYPE_STRING) {
			        if( is_numeric($fieldValueRef['value']) && $fieldValueRef['value']=== '0') {
			            $pair[$fieldName] = $fieldValueRef ['value'];
			        } else {
			            throw new DBColumnNotNullConstraintException(static::$tableName, $fieldName, $this->p );				            
			        }
			    } else {}
			}
		}
		
		return $pair;
	}

	public function getArray() {
		$array = array ();

		foreach ( $this->p as $key => $value ) {
			$array [$key] = $value ['value'];
		}

		return $array;
	}

	public function getObject() {
		$obj = new \stdClass ();

		foreach ( $this->p as $key => $value ) {
			$obj->$key = $value ['value'];
		}

		return $obj;
	}

	private function saveByCreate() {
		//Debug::add ( "CREATE OBJECT BEGIN");
		$SQL = "INSERT INTO `%tableName%`(%columns%) VALUES(%values%);";

		$table = static::$tableName;
		$columns = '';
		$values = '';

		$comma = false;

		$pair = $this->prepareFieldValuePairForSave ( true );
		$pp = self::getPFromPCache();

		foreach ( $pair as $key => $value ) {
			if ( $pp [$key] ['extra'] == 'auto_increment') {
				continue;
			}

			//$skipFlag = false;
			//if (self::getFieldType ( $key ) === self::TABLE_FIELD_TYPE_NUMERIC) {
			//	$skipFlag = (empty ( $value ) && ($value !== 0));
			//} else {
			//	$skipFlag = (empty ( $value ));
			//}
			//number , empty(0) return true
			//if (! ($skipFlag) ) {
				
				$value = self::safeValue($key, $value );
				//Debug::addLog('value after '.__METHOD__ . ':'. $value );
				if( !self::isNumeric($key)) {
					$value = self::_embrace_char($value,'\'');
				}
				
				if ($comma === false) {
					$columns .= "`{$key}`";
					$values .=  $value ;
					$comma = true;
				} else {
					$columns .= ',' . "`{$key}`";
					$values .= ',' . $value ;
				}
			//}
		}

		$search = array ("%tableName%", "%columns%", "%values%" );
		$replace = array ($table, $columns, $values );

		$SQL = str_replace ( $search, $replace, $SQL );
		$dbi = self::GetDBI();
		$flag = $dbi->query ( $SQL );

		if ($flag !== false) {
			$id_last = 0;
			$autoIncrementField = self::getAutoIncrementField();
				if( !empty( $autoIncrementField)) {
				$id_last = $dbi->getLastInsertId ();
	//			Debug::addTrace( 'last id: '. $id_last  . Common::pr( $pp, true , ', pp' )  ;
	//			Debug::addTrace( 'pkName: '. static::$pkName  ;
				//modified 2011.5.5, for none id primary key bug
			}
			if ( ($id_last > 0) && $autoIncrementField ) {
				Debug::core ( 'reload by autoincrement id ' . static::$tableName . '.' .$autoIncrementField.' : ' . $id_last );
				$flag = $this->reload( array( $autoIncrementField => $id_last ) );		//
			} else {
			    Debug::core( 'reload by pk');
				$flag = $this->reload();
			}
		}

		//Debug::add ( "CREATE OBJECT END, CREATE " . (($flag) ? 'SUCCESS' : 'FAILED') . "");
		return $flag;
	}

	private function saveByUpdate() {
		//Debug::add ( "UPDATE OBJECT BEGIN");

		$sql_header = "UPDATE " . "`" . static::$tableName . "`";

		$sql_fieldAndValue = 'SET ';
		$comma = false;

		$pair = $this->prepareFieldValuePairForSave ( false );
		$pp = self::getPFromPCache();

		foreach ( $pair as $key => $value ) {
		    
			//if ($pp [$key] ['extra'] === 'auto_increment') {
			//	continue;
			//}

			$skipFlag = false;
			if (self::getFieldType ( $key ) === self::TABLE_FIELD_TYPE_NUMERIC) {
				$skipFlag = (empty ( $value ) && ($value !== 0));
			} else {
				$skipFlag = (empty ( $value ));
			}

			if( !$skipFlag) {
				
				$value = self::safeValue($key, $value );
				
				if( !self::isNumeric($key)) {
					$value = self::_embrace_char($value,'\'');
				}
				if ($comma === false) {
					$sql_fieldAndValue .= " " . "`" . $key . "`" . "=" . $value ;
					$comma = true;
				} else {
					$sql_fieldAndValue .= "," . "`" . $key . "`" . "=" . $value ;
				}
			}
		}

		$sql_where = "WHERE 1" . $this->_get_primary_key_cda_string();
		$sql_limit = "LIMIT 1;";
		//$sql_limit = "";


		$SQL = $sql_header . PHP_EOL . $sql_fieldAndValue . PHP_EOL . $sql_where . PHP_EOL . $sql_limit;

		$dbi = self::GetDBI();
		
		$flag = $dbi->query ( $SQL );

		//Debug::addTrace('UPDATE DBObject QUERY SQL: '. $SQL );

		$affactRows = $dbi->getAffectRows ();

		//by fix bug when update the row with the save value in db, we evalute by -1
		// default return affact rows, none affact, return 0, error occured , return -1 by  mysql_affected_row
		if ($affactRows === -1) {
			//echo 'error';
			//var_dump( $SQL );
			//Debug::add ( '#UPDATE FAILED');
			return false;
		}

		//Debug::add ( "UPDATE OBJECT END, UPDATE " . (($flag) ? 'SUCCESS' : 'FAILED') . "");
		return true;
	}

	public function saveField($fieldName, $value) {
		$this->set ( $fieldName, $value );
		$rs = $this->save ();
		
		if( $rs === false ) {
			Debug::error( $this->getDPObjectKey() .' save failed, '. $fieldName.':'.$value );
		}
		return $rs;
	}
	
	public function __toString() {
	    
	    $arr = array();	    
	    $fields= $this->getAllFieldNames();
	    
	    foreach ( $fields as $field ) {	        
	        $arr[$field] = $this->get($field);
	    }
	    
	    return json_encode($arr);
	}

	public function __destruct() {
		//save the object automatically if
		try {
			if ($this->getSaveFlag () === true) {
				Debug::core( '#AUTO SAVE BEGIN : ' . $this->getPrimaryKeyAsString () );
				$auto_save = $this->save ();				
				Debug::core( '#AUTO SAVE END : ' . $this->getPrimaryKeyAsString () . 'save result: ' . $auto_save );
			}
		} catch ( \Exception $e ) {
			//echo '#AUTO SAVE EXCEPTION' . DP_NEW_LINE;
			//echo $e->getMessage ();
			//echo $e->getTraceAsString ();
			
			Debug::exception($e);
		}

		parent::__destruct ();
	}
}
?>