<?php

namespace daophp\cache;
use daophp\core\object\SingletonObject ;
use daophp\core\Resource ;
use daophp\core\Debug;
use daophp\core\Common;

class RedisCacheProvider extends SingletonObject implements CacheProvider, ListCacheProvider,SetCacheProvider,Resource {
	
	private static $host = DP_REDIS_CACHE_SERVER; //default
	private static $port = DP_REDIS_CACHE_PORT; //default
	private static $timeout = 1 ;
	
	private static $serverConnError = false ;
	
	/* (non-PHPdoc)
	 * @see CacheProvider::add()
	 */
	public function add($key, $value, $ttl = 0, $flag = 0) {
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		// TODO Auto-generated method stub
		throw new \Exception('not implemented yet' .__METHOD__ );
	}

	/* (non-PHPdoc)
	 * @see CacheProvider::delete()
	 */
	public function delete($key,$flag=0) {
		if(!$this->isAvailable()) {
			Debug::addCoreLog('redis not available now, re-init',false );
			$this->init();
		}
		
		// TODO Auto-generated method stub
		if( !self::$redis ) {
			Debug::addCoreLog('null self::$redis',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		
		return self::$redis->delete($key);
	}

	/* (non-PHPdoc)
	 * @see CacheProvider::get()
	 */
	public function get($key,$flag = 0) {
		if(!$this->isAvailable()) {
			Debug::addCoreLog('redis not available now, re-init',false );
			$this->init();
		}
		
		// TODO Auto-generated method stub
		if( !self::$redis ) {
			Debug::addCoreLog('null self::$redis',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		$hit = self::$redis->get($key) ;
		
		if($hit === false ) {
			Debug::addCoreLog('not hit key from redis:  '.$key, false );
			return false;
		} else {
			Debug::addCoreLog('hit key from redis:  '.$key, false );
			$rs = unserialize($hit);
			if( $rs === false ) {
				Debug::addError('unserialize failed for key: '.$key .' get hit:'.$hit ) ;
				return false;
			}
			return $rs;
		}
	}

	/* (non-PHPdoc)
	 * @see CacheProvider::set()
	 */
	public function set($key, $value, $ttl = -1, $flag = 0) {
		if($ttl == -1 ) {
			$ttl = DP_MONTH_TIME ;
		}
		
		if(!$this->isAvailable()) {
			Debug::addCoreLog('redis not available now, re-init',false );
			$this->init();
		}
		
		// TODO Auto-generated method stub
		if( !self::$redis ) {
			Debug::addCoreLog('null self::$redis',false );
			return false;
		}
		
		$value = serialize($value);
		
		if( $value === false ) {
			Debug::addError('serilize failed for key: '. $key . 'value:'.Common::pr($value,true) );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}	
		$rs = self::$redis->setex($key, $ttl ,$value ) ;
		
		if($rs === true ) {
			Debug::addCoreLog('set hit key success to redis: '.$key.',value: '.$value, false );
			return true;
		} else {
			Debug::addCoreLog('set key failed to redis: '.$key.',value: '.$value, false );
			return false;
		}
	}
	
	public function exists($key,$flag=0){
		if(!$this->isAvailable()) {
			Debug::addCoreLog('redis not available now, re-init',false );
			$this->init();
		}
		
		if( !self::$redis ) {
			Debug::addCoreLog('null self::$redis',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		return self::$redis->exists($key) ;
	}

	/* (non-PHPdoc)
	 * @see CacheProvider::deleteAll()
	 */
	public function deleteAll() {
		// TODO Auto-generated method stub
		throw new \Exception('not implemented yet' .__METHOD__ );
	}

	/* (non-PHPdoc)
	 * @see ListCacheProvider::push()
	 */
	public function push($key, $value, $flag = 0) {
		if(!$this->isAvailable()) {
			Debug::addCoreLog('redis not available now, re-init',false );
			$this->init();
		}
		
		// TODO Auto-generated method stub
		if( !self::$redis ) {
			Debug::addCoreLog('null self::$redis',false );
			return false;
		}
		
		$value = serialize($value);
		
		if( $value === false ) {
			Debug::addError('serilize failed for key: '. $key . 'value:'.Common::pr($value,true) );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		$rs = self::$redis->rpush($key,$value) ;
		
		if($rs === false ) {
			Debug::addCoreLog('push key failed to redis '.$key.',value:'.$value, false );
			return false;
		} else {
			Debug::addCoreLog('push key success to redis '.$key .',value:'.$value, false );
			return $rs;
		}		
	}

	/* (non-PHPdoc)
	 * @see ListCacheProvider::pop()
	 */
	public function pop($key, $maxCount = 10,$flag=0) {
		if(!$this->isAvailable()) {
			Debug::addCoreLog('redis not available now, re-init',false );
			$this->init();
		}
		
		// TODO Auto-generated method stub
		if( !self::$redis ) {
			Debug::addCoreLog('null self::$redis',false );
			return false;
		}

		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
			
		if( !self::$redis->lSize($key) ) {
			Debug::addCoreLog('no element from redis for list key: '. $key , false );
			return null;
		}
		
		$rs = array() ;
		do {
			$tmp = self::$redis->lPop($key);
			Debug::addCoreLog('lpop from redis: '. $key. ' get: '. $tmp ,false );
			array_push($rs, $tmp );
		} while( self::$redis->lSize($key) && count($rs) < $maxCount ) ;
		
		$shouldReorder = false;
		if( count($rs) ) {
			foreach($rs as $k => $o) {
				$r_un = unserialize($o);
				if( $rs === false ) {
					$shouldReorder = true;
					Debug::addError('unserialize failed for key: '.$key .' get hit:'. $o ) ;
				}
				$rs[$k] = $r_un ;
			}
		}
		
		if( $shouldReorder ) {
			$rs = array_values($rs) ;
		}
		return $rs;
	}
	
	public function size($key,$flag) {
		if(!$this->isAvailable()) {
			Debug::addCoreLog('redis not available now, re-init',false );
			$this->init();
		}
		
		// TODO Auto-generated method stub
		if( !self::$redis ) {
			Debug::addCoreLog('null self::$redis',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		
		return self::$redis->lSize($key);
	}
	
	
	
	/**
	 * for set begin
	 */
	public function sAdd($key, $value ,$flag=0 ) {
		if(!$this->isAvailable()) {
			Debug::addCoreLog('redis not available now, re-init',false );
			$this->init();
		}
		
		// TODO Auto-generated method stub
		if( !self::$redis ) {
			Debug::addCoreLog('null self::$redis',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		
		return self::$redis->sAdd( $key, $value);
	}
	
	public function sContains($key,$value ,$flag=0 ) {
		
		if(!$this->isAvailable()) {
			Debug::addCoreLog('redis not available now, re-init',false );
			$this->init();
		}
		
		// TODO Auto-generated method stub
		if( !self::$redis ) {
			Debug::addCoreLog('null self::$redis',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		
		return self::$redis->sContains( $key, $value );
	}
	
	public function sRemove($key, $value,$flag=0) {
		if(!$this->isAvailable()) {
			Debug::addCoreLog('redis not available now, re-init',false );
			$this->init();
		}
		
		// TODO Auto-generated method stub
		if( !self::$redis ) {
			Debug::addCoreLog('null self::$redis',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		
		return self::$redis->sRemove( $key, $value );
	}
	
	public function sSize($key,$flag,$flag=0) {
		if(!$this->isAvailable()) {
			Debug::addCoreLog('redis not available now, re-init',false );
			$this->init();
		}
		
		// TODO Auto-generated method stub
		if( !self::$redis ) {
			Debug::addCoreLog('null self::$redis',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		
		return self::$redis->sSize($key);
	}
	
	public function sUnion($set_1, $set_2,$flag=0){
		if(!$this->isAvailable()) {
			Debug::addCoreLog('redis not available now, re-init',false );
			$this->init();
		}
		
		// TODO Auto-generated method stub
		if( !self::$redis ) {
			Debug::addCoreLog('null self::$redis',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key_1 = DP_CACHE_KEY_PREFIX . $set_1 ;
			$set_2 = DP_CACHE_KEY_PREFIX . $set_2 ;
		}
		
		return self::$redis->sUnion($set_1, $set_2);
	}
	public function sPop($key,$flag=0) {
			if(!$this->isAvailable()) {
			Debug::addCoreLog('redis not available now, re-init',false );
			$this->init();
		}
		
		// TODO Auto-generated method stub
		if( !self::$redis ) {
			Debug::addCoreLog('null self::$redis',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		
		return self::$redis->sPop($key);
	}
	
	public function sGetMembers($key,$flag=0) {
		if(!$this->isAvailable()) {
			Debug::addCoreLog('redis not available now, re-init',false );
			$this->init();
		}
		
		// TODO Auto-generated method stub
		if( !self::$redis ) {
			Debug::addCoreLog('null self::$redis',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		
		return self::$redis->sGetMembers($key);
	}
	
	/**
	 * for set end
	 */

	//private static $pid = -1;
	private static $redis = null;
	public function __construct( $params = array() ) {
		if( self::$redis == null ) {
			$this->init();
			parent::__construct();	
		}		
	}
	
	public function isAvailable ( $forceRetryConnection = false ) {
		if( self::$redis == null || $forceRetryConnection ) {
			$this->init($forceRetryConnection);
		}
		
		if(!self::$redis ) {
			return false;
		}
		
		$rs = false;
		try {
			$rs = self::$redis->ping();
		} catch (\Exception $e) {
			Debug::exception($e);
			return false;
		}
		
		return $rs;
	}
	
	public function init($reConn = false) {
		if( self::$serverConnError && !$reConn) {
			return false ;
		}
		
		$s = @fsockopen(self::$host,self::$port,$errno,$errstr, self::$timeout );
		if (!$s){
			Debug::addError('connect to redis server failed, host: '.self::$host .':'.self::$port.', '. $errno.':'.$errstr , false );
			self::$serverConnError = true;
			return false;
		}
		
		self::$serverConnError = false;
		
		if(self::$redis) {
			self::$redis->close() ;
		} else {
			self::$redis = new \Redis() ;
		}
		
		self::$redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);    // use built-in serialize/unserialize
		self::$redis->setOption(\Redis::OPT_PREFIX, 'dp:'); // use custom prefix on all keys		
		
		$try = 0;
		
		do {
			if($try>0) {
				Debug::addWarning('connect redis server, try: '. $try .', host:'. self::$host.' port: '.self::$port.'' ,false ) ;
			}
			$connect = false ;
			try {
				$connect = self::$redis->connect(self::$host,self::$port);//please dont set time out here...for win ,if has a timeout ,don not know localhost, only recoginize 127.0.0.1
			} catch( \RedisException $e ) {
				Debug::exception($e);
			}
			usleep(50*$try++) ;
		} while ( $connect !== true && $try < 3 );
		
		if( $connect === false ) {
			self::$redis = null;
			Debug::addError('connect redis server failed, host:'. self::$host.' port: '.self::$port,false ) ;
		} else {
			Debug::addCoreLog('connect redis server ok, host:'. self::$host.' port: '.self::$port.'' ,false ) ;
		}
	}
	
	public function free() {
		$rs = false;
		if(self::$redis) {
			//Debug::addCoreLog('close redis ...') ;
			try {
				$rs = self::$redis->close() ;
			} catch( \Exception $e) {
				Debug::exception($e);
			}
			self::$redis = null;
		}
		
		return $rs;
	}
	
	public function __destruct() {
		$this->free();
	}
}
?>