<?php
if (defined('IN_APP') === false) exit('Access Dead');

spl_autoload_register("auto_load");

function auto_load($class_name) {
	foreach(array('core', 'helper', 'library', 'adapter/database', 'adapter/cache') as $folder) {
		$file_path = KERNEL_ROOT.'/'.$folder.'/'.strtolower($class_name).'.php';

		if (file_exists($file_path) === true) {
			require_once $file_path;
		}
	}
}
?>