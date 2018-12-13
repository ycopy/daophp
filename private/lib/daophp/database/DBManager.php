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
 * 
 * Enter description here ...
 * @author ping.cao
 *
 */


namespace daophp\database ;

use daophp\core\DaoPHP ;
use daophp\core\object\DPObject ;

class DBManager extends DPObject {

	const MYSQL 	= 'mysql' ;
	const SQLLITE 	= 'sqlite' ;
	const FILE 		= 'file' ;
	const MS_SQL 	= 'sqlserver' ;
	
	
	const DB_HOST				= 'db_host';
	const DB_USER_NAME			= 'db_username';
	const DB_USER_PASS			= 'db_pwd';
	const DB_NAME				= 'db_name';
	
	private static $_dbConfigCheckKeys = array(
		self::DB_HOST,
		self::DB_USER_NAME,
		self::DB_USER_NAME,
		self::DB_NAME
	);
	
	private static $_dbConfig = array() ;
	
	public static function setDBConfig( array $dbConfig ) {
		self::$_dbConfig = $dbConfig ;
	}
	
	public static function getDBConfig() {
		return self::$_dbConfig ;
	}
		
	private static $_dbi = null;
	
	public static function GetDBI( $dbType = self::MYSQL ) {
		
		foreach( self::$_dbConfigCheckKeys as $key ) {
			if( !isset(self::$_dbConfig[$key] ) ) {
				throw new \InvalidArgumentException( 'missing <'. $key .'> for mysql connection info' );
			}
		}
		
		switch( $dbType )
		{
			case self::MYSQL:
			    {
			        if( self::$_dbi== null ) {			            
			            self::$_dbi= new Mysql( self::$_dbConfig );
			        }
			        
			        return self::$_dbi;
			    }
				break;
			default:
			    throw new \Exception('db type not specified');
				break;	
		}
	}
	
	/*
	public static function transactionBegin() {	
	    $this->GetDBI();
	    return self::$_dbi->transactionBegin();
	}
	
	public static function transactionRollback() {	    
	    $this->GetDBI();
	    return self::$_dbi->transactionRollback();
	}
	
	public static function transactionCommit() {	    
	    $this->GetDBI();
	    return self::$_dbi->transactionCommit();
	}
	*/
}

?>