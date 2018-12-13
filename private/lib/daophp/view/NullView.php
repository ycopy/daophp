<?php

namespace daophp\view;

use daophp\view\AbstractView;

class NullView extends AbstractView {         
    public function render() {
        return '';   
    }
}