<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Benchmark {
	public static function time_parts() {
		return explode(" ",microtime());
	}

	public static function start() {
		$time_parts = self::time_parts();
		return $time_parts[1].substr($time_parts[0], 1);
	}


	public static function compared_time($start_time) {
		$time_parts = self::time_parts();
		return round(intval($time_parts[1].substr($time_parts[0], 1)) - $start_time,6);
	}
}
?>