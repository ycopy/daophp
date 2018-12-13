<?php

/*************************************************

DaoPHP - the PHP Web Framewrok
Author: cpingg@gmail.com
Copyright (c): 2008-2010 DaoPHP, all rights reserved
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
 * Please use DBManager::getDBI() To Create a Mysql Instance
 * Enter description here ...
 * @author ping.cao
 *
 */

namespace daophp\database ;

use daophp\core\Resource;
use daophp\core\Debug;

final class Mysql implements DBI,Resource {

	private $retryConnect = true;
	private $retryTimes = 3;

	/**
	 * Inherited from Object
	 *
	 * @var string
	 */
	protected $name = 'Mysql';

	private $dbHost;
	private $dbName;
	private $dbUserName;
	private $dbUserPass;

	private $persistant = false;
	private $autoConnect = true;
	private $collation = 'UTF8';
	/**
	 * @see DBI::desc()
	 *
	 * @param unknown_type $tableName
	 */
	public function desc($tableName) {
	    
		if (empty ($tableName)) {
			throw new DBNullTableNameException ();
		}

		$rows = array();
		try {
		    $desc = 'DESC `'. $tableName . '`' ;
		    Debug::core('sql: '. $desc );
		    
		    $this->query ( $desc ) ;		    
		 	$rows = $this->getAll (MYSQLI_ASSOC);
		} catch ( \Exception $e ) {
			throw $e;
		}	
		return $rows;
	}

	private $SQL = array ();
	private $dbLink = null;
	private $dbResult = null;


	/* (non-PHPdoc)
	 * @see DBI::getDBlink()
	 */
	public function getDbLink() {
		// TODO Auto-generated method stub
		if( $this->dbLink== null ) {
			$this->connect();
		}
		
		assert( is_resource($this->dbLink));		
		return $this->dbLink ;
	}

	public static function escapeString( $string, $useSafeEscape =  true ) {
		if($useSafeEscape) {
			return DBObject::safeStringEscape($string) ;
		} else {
			return mysqli_real_escape_string($string, $this->getDBlink() );
		}
	}

	public function __construct($options) {
		if (array_key_exists ( DBManager::DB_HOST, $options )) {
			$this->dbHost = $options [DBManager::DB_HOST];
		}

		if (array_key_exists ( DBManager::DB_NAME, $options )) {
			$this->dbName = $options [DBManager::DB_NAME];
		}

		if (array_key_exists ( DBManager::DB_USER_NAME, $options )) {
			$this->dbUserName = $options [DBManager::DB_USER_NAME];
		}

		if (array_key_exists ( DBManager::DB_USER_PASS, $options )) {
			$this->dbUserPass = $options [DBManager::DB_USER_PASS];
		}

		if (array_key_exists ( 'persistant', $options )) {
			$this->persistant = $options ['persistant'];
		}

		if (array_key_exists ( 'autoConnect', $options )) {
			$this->autoConnect = $options ['autoConnect'];
		}

		if (array_key_exists ( 'collation', $options )) {
			$this->collation = $options ['collation'];
		}

		if ($this->autoConnect == true) {
			$this->init ();
		}
	}

	public function getDBName() {
		return $this->dbName ;
	}

	public function getHost() {
		return $this->dbHost ;
	}

	public function getUserName() {
		return $this->dbUserName ;
	}

	public function getUserPassword() {
		return $this->dbUserPass ;
	}

	protected function connect() {
		$this->close() ;
		
		Debug::core('connect db: '. $this->dbHost . '.' . $this->dbUserName ,false );
		$retryTimes = 0 ;
		do {
			if ($this->persistant === true) {
				$this->dbLink = mysqli_connect ( 'p:'. $this->dbHost, $this->dbUserName, $this->dbUserPass );
			} else {
				$this->dbLink = mysqli_connect ( $this->dbHost, $this->dbUserName, $this->dbUserPass );
			}

			if($retryTimes) {
				usleep( 50 * $retryTimes);
			}
		} while ( $this->dbLink == null && (($retryTimes++)<$this->retryTimes) );
	
		if( $this->dbLink == null) {
			throw new DBConnectException( $this->dbHost, $this->dbUserName, $this->dbUserPass );
		}
		
		if( !mysqli_select_db ( $this->dbLink, $this->dbName)) {
		     throw new \Exception('select db error, dbHost: '.$this->dbHost . '.'.$this->dbName . ', error no: '. mysqli_errno($this->dbLink) , false );		     
		}
		
		if(!$this->setNames()) {		    
		    throw new \Exception('set names failed, collation: '. $this->collation . 'dbHost: '.$this->dbHost . '.'.$this->dbName . ', error no: '. mysqli_errno($this->dbLink) , false );
		}

		$auto_commit_query = 'SET AUTOCOMMIT=0;';
		if( $this->autoCommit ) {
		    $auto_commit_query = 'SET AUTOCOMMIT=1;';
		}
		
		if( !$this->query( $auto_commit_query ) ) {
		    throw new DBException('init autoCommit to false error');
		}
		
		assert( $this->dbLink != null );
		return $this->dbLink;
	}

	public function setNames() {
		if ( $this->dbLink == null ) {
			$this->connect ();
		}

		$set_name_query = 'SET NAMES ' . $this->collation ;
		return $this->query ( $set_name_query );		
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $collation
	 * @return string , the previous collation
	 */
	public function setCollation($collation) {
		$before = $this->collation;
		$this->collation = $collation;
		$this->setNames ();
		return $before;
	}

	private function freeDbResult() {
		if ( ! $this->dbResult instanceof \mysqli_result ) {		    
		    return true;		    
		}
		
		if(mysqli_free_result ( $this->dbResult )) {
			$this->dbResult = null ;
			return true;
		}
		return false;
	}

	/**
	 * @see DBI::close()
	 *
	 */
	public function close() {	
		if( $this->dbLink == null ) {
		   return true;
		}
		
		$this->freeDbResult ();				
		if( mysqli_close ( $this->dbLink )) {
			$this->dbLink = null ;
			return true;
		}
		return false;
	}

	/**
	 * @see DBI::getAffactRows()
	 *
	 */
	public function getAffectRows() {
		if ( $this->dbLink == null ) {
		    throw new DBInvalidResourceLinkException();
		}
		
		return mysqli_affected_rows ( $this->dbLink );		
	}

	/**
	 * @see DBI::getNumberRows()
	 *
	 */
	public function getNumberRows() {		
		if (  $this->dbResult == null ) {
		    throw new DBInvalidResultException();
		}
		
		return mysqli_num_rows ( $this->dbResult );		
	}

	/**
	 * @see DBI::getAll()
	 *
	 */
	public function getAll($type = MYSQLI_ASSOC ) {	    
	    
	    if ( !$this->dbResult instanceof \mysqli_result ) {
	        throw new DBInvalidResultException();
	    }
	    
		$tmp = array ();
		$rows = array ();
		while ( ( $tmp = mysqli_fetch_array($this->dbResult, $type)) != NULL ) {
			array_push( $rows, $tmp );
		}	
		
		return $rows;
	}

	/**
	 * return all the result with a object array
	 */

	public function getAllObject() {	    
	    if ( !$this->dbResult instanceof \mysqli_result ) {
	        throw new DBInvalidResultException();
	    }
	    
		$tmp = null;
		$rows = array ();

	    while ( ($tmp = mysqli_fetch_object($this->dbResult)) != null ) {
	        array_push($rows, $tmp);
		}		
		
		return $rows;
	}

	/**
	 * @see DBI::getArray()
	 *
	 */
	public function getArray() {	    
	    if ( !$this->dbResult instanceof \mysqli_result ) {
	        throw new DBInvalidResultException();
	    }
	    
	    return mysqli_fetch_array( $this->dbResult, MYSQLI_NUM);
	}

	/**
	 * @see DBI::getAssoc()
	 *
	 */
	public function getAssoc() {	    
	    if ( !$this->dbResult instanceof \mysqli_result ) {
	        throw new DBInvalidResultException();
	    }
		return mysqli_fetch_array ( $this->dbResult, MYSQLI_ASSOC );
	}

	public function getObject() {
	    if ( !$this->dbResult instanceof \mysqli_result ) {
	        throw new DBInvalidResultException();
	    }
		return mysqli_fetch_object ( $this->dbResult );
	}

	/**
	 * @see DBI::getConnection()
	 *
	 */
	public function getConnection() {
		if ( $this->dbLink == null ) {
			return $this->connect ();
		}
		
		return $this->dbLink;		
	}

	/**
	 * @see DBI::getLastInsertId()
	 *
	 */
	public function getLastInsertId() {
		if ( $this->dbLink != null ) {
			return mysqli_insert_id ( $this->dbLink );
		}
		
		throw new DBInvalidResourceLinkException();
	}

	/**
	 * @see DBI::query()
	 *
	 * @param string $sql
	 * @return
	 */
	public function query($sql) {
		if( $this->dbLink == null ) {
			$this->connect ();
		}

		// free the previous result before do further operation,
		// cpingg@gmail.com 2011.05.26
		$this->freeDbResult();
		//$sql = mysql_real_escape_string( $sql, $this->dbLink );
		//echo DP_LT.'SQL'. DP_GT ."\n" . $sql  ."\n" ;
		Debug::core ( DP_LT.'SQL'. DP_GT ."\n" . $sql  ."\n" );

		$try = 0;
		$maytry = false;
		$errno = 0;
		
		do {		    
		    ++ $try ;
		    $this->dbResult = mysqli_query ( $this->dbLink, $sql );	
			
			if( $this->dbResult == false ) {
				$errno = mysqli_errno($this->dbLink);
				Debug::core('mysql errno: '. $errno );
				
				if( $errno == 2006 ) {					    
					$this->connect() ;
					$maytry = true;						
				} 					
				else if( $errno == 1205 ) {
				    $maytry=true;					    
				}
				else {
				    $maytry = false;
				}
			}
		} while($maytry && $try<3 ) ;

		if ($this->dbResult === false) {
		    $this->dbResult = null;
		    
			$errstr = mysqli_error( $this->dbLink ) ;
			switch( $errno ) {
				case 1062:
				    throw new DBDuplicateEntryException($sql,$errstr);
				case 1114:
				    throw new DBTableIsFullException($errstr);
				case 1146:
				    throw new DBTableNotFoundException($errstr);
				case 2006:
					throw new DBDownException($this->dbHost);
				case 1205:
					throw new DBWaitLockException($this->dbHost,$sql);
				default: {
					$debugString = 'mysql query error: '. DP_NEW_LINE ;
					$debugString .= 'code : ' . $errno.  DP_NEW_LINE ;
					$debugString .= 'message: ' . $errstr.  DP_NEW_LINE ;
					$debugString .= 'sql : ' . $sql .  DP_NEW_LINE ;
					//Debug::add ( '!DBI Error: ' . $debugString );
					throw new DBException ( $debugString, $errno);
					break;
				}
			}
		}
		
		assert($this->dbResult !== false);
		return true;
	}

	/**
	 * @see DBI::seek()
	 *
	 * @param unknown_type $rowNumber
	 */
	public function seek($rowNumber) {
		if (  $this->dbResult != null ) {
			return mysqli_data_seek ( $this->dbResult, $rowNumber );
		}
		
		throw new DBInvalidResultException();
	}


	private $autoCommit = true;
	public function isAutoCommit() {
		return $this->autoCommit;
	}

	/**
	 * @see DBI::transactionCommit()
	 *
	 */
	/**
	 * @param unknown_type $flag
	 */
	public function autoCommit($flag) {
		if( $this->autoCommit === true && $flag === true ) {
			return true;
		} else if($this->autoCommit === false && $flag === false ) {
			return true;
		} else {

		$rs = false;
			try {
				if( $flag ) {
					$rs = $this->query('SET AUTOCOMMIT=1;');
				} else {
					$rs = $this->query('SET AUTOCOMMIT=0;');
				}
			} catch( \Exception $e){
				Debug::exception($e);
				throw $e;
			}

			if( $rs ) {
				$this->autoCommit = (bool) $flag ;
			}
		}

		Debug::core('switch autoCommit to '. (($flag) ? 'true' : 'false' ) . ' ' . ( ($rs) ? 'success' : 'failed' ) );
		return $rs;
	}

	public function transactionCommit() {
		return $this->query('COMMIT;') ;
	}

	/**
	 * @see DBI::transactionRollback()
	 *
	 */
	public function transactionRollback() {
		return $this->query('ROLLBACK;') ;
	}

	/**
	 * @see DBI::transactionStart()
	 *
	 */
	public function transactionBegin() {
		return $this->query('BEGIN;') ;
	}

	public function __destruct() {
		$this->free ();
	}
	/* (non-PHPdoc)
 	 *  alias to connect
	 * @see Resource::init()
	 */
	public function init() {
	    return $this->connect();
    }

	/* (non-PHPdoc)
	 * alias to close
	 * @see Resource::free()
	 */
	public function free() {
		// TODO Auto-generated method stub
		return $this->close();
	}
}
?>