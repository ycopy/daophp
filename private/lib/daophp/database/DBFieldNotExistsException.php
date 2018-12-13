<?php
namespace daophp\database ;

class DBFieldNotExistsException extends DBException {
    public function __construct( $field, $table ) {
        $msg = <<<EOM
field not exists : {$field} in {$table}
EOM;
        parent::__construct ( $field );
    }
}