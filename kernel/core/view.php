<?php
if (defined('IN_APP') === false) exit('Access Dead');

class View {

	const FILE_NOT_FOUND 			= -1;
	const CONTENT_IS_EMPTY 			= -2;
	const AUTO_RENDER_VIEW_NOT_FOUND= -3;

	protected static $view_folder			= 'view';
	protected static $view_cache_folder		= 'cache/view';
	protected static $theme					= '';
	protected static $strip_tab				= true;
	protected static $word_wrap				= true;
	protected static $debug					= false;
	protected static $view_access_string	= "IN_APP";
	protected static $default_theme_folder	= "default";

	protected static $view_content;
	protected static $view_file_path;
	protected static $view_file_cached_path;

	public static function init($settings = "") {
		if (empty($settings) === false) {
			foreach($settings as $_key => $_value) {
				self::$$_key = $_value;
			}
		}
	}

	public static function render($view_file = "") {
		if (empty($view_file) === true) {
			$trace = array_shift(debug_backtrace());

			if (isset($trace['file']) === true) {
				$view_file = str_replace(".php", ".html", basename($trace['file']));
			}else{
				self::error(self::AUTO_RENDER_VIEW_NOT_FOUND);
			}
		}

		if (self::prepare_compile_path($view_file, self::$theme) === true) {
			return self::$view_file_cached_path;
		}
	}

	private static function prepare_compile_path($view_file_name, $theme) {
		self::$theme = empty($theme) ? 'default' : $theme;

		// If not found setting's theme/file, then theme will set to "default theme"
		$view_file_folder = self::$view_folder.DIRECTORY_SEPARATOR.self::$theme;
		if (is_dir($view_file_folder) === false || file_exists($view_file_folder) === false) {
			self::get_compiled_path($view_file_name, self::$default_theme_folder);
		}
		unset($view_file_folder);

		// Define view path and cache store path
		self::$view_file_path 		 = self::$view_folder.DIRECTORY_SEPARATOR.self::$theme.DIRECTORY_SEPARATOR.$view_file_name;
		self::$view_file_cached_path = self::$view_cache_folder.DIRECTORY_SEPARATOR.self::$theme.DIRECTORY_SEPARATOR.$view_file_name.'.php';

		// Create cache folder if not exists
		$cache_directory = dirname(self::$view_file_cached_path);
		if (is_dir($cache_directory) === false && file_exists($cache_directory) === false) {
			mkdir($cache_directory, 0777, true);
		}

		// If file not exists in selected theme. try to select default theme. if all not.then throw error
		if (is_file(self::$view_file_path) === false || file_exists(self::$view_file_path) === false) {
			self::$view_file_path = self::$view_folder.DIRECTORY_SEPARATOR.self::$default_theme_folder.DIRECTORY_SEPARATOR.$view_file_name;

			if (is_file(self::$view_file_path) === false || file_exists(self::$view_file_path) === false) {
				self::error(self::FILE_NOT_FOUND);
			}
		}
	
		// If not expired not render again
		if (file_exists(self::$view_file_cached_path) && (filemtime(self::$view_file_path) <= filemtime(self::$view_file_cached_path))) {
			return true;
		}else{
			self::$view_content = file_get_contents(self::$view_file_path);

			if (strlen(trim(self::$view_content)) <= 0) {
				self::error(self::CONTENT_IS_EMPTY);
			}else{
				$view_file_cached_theme_folder = self::$view_cache_folder.DIRECTORY_SEPARATOR.self::$theme;

				if (file_exists($view_file_cached_theme_folder) === false) {
					mkdir($view_file_cached_theme_folder, 0777);
				}
				unset($view_file_cached_theme_folder);

				self::process_compile_path();

				return true;
			}
		}
	}

	private static function process_compile_path() {
		$pattern  = array(
			'#{(\$[a-zA-Z_][a-zA-Z0-9_\->\.\[\]\'\$\(\)]*)}#s',
			'#\$\{(.+?)\}#i',
			'#{set:(.+?)}#i',
			'#{_\(\"(.+?)\"\)}#i',
			'#{% include\s+(.*?) %}#i',
			'#{% foreach\s+(\S+)\s+(\S+)\s+(\S+) %}#i',
			'#{% foreach\s+(\S+)\s+(\S+) %}#i',
			'#{% for\s+(.*?)\s+(.*?)\s+(.*?) %}#i',
			'#{% if\s+(.*?) %}#i',
			'#{% elseif\s+(.*?) %}#i',
			'#{% else %}#i',
			'#{% when\s+(.*?)\s+(.*?)\s+(.*?) %}#i',
			'#<!--\${(.*?)}-->#ism',
		);

		$replace = array(
			'<?php echo \1; ?>',
			'<?php echo \1; ?>',
			'<?php \1; ?>',
			'<?php echo Language::get_text("\1"); ?>',
			'<?php include_once View::render(\'\1\'); ?>',
			'<?php if (is_array(\1)) { foreach(\1 as \2 => \3) { ?>',
			'<?php if (is_array(\1)) { foreach(\1 as \2) { ?>',
			'<?php for(\1;\2;\3) { ?>',
			'<?php if (\1) { ?>',
			'<?php } elseif (\1) { ?>',
			'<?php } else { ?>',
			'<?php echo (\1) ? \2 : \3; ?>',
			'<?php \1 ?>',
		);

		self::$view_content = preg_replace($pattern, $replace, self::$view_content);

		$pattern = array(
			'#{% /foreach %}#i',
			'#{% /for %}#i',
			'#{% /if %}#i',
		);

		$replace= array(
			'<?php } } ?>',
			'<?php } ?>',
			'<?php } ?>',
		);

		self::$view_content = preg_replace($pattern, $replace, self::$view_content);

		self::$view_content = self::$strip_tab === true ? preg_replace("/\t/s", "", self::$view_content) : self::$view_content;
		self::$view_content = self::$word_wrap === true ? self::$view_content : preg_replace("/([\n|\r|\r\n|\t]+)/s", "", self::$view_content);

		self::save();
	}

	private static function save() {
		file_put_contents(
			self::$view_file_cached_path,
			"<?php if(!defined('".self::$view_access_string."')) exit('Access Dead'); ?>\n".self::$view_content
		);
	}

	private static function error($exception) {
		$message = '';

		switch($exception) {
			case self::FILE_NOT_FOUND:
				$message = "File not found";
				break;
			case self::CONTENT_IS_EMPTY:
				$message = "File content is empty";
				break;
			case self::AUTO_RENDER_VIEW_NOT_FOUND;
				$message = "Auto render view not found";
				break;
		}

		if (self::$debug === true) {
			exit(self::debug($message, self::$view_file_path));
		}else{
			exit($message);
		}
	}

	private static function debug($message, $view_file_path) {
		echo "<strong>",$message,": </strong>",$view_file_path,"<br />";
	}
}
?>