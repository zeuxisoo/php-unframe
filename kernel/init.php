<?php
error_reporting(E_ALL);
header("content-Type: text/html; charset=UTF-8");

if (version_compare(PHP_VERSION, '6.0.0', '<') === true) {
	@set_magic_quotes_runtime(0);
}

define('IN_APP', true);
define('KERNEL_ROOT', str_replace('\\', '/', dirname(__FILE__)));
define('WWW_ROOT', dirname(KERNEL_ROOT));
define('APP_ROOT', dirname(KERNEL_ROOT));

define('INITIAL_ROOT', KERNEL_ROOT.'/initializer');
define('LIBRARY_ROOT', KERNEL_ROOT.'/library');
define('ADAPTER_ROOT', KERNEL_ROOT.'/adapter');
define('ENV_ROOT',     KERNEL_ROOT.'/environment');

if (defined('CACHE_ROOT') === false) define('CACHE_ROOT', APP_ROOT.'/cache');
if (defined('VIEW_ROOT') === false) define('VIEW_ROOT', APP_ROOT.'/view');
if (defined('LANGUAGE_ROOT') === false) define('LANGUAGE_ROOT', APP_ROOT.'/language');

require_once KERNEL_ROOT."/config.php";
require_once KERNEL_ROOT."/common.php";

$environment_file_paths = glob(CACHE_ROOT."/*.env");
if (is_array($environment_file_paths) === true) {
	$environment_file_path = array_shift($environment_file_paths);
	$real_environment_path = ENV_ROOT.'/'.str_replace(".env", ".php", basename($environment_file_path));
	if (file_exists($real_environment_path) === true && is_file($real_environment_path)) {
		require_once $real_environment_path;
	}
	unset($environment_file_path, $real_environment_path);
}
unset($environment_file_paths);

if (defined('SITE_URL') === false) define('SITE_URL', $config['init']['site_url']);
if (defined('STATIC_URL') === false) define('STATIC_URL', SITE_URL.'/static');
if (defined('ATTACHMENT_ROOT') === false) define('ATTACHMENT_ROOT', APP_ROOT.'/'.$config['init']['attachment_folder']);
if (defined('ATTACHMENT_URL') === false) define('ATTACHMENT_URL', SITE_URL.'/'.$config['init']['attachment_folder']);
if (defined('ADMIN_ROOT') === false) define('ADMIN_ROOT', APP_ROOT.'/'.$config['init']['admin_folder']);
if (defined('ADMIN_URL') === false) define('ADMIN_URL', SITE_URL.'/'.$config['init']['admin_folder']);
if (defined('PLUGIN_ROOT') === false) define('PLUGIN_ROOT', APP_ROOT.'/plugin');

foreach(array('_COOKIE', '_POST', '_GET', '_FILES', '_REQUEST') as $_request) {
	if (in_array($_request, array('_COOKIE', '_POST', '_GET')) === true) {
	    foreach($$_request as $_key => $_value) {
	        $_key{0} != '_' && $$_key = Secure::add_slash($_value);
	    }
	}

	$$_request = Secure::add_slash($$_request);
}
unset($_request, $_key, $_value, $_request);

if (function_exists("date_default_timezone_set")) {
	date_default_timezone_set($config['init']['timezone']);
}

if ($config['init']['header_no_cache'] === true) {
	header("Cache-Control: no-cache, must-revalidate, max-age=0");
	header("Expires: 0");
	header("Pragma:	no-cache");
}

if ($config['init']['show_php_error'] === false) {
	error_reporting(E_ALL & ~E_NOTICE);
}

Session::init();

foreach(glob(INITIAL_ROOT."/*.php") as $initializer) {
	if (file_exists($initializer) === true) {
		require_once $initializer;
	}
}
unset($initializer);
?>