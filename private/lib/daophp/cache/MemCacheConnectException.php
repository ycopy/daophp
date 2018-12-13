<?php

namespace daophp\cache;


class MemCacheConnectException extends \Exception {
	public function __construct( $host, $port )
	{
		$msg = <<<EOM
connect to memcache {$host}:{$port} failed
EOM;
	parent::__construct( $msg );
	}
}
?>