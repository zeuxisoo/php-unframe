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

define('CACHE_ROOT', APP_ROOT.'/cache');
define('VIEW_ROOT', APP_ROOT.'/view');
define('LOCALE_ROOT', APP_ROOT.'/locale');

require_once KERNEL_ROOT."/config.php";
require_once KERNEL_ROOT."/common.php";

$environment_path = ENV_ROOT.'/'.strtolower($config['init']['environment']).'.php';
if (isset($config['init']['environment']) === true && file_exists($environment_path) === true) {
	require_once $environment_path;
}
unset($environment_path);

define('SITE_URL', $config['init']['site_url']);
define('STATIC_URL', SITE_URL.'/static');
define('ATTACHMENT_ROOT', APP_ROOT.'/'.$config['init']['attachment_folder']);
define('ATTACHMENT_URL', SITE_URL.'/'.$config['init']['attachment_folder']);
define('ADMIN_ROOT', APP_ROOT.'/'.$config['init']['admin_folder']);
define('ADMIN_URL', SITE_URL.'/'.$config['init']['admin_folder']);
define('PLUGIN_ROOT', APP_ROOT.'/plugin');

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