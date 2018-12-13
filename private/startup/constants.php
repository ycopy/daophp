<?php

/**
 * ***********************************************
 *
 * DaoPHP - the PHP Web Framework
 * Author: cpingg@gmail.com
 * Copyright (c): 2008-2010 DaoPHP Group, all rights reserved
 * Version: 1.0.0
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * You may contact the author of DaoPHP by e-mail at:
 * cpingg@gmail.com
 *
 * The latest version of DaoPHP can be obtained from:
 * https://cp-daophp.googlecode.com/svn/trunk/
 *
 * ***********************************************
 */
/**
 * -------------------SYS CONFIGURE-----------------------------
 */
define('_ZG_FLAG_', true);

define('DP_TIME_ZONE', 'Asia/Shanghai'); // GMT 8 Asia/Shanghai
define('DP_SECOND_TIME', 1);
define('DP_MINUTE_TIME', 60);
define('DP_HOUR_TIME', 60 * 60);
define('DP_DAY_TIME', 24 * 60 * 60);
define('DP_WEEK_TIME', 7 * 24 * 60 * 60);
define('DP_MONTH_TIME', 30 * 24 * 60 * 60);

define('DP_OS_WIN', 'WIN');
define('DP_OS_NOT_WIN', 'NOT_WIN');
define('DP_OS', stripos(PHP_OS, 'LINUX') !== FALSE ? DP_OS_NOT_WIN : DP_OS_WIN);
if (!defined('DP_CLASS_EXTENSION_NAME')) {
    define('DP_CLASS_EXTENSION_NAME', '.php');
}

define('DP_LANG', 'en-US');
define('DP_CACHE_TYPE', NULL);

/**
 * ---------------------URL ROUNTER CONFIGURE----------------------------
 */
define('DP_REQUEST_MODULE_KEY', 'module');
define('DP_REQUEST_CONTROLLER_KEY', 'controller');
define('DP_REQUEST_ACTION_KEY', 'action');
define('DP_REQUEST_RESPONSE_TYPE_KEY', 'response_type');

define('DP_DEFAULT_MODULE', 'default_module');
define('DP_DEFAULT_CONTROLLER', 'index');
define('DP_DEFAULT_ACTION', 'index');

/**
 * ---------------------DIR CONFIGURE----------------------------
 */
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR); // DIRECTORY_SEPARATOR is a php intenal constant
}

if (!defined('DP_ROOT_DIR')) {
    $root_path = substr(__DIR__, 0, strlen(__DIR__) - 7);
    define('DP_ROOT_DIR', $root_path);
}

define('DP_WWWROOT_DIR', DP_ROOT_DIR . 'wwwroot' . DS);
define('DP_CLIROOT_DIR', DP_ROOT_DIR . 'cliroot' . DS);
define('DP_BOOTSTRAP_DIR', DP_ROOT_DIR . 'bootstrap' . DS);
define('DP_CLIROOT_CENTOS_DIR', DP_CLIROOT_DIR . 'centos' . DS);
define('DP_CLIROOT_WINDOWS_DIR', DP_CLIROOT_DIR . 'windows' . DS);

if (DP_OS == 'NONE_WIN') {
    define('DP_CLIROOT_REAL_DIR', DP_CLIROOT_CENTOS_DIR);
} else {
    define('DP_CLIROOT_REAL_DIR', DP_CLIROOT_WINDOWS_DIR);
}

// for system i18n
define('DP_I18N_DIR', DP_ROOT_DIR . 'i18n' . DS);

// cpingg@gmail.com add 2010.8.31
define('DP_TMP_DIR', DP_ROOT_DIR . 'tmp' . DS);
define('DP_LOG_DIR', DP_TMP_DIR . 'cgi_log' . DS);
define('DP_CLI_LOG_DIR', DP_TMP_DIR . 'cli_log' . DS);

define('DP_LIB_DIR', DP_ROOT_DIR . 'lib' . DS);
define('DP_LIB_DAOPHP_DIR', DP_LIB_DIR . 'daophp' . DS);

define('DP_LIB_DAOPHP_CACHE_DIR', DP_LIB_DAOPHP_DIR . 'cache' . DS);
define('DP_LIB_DAOPHP_FILE_DIR', DP_LIB_DAOPHP_DIR . 'file' . DS);
define('DP_LIB_DAOPHP_DATABASE_DIR', DP_LIB_DAOPHP_DIR . 'database' . DS);
define('DP_LIB_DAOPHP_CORE_DIR', DP_LIB_DAOPHP_DIR . 'core' . DS);
define('DP_LIB_DAOPHP_MAILER_DIR', DP_LIB_DAOPHP_DIR . 'mailer' . DS);
define('DP_LIB_DAOPHP_VIEW_DIR', DP_LIB_DAOPHP_DIR . 'view' . DS);
define('DP_LIB_DAOPHP_PUB_INTERFACE_DIR', DP_LIB_DAOPHP_CORE_DIR . 'interface' . DS);
define('DP_LIB_DAOPHP_REQUEST_DIR', DP_LIB_DAOPHP_DIR . 'request' . DS);
define('DP_LIB_DAOPHP_RESPONSER_DIR', DP_LIB_DAOPHP_DIR . 'responser' . DS);
define('DP_MODULES_DIR', DP_ROOT_DIR . 'modules' . DS);

/**
 * set the js/css is or not cached
 * default TRUE
 * FALSE in development environment
 * TRUE in product environment
 */
define('DP_WEB_CACHE', TRUE);

/**
 * set ajax request max time(microseconds)銆�
 * default 6000 microseconds
 */

define('DB_HOST', '127.0.0.1');
define('DB_USER_NAME', 'user');
define('DB_USER_PASS', 'password');
define('DB_NAME', 'dbname');

define('DB_TEST_HOST', '127.0.0.1');
define('DB_TEST_USER_NAME', 'user');
define('DB_TEST_USER_PASS', 'password');
define('DB_TEST_NAME', 'dbname');


// cpingg@gmail.com 2010.8.29 for cli mode
define('EXEC_MODE_CLI', 'CLI');
define('EXEC_MODE_CGI', 'CGI');

define("DP_EXEC_MODE", stripos(php_sapi_name(), 'cli') !== FALSE ? EXEC_MODE_CLI : EXEC_MODE_CGI);

if (DP_EXEC_MODE == EXEC_MODE_CLI) {
    define('DP_LT', '<');
    define('DP_GT', '>');
    define('DP_NBSP', ' ');
    define('DP_NEW_LINE', "\n");
    define("DP_TAP", "\t");
} else {
    define('DP_LT', '&lt;');
    define('DP_GT', '&gt;');
    define('DP_NBSP', '&nbsp;');
    define('DP_NEW_LINE', "<br />");
    define("DP_TAP", "&nbsp;&nbsp;&nbsp;&nbsp;");
}

/**
 * ---------------------SESSION CONFIGURE----------------------------
 */
define('DP_SESSION_AUTO_START', DP_EXEC_MODE !== EXEC_MODE_CLI); // could be yes, no
define('DP_SESSION_LIFE_TIME', 60 * 60 * 2); // Session life time 120mins
define('DP_SESSION_CACHE_TIME', 60 * 2); // Session cache time 60mins
define('DP_SESSION_PREFIX', 'daophp_');

/**
 * ---------------------SERVER CONFIGURE----------------------------
 */
// cli do not has this field
if (isset($_SERVER['SERVER_ADDR']) && !empty($_SERVER['SERVER_ADDR'])) {
    define('DP_SERVER_ADDR', $_SERVER['SERVER_ADDR']);
} else {
    define('DP_SERVER_ADDR', DP_getServerAddr());
}

// cli do not has this field
if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
    
    define('DP_HTTP_HOST', $_SERVER['HTTP_HOST']);
    if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
        define('DP_HOST', 'https://' . $_SERVER['HTTP_HOST']);
    } else {
        
        if( $_SERVER['SERVER_PORT'] == 80) {
            define('DP_HOST', 'http://' . $_SERVER['HTTP_HOST']);
        } else {            
            define('DP_HOST', 'http://'. $_SERVER['HTTP_HOST'] . ':'. $_SERVER['SERVER_PORT']);
        }
    }
} else {
    
    if( DP_EXEC_MODE == EXEC_MODE_CGI ) {        
        die('invalid request');
    }
    
    define('DP_HOST', DP_getHostName());
}

// cli do not has this field
if (isset($_SERVER['SERVER_NAME']) && !empty($_SERVER['SERVER_NAME'])) {
    define('DP_SERVER_NAME', $_SERVER['SERVER_NAME']);
} else {
    define('DP_SERVER_NAME', DP_getHostName());
}

function DP_getServerAddr()
{
    if (isset($_SERVER["SERVER_ADDR"])) {
        return $_SERVER["SERVER_ADDR"];
    } else {
        // Running CLI
        if (stripos(PHP_OS, 'WIN') !== FALSE) {
            // Rather hacky way to handle windows servers
            exec('ipconfig', $catch);
            foreach ($catch as $line) {
                // echo $line;
                if (preg_match('/IPv4/i', $line)) {
                    preg_match('/IPv4.*?:\s*([\d\.]+)\s*/i', $line, $match);
                    return $match[1];
                } else if (preg_match('/IP Address.*?:\s*([\d\.]+)/i', $line, $match)) {
                    return $match[1];
                }
            }
        } else {
            $ifconfig = shell_exec('/sbin/ifconfig eth0');
            preg_match('/inet\s+addr:([\d\.]+)\s+/', $ifconfig, $match);

            if (!isset($match[1]) || empty($match[1])) {
                // try wlan0
                $ifconfig = shell_exec('/sbin/ifconfig wlan0');
                preg_match('/inet\s+addr:([\d\.]+)\s+/', $ifconfig, $match);
            }
            // Debug::addLog('RUN IP is '. $match[1] );
            return $match[1];
        }
    }
}

function DP_getHostName()
{
    if (stripos(PHP_OS, 'WIN') !== FALSE) {
        return 'W_LOCAL';
    } else {
        $sh = 'hostname';
        return trim(shell_exec($sh));
    }
}
