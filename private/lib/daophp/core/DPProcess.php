<?php

/*************************************************

DaoPHP - the PHP Web Framewrok
Author: cpingg@gmail.com
Copyright (c): 2008-2010 New Digital Group, all rights reserved
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

namespace daophp\core;
use daophp\core\object\DPObject ;

/**
 * usage
 * 
  	$options = array(
		'CMD' => 'CMD LINE',
		DPProcess::FP_STDOUT => array('STD OUT FILE','a'),
		DPProcess::FP_STDERR => array('STD ERROR FILE','a')
	);
	$DPProcessHolder = new DPProcess($options) ;
	$DPProcessHolder->start() ;
 * Enter description here ...
 * @author ping.cao
 *
 */

class DPProcess extends DPObject {

	const FP_STDIN 	= 0;
	const FP_STDOUT = 1;
	const FP_STDERR = 2;

	//default config
	private $descriptorspecConfig = array(
		self::FP_STDIN 	=> array("pipe", "r"),   // stdin
		self::FP_STDOUT => array("pipe", "w"),  // stdout
		self::FP_STDERR => array("pipe", "w")   // stderr
	);

	private $pipes = null ;

	private $processResource = null ;
	private $CMD = '';

	private $exitCode = '';
	private $terminateCode = '';

	private $status = array();

	public function __construct( $options ) {

		parent::__construct();

		if( isset( $options['CMD']) && !empty($options['CMD']) ) {
			$this->CMD = $options['CMD'] ;
		}

		if( isset( $options[self::FP_STDIN]) && is_array( $options[self::FP_STDIN]) && count( $options[self::FP_STDIN] ) ) {
			$this->descriptorspecConfig[self::FP_STDIN] = $options[self::FP_STDIN] ;

			if( $this->descriptorspecConfig[self::FP_STDIN][0] == 'file' ) {
				$dir = dirname( $this->descriptorspecConfig[self::FP_STDIN][1]) ;

				if( !file_exists( $dir ) ) {
					if(!mkdir($dir, 0700, true ) ) {
						throw new DirMakeException($dir) ;
					}
				}
			}
		}

		if( isset( $options[self::FP_STDOUT]) && is_array( $options[self::FP_STDOUT]) && count($options[self::FP_STDOUT] ) ) {
			$this->descriptorspecConfig[self::FP_STDOUT] = $options[self::FP_STDOUT] ;

			if( $this->descriptorspecConfig[self::FP_STDOUT][0] == 'file' ) {
				$dir = dirname( $this->descriptorspecConfig[self::FP_STDOUT][1]) ;
				if( !file_exists( $dir ) ) {
					if(!mkdir($dir, 0700, true ) ) {
						throw new DirMakeException($dir) ;
					}
				}
			}
		}

		if( isset( $options[self::FP_STDERR]) && is_array( $options[self::FP_STDERR] ) && count( $options[self::FP_STDERR] ) ) {
			$this->descriptorspecConfig[self::FP_STDERR] = $options[self::FP_STDERR] ;

			if( $this->descriptorspecConfig[self::FP_STDERR][0] == 'file' ) {
				$dir = dirname( $this->descriptorspecConfig[self::FP_STDERR][1]) ;
				if( !file_exists( $dir ) ) {
					if(!mkdir($dir, 0700, true ) ) {
						throw new DirMakeException($dir) ;
					}
				}
			}
		}


		$info = 'CMD: '. $this->CMD . DP_NEW_LINE ;
		$info .= 'STDIN: '. $this->descriptorspecConfig[self::FP_STDIN][0]. ':'. $this->descriptorspecConfig[self::FP_STDIN][1] .DP_NEW_LINE ;
		$info .= 'STDOUT: '.$this->descriptorspecConfig[self::FP_STDOUT][0]. ':' .$this->descriptorspecConfig[self::FP_STDOUT][1] .DP_NEW_LINE ;
		$info .= 'STDERR: '. $this->descriptorspecConfig[self::FP_STDOUT][0]. ':' .$this->descriptorspecConfig[self::FP_STDERR][1] .DP_NEW_LINE;

		Debug::addLog( $this->getDPObjectKey(). ' DPProcess init OK, '.DP_NEW_LINE. $info  ) ;
	}

	public function start() {

		if( empty( $this->CMD ) ) {
			throw new \Exception('INVALID DPProcess Start Paramster, NULL CMD FOUND') ;
		}

		$this->processResource = proc_open( $this->CMD, $this->descriptorspecConfig , $this->pipes ) ;

		if( !is_resource( $this->processResource ) ) {
			throw new \Exception('proc_open() error, params, '.DP_NEW_LINE. 'CMD: '. $this->CMD .DP_NEW_LINE. Common::pr($this->descriptorspecConfig, true, 'configInfo' ) ) ;
		}

		$this->status = proc_get_status( $this->processResource );

		Debug::addLog('PID: '.$this->status['pid'] .' DPProcess start OK, PPID: '. getmypid() ) ;

	}

	public function terminate() {
		if( is_resource($this->processResource) ) {
			$this->terminateCode = proc_terminate($this->processResource) ;
		}

		Debug::addLog('DPProcess Terminate OK ' ) ;

	}

	public function stop(){
		if( is_resource($this->processResource) ) {
			$this->exitCode = proc_close($this->processResource) ;
			Debug::addLog('PID: '.$this->status['pid'] .' DPProcess Stop OK, PPID: '. getmypid() ) ;

		}
	}


	public function __destruct(){
		$this->stop() ;
	}
}

?>