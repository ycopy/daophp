<?php

namespace daophp\view;
use daophp\core\object\DPObject;

abstract class AbstractView extends DPObject {
    
    /**
     * render result
     * @var unknown_type
     */
    protected $vars = array();
    protected $result = '';
    protected $rendered = false;
    
    public function reset( $vars = array() ) {

       if( !is_array( $vars)) {
           throw new \InvalidArgumentException('args must be array');           
       }
        
       $this->vars = $vars;
       $this->result = ''; 
       $this->rendered = false ;
    }
    
   
    public function isRendered() {
        return $this->rendered === true;
    }
    
    public function assign( $key,$value) {
        return $this->__set($key,$value);
    }
    
    public function __set($key,$value) {
        
        $this->vars[$key] = $value;
        return $this;
    }
    
    public function __get( $key ) {
        if( isset( $this->vars[$key]) ) {
            if( $this->vars[$key] instanceof View ) {
                return $this->vars[$key]->render() ;
            } else {
                return $this->vars[$key] ;
            }
        }
        
        return null;
    }
    
    public function __isset($key) {
        return isset($this->vars[$key]);
    }
    
    
    public function __unset($key) {
        unset($this->vars[$key]) ;
    }
    
    public abstract function render();
}