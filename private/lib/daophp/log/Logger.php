<?php

namespace daophp\log;



interface Logger {
	
	const INFO      = 0x01;
	const WARN 		= 0x02;
	const ERROR		= 0x04;
	const CORE		= 0x08;
	
	
	public function addLogFlag( $flag );
	public function removeLogFlag($flag);
	
	
	public function log( $log, $level );
	
	public function flush();
}