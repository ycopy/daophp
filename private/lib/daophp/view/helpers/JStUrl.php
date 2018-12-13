<?php


include_once 'Url.php';    
class JStUrl extends Url {	
	
    /*
    public function call()
    {
        $args = func_get_args();
        
        if(count($args) == 0) {
            throw new InvalidArgumentException("invalid args for JSUrl->call");
        }
        
        $namespace= 'daophp';
        
        if( count($args) == 2) {
            $namespace= $args[1];
        }
        
        return  DaoPHP::getInstance()->getHostUrl() . $namespace .'/js/' . $args[0];
    }	
	*/
}