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

namespace daophp\core;

use daophp\core\object\SingletonObject ;
class UserAgent extends SingletonObject {
	

	private $uaServerInfo = array();
	
	/**
	 * hold the ua properties 
	 * @var stdClass
	 */
	private $uaStd = null;
	
	public function __construct( $options ) {
		if ( isset ( $options['server'] ) 
			&& is_array( $options['server'] 
			&& count( $options['server'] )) ) {
				$this->$uaServerInfo = $options['server'];
			}
			
			$this->loadUaStd();
	}
	
	public function getUaStd() {
		if( $this->uaStd === null) {
			$this->loadUaStd();
		}
		return $this->uaStd;
	}
	
	private function loadUaStd() {
		$wurflManager = LibManager::loadLibManager( 'wurfl' ) ;
		$uaManager = $wurflManager->getUaManager();
		
		$this->uaStd = $uaManager->getUa( $this->uaServerInfo['server']['HTTP_USER_AGENT'] );
	}
}
?>