<?php 

namespace daophp\database ;



/**
 * IF THE INTERFACE IMPLEMENT , WE CAN USE $THIS->GetDBI to get a DBI HANDLE 
 */
interface GetDBI {
	public static function GetDBI() ;
	public static function setDBI( DBI $dbi );
}
?>