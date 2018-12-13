<?php

namespace daophp\database ;


interface DBRecordOperation {


	/**
	 * save self
	 */
	public function save();


	/**
	 * delete self
	 */
	public function delete();

	/**
	 * set a field value for this obj
	 * @param string $fieldName
	 * @param string $value
	 */
	public function set( $fieldName, $value );
	public function get( $fieldName);



	/**
	 * launch a sql query to db and say whether it's exists yet
	 * @return boolean
	 */
	public function exists();

	/**
	 * return boolean
	 * @param unknown_type $params
	 */
	public static function hasAny( array $params = array() );

}

?>