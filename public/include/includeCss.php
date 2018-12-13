<?php
    /* load css style */
    if (!empty($this -> css)) {
        $t = substr(strval(time()), 6);
        if (is_array($this -> css)) {
            foreach ($this -> css as $v) {
                if (DP_WEB_CACHE) {
                    echo "<link rel=\"stylesheet\" href=\"statics/css/{$v}\" type=\"text/css\" />";
                } else {
                    echo "<link rel=\"stylesheet\" href=\"statics/css/{$v}?t={$t}\" type=\"text/css\" />";
                }
            }
        } else {
            if (DP_WEB_CACHE) {
                echo "<link rel=\"stylesheet\" href=\"statics/css/{$this -> css}\" type=\"text/css\" />";
            } else {
                echo "<link rel=\"stylesheet\" href=\"statics/css/{$this -> css}?t={$t}\" type=\"text/css\" />";
            }
        }
    }
