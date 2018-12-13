<?php

namespace daophp\log;

use daophp\file\FileWriteException;

use daophp\file\FileOpenException;

use daophp\core\object\DPObject;

class FileLogger extends DPObject implements Logger {
	
	private $_fileHandle = null ;
	
	protected static $level = array(
			self::INFO	=> '[INFO]	',
			self::WARN	=> '[WARN]	',
			self::ERROR => '[ERROR]	',
			self::CORE	=> '[CORE]	'
	);
	
	private $_logFlag = 0;
	
	public function addLogFlag($flag) {
		$this->_logFlag |= $flag ;
		return $this;
	}
	
	public function removeLogFlag($flag) {
		$this->_logFlag &= ~$flag ;
		return $this;
	}
	
	private $_filePath = '';
	public function __construct( $filePath, $openMode = 'w+' ) {
		
		$this->_filePath = $filePath;
		if( !($this->_fileHandle = fopen( $filePath, $openMode ) ) ) {
			throw new FileOpenException($filePath) ;
		}
		
		parent::__construct() ;
	}
	
	public function log( $log, $level ) {
		
		if( ($this->_logFlag & $level) === 0 ) {
			return ;
		}
		
		$str_replaceSrc = array('&lt;','&gt;','&nbsp;', '<br />');
		$str_replaceTar = array('<','>',' ', "\n" );
		
		$log = str_replace($str_replaceSrc, $str_replaceTar, $log );		
		
		$time = microtime(true);
		
		$second_and_micro = explode('.', $time);
		$date = date('Y-m-d H:i:s', $second_and_micro[0]);
		
		$log_date = sprintf("%s.%-4s", $date, $second_and_micro[1]);		
		$log = $log_date. " ". self::$level[$level]. $log . "\n" ;
		
		if( fwrite($this->_fileHandle, $log ) === false ) {
			throw new FileWriteException( $this->_filePath ) ;
		}
	}
	
	public function flush() {
		fflush($this->_fileHandle);
	}
	
	public function __destruct() {
		
		if($this->_fileHandle) {
			fclose($this->_fileHandle) ;
		}
		
		parent::__destruct();
	}
}