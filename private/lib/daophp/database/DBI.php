<?php

namespace daophp\database ;


/**
 * @modify 2009-08-12
 * @author cpingg@gmail.com
 * add function getAllObject
 */
interface DBI {
	public function getConnection();
	
	/**
	 * return db link resource
	 * Enter description here ...
	 */
	public function getDBlink();
	
	/**
	 * get all the result , return by array
	 * @param unknown_type $type
	 */
	public function getAll( $type = MYSQLI_ASSOC );
	
	/**
	 * get all the result, retury by a object of array
	 */
	public function getAllObject();
	
	/**
	 * get cur obj, if no one currently, retur false
	 */
	public function getObject();
	public function getAssoc();
	public function getArray();
	public function getAffectRows();
	public function getNumberRows();
	/**
	 * return a last auto_increment id , if no one, return 0
	 */
	public function getLastInsertId();
	public function seek( $rowNumber );
	
	public function query( $sql );
	public function desc( $tableName );
	
	public function isAutoCommit();

	public function autoCommit( $flag ) ;
	public function transactionBegin();
	public function transactionRollback();
	public function transactionCommit();
	public function close();	
}
?>