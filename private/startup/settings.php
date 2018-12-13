<?php
/**
 * show error
 */
@ini_set('display_errors', 1);
error_reporting(E_ALL);

ini_set('memory_limit','512M');


if (defined("DP_TIME_ZONE")) {
    date_default_timezone_set(DP_TIME_ZONE);
}

/**
 * adjust some php env in php script
 */
if (!mb_internal_encoding("UTF-8")) {
    exit('mb_internal_encoding("UTF-8") FAILED');
}

/**
 * set session
 */
session_cache_expire(DP_SESSION_CACHE_TIME);
session_set_cookie_params(DP_SESSION_LIFE_TIME);
if (DP_SESSION_AUTO_START === TRUE) {
    if (!ini_get('session.auto_start')) {
        session_start();
    }
}

/**
 * set the site base url with a trailing slash
 */
$urls = explode('/', @$_SERVER['REQUEST_URI']);
$requestUri = '';
if (empty($urls[1])) {
    $requestUri = '/';
} else {
    array_pop($urls);
    $requestUri = implode('/', $urls) . '/';
}
define('DP_BASE_URL', DP_HOST . $requestUri);