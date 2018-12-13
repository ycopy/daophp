<?php
namespace daophp\log ;

interface Logable {
	public function setLoggerManager( LoggerManager $loggerManager = null ) ;
	public function log($log, $level) ;
}