<?php


namespace daophp\request ;
use daophp\core\object\DPObject ;

class CLIRequest extends DPObject implements Request {
	
	
	public function get( $index ) {
		return $this->getParam($index);
	}
	
	public function getParam( $index ) {
		return $_SERVER['argv'][$index+3] ;
	}
	
	
	public function getModule() {
		return $_SERVER['argv'][1];
	}
	
	public function getController() {
		return $_SERVER['argv'][2];
		
	}
	
	public function getAction() {
		return $_SERVER['argv'][3];
	}
}