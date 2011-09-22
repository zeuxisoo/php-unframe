<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Plugin {

	const IS_FILTER = 1;
	const IS_ACTION = 2;

	private static $plugin_folder = "plugin";
	private static $plugin_filter = array();
	private static $plugin_action = array();
	private static $sorted_filter = array();
	private static $sorted_action = array();

	public static function set_settings($settings = array()) {
		if (is_array($settings) === true && empty($settings) === false) {
			foreach($settings as $k => $v) {
				self::$$k = $v;
			}
		}

		foreach(glob(self::$plugin_folder."/*") as $file_path) {
			if (is_file($file_path) === true) {
				require_once $file_path;
			}else{
				foreach(glob($file_path.'/*') as $plugin_file_path) {
					require_once $plugin_file_path;	
				}
			}
		}
	}

	public static function add_filter($tag, $function_name, $priority = 10, $total_arguments = 1) {
		$unique_id = self::get_unique_id($tag, $function_name, $priority, self::IS_FILTER);
		self::$plugin_filter[$tag][$priority][$unique_id] = array(
			'function' => $function_name,
			'total_arguments' => $total_arguments
		);
		unset(self::$sorted_filter[$tag]);
	}

	public static function apply_filter($tag, $value) {
		$arguments = array();

		if (isset(self::$sorted_filter[$tag]) === false) {
			if (isset(self::$plugin_filter[$tag]) === true) {
				ksort(self::$plugin_filter[$tag]);	// 優先櫂, 10 是最晚執行, 1 是最先執行
			}
			self::$sorted_filter[$tag] = true;
		}

		if (empty($arguments)) {
			$arguments = func_get_args();
		}

		if (isset(self::$plugin_filter[$tag]) === true) {
			reset(self::$plugin_filter[$tag]);

			foreach(self::$plugin_filter[$tag] as $priority) {
				foreach($priority as $filter) {
					if (is_null($filter['function']) === false) {
						$arguments[1] = $value;
						$value = call_user_func_array($filter['function'], array_slice($arguments, 1, (int) $filter['total_arguments']));
					}
				}
			}
		}

		return $value;
	}

	public static function remove_filter($tag, $function_name, $priority = 10) {
		$unique_id = self::get_unique_id($tag, $function_name, $priority, self::IS_FILTER);

		if (isset(self::$plugin_filter[$tag][$priority][$function_name]) === true) {
			unset(self::$plugin_filter[$tag][$priority][$function_name]);

			if (empty(self::$plugin_filter[$tag][$priority])) {
				unset(self::$plugin_filter[$tag][$priority]);
			}

			unset(self::$sorted_filter[$tag]);
		}
	}

	public static function remove_all_filter($tag, $priority = false) {
		if (isset(self::$plugin_filter[$tag]) === true) {
			if ($priority !== false && isset(self::$plugin_filter[$tag][$priority])) {
				unset(self::$plugin_filter[$tag][$priority]);
			}else{
				unset(self::$plugin_filter[$tag]);
			}
		}

		if (isset(self::$sorted_filter[$tag]) === true) {
			unset(self::$sorted_filter[$tag]);
		}
	}

	public static function add_action($tag, $function_name, $priority = 10, $total_arguments = 1) {
		$unique_id = self::get_unique_id($tag, $function_name, $priority, self::IS_ACTION);
		self::$plugin_action[$tag][$priority][$unique_id] = array(
			'function' => $function_name,
			'total_arguments' => $total_arguments
		);
		unset(self::$sorted_action[$tag]);
	}

	public static function do_action($tag, $argument = '') {
		if (isset(self::$plugin_action) === false) {
			self::$plugin_action = array();
		}

		$arguments = array();
		if (is_array($argument) === true && count($argument) === 1 && isset($argument[0]) === true && is_object($argument[0]) === true) {
			$arguments[] = &$argument[0];
		}else{
			$arguments[] = $argument;
		}

		for($i=2; $i<func_num_args(); $i++) {
			$arguments[] = func_get_arg($i);
		}

		if (isset(self::$sorted_action[$tag]) === false) {
			ksort(self::$plugin_action[$tag]);
			self::$sorted_action[$tag] = true;
		}

		if (isset(self::$plugin_action[$tag]) === true) {
			reset(self::$plugin_action[$tag]);

			foreach(self::$plugin_action[$tag] as $priority) {
				foreach($priority as $action) {
					if (is_null($action['function']) === false) {
						call_user_func_array($action['function'], array_slice($arguments, 0, (int) $action['total_arguments']));
					}
				}
			}
		}
	}

	public static function remove_action($tag, $function_name, $priority = 10, $total_arguments = 1) {
		$unique_id = self::get_unique_id($tag, $function_name, $priority, self::IS_ACTION);

		if (isset(self::$plugin_action[$tag][$priority][$function_name]) === true) {
			unset(self::$plugin_action[$tag][$priority][$function_name]);

			if (empty(self::$plugin_action[$tag][$priority])) {
				unset(self::$plugin_action[$tag][$priority]);
			}

			unset(self::$sorted_action[$tag]);
		}
	}

	public static function remove_all_action($tag, $priority = false) {
		if (isset(self::$plugin_action[$tag]) === true) {
			if ($priority !== false && isset(self::$plugin_action[$tag][$priority])) {
				unset(self::$plugin_action[$tag][$priority]);
			}else{
				unset(self::$plugin_action[$tag]);
			}
		}

		if (isset(self::$sorted_action[$tag]) === true) {
			unset(self::$sorted_action[$tag]);
		}
	}

	private static function get_unique_id($tag, $object, $priority, $type) {
		static $filter_id_counter = 0;

		if (is_string($object) === true) {
			return $object;
		}

		$object = is_object($object) === true ? array($object, '') : (array) $object;

		if (is_object($object[0]) === true) {
			if (function_exists("spl_object_hash") === true) {
				return spl_object_hash($object[0]).$object[1];
			}else{
				$class_hash = get_class($object[0]).$object[1];

				// If defined identifiy id will use classid
				if (isset($object[0]->filter_id) === true) {
					$class_hash .= $object[0]->filter_id;
				}else{
					if ($type === self::IS_FILTER) {
						$class_hash .= isset(self::$plugin_filter[$tag][$priority]) ? count(self::$plugin_filter[$tag][$priority]) : $filter_id_counter;
					}else{
						$class_hash .= isset(self::$plugin_action[$tag][$priority]) ? count(self::$plugin_action[$tag][$priority]) : $filter_id_counter;
					}

					$object[0]->filter_id = $class_hash;
					++$filter_id_counter;
				}

				return $class_hash;
			}
		}else if (is_string($object[0]) === true) {
			return $object[0].$object[1];	// Callback static method
		}
	}
}
?>