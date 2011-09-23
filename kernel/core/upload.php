<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Upload {
	const CAN_NOT_REMOVE_FILE	= -1;
	const INVALID_FILE_FORMAT	= -2;
	const NOT_WRITEABLE_ROOT	= -3;
	const OVERWRITE_FILE_EXISTS	= -4;
	const FILE_SIE_NOT_ALLOW	= -5;

	const SINGLE_UPLOAD_SUCCESS = 1;

	private static $instance = null;

	private $allow_format	= array('gif','jpg','jpeg','png','zip','rar','pdf','7z','html','doc','gz','tar','txt');
	private $allo_file_size	= 0;		// 0: not check file size
	private $save_root		= 'attachment';
	private $auto_overwrite	= true;

	public static function instance($settings = array()) {
		if (self::$instance === null) {
			$class_name = __class__;
			self::$instance = new $class_name();
		}

		foreach($settings as $key => $value) {
			self::$instance->$key = $value;
		}

		return self::$instance;
	}

	public function file_extension($file_name) {
		return strtolower(trim(substr(strrchr($file_name, '.'), 1)));
	}

	public function save_name($file_name) {
		return md5(uniqid(rand(), true)).'.'.$this->file_extension($file_name);
	}

	public function valid_file_extension($file_extension) {
		return is_array($this->allow_format) === true && in_array(strtolower($file_extension), $this->allow_format) === true;
	}

	public function message($message_no = "") {
		switch($message_no) {
			case self::CAN_NOT_REMOVE_FILE:
				return t("Can not remove file");
				break;
			case self::INVALID_FILE_FORMAT:
				return t("File format not allow");
				break;
			case self::NOT_WRITEABLE_ROOT:
				return t("Can not write file");
				break;
			case self::OVERWRITE_FILE_EXISTS:
				return t("Overwrite disabled and file exists");
				break;
			case self::FILE_SIE_NOT_ALLOW:
				return t("File is too large");
				break;
			case self::SINGLE_UPLOAD_SUCCESS:
				return t("Upload single file success");
				break;
		}
	}

	public function single_upload($file_source) {
		$target_file_path = $this->save_root.'/'.$this->save_name($file_source['name']);

		if (is_dir($this->save_root) === false && file_exists($this->save_root) == false) {
			mkdir($this->save_root, 0777, true);
		}

		$status = false;

		if ($this->valid_file_extension($this->file_extension($file_source['name'])) === false) {
			$message_no = self::INVALID_FILE_FORMAT;
		}elseif (is_writable($this->save_root) === false) {
			$message_no = self::NOT_WRITEABLE_ROOT;
		}elseif ($this->auto_overwrite === false && file_exists($target_file_path) === true) {
			$message_no = self::OVERWRITE_FILE_EXISTS;
		}elseif ($this->allo_file_size != 0 && $file_source["size"] > $this->allo_file_size) {
			$message_no = self::FILE_SIE_NOT_ALLOW;
		}elseif(move_uploaded_file($file_source["tmp_name"], $target_file_path) === false) {
			$message_no = $file_source["error"];
		}else{
			$message_no = self::SINGLE_UPLOAD_SUCCESS;
			$status = true;
		}

		return array(
			'status' => $status,
			'message' =>$this->message($message_no),
			'origin_file' => array(
				'name' => $file_source["name"],
			),
			'saved_file' => array(
				'path' => $target_file_path,
				'name' => basename($target_file_path),
			),
		);
	}

	public function multi_upload($file_sources) {
		$status = array();

		if (is_array($file_sources) === true) {
			for($i=0, $j=count($file_sources['name']); $i<$j; $i++) {
				if (empty($file_sources['tmp_name'][$i]) === false) {
					$single_file_source = array(
						'tmp_name' => $file_sources['tmp_name'][$i],
						'name' => $file_sources['name'][$i],
						'type' => $file_sources['type'][$i],
						'size' => $file_sources['size'][$i],
						'error' => $file_sources['error'][$i]
					);

					$status[] =$this->single_upload($single_file_source);
				}
			}
		}

		return $status;
	}
}
?>