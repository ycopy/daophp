<?php

namespace daophp\responser ;

use daophp\core\ClassNotFoundException ;

class ResponserNotFoundException extends ClassNotFoundException {
	public function __construct($responser) {
$msg =<<< EOM
Responser Not Found: {$responser}
EOM;

	parent::__construct( $responser, DP_LIB_DAOPHP_RESPONSER_DIR, $msg );
	}
}
?>