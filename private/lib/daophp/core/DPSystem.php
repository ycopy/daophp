<?php


namespace daophp\core;
use daophp\core\object\SingletonObject ;


class DPSystem extends SingletonObject {
	/*---------------------------some tools to fetch system info-------------------------------*
	 * @moved from MultiProcessUpdater
	 * @date 2011.12.01
	 * @cpingg@gmail.com
	 */
	
	const SYSTEM_INFO_TYPE_LOAD 		='cur_load' ;
	const SYSTEM_INFO_TYPE_UP_DAYS 		='uptime_days' ;
	const SYSTEM_INFO_TYPE_ONLINE_USERS ='online_user' ;
	
	public static function getSystemUptimeInfo( $type = self::SYSTEM_INFO_TYPE_LOAD, $checkCount = 5 ) {
		if( DP_OS == DP_OS_WIN ) {
			Debug::addCoreLog('GET SYSTEM INFO FAILED, only for non_win, for win always return 1', false ) ;
			return 1;
		}
		if( DP_EXEC_MODE == EXEC_MODE_CGI ) {
			Debug::addError('forbidden operaton in cgi mode');
			return null;
		}
		$shell_command = 'uptime';
		$preg = '/.*up\s+((?P<uptime_days>\d+)\s+(?P<update_type>\w+)|(?P<uptime>.*?))[\.,]?\s+(?P<online_user>\d+)\s+user.*load\s+average\:\s*(?P<cur_load>[\d\.]+)(?:\.|,)\s+/is' ;
						
		$resultArray = array() ; //in order to determin the accurate count, we should retry many times, and then return the avg
		$index = 0;
		
		do {
			$current = shell_exec( $shell_command );
			preg_match($preg, $current, $match );
			
			if( isset($match[$type]) && !empty($match[$type]) ) {
				$resultArray[$index++] = floatval($match[$type]);
			} else {
				$index++;
				Debug::addError("GET SYSTEM INFO FAILED, get : ". $current ) ;
			}
			
			usleep( 20000 );
		} while ( $index < $checkCount );
		
		$result = (array_sum( $resultArray )) / count($resultArray ) ;
		Debug::trace('current<'. $type .'> info: '. $result ,false );
		return $result;
	}
	
	public static function getSystemOverviewInfo() {
		if( DP_OS == DP_OS_WIN ) {
			Debug::core('GET SYSTEM INFO FAILED, only for non_win, for win always return 1', false ) ;
			return 1;
		}
		if( DP_EXEC_MODE == EXEC_MODE_CGI ) {
			Debug::error('forbidden operaton in cgi mode');
			return null;
		}
	
		$info = self::getHttpdProcessInfo() . "\n";
		$info .= self::getNetstatInfo() . "\n";
		$info .= self::getPHPProcessInfo() . "\n";
		$info .= self::getDBConnectionInfo() . "\n";
		
		return $info;
	}
	
	public static function getHttpdProcessInfo() {
		if( DP_OS == DP_OS_WIN ) {
			Debug::core('GET SYSTEM INFO FAILED, only for non_win, for win always return 1', false ) ;
			return 1;
		}
		if( DP_EXEC_MODE == EXEC_MODE_CGI ) {
			Debug::error('forbidden operaton in cgi mode');
			return null;
		}
		
		$shell_cmd = 'ps aux | grep httpd' ;
		$http_run_info = shell_exec($shell_cmd);
		return 	$http_run_info;	
	}
	
	public static function getNetstatInfo() {		
		if( DP_OS == DP_OS_WIN ) {
			Debug::core('GET SYSTEM INFO FAILED, only for non_win, for win always return 1', false ) ;
			return 1;
		}
		if( DP_EXEC_MODE == EXEC_MODE_CGI ) {
			Debug::error('forbidden operaton in cgi mode');
			return null;
		}

		$shell_cmd = 'netstat -a | grep kanshu007.com' ;		
		$netstat_a_info = shell_exec($shell_cmd);
		return $netstat_a_info ;		
	}
	
	public static function getPHPProcessInfo(){
		if( DP_OS == DP_OS_WIN ) {
			Debug::core('GET SYSTEM INFO FAILED, only for non_win, for win always return 1', false ) ;
			return 1;
		}
		if( DP_EXEC_MODE == EXEC_MODE_CGI ) {
			Debug::error('forbidden operaton in cgi mode');
			return null;
		}		
		$shell_cmd = 'ps aux | grep php' ;		
		$php_info = shell_exec($shell_cmd);
		return $php_info ;			
	}
	
	public static function getDBConnectionInfo() {
		if( DP_OS == DP_OS_WIN ) {
			Debug::core('GET SYSTEM INFO FAILED, only for non_win, for win always return 1', false ) ;
			return 1;
		}
		if( DP_EXEC_MODE == EXEC_MODE_CGI ) {
			Debug::error('forbidden operaton in cgi mode');
			return null;
		}
		$shell_cmd = 'netstat -a | grep mysql' ;		
		$mysql_info = shell_exec($shell_cmd);
		return $mysql_info ;			
	}
	
	public static function getSystemInfo() {
		if( DP_EXEC_MODE === EXEC_MODE_CLI ) {
			return self::getSystemOverviewInfo();
		} else {
			return Common::pr($_SERVER,true, 'serverinfo');
		}
	}
	
    public static function isSystemOverload(&$curLoad = null) {
    	$uptimeLoad = self::getSystemUptimeInfo(self::SYSTEM_INFO_TYPE_LOAD);
    	$curLoad = $uptimeLoad;
    	return $uptimeLoad>5.0 ;
    }
}
?>
