<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Util {
	
	public static function end_with($haystack, $needle, $case=true) {
		if ($case) {
			return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
		}
		return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
	}

	public static function string_length_by_utf8($string) {
		return count(preg_split("//u", $string)) - 2;
 	}
 
	public static function substring_by_utf8($text, $start, $limit, $encode = 'UTF-8') {
		if (function_exists("mb_substri")) {
			return mb_substr($text, $start, $limit, $encode);
		}else{
			return preg_replace(
				'#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$start.'}'.'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$limit.'}).*#s',
				'$1',
				$text
			);
		}
	}

	public static function client_ip() {
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$online_ip = getenv('HTTP_CLIENT_IP');
		} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$online_ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
			$online_ip = getenv('REMOTE_ADDR');
		} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$online_ip = $_SERVER['REMOTE_ADDR'];
		}else{
			$online_ip = "0.0.0.0";
		}
		return preg_replace("/^([\d\.]+).*/", "\\1", $online_ip);
	}

	public function random_string($length = 6) {
		$characters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ+-*#&@!?";
		$char_length = strlen($characters);

		$result = array();

		for ($i=0; $i<$length; $i++) {
			$index = mt_rand(0, $char_length - 1);
			$result[] = $characters[$index];
		}

		return implode("", $result);
	}

	public static function folder_size($directory) {
		$handler = opendir($directory);
		$size = 0;

		while ($file = readdir($handler)) {
			if ($file != '.' && $file != '..') {
				$path = $directory."/".$file;
				if (@is_dir($path)) {
					$size += self::folder_size($path);
				} else {
					$size += filesize($path);
				}
			}
		}
		closedir($handler);

		return $size;
	}

	public static function size_format($size) {
		$sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		
		if ($size == 0) {
			return 0;
		}

		return (round($size / pow(1024, ($i = floor(log($size, 1024)))), $i > 1 ? 2 : 0) . $sizes[$i]);
	}
}
?>