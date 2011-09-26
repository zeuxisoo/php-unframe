<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Route {

	const NOT_MATCH_ROUTE = -1;

	public static function request_uri() {
		$uri = '';
		if (empty($_SERVER['PATH_INFO']) === false) {
			$uri = $_SERVER['PATH_INFO'];
		}else{
			if (isset($_SERVER['REQUEST_URI']) === true) {
				$scheme = empty($_SERVER['HTTPS']) === true || $_SERVER['HTTPS'] === 'off' ? 'http' : 'https';
				$uri = parse_url($scheme.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], PHP_URL_PATH);
			}elseif (isset($_SERVER['PHP_SELF']) === true) {
				$uri = $_SERVER['PHP_SELF'];
			}else{
				return "";
            }

            $request_uri = isset($_SERVER['REQUEST_URI']) === true ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];
			$script_name = $_SERVER['SCRIPT_NAME'];

			$base_uri = strpos($request_uri, $script_name) === 0 ? $script_name : str_replace('\\', '/', dirname($script_name));
			$base_uri = rtrim($base_uri, '/');

			if ($base_uri !== '' && strpos($uri, $base_uri) === 0) {
				$uri = substr($uri, strlen($base_uri));
			}

			return '/' . ltrim($uri, '/');
        }
	}

	public static function map($pattern_uri, $callback = null, $sensitive = false, $strict = false) {
		$request_uri = self::request_uri();
		$match_words = "/(?<!\\\):([a-zA-Z_][a-zA-Z_0-9]*)?(?:#(.*?)#)?/";

		// Get all names such as :id, :name
		preg_match_all($match_words, $pattern_uri, $names, PREG_PATTERN_ORDER);
		$names = $names[1];

		//[Form]: /user/:id, [To]: /user/([a-zA-Z0-9_\+\-%]+) OR /user/(custom rule)
		$match_path  = preg_replace_callback($match_words, array(__class__, "match_path"), $pattern_uri);
		$match_path .= $strict === true ? '' : '/?';

		$match_path  = $sensitive === true ? "@".$match_path."@" : "@".$match_path."@i";

		// Fill params
		$params = array();
		if (preg_match($match_path, $request_uri, $values) == true) {
			array_shift($values);

			foreach($names as $index => $name) {
				$params[$name] = rawurldecode($values[$index]);
			}

			if (is_callable($callback) === true) {
				call_user_func_array($callback, array_values($params));
			}
		}else{
			self::error(self::NOT_MATCH_ROUTE, $pattern_uri);
		}

		return $params;
	}

	public static function match_path($matches) {
		return isset($matches[2]) === true ? '('.$matches[2].')' : '([a-zA-Z0-9_\+\-%]+)';
	}

	private static function error($type, $message = "") {
		switch($type) {
			case self::NOT_MATCH_ROUTE:
				$message = "Not found matched route case by ".$message;
				break;
		}

		exit(sprintf("<strong>[Error]:</strong> %s", $message));
	}
}
?>