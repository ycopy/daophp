<?php

use daophp\core\Helper;
use daophp\core\DaoPHP;

class Post implements Helper {
    
    public function call() {        
        $args_num = func_num_args();        
        
        if($args_num == 1) {            
            return DaoPHP::getInstance()->getRequest()->getPostVar( func_get_arg(0) );            
        }
        
        //if( $args_num >= 2) {            
        //   return call_user_func_array( array( DaoPHP::getInstance()->getRequest(), 'setCookie' ), func_get_args() );
        //}
        
        throw new InvalidArgumentException('invalid args');
    }
    
}