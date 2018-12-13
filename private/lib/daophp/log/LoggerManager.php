<?php

namespace daophp\log;

use daophp\core\object\DPObject;


class LoggerManager extends DPObject {
	
	private $_loggers = array() ;
	
	public function log( $log, $level ) {
		
		foreach($this->_loggers as $logger ) {
			$logger->log($log, $level);
		}
		
	}
	
	public function flush() {
		foreach($this->_loggers as $logger ) {
			$logger->flush();
		}
	}
	
	public function addLogFlag( $flag ) {
		foreach($this->_loggers as $logger ) {
			$logger->addLogFlag($flag);
		}
	}
	
	public function removeLogFlag( $flag ) {
		foreach($this->_loggers as $logger ) {
			$logger->removeLogFlag($flag);
		}
	}
	
	
	
	public function registerLogger( $loggerName, Logger $logger) {
		$this->_loggers[$loggerName] = $logger ;
		return $this;
	}
	
	public function unregisterLogger( $loggerName ) {
		
		if( isset( $this->_loggers[$loggerName]) ) {
			unset($this->_loggers) ;
		}
		
	}
}