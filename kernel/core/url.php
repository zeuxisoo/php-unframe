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

}
?>