<?php

namespace daophp\net\Http ;

class HTTPResponse implements \Serializable
{
	private $response = ''; //for debug

	private $costTime = 0 ;
	
	private $code = '';
	private $headers 	= '';
	private $body 		= '';
	
	private $etag = '';
	
	const SUCCESS = 200;
	const CREATED = 201;
	const ACCEPTED = 202;
	const MOVED = 301;
	const FOUND = 302;
	const NOT_MODIFIED = 304;
	// Failure Codes
	const BAD_REQUEST = 400;
	const UNAUTHORIZED = 401;
	const FORBIDDEN = 403;
	const NOT_FOUND = 404;
	const REQUEST_TIMEDOUT = 408;
	const PRECONDITION_FAILED = 412;
	const EXPECTATION_FAILED = 417;
	// Server Error Codes
	const INTERNAL_SERVER_ERROR = 500;
	const SERVICE_UNAVAILABLE = 503;
	const GATEWAY_TIMEDOUT = 504;
	// Internet API Error Codes
	const TIMEOUT = 12002;
	
	const HEADER_TYPE_ETAG		 			= 'HEADER_ETAG';
	const HEADER_TYPE_CONTENT_LENGTH		= 'HEADER_CONTENT_LENGTH';
	const HEADER_TYPE_CONTENT_TYPE			= 'HEADER_CONTENT_TYPE';

	private static function _getHeaderInfoFromHeaders($headers, $headerType) {
		
		switch( $headerType ) {
			case self::HEADER_TYPE_ETAG :
				{
					$eTagPattern = '/.*Etag\:\s*\"(?P<etag>\w+)\"\s*/is' ;
					if( preg_match($eTagPattern,$headers, $matchs) ) {
						
						if( isset($matchs['etag']) && !empty($matchs['etag']) ) {
							return $matchs['etag'] ;
						}
					}
					return '';
				}
				break;
		}
	}
	
	public function setEtag( $etag ) {
		$this->etag = $etag ;
	}
	
	public function getEtag() {
		
		if( empty($this->etag) && !empty($this->headers)) {
			$this->etag = $this->_getHeaderInfoFromHeaders($this->headers, self::HEADER_TYPE_ETAG) ;
		}

		return $this->etag ;
	}
	
	public function isPreconditionFailed() {
		return $this->code == self::PRECONDITION_FAILED ;
	}
	
	/**
	 * @return boolean
	 */
	public function isSuccessful() {
		return self::_isSuccessfulCode($this->code);
	}

	/**
	 * @param int $code
	 * @return boolean
	 */
	private static function _isSuccessfulCode($code) {
		return in_array(
			$code,
			array(
				self::SUCCESS,
				self::CREATED,
				self::ACCEPTED,
			)
		);
	}

	public function __construct( $code, $body , $headers = '', $response = '' ) {
		$this->code = intval($code) ;
		$this->body = $body ;
		
		$this->headers = $headers ;
		$this->response = $response ;
	}
	
	public function setCostTime( $costTime ) {
		$this->costTime = $costTime ;
		return $this;
	}
	public function getCostTime() {
		return $this->costTime ;
	}

	public function getCode() {
		return $this->code;
	}

	public function getBody() {
		return $this->body;
	}
	
	public function getResponse() {
		return $this->response ;
	}
	
	public function isTimeOut(){
		return $this -> code == self::GATEWAY_TIMEDOUT || $this -> code == self::REQUEST_TIMEDOUT || $this -> code == self::TIMEOUT;
	}
	
	public function isNotFound() {
		return $this->code == self::NOT_FOUND ;
	}
	
	public function isNotModified() {
		return $this->code == self::NOT_MODIFIED ;
	}
	
	public function serialize() {
		return serialize(
			array(
				'costTime' => $this->costTime ,
				'code' => $this->code,
				'body' => $this->body,
				'headers' => $this->headers,
				'response' => $this->response
			)
		);
	}
	
	public function __toString() {
		return $this->response;
	}
	
	public function unserialize($serialized) {
		$info = unserialize($serialized) ;
		
		$this->costTime = $info['costTime'] ;
		$this->code = $info['code'] ;
		$this->body = $info['body'] ;
		$this->headers = $info['headers'] ;
		$this->response = $info['response'];
	}
}