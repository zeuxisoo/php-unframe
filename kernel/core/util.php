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

}
?>