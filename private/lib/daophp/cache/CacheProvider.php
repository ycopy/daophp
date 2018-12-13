<?php

namespace daophp\cache;


interface CacheProvider  {
	
	const FLAG_DISABLE_PREFIX = 0x01;
	
	public function isAvailable();
	public function exists($key,$flag=0);
	public function add( $key , $value , $ttl=0, $flag = 0 );
	public function delete( $key,$flag = 0);
	public function get( $key, $flag = 0);	
	public function set ($key, $value, $ttl=0 , $flag = 0 );
	public function deleteAll() ;//clear all the items
}
?>