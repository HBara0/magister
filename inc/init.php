<?php

/*
 *
 * WARNING! any change here can affect other parts of the website
 *
 */
if (strpos(strtolower($_SERVER['PHP_SELF']), 'init.php') !== false) {
    die('Not allowed.');
}
if (!defined('ROOT')) {
    define('ROOT', dirname(dirname(__FILE__)) . '/');
}

define('INC_ROOT', 'inc/');

error_reporting(E_ALL & ~E_NOTICE);
require_once ROOT . INC_ROOT . 'config.php';
/*
 * Vital definitions
 * Tprefix			The DB table prefix. Directly loaded from inc/config.php
 * DB_NAME			The DB name. Directly loaded from inc/config.php
 * DOMAIN			High domain where the files are located
 * COOKIE_DOMAIN 	.domainname.prefix
 * COOKIE_PATH		Path to the application
 * COOKIE_PREFIX	A prefix for the cookies. Needed when running multiple Magister copies on the same server
 */
define('Tprefix', $table_prefix);
define('DB_NAME', $database);
define('DOMAIN', 'http://127.0.0.1/magister');
define('COOKIE_DOMAIN', '127.0.0.1');
define('COOKIE_PATH', '/magister/');
define('COOKIE_PREFIX', 'magisterproduciton_');
define('ADMIN_DIR', 'manage');
define("TEMPLATES_SYSTEM", 'FILE');
define("TIME_NOW", time());

$config['admindir'] = ADMIN_DIR;

$settings['rootdir'] = DOMAIN;
$settings['cookie_prefix'] = COOKIE_PREFIX;

function __autoload($className) {
    require_once ROOT . INC_ROOT . $className . '_class.php';
}

require_once ROOT . INC_ROOT . 'functions.php';

$errorhandler = new ErrorHandler();
$timer = new Timer();
$core = new Core();

if (file_exists(ROOT . INC_ROOT . 'settings.php')) {
    require_once ROOT . INC_ROOT . 'settings.php';
}

/* if(!file_exists(ROOT.INC_ROOT."settings.php") || !$settings)
  {
  //rebuild_settings();
  } */

$core->settings = &$settings;
$core->parse_cookies();

$db = new MySQLiConnection($config['database']['database'], $config['database']['hostname'], $config['database']['username'], $config['database']['password']);

$template = new Templates();
$session = new Sessions();
$log = new Log();
$cache = new Cache();
?>