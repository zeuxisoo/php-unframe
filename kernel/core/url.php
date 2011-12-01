<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Url {

	public static function php_self() {
		$php_self[] = isset($_SERVER['PHP_SELF']) === true ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];

		if (substr($php_self[0], -1) == '/') {
			$php_self[] = 'index.php';
		}

		return join("", $php_self);
	}

	public static function php_uri() {
		return isset($_SERVER['SCRIPT_URI']) === true ? $_SERVER['SCRIPT_URI'] : $_SERVER['REQUEST_URI'];
	}

	public static function build($url, $parameters = array()) {
		return empty($parameters) === true ? $url : $url."?".http_build_query($parameters);
	}

	public static function redirect($url, $query_string = array(), $time = 0) {
		$url = str_replace(array("\n", "\r"), '', $url);

		if (empty($query_string) === false) {
			$url .= "?".http_build_query($query_string);
		}

		if (headers_sent() === false) {
			header("Content-Type:text/html; charset=utf-8");

			if ($time === 0) {
				header("Location: ".$url);
			}else{
				header("refresh:{$time};url={$url}");
			}

			exit();
		}else{
			exit("<meta http-equiv='refresh' content='{$time};url={$url}'>");
		}
	}

	public static function schema() {
		$schemas = array();

		$schemas['protocol'] = isset($_SERVER['HTTPS']) === true && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
		$schemas['site']     = $schemas['protocol'] . '://' . $_SERVER['HTTP_HOST'];
		$schemas['script']   = basename($_SERVER['SCRIPT_FILENAME']);

		$schemas['real_folders'] = self::clean_up(explode("/", str_replace($schemas['script'], "", $_SERVER['PHP_SELF'])));
		$schemas['total_real_folder'] = count($schemas['real_folders']);

		$schemas['fake_folders'] = array_diff(
			self::clean_up(explode("/", str_replace($schemas['script'], "", $_SERVER['REQUEST_URI']))),
			$schemas['real_folders']
		);
		$schemas['total_fake_folder'] = count($schemas['fake_folders']);

		$schemas['base_url'] = $schemas['site'] . "/" . implode("/", $schemas['real_folders']) . "/";
		$schemas['this_url'] = $schemas['base_url'] . implode("/", $schemas['fake_folders']);

		if (substr($schemas['this_url'], -1) != '/') {
			$schemas['this_url'] .= "/";
		}

		return $schemas;
	}

	private static function clean_up($array) {
		$cleaned_array = array();

		foreach($array as $key => $value) {
			$pos = strpos($value, "?");

			if($pos !== false) {
				break;
			}

			if($key != "" && $value != "") {
				$cleaned_array[] = $value;
			}
		}

		return $cleaned_array;
	}
}
?>