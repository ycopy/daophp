<?php

namespace daophp\cache;


class CacheTypeException extends \Exception {
	public function __construct( $type )
	{
		$supportedCacheType = '';
		try {
			$reflectClass = new \ReflectionClass( 'CacheManager') ;
			$constants = $reflectClass->getConstants();
			foreach (  $constants as $key => $value )
			{
				if( substr( $key, 0, 10) == 'CACHE_TYPE')
				{
					$supportedCacheType .= $value ."\t" ;
				}
			}
		} catch (\Exception  $e )
		{
			throw $e;
		}
		
		$msg = <<<EOM
<b>{$type}</b> is not a valid Cache Type
we support the following type right now
{$supportedCacheType}
EOM;

	parent::__construct( $msg );
	}
}
?>