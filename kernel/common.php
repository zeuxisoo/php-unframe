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

function import($path_string = ""){ 
	$current_path = getcwd(); 
	$import_path = $current_path.'/'.str_replace(".", "/", $path_string);

	if(substr($import_path,-1) != "*") {
		$import_path .= ".php";
	}

	foreach(glob($import_path) as $file_path){
		if(is_dir($file_path) === true) {
			$file_path = str_replace($current_path, '', $file_path);
			import($file_path."/*");
		}

		if (substr($file_path,-4) != ".php") {
			continue;
		}

		require_once($file_path);
	} 
} 

// Alias method
function t() {
	return call_user_func_array(array("Locale", "translate"), func_get_args());
}
?>