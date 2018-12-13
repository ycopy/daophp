<?php

namespace daophp\acl;

Abstract class AbstractAclController {
	abstract public function isAllowed( $identity, $module, $controller,$action ) ;
	
	abstract public function isDenied( $identity, $module, $controller, $action );
	
}