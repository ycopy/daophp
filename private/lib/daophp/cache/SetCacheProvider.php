<?php

namespace daophp\cache;

interface SetCacheProvider {
	public function sAdd($key, $value ,$flag=0 );
	public function sContains($key,$value ,$flag=0 ) ;
	public function sRemove($key, $value,$flag=0);
	public function sSize($key,$flag,$flag=0);
	public function sUnion($set_1, $set_2,$flag=0);
	public function sPop($key,$flag=0);
	public function sGetMembers($key,$flag=0);
}