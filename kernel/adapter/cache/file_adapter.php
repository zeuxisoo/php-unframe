<?php
if (defined('IN_APP') === false) exit('Access Dead');

class File_Adapter extends Cache_Adapter {

	private $cache_root 	= "";
	private $access_string	= "IN_APP";

	public function __construct($settings) {
		$this->cache_root = $settings['cache_root'];
	}

	public function add($name, $value) {
		$cache_file_path = $this->get_cache_file_path($name);

		$cache = array();

		if (file_exists($cache_file_path) === true && is_file($cache_file_path) === true) {
			require_once $cache_file_path;
		}

		if (isset($cache[$name]) === true) {
			return false;
		}else{
			$cache[$name] = $value;

			return $this->write_cache($cache_file_path, $name, $cache);
		}
	}

	public function set($name, $value) {
		$cache_file_path = $this->get_cache_file_path($name);

		$cache = array();

		if (file_exists($cache_file_path) === true && is_file($cache_file_path) === true) {
			require_once $cache_file_path;
		}

		$cache[$name] = $value;

		return $this->write_cache($cache_file_path, $name, $cache);
	}

	public function get($name) {
		$cache_file_path = $this->get_cache_file_path($name);

		$cache = array();

		if (file_exists($cache_file_path) === true && is_file($cache_file_path) === true) {
			require_once $cache_file_path;
		}

		return isset($cache[$name]) === true ? $cache[$name] : "";
	}

	public function delete($name) {
		$cache_file_path = $this->get_cache_file_path($name);

		$cache = array();

		if (file_exists($cache_file_path) === true && is_file($cache_file_path) === true) {
			require_once $cache_file_path;
		}

		if (isset($cache[$name]) === true) {
			unset($cache[$name]);
		}

		return $this->write_cache($cache_file_path, $name, $cache);
	}

	public function clear($name = "") {
		@unlink($this->get_cache_file_path($name));
	}

	private function get_cache_file_path($name) {
		return $this->cache_root.'/'.md5($name).'.php';
	}

	private function write_cache($cache_file_path, $name, $cache) {
		return file_put_contents(
			$cache_file_path,
			"<?php\nif(!defined('".$this->access_string."')) exit('Access Denied');\n".'$'."cache['".$name."'] = ".var_export($cache[$name], true)."?>"
		);
	}

}
?>