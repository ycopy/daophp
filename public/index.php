<?php

/*************************************************

DaoPHP - the PHP Web Framework
Author: cpingg@gmail.com
Copyright (c): 2008-2010 DaoPHP Group, all rights reserved
Version: 1.0.0

 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

You may contact the author of DaoPHP by e-mail at:
cpingg@gmail.com

The latest version of DaoPHP can be obtained from:
https://cp-daophp.googlecode.com/svn/trunk/

*************************************************/

//var_dump(microtime(true));

//$micro_and_seconds  = explode(" ",microtime());

//var_dump($result);

//$dpExecStartTimes = microtime();
//var_dump($dpExecStartTimes);

//$time = explode( ' ', $dpExecStartTimes);
//var_dump($time);

$dpExecStartTime = microtime(true);
//var_dump($dpExecStartTime);

//$time2 = explode( '.', $dpExecStartTime);
//var_dump($time2);

//$date_time= date('Y-m-d H:i:s', microtime(true));
//var_dump($date_time);
//$xx = xxx();
define ( '_DPEXEC_', 1 );
//$xx = XXX();
// ini_set('log_errors', 1);
// ini_set('error_log', '/share/log/php.log');

// ini_set('display_errors','1');
// error_reporting(E_ALL);

 //var_dump( ini_get('log_errors') ) ;
//echo ini_get('error_log');

// $xxx->do(); ;

// exit();


require_once dirname ( __FILE__ ) . './../private/daophp.php';

// require_once DP_LIB_DAOPHP_CORE_DIR .'DaoPHP.php' ;
use daophp\core\DaoPHP;
use daophp\core\Debug;
use daophp\core\I18n;
use daophp\core\Controller;
use daophp\core\InvalidTaskException ;
use daophp\view\ViewFileNotFoundException ;
use daophp\core\ClassNotFoundException;
use daophp\responser\Responser ;

//DaoPHP::clearClassStructXmlFile( DP_LIB_DAOPHP_DIR ) ;
//echo 'here before';



//assert(false);


//$xx = XXX();

$daophp = DaoPHP::getInstance ();


//die() ;
//$daophp->registerPluge( )
/**
 * Init the whole site's request information
 */


try {
	
	
	//var_dump($__DP_CONFIG);
	
	$initArray = array(
		'dp_start_time' => $dpExecStartTime , 
	);	
	
	$daophp->init ($initArray);	
	
	$daophp->exec ();
	DaoPHP::exitSite();
	
} catch( Exception $e ) {	

	if( DP_EXEC_MODE == EXEC_MODE_CLI ) {
		echo $e->getMessage() . "\n";
		echo "file: ". $e->getFile() . ':<'+ $e->getLine() +'>' ;
		echo $e->getTraceAsString(). "\n";
		
	} else {
	    $responser = $daophp->getResponser() ;
	    header("HTTP/1.1 500 Service Unavailable");
	     
		if( $responser->getType() == Responser::TYPE_JSON ) {
			header('content-type: application/javascript; charset=' . $responser->getCharset() );
			
			$result = array() ;
			$result['code'] = '500';
			
			
			$str  = '';
			$str .= 'message: '. $e->getMessage() ."\n";
			$str .= $e->getTraceAsString() ;
			
			$str .= 'requestInfo: '.print_r( $_REQUEST, true );
			
			$result['message'] = $str ;	
			
			echo json_encode( $result );
		} else {		
		
			$str = '<pre>' ;
			$str .= 'message: '. $e->getMessage() ."\n";
			$str .= $e->getTraceAsString() ;
			
			$str .= 'requestInfo: '.print_r( $_REQUEST, true );
			$str .= '</pre>' ;
			
			echo $str ;
		}
	}
}
?>