<?php


namespace daophp;

class AbstractBootstrap {

	private $_startuped = false;
	
	public function startup() {
	
		if( $this->_startuped ) {
			return ;
		}
		
		$reflection = new \ReflectionObject( $this );		
		$methods = $reflection->getMethods();
		
	
		foreach(  $methods as $method ) {
			
			$name = $method->getName() ;
			if( substr( $name, 0, 1) == '_' ) {
				$this->$name() ;
			}
		}
		
		$this->_startuped = true;
	}
	
}