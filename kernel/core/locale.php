<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Locale {

	const COMPILE_ALL 	= 1;
	const COMPILE_LIST	= 2;

	const LOCALE_ROOT_NOT_FOUND = -1;

	private static $locale_root 	= "";
	private static $locale_name 	= "";
	private static $locale_name_root= "";
	private static $access_string 	= "IN_APP";
	private static $cache_file_path = "";
	
	public static function init($settings) {
		self::$locale_root 		= $settings['locale_root'];
		self::$locale_name 		= $settings['locale_name'];
		self::$locale_name_root = self::$locale_root."/".self::$locale_name;
		self::$cache_file_path 	= CACHE_ROOT."/locale/".self::$locale_name.".php";

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

		$locale_table = $cache[self::$locale_name];
		$string = isset($locale_table[$string]) === true && empty($locale_table[$string]) === false ? $locale_table[$string] : $string;

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
		$locale_list = glob(self::$locale_name_root."/*.php");

		if (is_dir(self::$locale_name_root) === true) {
			// If locale cache file not exists, collect all locale file into one file
			if (file_exists(self::$cache_file_path) === false) {
				self::compile($locale_list, self::COMPILE_ALL);
			}else{
				// Search all locale file who was modifited and update it only
				$renew_locale_list = array();

				foreach($locale_list as $file_path) {
					if (filemtime($file_path) > filemtime(self::$cache_file_path)) {
						$renew_locale_list[] = $file_path;
					}
				}

				if (empty($renew_locale_list) === false) {
					self::compile($renew_locale_list, self::COMPILE_LIST);
				}
			}

			return true;
		}else{
			self::error(self::LOCALE_ROOT_NOT_FOUND, self::$locale_name_root);
		}
	}

	private static function compile($locale_file_path_list, $compile_mode) {
	 	$lang = $application_language = array();

		// Include compile file
		foreach($locale_file_path_list as $file_path) {
			include $file_path;

			switch($compile_mode) {
				case self::COMPILE_ALL:
					$application_language = array_merge($application_language, $lang);
					break;
				case self::COMPILE_LIST;
					include_once self::$cache_file_path;
					$cache[self::$locale_name] = array_merge($cache[self::$locale_name], $lang);
					break;
			}
		}

		// Set application locale content to merged new locale when compile mode is renew
		if ($compile_mode === self::COMPILE_LIST) {
			$application_language = $cache[self::$locale_name];
		}

		// Make locale cache
		file_put_contents(
			self::$cache_file_path,
			"<?php if(!defined('".self::$access_string."')) exit('Access Dead');\n\$cache['".self::$locale_name."'] = ".var_export($application_language, true).";\n".'?>'
		);
	}

	private static function error($type, $message) {
		$label = "";

		switch($type) {
			case self::LOCALE_ROOT_NOT_FOUND;
				$label = "Not found locale folder";
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