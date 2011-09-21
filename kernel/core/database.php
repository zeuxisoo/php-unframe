<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Database {
	
	const CONFIG_IS_EMPTY		= -1;
	const NOT_FOUND_ADAPTER		= -2;
	const DATABASE_NAME_IS_EMPTY= -3;

	private static $prefix 		= "";
	private static $instance	= null;

	public static function init($settings) {
		if (empty($settings) === true) {
			self::error(self::CONFIG_IS_EMPTY);
		}elseif (file_exists(ADAPTER_ROOT.'/database/'.strtolower($settings['adapter']).'_adapter.php') === false) {
			self::error(self::NOT_FOUND_ADAPTER);
		}elseif (empty($settings['database']) === true) {
			self::error(self::DATABASE_NAME_IS_EMPTY);
		}else{
			if (empty($settings['host']) === true) {
				$settings['host'] = "localhost";
			}

			$adapter = $settings['adapter'].'_adapter';

			self::$instance = new $adapter($settings);
			self::$instance->set_debug($settings['debug']);

			return self::$instance;
		}
	}

	public static function instance() {
		return self::$instance;
	}

	private static function error($message) {
		switch($message) {
			case self::CONFIG_IS_EMPTY:
				$message = "Must provide config details";
				break;
			case self::NOT_FOUND_ADAPTER:
				$message = "Not found support adapter in adapter/database folder";
				break;
			case self::DATABASE_NAME_IS_EMPTY:
				$message = "Database name can not empty";
				break;
		}
		
		exit(sprintf("<p>[Database Core] %s</p>", $message));
	}

}
?>