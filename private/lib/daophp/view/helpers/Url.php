<?php


use daophp\core\Helper;

class Url implements Helper {
    /**
     * {@inheritDoc}
     * @see \daophp\core\Helper::call()
     */
    public function call()
    {
        $args = func_get_args();        
        return call_user_func_array('Url', $args );   
    }
}