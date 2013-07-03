<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Paginate {

	private static $row_count	= 0;
	private static $page_number	= 1;
	private static $per_page	= 18;
	private static $have_page	= 0;
	private static $offset		= 0;
	private static $page_param 	= 'page';

	private static $query_string_array = "";
	private static $script = "";

	public static function init($settings) {
		foreach($settings as $key => $value) {
			self::$$key = $value;
		}
	}

	public static function offset() {
		self::prepare();
		self::calculate();

		return self::$offset;
	}

	private function prepare() {
		$current_uri = isset($_SERVER['SCRIPT_URI']) === true ? $_SERVER['SCRIPT_URI'] : $_SERVER['REQUEST_URI'];
		$query_position = strpos($current_uri, '?');

		$script = $current_uri;
		$query_string_array = array();

		if ($query_position > 0) {
			parse_str($query_string = substr($current_uri, $query_position + 1), $query_string_array);
			$script = substr($current_uri, 0, $query_position);
		}

		self::$query_string_array = $query_string_array;
		self::$script = $script;
	}

	private function calculate() {
		self::$have_page = ceil(self::$row_count / self::$per_page);

		if (self::$have_page <= 0) {
			self::$have_page = 1;
		}

		$page_param = Request::get(self::$page_param);

		self::$page_number = empty($page_param) === false ? abs(intval($page_param)) : 0;

		if (self::$page_number == 0) {
			self::$page_number = 1;
		}

		if (self::$page_number > self::$have_page) {
			self::$page_number = self::$have_page;
		}

		self::$offset = (self::$page_number - 1) * self::$per_page;
	}

	public function build($view_name = 'default', $show_total = true) {
		$from = self::$per_page * (self::$page_number - 1) + 1;

		if ($from > self::$row_count) {
			$from = self::$row_count;
		}

		$to = self::$page_number * self::$per_page;

		if ($to > self::$row_count) {
			$to = self::$row_count;
		}

		$index 		= '&laquo;';
		$previous	= '&lsaquo;';
		$next 		= '&rsaquo;';
		$end 		= '&raquo;';

		if (self::$have_page <= 7) {
			$range = range(1, self::$have_page);
		} else {
			$min = self::$page_number - 3;
			$max = self::$page_number + 3;

			if ($min < 1) {
				$max += (3 - $min);
				$min  = 1;
			}

			if ($max > self::$have_page) {
				$min -= $max - self::$have_page;
				$max  = self::$have_page;
			}

			$min = $min > 1 ? $min : 1;
			$range = range($min, $max);
		}

		include_once View::render("paginate/".$view_name.".html");
	}

	private static function build_url($page_param, $page_number) {
		$query_string_array = self::$query_string_array;
		$query_string_array[$page_param] = $page_number;
		return self::$script.'?'.http_build_query($query_string_array);
	}
}
