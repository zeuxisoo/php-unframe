<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Table {

	private static $db = null;
	private $table_name		= "";
	private $column_values	= array();
	private $primary_key 	= array();
	private $last_insert_id = -1;

	public function __construct($table_name, $primary_key_value = "", $primary_key_name = "id") {
		$this->table_name = $table_name;

		if (empty($primary_key_value) === false) {
			$this->set_primary_key($primary_key_name, $primary_key_value);
		}
	}

	public function __set($name, $value) {
		$this->column_values[$name] = $value;
	}

	public function __call($name, $arguments) {
		if (preg_match("/^find_by_([_a-zA-Z]\w*)$/", $name, $match) == true) {
			return self::fetch_all(self::prefix($this->table_name), array(
				$match[1] => $arguments[0],
			));
		}else{
			return array();
		}
	}

	public static function __callStatic($name, $arguments) {
		if (preg_match("/^find_by_([_a-zA-Z]\w*)$/", $name, $match) == true) {
			return self::fetch_all($arguments[0], array(
				'where' => array(
					$match[1] => $arguments[1],
				)
			));
		}else{
			return array();
		}
	}

	public function set_primary_key($name, $value) {
		$this->primary_key['name'] = $name;
		$this->primary_key['value'] = $value;
	}

	public function init($settings) {
		self::$db = $settings['db'];
	}

	public function save() {
		if (empty($this->column_values) === false) {

			$escaped_values = array();
			foreach($this->column_values as $name => $value) {
				$escaped_values[$name] = self::escape($this->column_values[$name]);
			}

			// Update | Create
			if (empty($this->primary_key) === false) {
				self::update($this->table_name, $escaped_values, array(
					"where" => array(
						$this->primary_key['name'] => $this->primary_key['value']
					),
				));
			}else{
				$column_string = "`".implode("`, `", array_keys($escaped_values))."`";
				$value_string = "'".implode("','", array_values($escaped_values))."'";

				self::$db->update("INSERT INTO ".self::prefix($this->table_name)." (".$column_string.") VALUES (".$value_string.")");

				$this->last_insert_id = self::$db->get_last_insert_id();

				return $this->last_insert_id;
			}
		}
	}

	// Static method
	public static function prefix($table_name) {
		return self::$db->prefix.$table_name;
	}

	public static function count($table_name, $condition = array(), $count_by_key = '1') {
		$where = "";
		if (empty($condition) === false) {
			$where = self::build_where($condition);
		}

		$row = self::$db->result(self::$db->query("
			SELECT COUNT(".$count_by_key.") AS count
			FROM ".self::prefix($table_name)."
			{$where}
		"), 0);

		return (int) $row;
	}

	public static function columns($table_name) {
		$columns = array();
		$query = self::$db->query("SHOW COLUMNS FROM ".self::prefix($table_name));
		while($row = self::$db->fetch_array($query)) {
			$columns[] = $row['Field'];
		}
		return $columns;
	}

	public static function delete($table_name, $condition = array()) {
		$where = self::build_where($condition);

		return self::$db->update("DELETE FROM ".self::prefix($table_name)." $where");
	}

	public static function last_insert_id() {
		return self::$db->get_last_insert_id();
	}

	public static function escape($value) {
		$value = self::$db->escape($value);

		if (self::$db instanceof SQLite_Adapter && preg_match("/^'(.*)'$/", $value, $matches) == true) {
			$value = $matches[1];
		}

		return $value;
	}

	public static function fetch_all($table_name, $condition = array()) {
		$select= isset($condition['select']) === false ? "*" : $condition['select'];
		$where = isset($condition['where']) === true ? self::build_where($condition['where']) : "";
		$order = isset($condition['order']) === false ? "" : $condition['order'];
		$offset= isset($condition['offset']) === false ? null : (int) $condition['offset'];
		$is_one= isset($condition['one']) === false ? false : $condition['one'];

		if (empty($order) === false && stristr(strtolower($order), "order") === false) {
			$order = "ORDER BY ".$order;
		}

		if ($is_one === true) {
			$limit = "LIMIT 0, 1";
		}else{
			$size = isset($condition['size']) === false ? null : (int) $condition['size'];

			$limit = "";
			if ($offset !== null && $size !== null) {
				$limit = "LIMIT {$offset}, {$size}";
			}
		}

		$sql = "
			SELECT {$select}
			FROM ".self::prefix($table_name)."
			{$where}
			{$order}
			{$limit}
		";

		return $is_one === true ? self::$db->fetch_one($sql) : self::$db->fetch_all($sql);
	}

	public static function fetch_one($table_name, $condition = array()) {
		return self::fetch_all($table_name, array_merge(array(
			"one" => true,
		), $condition));
	}

	public static function find_by_column($table_name, $column_name, $column_value) {
		return self::fetch_all($table_name, array(
			'where' => array(
				$column_name => $column_value,
			)
		));
	}

	private static function build_where($condition) {
		if (is_array($condition) === true) {
			if (isset($condition['where']) === false) {
				$condition = array(
					'where' => $condition
				);
			}

			$where = $condition['where'];
			$where_logic = isset($condition['where_logic']) === false ? "AND" : $condition['where_logic'];

			$where_string = array();
			foreach($where as $key => $value) {
				$where_string[] = " `{$key}` = '".self::escape($value)."'";
			}
			$where_string = implode(" ".$where_logic." ", $where_string);
		}else{
			$where_string = $condition;
		}

		if (empty($where_string) === false && stristr(strtolower($where_string), "where") === false) {
			$where_string = "WHERE {$where_string}";
		}

		return $where_string;
	}

	private static function update($table_name, $escaped_values, $condition = array()) {
		$update_pairs = array();
		if (is_array($escaped_values)) {
			foreach($escaped_values as $name => $value) {
				$update_pairs[] = "`{$name}` = '{$value}'";
			}
		}
		$update_string = implode(", ", $update_pairs);

		$where = self::build_where($condition);

		self::$db->update("
			UPDATE ".self::prefix($table_name)."
			SET {$update_string}
			{$where}
		");
	}

}
?>