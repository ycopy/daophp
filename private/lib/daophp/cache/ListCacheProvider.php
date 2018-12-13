<?php

namespace daophp\cache;


interface ListCacheProvider {
	public function push($key, $value,$flag=0 );
	public function pop($key, $maxCount = 10 ,$flag=0);
	public function size($key,$flag);
}

?>