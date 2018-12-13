<?php

namespace daophp\net\http ;

class CURLTransport implements HTTPTransport {

	private $_useCurlMulti = false;

	public function init() {
		if(  $this->_useCurlMulti ) {
			$this->_initCurlMultiHandle();
		}
	}

	public function close() {
		$this->_closeCurlMultiHandle();
	}

	private $_curlMultiHandle = null;

	private  function _initCurlMultiHandle() {
		$this->_curlMultiHandle = curl_multi_init() ;
	}

	private function _getCurlMultiHandle() {
		return $this->_curlMultiHandle ;
	}

	public function _closeCurlMultiHandle() {
		if( $this->_curlMultiHandle ) {
			curl_multi_close($this->_curlMultiHandle) ;
			$this->_curlMultiHandle = null;
		}
	}

	public function __construct( $log , $useCurlMultiHandle = false ) {
		$this->setLog($log);

		$this->_useCurlMulti = $useCurlMultiHandle ;
		$this->init();
	}

	public function __destruct() {
		$this->_closeCurlMultiHandle() ;
	}

	private $_log = null;
	public function setLog( $log ) {
		$this->_log = $log ;
	}

	public function log($message, $level ) {
		if($this->_log) {
			$this->_log->log( $message, $level );
		}
	}


	public static $curlOptionsArray = array (
	        47 => 'CURLOPT_POST',
	        64 => 'CURLOPT_SSL_VERIFYPEER',
	        19913 => 'CURLOPT_RETURNTRANSFER',
	        155 => 'CURLOPT_TIMEOUT_MS',
	        156 => 'CURLOPT_CONNECTTIMEOUT_MS',
	        10015 => 'CURLOPT_POSTFIELDS',
	        91 => 'CURLOPT_DNS_USE_GLOBAL_CACHE',
	        92 => 'CURLOPT_DNS_CACHE_TIMEOUT',
	        3 => 'CURLOPT_PORT',
	        10001 => 'CURLOPT_FILE',
	        10009 => 'CURLOPT_READDATA',
	        10009 => 'CURLOPT_INFILE',
	        14 => 'CURLOPT_INFILESIZE',
	        10002 => 'CURLOPT_URL',
	        10004 => 'CURLOPT_PROXY',
	        41 => 'CURLOPT_VERBOSE',
	        42 => 'CURLOPT_HEADER',
	        10023 => 'CURLOPT_HTTPHEADER',
	        43 => 'CURLOPT_NOPROGRESS',
	        20056 => 'CURLOPT_PROGRESSFUNCTION',
	        44 => 'CURLOPT_NOBODY',
	        45 => 'CURLOPT_FAILONERROR',
	        46 => 'CURLOPT_UPLOAD',
	        47 => 'CURLOPT_POST',
	        48 => 'CURLOPT_FTPLISTONLY',
	        50 => 'CURLOPT_FTPAPPEND',
	        51 => 'CURLOPT_NETRC',
	        52 => 'CURLOPT_FOLLOWLOCATION',
	        54 => 'CURLOPT_PUT',
	        10005 => 'CURLOPT_USERPWD',
	        10006 => 'CURLOPT_PROXYUSERPWD',
	        10007 => 'CURLOPT_RANGE',
	        13 => 'CURLOPT_TIMEOUT',
	        155 => 'CURLOPT_TIMEOUT_MS',
	        10015 => 'CURLOPT_POSTFIELDS',
	        10016 => 'CURLOPT_REFERER',
	        10018 => 'CURLOPT_USERAGENT',
	        10017 => 'CURLOPT_FTPPORT',
	        85 => 'CURLOPT_FTP_USE_EPSV',
	        19 => 'CURLOPT_LOW_SPEED_LIMIT',
	        20 => 'CURLOPT_LOW_SPEED_TIME',
	        21 => 'CURLOPT_RESUME_FROM',
	        10022 => 'CURLOPT_COOKIE',
	        96 => 'CURLOPT_COOKIESESSION',
	        58 => 'CURLOPT_AUTOREFERER',
	        10025 => 'CURLOPT_SSLCERT',
	        10026 => 'CURLOPT_SSLCERTPASSWD',
	        10029 => 'CURLOPT_WRITEHEADER',
	        81 => 'CURLOPT_SSL_VERIFYHOST',
	        10031 => 'CURLOPT_COOKIEFILE',
	        32 => 'CURLOPT_SSLVERSION',
	        33 => 'CURLOPT_TIMECONDITION',
	        34 => 'CURLOPT_TIMEVALUE',
	        10036 => 'CURLOPT_CUSTOMREQUEST',
	        10037 => 'CURLOPT_STDERR',
	        53 => 'CURLOPT_TRANSFERTEXT',
	        19913 => 'CURLOPT_RETURNTRANSFER',
	        10028 => 'CURLOPT_QUOTE',
	        10039 => 'CURLOPT_POSTQUOTE',
	        10062 => 'CURLOPT_INTERFACE',
	        10063 => 'CURLOPT_KRB4LEVEL',
	        61 => 'CURLOPT_HTTPPROXYTUNNEL',
	        69 => 'CURLOPT_FILETIME',
	        20011 => 'CURLOPT_WRITEFUNCTION',
	        20012 => 'CURLOPT_READFUNCTION',
	        20079 => 'CURLOPT_HEADERFUNCTION',
	        68 => 'CURLOPT_MAXREDIRS',
	        71 => 'CURLOPT_MAXCONNECTS',
	        72 => 'CURLOPT_CLOSEPOLICY',
	        74 => 'CURLOPT_FRESH_CONNECT',
	        75 => 'CURLOPT_FORBID_REUSE',
	        10076 => 'CURLOPT_RANDOM_FILE',
	        10077 => 'CURLOPT_EGDSOCKET',
	        78 => 'CURLOPT_CONNECTTIMEOUT',
	        156 => 'CURLOPT_CONNECTTIMEOUT_MS',
	        64 => 'CURLOPT_SSL_VERIFYPEER',
	        10065 => 'CURLOPT_CAINFO',
	        10097 => 'CURLOPT_CAPATH',
	        10082 => 'CURLOPT_COOKIEJAR',
	        10083 => 'CURLOPT_SSL_CIPHER_LIST',
	        19914 => 'CURLOPT_BINARYTRANSFER',
	        99 => 'CURLOPT_NOSIGNAL',
	        101 => 'CURLOPT_PROXYTYPE',
	        98 => 'CURLOPT_BUFFERSIZE',
	        80 => 'CURLOPT_HTTPGET',
	        84 => 'CURLOPT_HTTP_VERSION',
	        10087 => 'CURLOPT_SSLKEY',
	        10088 => 'CURLOPT_SSLKEYTYPE',
	        10026 => 'CURLOPT_SSLKEYPASSWD',
	        10089 => 'CURLOPT_SSLENGINE',
	        90 => 'CURLOPT_SSLENGINE_DEFAULT',
	        10086 => 'CURLOPT_SSLCERTTYPE',
	        27 => 'CURLOPT_CRLF',
	        10102 => 'CURLOPT_ENCODING',
	        59 => 'CURLOPT_PROXYPORT',
	        105 => 'CURLOPT_UNRESTRICTED_AUTH',
	        106 => 'CURLOPT_FTP_USE_EPRT',
	        121 => 'CURLOPT_TCP_NODELAY',
	        10104 => 'CURLOPT_HTTP200ALIASES',
	        30146 => 'CURLOPT_MAX_RECV_SPEED_LARGE',
	        30145 => 'CURLOPT_MAX_SEND_SPEED_LARGE',
	        107 => 'CURLOPT_HTTPAUTH',
	        111 => 'CURLOPT_PROXYAUTH',
	        110 => 'CURLOPT_FTP_CREATE_MISSING_DIRS',
	        10103 => 'CURLOPT_PRIVATE',
	        129 => 'CURLOPT_FTPSSLAUTH',
	        138 => 'CURLOPT_FTP_FILEMETHOD',
	        137 => 'CURLOPT_FTP_SKIP_PASV_IP'
	);

	public static function SetCurlOptionsStringKey( $curlOptions ) {

		$result = array();
		foreach( $curlOptions as $key => $value ) {

			if( isset( self::$curlOptionsArray[$key]) ) {
				$result[self::$curlOptionsArray[$key]] = $value;
			}
		}

		return $result;
	}

	const CURLE_OK 									= 0;
	const CURLE_UNSUPPORTED_PROTOCOL 				= 1;
	const CURLE_FAILED_INIT							= 2;
	const CURLE_URL_MALFORMAT						= 3;
	const CURLE_NOT_BUILT_IN						= 4;
	const CURLE_COULDNT_RESOLVE_PROXY				= 5;
	const CURLE_COULDNT_RESOLVE_HOST				= 6;
	const CURLE_COULDNT_CONNECT      				= 7;
	const CURLE_FTP_WEIRD_SERVER_REPLY				= 8;
	const CURLE_REMOTE_ACCESS_DENIED				= 9;
	const CURLE_FTP_ACCEPT_FAILED   				= 10;
	const CURLE_FTP_WEIRD_PASS_REPLY				= 11;
	const CURLE_FTP_ACCEPT_TIMEOUT  				= 12;
	const CURLE_FTP_WEIRD_PASV_REPLY				= 13;
	const CURLE_FTP_WEIRD_227_FORMAT				= 14;
	const CURLE_FTP_CANT_GET_HOST   				= 15;
	const CURLE_FTP_COULDNT_SET_TYPE				= 17;
	const CURLE_PARTIAL_FILE        				= 18;
	const CURLE_FTP_COULDNT_RETR_FILE				= 19;
	const CURLE_QUOTE_ERROR          				= 21;
	const CURLE_HTTP_RETURNED_ERROR  				= 22;
	const CURLE_WRITE_ERROR         				= 23;
	const CURLE_UPLOAD_FAILED       				= 25;
	const CURLE_READ_ERROR          				= 26;
	const CURLE_OUT_OF_MEMORY       				= 27;
	const CURLE_OPERATION_TIMEDOUT   				= 28;
	const CURLE_FTP_PORT_FAILED     				= 30;
	const CURLE_FTP_COULDNT_USE_REST				= 31;
	const CURLE_RANGE_ERROR         				= 33;
	const CURLE_HTTP_POST_ERROR     				= 34;
	const CURLE_SSL_CONNECT_ERROR    				= 35;
	const CURLE_BAD_DOWNLOAD_RESUME  				= 36;
	const CURLE_FILE_COULDNT_READ_FILE				= 37;
	const CURLE_LDAP_CANNOT_BIND    				= 38;
	const CURLE_LDAP_SEARCH_FAILED  				= 39;
	const CURLE_FUNCTION_NOT_FOUND  				= 41;
	const CURLE_ABORTED_BY_CALLBACK 				= 42;
	const CURLE_BAD_FUNCTION_ARGUMENT				= 43;
	const CURLE_INTERFACE_FAILED    				= 45;
	const CURLE_TOO_MANY_REDIRECTS  				= 47;
	const CURLE_UNKNOWN_OPTION      				= 48;
	const CURLE_TELNET_OPTION_SYNTAX				= 49;
	const CURLE_PEER_FAILED_VERIFICATION			= 51;
	const CURLE_GOT_NOTHING         				= 52;
	const CURLE_SSL_ENGINE_NOTFOUND 				= 53;
	const CURLE_SSL_ENGINE_SETFAILED				= 54;
	const CURLE_SEND_ERROR          				= 55;
	const CURLE_RECV_ERROR          				= 56;
	const CURLE_SSL_CERTPROBLEM      				= 58;
	const CURLE_SSL_CIPHER          				= 59;
	const CURLE_SSL_CACERT          				= 60;
	const CURLE_BAD_CONTENT_ENCODING				= 61;
	const CURLE_LDAP_INVALID_URL    				= 62;
	const CURLE_FILESIZE_EXCEEDED    				= 63;
	const CURLE_USE_SSL_FAILED      				= 64;
	const CURLE_SEND_FAIL_REWIND     				= 65;
	const CURLE_SSL_ENGINE_INITFAILED				= 66;
	const CURLE_LOGIN_DENIED        				= 67;
	const CURLE_TFTP_NOTFOUND       				= 68;
	const CURLE_TFTP_PERM           				= 69;
	const CURLE_REMOTE_DISK_FULL     				= 70;
	const CURLE_TFTP_ILLEGAL        				= 71;
	const CURLE_TFTP_UNKNOWNID        				= 72;
	const CURLE_REMOTE_FILE_EXISTS     				= 73;
	const CURLE_TFTP_NOSUCHUSER         			= 74;
	const CURLE_CONV_FAILED         				= 75;
	const CURLE_CONV_REQD           				= 76;
	const CURLE_SSL_CACERT_BADFILE     				= 77;
	const CURLE_REMOTE_FILE_NOT_FOUND  				= 78;
	const CURLE_SSH                  				= 79;
	const CURLE_SSL_SHUTDOWN_FAILED   				= 80;
	const CURLE_AGAIN               				= 81;
	const CURLE_SSL_CRL_BADFILE        				= 82;
	const CURLE_SSL_ISSUER_ERROR        			= 83;
	const CURLE_FTP_PRET_FAILED         			= 84;
	const CURLE_RTSP_CSEQ_ERROR          			= 85;
	const CURLE_RTSP_SESSION_ERROR          		= 86;
	const CURLE_FTP_BAD_FILE_LIST          			= 87;
	const CURLE_CHUNK_FAILED             			= 88;

	private $_useEtag = false;

	public function isEtagOn() {
		return $this->_useEtag === true;
	}

	public function turnEtagOn() {
		$this->_useEtag = true;
	}

	public function turnEtagOff() {
		$this->_useEtag = false;
	}

	private static $defaultCurlOptions = array(
		CURLOPT_SSL_VERIFYPEER 		=> false, // Don't check certificate
		CURLOPT_SSL_VERIFYHOST 		=> false, // Fixed: https, SSL peer certificate or SSH remote key was not OK
	    CURLOPT_RETURNTRANSFER 		=> true,
		CURLOPT_TIMEOUT_MS			=> 10000 , // Max execution time
		CURLOPT_CONNECTTIMEOUT_MS	=> 10000 , // Max connection waiting time
		CURLOPT_FOLLOWLOCATION		=> 1 //Added 2014.06.03, for eve redirect
	);

	const CURL_TRY_TIME                    = 3;
	const DELAY_TIMELIMIT_FOR_DEVELOPMENT  = 30;
	const DELAY_TIMELIMIT_FOR_PRODUCT      = 10;

	const CURL_TIMEOUT_MS_FOR_DEVELOPMENT = 30000;

	/**
	 */
	public function sendGet($url, $data = array(), $options = array(), $source = 'UNKNOWN' ) {

		if( $this->isEtagOn() ) {
			$options[CURLOPT_HEADER] = true ;
		}

		if(count($data) > 0) {
			$url .= '?' . http_build_query($data);
		}

		$curlOptions = $options + self::$defaultCurlOptions ;

		if( WebEnv::isDevelopment() ) {
			$curlOptions[CURLOPT_TIMEOUT_MS] = self::CURL_TIMEOUT_MS_FOR_DEVELOPMENT;
			$curlOptions[CURLOPT_CONNECTTIMEOUT_MS] = self::CURL_TIMEOUT_MS_FOR_DEVELOPMENT;
		}

// 		$this->log('[sendGet Begin] '. $url . print_r( $curlOptions , true ) , Logger::WARN );

		$response = null;
		$try = 1;

		do {
			try {
// 				$this->log('[sendGet{'. $try .'}] '. $url ."\n" . print_r( self::SetCurlOptionsStringKey($curlOptions) , true ) , Logger::INFO );
				$response = $this->_request($url, $curlOptions , $source ) ;
			} catch ( CURLException $e ) {

				if( $e->isHostNotResolved() ) {
					throw $e;
				}

				if( $try >= self::CURL_TRY_TIME ) {
					throw new CurlTimeoutException( 'curl failed after trying <'. self::CURL_TRY_TIME .'> times, '. "\ndetail curl info: ". $e->getMessage() . "\n". $e->getTraceAsString(), $e->getCode(), $e ) ;
				} else {

					//only retry on timeout
					$this->log('[sendGet] CURL retries {'. ($try - 1) .'} times, URL: '. $url , Logger::WARN );
					usleep($try * 100);

					if( WebEnv::isDevelopment() ) {
						set_time_limit(self::DELAY_TIMELIMIT_FOR_DEVELOPMENT); //delay DELAY_TIMELIMIT_FOR_DEVELOPMENT seconds for every retry
					} else {
						set_time_limit(self::DELAY_TIMELIMIT_FOR_PRODUCT); //delay DELAY_TIMELIMIT_FOR_PRODUCT seconds for every retry
					}

					++$try;
				}

				$this->log('[sendGet Exception]'. $url . print_r( self::SetCurlOptionsStringKey($curlOptions) , true ) . $e->getCode() ."\n". $e->getMessage()."\n" , Logger::INFO );
			} catch( \Exception $e ){
				throw $e;
			}

		} while ( $response === null) ;

// 		$this->log('[sendGet End] '. $url ."\n" . print_r( self::SetCurlOptionsStringKey($curlOptions) , true ) . $response->getResponse() , Logger::INFO );
		return $response ;
	}

	/**
	 */
	public function sendPost($url, $data, $options = array(), $source = 'UNKNOWN' ) {

		$options[CURLOPT_HEADER] = true ;

		$curlOptions = array (
			CURLOPT_POST => true,
		) + $options + self::$defaultCurlOptions  ;

		if( !empty($options['etag']) ) {
			$curlOptions[CURLOPT_HTTPHEADER] = array (
				'If-Match: '. $options['etag']
			) ;
		}

		if( isset( $curlOptions['etag'] ) ) {
			unset( $curlOptions['etag'] ) ;
		}

		$curlOptions[CURLOPT_POSTFIELDS] = http_build_query($data);

	   if( WebEnv::isDevelopment() ) {
			$curlOptions[CURLOPT_TIMEOUT_MS] = self::CURL_TIMEOUT_MS_FOR_DEVELOPMENT;
			$curlOptions[CURLOPT_CONNECTTIMEOUT_MS] = self::CURL_TIMEOUT_MS_FOR_DEVELOPMENT;
		}

// 		$this->log('[sendPost Begin] '. $url . print_r( $curlOptions , true ) , Logger::WARN );

		$response = null;
		$try = 1;

		do {
			try {
// 				$this->log('[sendPost{'. $try .'}] '. $url  ."\n" . print_r( $curlOptions , true ) , Logger::WARN );
				$response = $this->_request($url, $curlOptions, $source );
			} catch ( CURLException $e ) {

				if( $e->isHostNotResolved() ) {
					throw $e;
				}

				if( $try >= self::CURL_TRY_TIME ) {
					throw new CurlTimeoutException( 'curl failed after trying <'. self::CURL_TRY_TIME .'> times, '. "\ndetail curl info: ". $e->getMessage() . "\n". $e->getTraceAsString(), $e->getCode(), $e ) ;
				} else {

					//only retry on timeout
					$this->log('[sendGet] CURL retries {'. ($try - 1) .'} times, URL: '. $url , Logger::WARN );
					usleep(($try * 100));

					if( WebEnv::isDevelopment() ) {
						set_time_limit(self::DELAY_TIMELIMIT_FOR_DEVELOPMENT); //delay DELAY_TIMELIMIT_FOR_DEVELOPMENT seconds for every retry
					} else {
						set_time_limit(self::DELAY_TIMELIMIT_FOR_PRODUCT); //delay DELAY_TIMELIMIT_FOR_PRODUCT seconds for every retry
					}

					++$try;
				}

				$this->log('[sendPost Exception]'. $url . print_r( self::SetCurlOptionsStringKey($curlOptions) , true ) . $e->getCode() ."\n". $e->getMessage()."\n" , Logger::INFO );

			} catch( \Exception $e ){
				throw $e;
			}

		} while ( $response === null ) ;

// 		$this->log('[sendPost End] '. $url . print_r( $curlOptions , true ) , Logger::WARN );

		return $response ;
	}

	public function sendDelete( $url, $data, $options = array(), $source = 'UNKNOWN') {
		$curlOptions = array (
			CURLOPT_CUSTOMREQUEST => 'DELETE',
		) + $options + self::$defaultCurlOptions  ;

		$curlOptions[CURLOPT_POSTFIELDS] = http_build_query($data);

// 		$this->log('[sendDelete Begin] '. $url . print_r( $curlOptions , true ) , Logger::WARN );

		if( WebEnv::isDevelopment() ) {
		    $curlOptions[CURLOPT_TIMEOUT_MS] = self::CURL_TIMEOUT_MS_FOR_DEVELOPMENT;
		    $curlOptions[CURLOPT_CONNECTTIMEOUT_MS] = self::CURL_TIMEOUT_MS_FOR_DEVELOPMENT;
		}

		$response = null;
		$try = 1;

		do {
			try {
// 				$this->log('[sendDelete{'. $try .'}] '. $url  ."\n" . print_r( $curlOptions , true ) , Logger::WARN );
				$response = $this->_request($url, $curlOptions, $source );
			} catch ( CURLException $e ) {

				if( $e->isHostNotResolved() ) {
					throw $e;
				}

				if( $try >= self::CURL_TRY_TIME ) {
					throw new CurlTimeoutException( 'curl failed after trying <'. self::CURL_TRY_TIME .'> times, '. "\ndetail curl info: ". $e->getMessage() . "\n". $e->getTraceAsString(), $e->getCode(), $e ) ;
				} else {

					//only retry on timeout
					$this->log('[sendGet] CURL retries {'. ($try - 1) .'} times, URL: '. $url , Logger::WARN );
					usleep(($try * 100));

					if( WebEnv::isDevelopment() ) {
						set_time_limit(self::DELAY_TIMELIMIT_FOR_DEVELOPMENT); //delay DELAY_TIMELIMIT_FOR_DEVELOPMENT seconds for every retry
					} else {
						set_time_limit(self::DELAY_TIMELIMIT_FOR_PRODUCT); //delay DELAY_TIMELIMIT_FOR_PRODUCT seconds for every retry
					}

					++$try;
				}
				$this->log('[sendDelete Exception]'. $url . print_r( self::SetCurlOptionsStringKey($curlOptions) , true ) . $e->getCode() ."\n". $e->getMessage()."\n" , Logger::INFO );

			} catch( \Exception $e ){
				throw $e;
			}

		} while ( $response === null ) ;

// 		$this->log('[sendDelete End] '. $url . print_r( $curlOptions , true ) , Logger::WARN );

		return $response ;
	}


	const HTTP_METHOD_GET 		= 'GET';
	const HTTP_METHOD_POST 		= 'POST';
	const HTTP_METHOD_DELETE 	= 'DELETE';
	const HTTP_METHOD_TRACE 	= 'TRACE';
	const HTTP_METHOD_PUT 		= 'PUT';
	const HTTP_METHOD_CONNECT 	= 'CONNECT';

	/**
	 * @param string $url
	 * @param array $curlOptions
	 */
	protected function _request( $url, array $curlOptions, $source ) {

		if( is_object( $source) ) {
			$source = get_class( $source );
		}

		$httpMethod = self::HTTP_METHOD_GET ;
		$data = '' ;

		if(isset($curlOptions[CURLOPT_POST]) && $curlOptions[CURLOPT_POST] ) {
			$httpMethod = self::HTTP_METHOD_POST ;
			$data = $curlOptions[CURLOPT_POSTFIELDS] ;
		} else if( isset($curlOptions[CURLOPT_CUSTOMREQUEST]) && $curlOptions[CURLOPT_CUSTOMREQUEST]) {
			$httpMethod = $curlOptions[CURLOPT_CUSTOMREQUEST] ;
			$data = $curlOptions[CURLOPT_POSTFIELDS];
		} else {}

		$this->log( 'Begin--'. $httpMethod. '['. $source .']' .'--'. $url . ((!empty($data)|| (is_array($data) &&count($data)) ) ? "\n". ', curlOptions: '."\n". print_r(self::SetCurlOptionsStringKey($curlOptions),true) : ', ' )."\n" . Debug::getCallStack(1,true) . "\n" , Logger::INFO );

		$beginTime = microtime(true);
		$curlHandle = curl_init($url);
		curl_setopt_array($curlHandle, $curlOptions);

		//curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Expect:'));
		$response = null;
		$curl_response = '';

//		$this->_curlMultiHandle  = null;

		if( $this->_curlMultiHandle ) {
			curl_multi_add_handle( $this->_curlMultiHandle, $curlHandle );

			$running = null;

			do {
				$mrc = curl_multi_exec( $this->_curlMultiHandle, $running );
			} while ( $mrc == CURLM_CALL_MULTI_PERFORM );

//			echo 'mrc: '. $mrc . ". running: ". $running ."\n" ;

			while( $running && $mrc == CURLM_OK ) {
 				/**
 				 * The curl_multi_select function(>=5.3.10, 5.3.9 and below versions works) cannot work correctly on windows platform for a bug in libcurl library.
 				 * Follow this comment(https://bugs.php.net/bug.php?id=61141) and libcurl API(http://curl.haxx.se/libcurl/c/curl_multi_fdset.html) documention,
 				 * we change the curl_multi_select function call like this:
				 *
				 */

 				if ( curl_multi_select($this->_curlMultiHandle) == -1 ) {
// 					echo 'select return -1' . "\n";
 					usleep(50);
 				}

				do {
					$mrc = curl_multi_exec($this->_curlMultiHandle, $running );
//					echo 're - mrc: '. $mrc . ". running: ". $running ."\n" ;
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}

			$curl_multi_info = curl_multi_info_read($this->_curlMultiHandle) ;

//			$log = 'running: '. $running ."\n" ;
//			$log .= 'url: '.print_r( $url, true ) . "\n". 'request data: '. print_r( $data, true ) ."\n" ;
//			$log .= 'curl_multi_info_read: '. print_r( $curl_multi_info, true ) ."\n";
//			$log .= 'curl_multi_getcontent : '. curl_multi_getcontent($this->_curlMultiHandle)."\n" ;
//			$log .= 'httpCode: '. curl_getinfo($curlHandle, CURLINFO_HTTP_CODE) .', errno : '.  curl_errno($curlHandle) . ' error: '. curl_error($curlHandle)."\n";
//			$log .= 'curlOptions: '.print_r($curlOptions, true) ."\n";
//			$log .= 'handle equal: '. (($curl_multi_info['handle'] === $curlHandle) ? 'yes' : 'no') ."\n";
//
//			$this->log( $log , Logger::INFO );

			$curl_response = curl_multi_getcontent($curlHandle) ;

			if( isset($curl_multi_info['result']) && $curl_multi_info['result'] !== self::CURLE_OK
				&& isset($curl_multi_info['handle']) && $curl_multi_info['handle'] === $curlHandle
			) {
				curl_multi_remove_handle($this->_curlMultiHandle, $curlHandle );

				$costTime = sprintf("%0.6f",(microtime(true) - $beginTime)) ;
				$this->processCurlError($curl_multi_info['result'], $curlHandle,$httpMethod, $curl_response , $curlOptions, $costTime, $source ) ;
			}

			curl_multi_remove_handle($this->_curlMultiHandle, $curlHandle );
		} else {
			$curl_response = trim(curl_exec($curlHandle));

			if( $errCode = curl_errno($curlHandle) ) {
				$costTime = sprintf("%0.6f",(microtime(true) - $beginTime)) ;
				$this->processCurlError($errCode, $curlHandle,$httpMethod, $curl_response, $curlOptions,$costTime, $source );
			}
		}

		$httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);


		if( isset($curlOptions[CURLOPT_HEADER]) && $curlOptions[CURLOPT_HEADER] ) {
			$headerSize = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE );

//			echo '$curl_response';
//			var_dump( $curl_response );
//			echo '$curl_response';
//			echo '<br />';

			$headers = '';
			$body = '';

			if(preg_match('/.*\r\n\r\n/', $curl_response) ) {
				/*
				 *
				 * fix two header issues
				 * merge two header into one
				 * fetch the last one for body
				 */
				$retArray=preg_split("/\r\n\r\n/",$curl_response);

//				echo '$retArray';
//				var_dump( $retArray);
//				echo '$retArray';
//				echo '<br />';

				$retArrayCount = count( $retArray );
				for($i=0;$i<=$retArrayCount-2;$i++){
					$headers .= $retArray[$i] . "\r\n" ;
				}

//				echo '$headers';
//				var_dump($headers);
//				echo $i;
//				echo '$headers';
//				echo '<br />';

				$body = $retArray[$retArrayCount-1];

//				echo '$body';
//				var_dump($body);
//				echo $i;
//				echo '$body';
//				echo '<br />';

			} else {
				$headers = substr($curl_response,0,$headerSize);
				$body = substr($curl_response,$headerSize );
			}

			if( empty($headers) ) {
				throw new CURLException('get empty headers, http result: ' . $curl_response .' headersize: #'. $headerSize .'#' );
			}

			$response = new HTTPResponse($httpCode, $body, $headers , $curl_response );
		} else {
			$response = new HTTPResponse($httpCode, $curl_response ,'', $curl_response );
		}
		curl_close($curlHandle);

		$costTime = sprintf("%0.6f",(microtime(true) - $beginTime)) ;
		$response->setCostTime( $costTime );

		$log = 'End--'. $httpMethod .'--'. $url ."\n" . sprintf("cost time: %0.4f", $costTime) . ', curl_response: '. "\n". $curl_response . "\n" ;
		$this->log( $log , Logger::INFO );

		HTTPDebug::getInstance()->log( $httpMethod, $source , $url, $curlOptions , $response );

		return $response;
	}

	/**
	 * @param int $errCode
	 * @param cURL handle $curlHandle
	 * @param string $result
	 */
	protected function processCurlError($errCode, $curlHandle,$httpMethod, $result, $curlOptions ,$costTime, $source ) {

		if( isset( $curlOptions[CURLOPT_POSTFIELDS]) && isset( $curlOptions[CURLOPT_POSTFIELDS]['password']) ) {

			$len = strlen($curlOptions[CURLOPT_POSTFIELDS]['password']);

			if( $len === 0 ) {
				$curlOptions[CURLOPT_POSTFIELDS]['password'] = '';
			} else if( $len > 6 ) {
				$curlOptions[CURLOPT_POSTFIELDS]['password'] = substr($curlOptions[CURLOPT_POSTFIELDS]['password'],0,3). str_repeat('*', strlen($curlOptions[CURLOPT_POSTFIELDS]['password']) - 6). substr($curlOptions[CURLOPT_POSTFIELDS]['password'],-3,3);
			} else {
				$curlOptions[CURLOPT_POSTFIELDS]['password'] = substr($curlOptions[CURLOPT_POSTFIELDS]['password'],0,1). str_repeat('*', strlen($curlOptions[CURLOPT_POSTFIELDS]['password']) - 1). substr($curlOptions[CURLOPT_POSTFIELDS]['password'],-1,1);
			}
		}

		$url = curl_getinfo($curlHandle, CURLINFO_EFFECTIVE_URL) ;
		$curlError = curl_error($curlHandle) ;
		curl_close($curlHandle);

		$response = new HTTPResponse($errCode, $result, '', 'curl_error: '. $curlError . "\n\nresult: ". $result ) ;
		$response->setCostTime( $costTime );

		$error = '<pre>';
		$error .= "<span style=\"color:red;\">CURL request error</span>" ;
		$error .= "\n\tCURL URL: ". $url;
		$error .= "\n\tCURL CODE: " .$errCode ;
		$error .= "\n\tCURL MESSAGE: " . $curlError ;
		$error .= "\n\tCURL RESPONSE: " . $result ;
		$error .= "\n\tCURL OPTIONS: " . print_r(self::SetCurlOptionsStringKey($curlOptions),true);
		$error .= '</pre>';

		HTTPDebug::getInstance()->log($httpMethod,$source,$url, $curlOptions,$response );

		$this->log('close curl handle, error: '. $errCode . ':'. @curl_error($curlHandle) , Logger::WARN ) ;

		throw new CURLException($error, $errCode);
	}
}