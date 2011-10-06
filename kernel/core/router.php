<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Router {

	const NOT_FOUND_ACTION	   = -1;
	const NOT_FOUND_CONTROLLER = -2;

	private static $instance = null;

	public static function instance() {
		if (self::$instance === null) {
			$class_name = __CLASS__;
			self::$instance = new $class_name();
		}

		return self::$instance;
	}

	public function route() {
		Route::map("/:controller", array($this, "run"));
		Route::map("/:controller/:action", array($this, "run"));
		Route::map("/:controller/:action/:id", array($this, "run"));
	}

	public function run($controller, $action = "", $id = "") {
		$controller = $controller."_Controller";
		
		if (class_exists($controller) === true) {
			$instance = new $controller();
			$instance->params = Route::params();

			if (empty($action) === true) {
				$instance->index();
			}else{
				if (method_exists($instance, $action) === true) {
					$instance->$action();
				}else{
					$this->error(self::NOT_FOUND_ACTION, $action);
				}
			}
		}else{
			$this->error(self::NOT_FOUND_CONTROLLER, $controller);
		}
	}

	private function error($error_code, $message = '') {
		switch($error_code) {
			case self::NOT_FOUND_ACTION;
				$message = "Not found action: ".$message;
				break;
			case self::NOT_FOUND_CONTROLLER;
				$message = "Not found controller: ".$message;
				break;
		}

		exit($message);
	}
}
?>