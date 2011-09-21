<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Util {
	
	public static function add_slash($string, $force = 0, $strip = false) {
		if(get_magic_quotes_gpc() == false || $force) {
			if(is_array($string)) {
				foreach($string as $key => $val) {
					$string[$key] = self::add_slash($val, $force, $strip);
				}
			} else {
				$string = addslashes($strip === true ? stripslashes($string) : $string);
			}
		}
		return $string;
	}

	public static function to_date_time($timestamp, $format = 'Y-m-d', $time_zone = 8) {
		return gmdate($format, $timestamp + $time_zone * 3600);
	}

}
?>