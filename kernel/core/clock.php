<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Clock {
	public static function to_date_time($timestamp, $format = 'Y-m-d', $time_zone = 8) {
		return gmdate($format, $timestamp + $time_zone * 3600);
	}
}
?>