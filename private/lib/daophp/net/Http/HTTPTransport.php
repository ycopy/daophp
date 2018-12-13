<?php 

namespace daophp\net\http ;

interface HTTPTransport {
	
	/**
	 * init common settings, global handle, etc
	 */
	public function init() ;
	
	/**
	 * used to clean some resourse for Transport, such as curl_multi_init 
	 * Enter description here ...
	 */
	public function close() ;
	
	
	/**
	 * Executes GET request with parameters and returns response
	 * 
	 * @param string $url
	 * @param array $data
	 */
	public function sendGet($url, $data, $options = array(), $source = 'UNKNOWN'  );
	
	/**
	 * Executes POST request with parameters and returns response
	 * 
	 * @param string $url
	 * @param array $data
	 */
	public function sendPost($url, $data, $options = array(), $source = 'UNKNOWN' );
	
	
	public function sendDelete( $url, $data, $options = array(), $source = 'UNKNOWN' ) ;
}