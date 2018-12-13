<?php


namespace daophp\cache;
use daophp\core\ClassNotFoundException;

use daophp\core\Debug;

use daophp\core\object\SingletonObject;
use daophp\core\Resource ;


class MemCachedProvider extends SingletonObject implements CacheProvider,Resource {
	private static $memcached = null;
	private static $globalFlag = 0; //compress and serial
	
	private static $timeout = 1 ; //do not set too long to avoid performance
	private static $host = DP_MEMCACHED_SERVER;
	private static $port = DP_MEMCACHED_PORT ;
	
	private static $useProcessSafe = false;
	
	private static $serverConnError = false ;
	
	private static $OP_CODE = array(
		0	=> 'RES_SUCCESS',
		1	=> 'RES_FAILURE',
		2	=> 'RES_HOST_LOOKUP_FAILURE',
		7	=> 'RES_UNKNOWN_READ_FAILURE',
		8	=> 'RES_PROTOCOL_ERROR',
		9	=> 'RES_CLIENT_ERROR',
		10	=> 'RES_SERVER_ERROR',
		5	=> 'RES_WRITE_FAILURE',
		12	=> 'RES_DATA_EXISTS',
		14	=> 'RES_NOTSTORED',
		16	=> 'RES_NOTFOUND',
		18	=> 'RES_PARTIAL_READ',
		19	=> 'RES_SOME_ERRORS',
		20	=> 'RES_RES_NO_SERVERS',
		21	=> 'RES_END',
		25	=> 'RES_ERRNO',
		31	=> 'RES_BUFFERED',
		30	=> 'RES_TIMEOUT',
		32	=> 'RES_BAD_KEY_PROVIDED',
		11	=> 'RES_CONNECTION_SOCKET_CREATE_FAILURE',
		1001=> 'RES_PAYLOAD_FAILURE',
		
	);
	
	
	
	public function __construct( $params = array() ) {
		if( self::$memcached == null ) {
			$this->init();
			//self::$memcached->setOption(\Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
			parent::__construct();	
		}
	}
	
	//it is not safe for multi process, cause some other process will colose this connection
	public function isAvailable () {
		if( self::$memcached == null ) {
			return false;
		}
		
		return true;
	}
	
	public function init( $reConn = false ) {
		if( self::$serverConnError && !$reConn) {
			return false ;
		}
		
		if(DP_EXEC_MODE == EXEC_MODE_CLI) {
			self::$useProcessSafe = true;
		}
		
		$s = @fsockopen(self::$host,self::$port,$errno,$errstr,self::$timeout);
		if (!$s){
			Debug::addError('connect to memcache server failed, '. $errno.':'.$errstr );
			self::$serverConnError = true;
			return false;
		}
		fclose($s);
		self::$serverConnError = false;
		
//		self::$pid = getmypid();
		//connect to memcached
		
		try {
			if(self::$useProcessSafe) {
				self::$memcached = new \Memcached(getmypid());
			} else {
				self::$memcached = new \Memcached(DP_SERVER_ADDR);
			}			
		} catch (ClassNotFoundException $e) {
			self::$memcached = null;
			Debug::addException($e) ;
			return null;
		}
		self::$memcached->setOption(\Memcached::OPT_HASH, \Memcached::HASH_CRC);
		self::$memcached->setOption(\Memcached::OPT_CONNECT_TIMEOUT, 2000);
		
		$serverList = self::$memcached->getServerList() ;
		
		$shouldAddNew = true;
		if( count($serverList) ) {
			foreach($serverList as $server) {
				if( ($server['host'] == self::$host) && (self::$port == $server['port']) ) {
					//once hit, do not add any more
					Debug::addCoreLog('cache server ,,hit you ..' . self::$host .':'.self::$port );
					$shouldAddNew = false;
					break;
				}
			}
		} 
		
		if($shouldAddNew) {
			if( !self::$memcached->addServer(self::$host, self::$port ) ) {
				self::$memcached = null ;
				throw new MemCacheConnectException(self::$host, self::$port) ;
			}
			Debug::addCoreLog('cache server ,,add new ..' . self::$host .':'.self::$port );
		}
	}
	
	public function free() {
		if(self::$memcached) {
			self::$memcached = null ;
		}
		return true;
	}
	
	
	/**
	 * @see CacheProvider::add()
	 *
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @param unknown_type $ttl
	 */
	public function add($key, $value, $ttl=0, $flag = 0 ) {
		
		if( !self::$memcached ) {
			Debug::addError(__METHOD__.', NO MEMCACHED EXISTS',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		return @self::$memcached->add($key, $value, $ttl ) ;
	}
	
	/**
	 * @see CacheProvider::delete()
	 *
	 * @param unknown_type $key
	 */
	public function delete($key,$flag = 0) {
		if( !self::$memcached ) {
			Debug::addError(__METHOD__.', NO MEMCACHED EXISTS',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		
		$try=0;
		do {
			$rs = self::$memcached->delete($key) ;
			$rsCode = self::$memcached->getResultCode() ;
			
			if( $rsCode !== \Memcached::RES_SUCCESS ) {
				
				if(isset(self::$OP_CODE[$rsCode])) {
					Debug::addWarning('delete key '.$key.' failed, reason, code<'. self::$OP_CODE[self::$memcached->getResultCode()] .'> ,message: '. self::$memcached->getResultMessage() ,false ) ;
				} else {
					Debug::addWarning('delete key '.$key.' failed, reason, code<unknown> ,message: '. self::$memcached->getResultMessage() ,false ) ;
				}
				
				if($rsCode === \Memcached::RES_NOTFOUND) {
					return false;
				}
			} else {
				Debug::addCoreLog('delete key success: '. $key , false );
				return true;
			}
			
			if($try>0){
				usleep(100*$try);
			}
		} while( $rsCode !== \Memcached::RES_SUCCESS && ($try++ < 3) ) ;
		
		return $rsCode === \Memcached::RES_SUCCESS ;
	}
	
	public function deleteAll() {
		if( !self::$memcached ) {
			Debug::addError(__METHOD__.', NO MEMCACHED EXISTS',false );
			return false;
		}
		
		return self::$memcached->flush() ;
	}
	
	/**
	 * @see CacheProvider::get()
	 *
	 * @param unknown_type $key
	 */
	public function get($key,$flag=0) {
		if( !self::$memcached ) {
			Debug::addError(__METHOD__.', NO MEMCACHED EXISTS',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		
		$hit = self::$memcached->get($key) ;
		$rsCode = self::$memcached->getResultCode() ;
		
		if( $rsCode !== \Memcached::RES_SUCCESS ) {
			Debug::addCoreLog('not hit key: '. $key , false ) ;
			if(isset(self::$OP_CODE[$rsCode])) {
				Debug::addWarning('delete key '.$key.' failed, reason, code<'. self::$OP_CODE[self::$memcached->getResultCode()] .'> ,message: '. self::$memcached->getResultMessage() ,false ) ;
			} else {
				Debug::addWarning('delete key '.$key.' failed, reason, code<unknown> ,message: '. self::$memcached->getResultMessage() ,false ) ;
			}
			return false;
		} else {
			Debug::addCoreLog('hit key: '. $key , false );
			return $hit;
		}
	}
	
/**
	 * @param unknown_type unknown_type $key
	 * @param unknown_type unknown_type $value
	 * @param unknown_type unknown_type $ttl
	 */
	public function set($key, $value, $ttl=0, $flag = 0 ) {
		if( !self::$memcached ) {
			Debug::addError(__METHOD__.', NO MEMCACHED EXISTS',false );
			return false;
		}
		
		if( !($flag & self::FLAG_DISABLE_PREFIX) ) {
			$key = DP_CACHE_KEY_PREFIX . $key ;
		}
		
		$try = 0;
		do {
			self::$memcached->get($key, null ,$cas );
			$exists_check = self::$memcached->getResultCode() ;
			
			$action = '';
			if( $exists_check == \Memcached::RES_NOTFOUND) {
				self::$memcached->add($key, $value, $ttl );
				$action = 'add' ;
			} else {
				self::$memcached->cas($cas, $key, $value, $ttl );
				$action = 'cas' ;
			}
			
			$rsCode = self::$memcached->getResultCode() ;
			if( $rsCode === \Memcached::RES_SUCCESS ) {
				Debug::addCoreLog($action .' cache success, key: '. $key, false ) ;
			} else {
				if(isset(self::$OP_CODE[$rsCode])) {
					Debug::addWarning($action .' key '.$key.' failed, reason, code<'. self::$OP_CODE[self::$memcached->getResultCode()] .'> ,message: '. self::$memcached->getResultMessage() ,false ) ;
				} else {
					Debug::addWarning($action. ' key '.$key.' failed, reason, code<unknown> ,message: '. self::$memcached->getResultMessage() ,false ) ;
				}
			}
			if( $try > 0) {
				usleep((100*$try));
			}
		} while( $rsCode !== \Memcached::RES_SUCCESS && ($try++ < 3) ) ;
		
		return ($rsCode === \Memcached::RES_SUCCESS);
	}
	
	public function exists($key,$flag = 0 ) {
		return $this->get($key,$flag) !== false ;
	}
	
	public function __destruct() {
		$this->free() ;
	}
}
?>