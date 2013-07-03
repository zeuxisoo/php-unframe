<?php
if (defined('IN_APP') === false) exit('Access Dead');

abstract class Cache_Adapter {

	abstract public function add($name, $value);	// Create record
	abstract public function set($name, $value);	// Update record
	abstract public function get($name);			// Get record by name
	abstract public function delete($name);			// Delete record by name
	abstract public function clear($name = "");		// Clean database

}
