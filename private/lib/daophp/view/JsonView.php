<?php

namespace daophp\view;

use daophp\view\AbstractView;

class JsonView extends AbstractView {    
    public function __construct() {}
    
    
    public function my_json_encode( $arr ) {
        return str_replace( '\\/', '/', json_encode($arr) );
        
        //		$str = "{";
        //		$shouldAppendComma = false;
        //
        //		foreach( $arr as $key => $value ) {
        //
        //
        //			if($shouldAppendComma) {
        //				$str .= ',' ;
        //			}
        //			$shouldAppendComma = true;
        //
        //			$str .= "\"". $key . "\":";
        //
        //			if( is_numeric( $value )) {
        //				$str .= $value;
        //			} else if( is_array( $value) ) {
        //				$str .= $this->my_json_encode( $value );
        //			} else if( is_string( $value )) {
        //				$str .= "\"" . $value . "\"";
        //			} else if( $value === null ){
        //				$str .= 'null' ;
        //			} else {
        //				trigger_error('invalid parameter found for a josn\'s value', E_USER_NOTICE );
        //			}
        //		}
        //
        //		$str .= "}";
        //
        //		return $str;
    }
    
    private $_useMyJsonEncode = false;
    public function useMyJsonEncode() {
        $this->_useMyJsonEncode = true;
    }
    
    
    public function render() {
        if( $this->_useMyJsonEncode ) {
              return $this->my_json_encode( $this->vars);            
        }
        return json_encode($this->vars);        
    }
}