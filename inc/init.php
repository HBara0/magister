<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Initialtions file
 * $id: init.php
 * Created: 	@zaher.reda		Feb 04, 2009 | 10:00 AM		
 * Last Update: @zaher.reda 	July 31, 2009 | 3:39 PM
 */
 
/* 
 *
 * WARNING! any change here can affect other parts of the website 
 *
 */
if(strpos(strtolower($_SERVER['PHP_SELF']), 'init.php') !== false) {
	die('Not allowed.');
}
if(!defined('ROOT'))
{
	define('ROOT', dirname(dirname(__FILE__)).'/');
}

define('INC_ROOT', 'inc/');

error_reporting(E_ALL & ~E_NOTICE);

require_once ROOT.INC_ROOT.'config.php';
/*
 * Vital definitions
 * Tprefix			The DB table prefix. Directly loaded from inc/config.php
 * DB_NAME			The DB name. Directly loaded from inc/config.php
 * DOMAIN			High domain where the files are located
 * COOKIE_DOMAIN 	.domainname.prefix
 * COOKIE_PATH		Path to the application
 * COOKIE_PREFIX	A prefix for the cookies. Needed when running multiple OCOS copies on the same server
 */
define('Tprefix', $config['database']['prefix']);
define('DB_NAME', $config['database']['database']);
define('DOMAIN', ''); // ex. http://localhost/web/ocos/
define('COOKIE_DOMAIN', ''); // ex. .localhost
define('COOKIE_PATH', ''); // ex: /web/ocos/
define('COOKIE_PREFIX', 'ocos_');
define('ADMIN_DIR', 'manage');
define("TIME_NOW", time());

$config['admindir'] = ADMIN_DIR;

$settings['rootdir'] = DOMAIN;
$settings['cookie_prefix'] = COOKIE_PREFIX;

function __autoload($className)  
{  
   require_once ROOT.INC_ROOT.$className.'_class.php';  
} 

require_once ROOT.INC_ROOT.'functions.php';

$errorhandler = new ErrorHandler();
$timer = new Timer();
$core = new Core();

if(file_exists(ROOT.INC_ROOT.'settings.php'))
{
	require_once ROOT.INC_ROOT.'settings.php';
}

/*if(!file_exists(ROOT.INC_ROOT."settings.php") || !$settings)
{
	//rebuild_settings();
}*/

$core->settings = &$settings;
$core->parse_cookies();

$db = new DBConnection($config['database']['database'], $config['database']['hostname'], $config['database']['username'], $config['database']['password']);

$template = new Templates();
$session = new Sessions();
$log = new Log();
?>