<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Clock {
	public static function to_date_time($timestamp, $format = 'Y-m-d', $time_zone = 8) {
		return gmdate($format, $timestamp + $time_zone * 3600);
	}

	public static function to_timestamp($date_time, $split_datetime = ' ', $split_date = '-', $split_time = ':') {
		$time_parts = explode($split_datetime, $date_time);

		if (count($time_parts) == 1) {
			list($year, $month, $day) = explode($split_date, $time_parts[0]);

			if (empty($year) === false && empty($month) === false && empty($day) === false) {
				return mktime(0, 0, 0, $month, $day, $year);
			}
		}else{
			list($year, $month, $day) = explode($split_date, $time_parts[0]);
			list($hour, $minute, $second) = explode($split_time, $time_parts[1]);

			if (empty($year) === false && empty($month) === false && empty($day) === false) {
				if (empty($hour) === false && empty($minute) === false && empty($second) === false) {
					return mktime($hour, $minute, $second, $month, $day, $year);
				}
			}
		}
		return "";
	}

	public static function human_time($timestamp, $out_range_date_format = 'Y-m-d H:i:s') {
		$limit = time() - $timestamp;

		if ($limit < 60) {
			$timestamp = t("%{limit} second ago", array('limit' => $limit));
		}

		if ($limit >= 60 && $limit < 3600) {
			$i = floor($limit / 60);
			$s = $limit % 60;
			$timestamp = t("%{miute} minute %{second} second ago", array('miute' => $i, 'second' => $s));
		}

		if ($limit >= 3600 && $limit < 3600*24) {
			$h = floor($limit / 3600);
			$i = ceil(($limit % 3600) / 60);
			$timestamp = t("%{hour} hour %{minute} minute ago", array('hour' => $h, 'minute' => $i));
		}

		if ($limit >= 3600 * 24 && $limit < 3600 * 24 * 30) {
			$d = floor($limit / (3600*24));
			$timestamp = t("%{day} day ago", array('day' => $d));
		}

		if ($limit >= 3600 * 24 * 30) {
			$timestamp = self::to_date_time($timestamp, $out_range_date_format);
		}

		return $timestamp;
	}
}
