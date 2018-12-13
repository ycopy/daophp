<?php


namespace daophp\net\Http ;

class HTTPDebug extends SingletonObject {
	
	private $_log = array() ;
	
	const REQUEST_URL 			= 'url';
	const REQUEST_CURL_OPTIONS 	= 'curlOptions';
	const REQUEST_RESPONSE		= 'response';
	const REQUEST_TIME			= 'ts';
	
	public function log( $method, $source, $url, $curlOptions, $response ) {
		if( !isset($this->_log[$source]) ) {
			$this->_log[$source] = array();	
		}
		
		if( !isset($this->_log[$source][$method])) {
			$this->_log[$source][$method] = array();
		}
		
// 		if( $callStack ) {
// 			echo '<pre>';
// 			echo $source . "\n";
// 			echo $method . "\n";
// 			exit($callStack );
// 			echo '</pre>';
// 		}
		
//		if( $callStack == null ) {
//			$callStack = Debug::getCallStack(4);
//		}
		
		array_push( $this->_log[$source][$method], 
			array (
				self::REQUEST_TIME			=> time(),
				self::REQUEST_URL			=> $url,
				self::REQUEST_CURL_OPTIONS	=> $curlOptions,
				self::REQUEST_RESPONSE 		=> $response,
			)
		);
	}
	
	public function getLog() {
		
		if( !count($this->_log)) {
			return array() ;
		}
		
		return $this->_log ;
	}
	
	
	public function dump( $htmlentitiesResponse = false ) {
		
		if( !count($this->_log)) {
			return '';
		}
		
//		return print_r($this->_log, true);
		
		$totalRequestCount = 0;
		$totalRequestTime = 0;
		
		$serviceStatistic = array() ;
		
		foreach($this->_log as $serviceName => $allRequest ){
			
			if( !isset($serviceStatistic[$serviceName]) ) {
				$serviceStatistic[$serviceName] =
					array(
						'get' => 0,
						'get_time'	=> 0,
						'post' => 0,
						'post_time' => 0,
						'requests' => array(),
					);
			}
			
			foreach( $allRequest as $method => $requests ) {
				
				foreach($requests as $request ) {
					if( $method == 'GET' ) {
						$serviceStatistic[$serviceName]['get']++;
						$serviceStatistic[$serviceName]['get_time'] += $request['response']->getCostTime();
					} else {
						$serviceStatistic[$serviceName]['post']++;
						$serviceStatistic[$serviceName]['post_time'] += $request['response']->getCostTime();
					}
					
					$response = $request[self::REQUEST_RESPONSE]->__toString() ;
					
					array_push($serviceStatistic[$serviceName]['requests'],
						array(
							'url' 			=> $request[self::REQUEST_URL],
							'curlOptions' 	=> $request[self::REQUEST_CURL_OPTIONS],
							'response'		=> ( $htmlentitiesResponse ? htmlentities($response ) : $response ),
							'callstack'		=> $request[self::REQUEST_CALLSTACK],
							'cost'			=> $request[self::REQUEST_RESPONSE]->getCostTime()
						)
					);
					
					$totalRequestCount++;
					$totalRequestTime += $request['response']->getCostTime();
				}
				
				if( $serviceStatistic[$serviceName]['get'] !==0 ) {
					$serviceStatistic[$serviceName]['getAvgTime'] = $serviceStatistic[$serviceName]['get_time'] / $serviceStatistic[$serviceName]['get'] ;
				}
				
				if($serviceStatistic[$serviceName]['post'] !==0 ) {
					$serviceStatistic[$serviceName]['postAvgTime'] = $serviceStatistic[$serviceName]['post_time'] / $serviceStatistic[$serviceName]['post'] ;
				}
				
				$serviceStatistic[$serviceName]['totalAvgTime'] = ($serviceStatistic[$serviceName]['get_time']+$serviceStatistic[$serviceName]['post_time']) / ($serviceStatistic[$serviceName]['get']+$serviceStatistic[$serviceName]['post']) ;
			}
		}
		
		return array(
			'total'		=> $totalRequestCount,
			'totalTime'	=> $totalRequestTime,
			'totalAvgTime' => sprintf( "%0.6f",$totalRequestTime / $totalRequestCount) ,
			'service'	=> $serviceStatistic,
		);
	}
}
?>
