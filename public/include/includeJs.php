<?php
    /*
     * global js settings
     */
    echo '<script type="text/javascript">var ajaxTimeout = ' . DP_AJAX_TIMEOUT . ';$.ajaxSetup({"timeout":' . DP_AJAX_TIMEOUT . '});</script>';

    /* load js script */
    if (!empty($this -> js)) {
        $t = substr(strval(time()), 6);
        if (is_array($this -> js)) {
            foreach ($this -> js as $v) {
                if (DP_WEB_CACHE) {
                    echo "<script type=\"text/javascript\" src=\"statics/js/{$v}\"></script>";
                } else {
                    echo "<script type=\"text/javascript\" src=\"statics/js/{$v}?t={$t}\"></script>";
                }
            }
        } else {
            if (DP_WEB_CACHE) {
                echo "<script type=\"text/javascript\" src=\"statics/js/{$this -> js}\"></script>";
            } else {
                echo "<script type=\"text/javascript\" src=\"statics/js/{$this -> js}?t={$t}\"></script>";
            }
        }
    }
