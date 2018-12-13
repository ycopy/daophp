<?php

namespace daophp\view;


class ViewTemplateEmptyException extends \Exception {
    public function __construct() {
        
        $msg = <<< EOM
view template name empty
EOM;
        parent::__construct( $msg );
    }
}
?>