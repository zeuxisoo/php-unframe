<?php
if (defined('IN_APP') === false) exit('Access Dead');

spl_autoload_register("auto_load");

function auto_load($class_name) {
	global $config;

	foreach(array('core', 'helper', 'library', 'adapter/database', 'adapter/cache') as $folder) {
		$file_path = KERNEL_ROOT.'/'.$folder.'/'.strtolower($class_name).'.php';

		if (file_exists($file_path) === true && is_file($file_path) === true) {
			require_once $file_path;
		}
	}

	if (is_array($config['init']['auto_load_folders']) === true && empty($config['init']['auto_load_folders']) === false) {
		$directories = $config['init']['auto_load_folders'];

		while (sizeof($directories)) {
			$directory = array_pop($directories);

			foreach(glob($directory."/*") as $file_path) {
				if (is_dir($file_path) === true) {
					array_push($directories, $file_path);
				}elseif (is_file($file_path) === true && preg_match("/\.(php|inc)$/", $file_path) == true) {
					require_once $file_path;
				}
			}
		}
	}
}

function import($path_string = ""){
	$import_path = WWW_ROOT.'/'.str_replace(".", "/", $path_string);

	if(substr($import_path,-1) != "*") {
		$import_path .= ".php";
	}

	foreach(glob($import_path) as $file_path){
		if(is_dir($file_path) === true) {
			$file_path = str_replace(WWW_ROOT, '', $file_path);
			import($file_path."/*");
		}

		if (substr($file_path,-4) != ".php") {
			continue;
		}

		require_once($file_path);
	}
}

function p() {
	echo "<pre>";
	foreach(func_get_args() as $argument) {
		print_r($argument); echo "\n";
	}
	echo "</pre>";
}

// Alias method
function t() {
	return call_user_func_array(array("Language", "translate"), func_get_args());
}
