<?php

namespace daophp\cache;
use daophp\core\object\DBObject ;

class MysqlMemoryObject extends DBObject {
	protected static $tableName = 'c' ;
	protected static $pkName = 'k' ;
}
?>