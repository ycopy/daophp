<?php


namespace daophp\cache;
use daophp\core\ClassNotFoundException;

use daophp\core\object\SingletonObject ;
use daophp\core\Resource ;
use daophp\core\Debug ;

class MemCacheProvider extends SingletonObject implements CacheProvider,Resource {
	private static $memcache = null;
	private static $globalFlag = 0; //compress and serial
	private static $timeout = 2 ; //do not set too long to avoid performance
	private static $host = DP_MEMCACHED_SERVER;
	private static $port = DP_MEMCACHED_PORT ;
	
//	private static $pid = -1;
	private static $serverConnError = false ;
	
	public function __construct( $params = array() ) {
		if( self::$memcache == null ) {
			$this->init();
			//self::$memcache->setOption(\Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
			parent::__construct();	
		}
	}
	
	//it is not safe for multi process, cause some other process will colose this connection
	public function isAvailable () {
		if( self::$memcache == null ) {
			return false;
		}
		
		return true;
	}
	
	public function init($reConn = false) {
		if( self::$serverConnError && !$reConn ) {
			return false ;
		}
		
		$s = @fsockopen(self::$host,self::$port,$errno,$errstr,self::$timeout);
		if (!$s){
			Debug::addError('connect to memcache server failed, '. $errno.':'.$errstr );
			self::$serverConnError = true;
			return false;
		}
		self::$serverConnError = false;
		
		try {
			
			self::$memcache = new \Memcache();
			
		} catch ( ClassNotFoundException $e) {
			self::$memcache = null;
			Debug::addException($e) ;
			return null;
		}
		
		self::$memcache->addServer(self::$host,self::$port);
	}
	
	public function free() {
		if(self::$memcache) {
			self::$memcache->close();
			self::$memcache = null ;
			Debug::addCoreLog('free memcache client ok');
		}
		return true;
	}
	
	
	/**
	 * @see CacheProvider::add()
	 *
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @param unknown_type $ttl
	 * 
	 * $flag could be MEMCACHE_COMPRESSED
	 */
	public function add($key, $value, $ttl=0, $flag = 0 ) {
		if( !self::$memcache ) {
			Debug::addError(__METHOD__.', NO MEMCACHED EXISTS',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		
		return @self::$memcache->add($key, $value, $ttl ) ;
	}
	
	/**
	 * @see CacheProvider::delete()
	 *
	 * @param unknown_type $key
	 */
	public function delete($key,$flag = 0) {
		if( !self::$memcache ) {
			Debug::addError(__METHOD__.', NO MEMCACHED EXISTS',false );
			return false;
		}
		
		$exists = $this->get($key,$flag);
		
		if($exists === false ) {
			Debug::addWarning('delete a key that not exists, key: '. $key );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		
		$retry = 0;
		
		do {
			$rs =self::$memcache->delete($key) ;
			if($retry>0) {
				usleep(100*$retry);
			}
		} while($rs === false && (++$retry<=3));
		
		if($rs === false){
			Debug::addError('delete key failed, key: '. $key );
		}
		
		return $rs ;
	}
	
	public function deleteAll() {
		if( !self::$memcache ) {
			Debug::addError(__METHOD__.', NO MEMCACHED EXISTS',false );
			return false;
		}
		
		return self::$memcache->flush() ;
	}
	
	/**
	 * @see CacheProvider::get()
	 *
	 * @param unknown_type $key
	 */
	public function get($key, $flag=0) {
		if( !self::$memcache ) {
			Debug::addError(__METHOD__.', NO MEMCACHED EXISTS',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		
		$hit = self::$memcache->get($key) ;
		
		if($hit === false) {
			Debug::addCoreLog('hit cache failed, key: '.$key ) ;
		} else {
			Debug::addCoreLog('hit cache ok: '. $key );
		}
		
		return $hit;
	}
	
/**
	 * @param unknown_type unknown_type $key
	 * @param unknown_type unknown_type $value
	 * @param unknown_type unknown_type $ttl
	 */
	public function set($key, $value, $ttl=0, $flag = 0 ) {
		if( !self::$memcache ) {
			Debug::addError(__METHOD__.', NO MEMCACHED EXISTS',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		
		$retry = 0;
		do {
			$rs = self::$memcache->set($key,$value,$flag,$ttl);
			if($retry>0) {
				usleep(100*$retry);
			}
		} while( $rs === false && (++$retry)<=3);
		
		if($rs === false) {
			Debug::addCoreLog('set cache failed after retry 3 times, key: '.$key ) ;
		} else {
			Debug::addCoreLog('set cache ok: '. $key );
		}
		
		return $rs;
	}
	
	public function exists($key,$flag=0) {
		return $this->get($key,$flag) !== false;
	}
	
	public function __destruct() {
		$this->free() ;
	}
}
?>