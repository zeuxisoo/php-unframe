<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Language {

	const COMPILE_ALL 	= 1;
	const COMPILE_LIST	= 2;

	const LANGUAGE_ROOT_NOT_FOUND = -1;

	private static $language_root 	= "";
	private static $language_name 	= "";
	private static $language_name_root= "";
	private static $access_string 	= "IN_APP";
	private static $cache_file_path = "";
	
	public static function init($settings) {
		self::$language_root 		= $settings['language_root'];
		self::$language_name 		= $settings['language_name'];
		self::$language_name_root = self::$language_root."/".self::$language_name;
		self::$cache_file_path 	= CACHE_ROOT."/language/".self::$language_name.".php";

		if (isset($settings['access_string']) === true) {
			self::$access_string = $settings['access_string'];
		}

		$cache_root = dirname(self::$cache_file_path);
		if (is_dir($cache_root) === false || file_exists($cache_root) === false) {
			mkdir($cache_root, 0777, true);
		}
		unset($cache_root);

		self::prepare();
	}

	public static function translate($string) {
		include self::$cache_file_path;

		$language_table = $cache[self::$language_name];
		$string = isset($language_table[$string]) === true && empty($language_table[$string]) === false ? $language_table[$string] : $string;

		// Format string if have extra arguments
		$arguments = func_get_args();
		$arguments[0] = $string;		// replace to matched string
		if (count($arguments) > 1) {
			if (is_array($arguments[1]) === true) {
				$string = call_user_func_array(array("self", "sprint_expand"), $arguments);
			}else{
				$string = call_user_func_array(function_exists("mb_sprintf") ? "mb_sprintf" : "sprintf", $arguments);
			}
		}

		return $string;
	}

	public static function clear() {
		@unlink(self::$cache_file_path);
	}

	private static function prepare() {
		$language_list = glob(self::$language_name_root."/*.php");

		if (is_dir(self::$language_name_root) === true) {
			// If language cache file not exists, collect all language file into one file
			if (file_exists(self::$cache_file_path) === false) {
				self::compile($language_list, self::COMPILE_ALL);
			}else{
				// Search all language file who was modifited and update it only
				$renew_language_list = array();

				foreach($language_list as $file_path) {
					if (filemtime($file_path) > filemtime(self::$cache_file_path)) {
						$renew_language_list[] = $file_path;
					}
				}

				if (empty($renew_language_list) === false) {
					self::compile($renew_language_list, self::COMPILE_LIST);
				}
			}

			return true;
		}else{
			self::error(self::LANGUAGE_ROOT_NOT_FOUND, self::$language_name_root);
		}
	}

	private static function compile($language_file_path_list, $compile_mode) {
	 	$lang = $application_language = array();

		// Include compile file
		foreach($language_file_path_list as $file_path) {
			include $file_path;

			switch($compile_mode) {
				case self::COMPILE_ALL:
					$application_language = array_merge($application_language, $lang);
					break;
				case self::COMPILE_LIST;
					include_once self::$cache_file_path;
					$cache[self::$language_name] = array_merge($cache[self::$language_name], $lang);
					break;
			}
		}

		// Set application language content to merged new language when compile mode is renew
		if ($compile_mode === self::COMPILE_LIST) {
			$application_language = $cache[self::$language_name];
		}

		// Make language cache
		file_put_contents(
			self::$cache_file_path,
			"<?php if(!defined('".self::$access_string."')) exit('Access Dead');\n\$cache['".self::$language_name."'] = ".var_export($application_language, true).";\n".'?>'
		);
	}

	private static function error($type, $message) {
		$label = "";

		switch($type) {
			case self::LANGUAGE_ROOT_NOT_FOUND;
				$label = "Not found language folder";
				break;
		}

		exit("<strong>[".$label."]</strong>:".$message);
	}

	/*
	 * 1. sprint_expand("Name: %{name}, Age: %{age}", array("name" => "Zeuxis", "age" => 18))
	 *	  - Name: Zeuxis, Age: 18
	 *
	 * 2. sprint_expand("Name: %{name}s, Age: %{age}0.2f", array("name" => "Zeuxis", "age" => 18), false)
	 *	  - Name: Zeuxis, Age: 18.00
	 */
	public static function sprint_expand($format, $arguments = array(), $auto_string = true) {
		$argument_numbers = array_slice(array_flip(array_keys(array(0 => 0) + $arguments)), 1);

		for ($position = 0; preg_match('/(?<=%)\{([a-zA-Z_]\w*)\}/', $format, $match, PREG_OFFSET_CAPTURE, $position);) {
			$argument_position = $match[0][1];
			$argument_length = strlen($match[0][0]);
			$argument_key = $match[1][0];

			if (array_key_exists($argument_key, $argument_numbers) === false) {
				user_error("sprint_expand(): Missing argument '${argument_key}'", E_USER_WARNING);
				return false;
			}

			$format = substr_replace(
				$format, 
				$replace = $argument_numbers[$argument_key] . ($auto_string === true ? '$s' : '$'),
				$argument_position, 
				$argument_length
			);

			$position = $argument_position + strlen($replace);
		}

		return vsprintf($format, array_values($arguments));
	}
}
?>