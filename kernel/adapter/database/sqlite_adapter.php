<?php
if (defined('IN_APP') === false) exit('Access Dead');

class SQLite_Adapter extends Database_Adapter {
	
	private static $instance= null;
	public $statement		= null;
	public $query_count		= 0;		// Database query count
	public $pdo				= null;		// Database connection instance
	public $version			= 0;		// Database version
	public $debug			= 0;		// Debug mode (on|off)
	public $configs			= array();
	public $debug_log		= array();

	public function __construct($config) {
		$this->configs = $config;
			
		if ($config['driver'][0] != '/') {
			$sqlite_root = WWW_ROOT."/".$config['host'];
		}else{
			$sqlite_root = $config['host'];
		}
		
		if (file_exists($sqlite_root) === false || is_file($sqlite_root) === false) {
			$this->halt("Can not found sqlite file");
		}else{
			$this->pdo = new PDO('sqlite:'.$sqlite_root);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION);
			
			$this->connect(null, null, null, null, null, null, null);
			
			self::$instance = $this->pdo;
		}
		
		return $this->pdo;
	}

	public function connect($host, $username, $password, $database, $prefix, $charset, $port) {
		if ($this->pdo) {
			$this->version = $this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
		} else {
			$this->halt('Can not connect DataBase Server or DataBase.');
		}
	}
	
	public function query($sql, $type = '') {
		try {
			$start_time = Benchmark::start();
		
			$this->statement = $this->pdo->prepare($sql);
			$this->statement->execute();
			
			$end_time = Benchmark::process_time($start_time);
		
			if(isset($this->debug) && $this->debug === true) {
				$this->debug_query($sql, $end_time);
			}
			
			return $this->statement;
		}catch(PDOException $e) {
			$this->halt($e->getMessage(), $sql);
		}
	}
	
	public function update($sql, $type = '') {
		try {
			$start_time = Benchmark::start();
		
			$affected_row = $this->pdo->exec($sql);
			
			$end_time = Benchmark::process_time($start_time);
		
			if(isset($this->debug) && $this->debug === true) {
				$this->debug_query($sql, $end_time);
			}
			
			return $affected_row;
		}catch(PDOException $e) {
			$this->halt($e->getMessage(), $sql);
		}
	}
	
	public function close() {
		$this->pdo = null;
	}
	
	public function set_debug($status = false) {
		$this->debug = $status;
	}
	
	public function get_debug_log() {
		return $this->debug_log;
	}

	public function get_error($sql = '') {
		if ($this->get_error_no() != '00000') {
			if ($this->statement) {
				$info = $this->statement->errorInfo();
			}else{
				$info = $this->pdo->errorInfo();
			}
			return $info[2];
		}
		return "";
	}
	
	public function get_error_no() {
		if ($this->statement) {
			return $this->statement->errorCode();
		}elseif ($this->pdo) {
			return $this->pdo->errorCode();
		}else{
			return 0x000;
		}
	}

	public function escape($data) {
		if(is_array($data)) {
			return array_map(array($this->pdo, "quote"), $data);
		}
		return $this->pdo->quote($data);
	}
	
	public function get_last_insert_id() {
		return $this->pdo->lastInsertId();
	}

	public function fetch_array($sql, $type = '') {
		return $this->statement->fetch(empty($type) === true ? PDO::FETCH_ASSOC : $type, PDO::FETCH_ORI_NEXT);
	}
	
	public function result($sql, $column_number) {
		if ($sql) {
			if ($sql instanceof PDOStatement) {
				$statement = $this->query($sql->queryString);
			}else{
				$statement = $this->query($sql);
			}
			
			$result = $statement->fetchColumn($column_number);
		} elseif ($this->statement) {
			$result = $this->statement->rowCount();
		} else {
			$result = 0;
		}
		
		return $result;
	}
	
	public function free_result($query) {
		if ($this->statement) {
			$this->statement = null;
		}
		
		$query = null;
	}

	// extra method
	public function fetch_one($sql, $type = '') {		
		$statement = $this->query($sql);
		$return = $statement->fetch(empty($type) === true ? PDO::FETCH_ASSOC : $type);
		return $return;
	}
	
	public function fetch_all($sql, $type = '') {
		$statement = $this->query($sql);
		$return = $statement->fetchAll(empty($type) === true ? PDO::FETCH_ASSOC : $type);
		return $return;
	}
	
	//
	public function debug_query($sql, $query_time) {
		if(preg_match("#^select#i", strtolower(trim($sql)))) {

			$sql_info = array();
			$query = $this->pdo->prepare("EXPLAIN $sql");
			$query->execute();
			while($row = $query->fetch(PDO::FETCH_BOTH , PDO::FETCH_ORI_NEXT)) {
				$sql_info[] = $row;
			}

			$this->debug_log[] = array(
				'type' => "select",
				'query_count' => $this->query_count,
				'sql' => $sql,
				'sql_info' => $sql_info,
				'query_time' => $query_time,
			);

		}else{
			$this->debug_log[] = array(
				'type' => "update",
				'query_count' => $this->query_count,
				'sql' => $sql,
				'query_time' => $query_time,
			);
		}
	}

}
?>