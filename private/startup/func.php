<?php

/*
function dp_assert( $assertion, $throwable=null ) {
    if( is_a($throwable, '\Exception') ) {        
        return assert( $assertion, $throwable);
    }    
    return assert($assertion);
}



function test_backtrace() {    
    $traceArray = debug_backtrace(true);    
    var_dump( $traceArray );
}
test_backtrace();

*/


function Url( $script_name, array $params = array()) {    
    assert( !empty( $script_name) );   
        
    if( !substr($script_name, 0,1) == '/') {         
        $script_name = '/' . $script_name;
    }

    if( count( $params) == 0 ) {        
        return $script_name;
    }    
    
    $key_pair = array();
    foreach ( $params as $key => $value) {
        array_push($key_pair, $key . '=' . $value) ;
    }
    
    if( strpos($script_name, '?') === false ) {        
        $script_name = $script_name . '?' ;
    } else {
        $script_name = $script_name . '&';
    }
    
    return $script_name . implode('&', $key_pair);
}

function HostUrl( $script_name, array $params = array()) {
    return DP_HOST . Url($script_name, $params );
}