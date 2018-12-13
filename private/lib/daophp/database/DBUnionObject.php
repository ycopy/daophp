<?php

namespace daophp\database;


use daophp\database\DBInvalidFieldNameException;

class DBUnionObject extends DBObject {
	public function get($fieldName) {
		
		$fieldName = strtolower($fieldName);
		
		/**
		 * as the union table don't has a field of id, so we return 
		 */
		if( $fieldName == 'id' ) {
			return self::ID_UNION ;
		}
		
		if (! array_key_exists ( $fieldName, $this->getTableFieldProperties () )) {
			throw new DBInvalidFieldNameException ( static::$tableName, $fieldName );
		}

		return $this->p [$fieldName] ['value'];
	}

	public function setID($id) {
		/**
		 * as we don't has id for union talbe , so just return ;
		 */
		return ;
	}
}

?>