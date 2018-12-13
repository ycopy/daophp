<?php

namespace daophp\cache;


class FileCacheProvider implements CacheProvider {
	/**
	 * @see CacheProvider::add()
	 *
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @param unknown_type $ttl
	 */
	public function add($key, $value, $ttl) {
	}
	
	/**
	 * @see CacheProvider::delete()
	 *
	 * @param unknown_type $key
	 */
	public function delete($key) {
	}
	
	/**
	 * @see CacheProvider::get()
	 *
	 * @param unknown_type $key
	 */
	public function get($key) {
	}
	
/**
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @param unknown_type $ttl
	 */
	public function set($key, $value, $ttl) {
		
	}
/* (non-PHPdoc)
	 * @see CacheProvider::toStdCacheArray()
	 */
	public function toStdCacheArray($original) {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see CacheProvider::getAll()
	 */
	public function getAll() {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see CacheProvider::notifyFull()
	 */
	public function notifyFull() {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see Singleton::getInstance()
	 */
	public static function getInstance($options = array()) {
		// TODO Auto-generated method stub
		
	}
/* (non-PHPdoc)
	 * @see CacheProvider::isAvailable()
	 */
	public function isAvailable() {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see CacheProvider::exists()
	 */
	public function exists($key, $flag = 0) {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see CacheProvider::deleteAll()
	 */
	public function deleteAll() {
		// TODO Auto-generated method stub
		
	}

}
?>