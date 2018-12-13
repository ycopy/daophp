<?php

namespace daophp\cache;

use daophp\core\Debug;

use daophp\core\DaoPHP;

use daophp\core\object\SingletonObject ;

final class CacheManager extends SingletonObject {
	
//	const CACHE_TYPE_APC 				= 'apc';
	const CACHE_TYPE_MEMCACHED 			= "memcached"; //can use now
	const CACHE_TYPE_MEMCACHE 			= "memcache"; //can use now
	const CACHE_TYPE_FILE 				= "file";
	const CACHE_TYPE_NULL 				= "null";
//	const CACHE_TYPE_EACCELERATOR 		= "eaccelerator";
	const CACHE_TYPE_MYSQL_MEMORY_TABLE = 'mysql'; //deprecated, has problem now
	const CACHE_TYPE_REDIS				= 'redis' ;
	
	/**
	 * All the cache provider must be get from this function
	 *
	 * @param unknown_type $type
	 * @param unknown_type $options
	 * @return unknown
	 */
	public static function getCacheProvider( $type = DP_CACHE_TYPE, $singletonFlag = 0 ) {
		
		if(empty($type)) {
			return null;
		}
		
		try{
			switch ( $type ) {
				case self::CACHE_TYPE_MEMCACHE:
					return MemCacheProvider::getInstance(array(),$singletonFlag);
				case self::CACHE_TYPE_MEMCACHED:
					return MemCachedProvider::getInstance(array(),$singletonFlag);
				case self::CACHE_TYPE_REDIS:
					return RedisCacheProvider::getInstance(array(),$singletonFlag);
				case self::CACHE_TYPE_NULL:
					return null;
				case self::CACHE_TYPE_FILE:
					return FileCacheProvider::getInstance(array(),$singletonFlag);
				case self::CACHE_TYPE_MYSQL_MEMORY_TABLE:
					return MysqlCacheProvider::getInstance(array(),$singletonFlag);
				default:
					throw new CacheTypeException( $type );
					break;
			}
		}catch( MemCacheConnectException $e ) {
			Debug::exception($e) ;
			DaoPHP::$useCache = false;//disable for connection errorf
			return null;
		} catch( \Exception $e ) {
			Debug::exception($e) ;
			return null;
		}
	}
}
?>