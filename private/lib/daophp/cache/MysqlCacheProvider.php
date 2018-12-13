<?php

namespace daophp\cache;

use daophp\core\Common;

use daophp\database\DBTableIsFullException;

use daophp\core\Debug;

use daophp\database\DBConnectException;

class MysqlCacheProvider implements CacheProvider {
	
	/* (non-PHPdoc)
	 * @see CacheProvider::add()
	 */
	public function add($key, $value, $ttl=-1) {
		return $this->set( $key, $value, $ttl );
	}

	/* (non-PHPdoc)
	 * @see CacheProvider::delete()
	 */
	public function delete($key,$big=false) {
		
		if( $big ) {
			MysqlMemoryObject::setTableName('cb');
		} else {
			MysqlMemoryObject::setTableName('c');
		}
		
		try {
			$obj = MysqlMemoryObject::getByPK($key) ;
			if( $obj ) {
				return $obj->delete() ;
			}
		} catch( DBConnectException $e ) {
			throw $e;
		} catch ( \Exception $e ) {
			Debug::addException($e);
			return false;
		}
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see CacheProvider::get()
	 */
	public function get($key,$big=false ,$deleteIfExpire = true) {
		if( $big ) {
			MysqlMemoryObject::setTableName('cb') ;
		} else {
			MysqlMemoryObject::setTableName('c') ;
		}
		
		try {
			$obj = MysqlMemoryObject::getByPK($key) ;
			if( !$obj ) {
				if( !$big ) {
					//auto retry big table
					MysqlMemoryObject::setTableName('cb') ;
					$obj = MysqlMemoryObject::getByPK($key) ;
					
					if( !$obj ) {
						return null;
					}
				} else {
					return null;
				}
			}
			
			$std = $this->toStdCacheArray( $obj );
			
			if( !$this->isExpire( $std ) ) {
				$rs = @unserialize($std['v']) ;//some times it return false
				if( $rs === false ) {
					return null;
				}
				return $rs;
			}
			
			if( $deleteIfExpire ) {
				$obj->delete() ;
			}
			
		} catch( DBConnectException $e ) {
			throw $e;
		} catch ( \Exception $e ) {
			Debug::addException($e);
		}
		return null;
	}

	/* (non-PHPdoc)
	 * @see CacheProvider::set()
	 */
	public function set($key, $value, $ttl=-1,$big=false) {
		try {
			$rs = serialize($value) ;
			
			if( $rs === false ) {
				Debug::addError('serialize return false, value: '. Common::pr($value, true, 'for serialize value')) ;
				return false;
			}
			
			$value = $rs ;
			$length = mb_strlen( $value );
					
			if( mb_strlen($key) > 255 ) {
				Debug::addError('cache key <'.$key.'> exceed 255');
				return false;
			}
			
			if( mb_strlen($value) > 8000 ) {
				Debug::addError('cache<'.$key.'> value length exceed length 8000, reach('.$length.'), skip, value<'.$value.'>' );
				return false ;
			}
			
			$isBig = $big || ($length > 2000) ;//if not set ,auto compute			
			if( $isBig ) {
				MysqlMemoryObject::setTableName('cb') ;
			} else {
				MysqlMemoryObject::setTableName('c') ;
			}
			
			$obj = new MysqlMemoryObject($key);
			
			$obj->set('v',$value );
			$obj->set('ttl',$ttl );
			Debug::addCoreLog('set cache<'.$key.'> '. $value . 'with ttl:'.$ttl );
			$rs = $obj->save();
			
			if( !$rs ) {
				Debug::addError('set cache<'.$key.'> '. $value . 'with ttl:'.$ttl . 'failed') ; 
			}
			
			return $rs;
		} catch( DBConnectException $e ) {
			throw $e;
		} catch( DBTableIsFullException $e ){
			$this->notifyFull();
			Debug::addException($e);
			return false ;
		} catch ( \Exception $e ) {
			Debug::addException($e);
			return false;
		}
	}
	
	/**
	 *  std cache array format
	 *  array(
	 *  	'k' => 
	 *  	'v' => 
	 *  	'ttl' =>
	 *  	'ts'  =>
	 *  )
	 */
/* (non-PHPdoc)
	 * @see CacheProvider::toStdCacheArray()
	 */
	public function toStdCacheArray($original) {
		// TODO Auto-generated method stub
		$t = array() ;
		$t['k'] = $original->get('k') ;
		$t['v'] = $original->get('v') ;
		$t['ttl'] = $original->get('ttl');
		$t['ts'] = strtotime($original->get('ts')) ;
		$t['big'] = $original::getTableName() === 'cb' ;
		return $t;
	}

/* (non-PHPdoc)
	 * @see CacheProvider::getAll()
	 */
	public function getAll() {
		MysqlMemoryObject::setTableName('c') ;
		$allObject = MysqlMemoryObject::getByCDA();
		$rs = array() ;
		if( count($allObject) ) {
			foreach( $allObject as $o ) {
				array_push($rs, $this->toStdCacheArray($o) );
			}
		}
		
		MysqlMemoryObject::setTableName('cb') ;
		$allObject = MysqlMemoryObject::getByCDA();
		
		if( count($allObject) ) {
			foreach( $allObject as $o ) {
				array_push($rs, $this->toStdCacheArray($o) );
			}
		}
		
		$allObject = null ;		
		return $rs;
	}
	
	public function notifyFull() {
		$this->deleteAllExpire() ;
	}
	
	public function isExpire( $stdCacheArray ,$ignoreExpireFlag = false ) {
		$expire = false;
		
		if( $stdCacheArray['ttl'] === -1 && ($ignoreExpireFlag ===false) ) {
			$expire = false ;
		} else {
			$expire = time() > ($stdCacheArray['ts']+$stdCacheArray['ttl'] ) ;
		}
		
		if( !$expire ) {
			Debug::addCoreLog('hit key<'.$stdCacheArray['k'].'> , ttl<'.$stdCacheArray['ttl'].'>, ts<'. date('Y-m-d H:i:s', $stdCacheArray['ts']) .'> value-'.mb_strlen($stdCacheArray['v']).'<'.$stdCacheArray['v'].'>' );
		} 
		return $expire;
	}
	
	public function deleteAllExpire( $ignoreExpireFlag = false ) {
		$a = $this->getAll();
		if( count( $a )) {
			foreach($a as $o ) {
				if( $this->isExpire($o,$ignoreExpireFlag)) {
					//echo 'delete: '. $o['k'] .'\n' ;
					$this->delete($o['k'], $o['big'] );
				}
			}
		}
	}
	
	public function deleteAll() {
		$a = $this->getAll();
		if( count( $a )) {
			foreach($a as $o ) {
				//echo 'delete: '. $o['k'] .'\n' ;
				$this->delete($o['k'], $o['big'] );
			}
		}
	}
/* (non-PHPdoc)
	 * @see CacheProvider::isAvailable()
	 */
	public function isAvailable() {
		// TODO Auto-generated method stub
		throw new \Exception('not implement') ;
	}

/* (non-PHPdoc)
	 * @see CacheProvider::exists()
	 */
	public function exists($key, $flag = 0) {
		// TODO Auto-generated method stub
		throw new \Exception('not implement') ;
	}

}