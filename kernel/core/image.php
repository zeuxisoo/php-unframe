<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Image {

	const IS_RESIZE = 1;

	private static $instance = null;

	private $save_root 	 = "";	// overwrite orgin file if empty
	private $prefix_name = "";

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
	
	public function single_resize($image_path, $width, $height) {
		$image_size = getimagesize($image_path);
		$current_image = array(
			'width' => $image_size[0],
			'height' => $image_size[1],
			'type' => $image_size[2]
		);
		unset($image_size);

		if ($current_image['width'] < $width && $current_image['height'] < $height) {
			$new_width = $current_image['width'];
			$new_height = $current_image['height'];
		}

		if($current_image['width'] > $current_image['height']) {
			$new_width = $width;
			$new_height = round(($width / $current_image['width']) * $current_image['height']);
		}else{
			$new_height = $height;
			$new_width = round(($height / $current_image['height']) * $current_image['width']);
		}

		$file_extension = $this->file_extension(basename($image_path));

		if (in_array(strtolower($file_extension), array('jpg', 'gif', 'png')) === true) {
			$image = imagecreatetruecolor($new_width, $new_height);
			$background = imagecolorallocate($image, 255, 255, 255);
			imagefill($image, 0, 0, $background);
			
			$src = null;
			if ($file_extension == 'jpg') {
				$src = imagecreatefromjpeg($image_path);
			}elseif ($file_extension == 'gif') {
				$src = imagecreatefromgif($image_path);
			}elseif($file_extension == 'png') {
				$src = imagecreatefrompng($image_path);
			}
			
			imagecopyresampled($image, $src, 0, 0, 0, 0, $new_width, $new_height, $current_image['width'], $current_image['height']);

			$save_root = trim($this->save_root);
			$file_name = basename($image_path);

			// Add prefix name in filename
			if (empty($this->prefix_name) === false) {
				$file_name = $this->prefix_name.$file_name;
			}

			// Declare save_path, save to current path or different path
			// If current path exists same name file will overwrite it
			if (empty($this->save_root) === false) {
				if (is_dir($this->save_root) === false && file_exists($this->save_root) == false) {
					mkdir($this->save_root, 0777, true);
				}

				$save_path = $save_root.'/'.$file_name;
			}else{
				$save_path = dirname($image_path).'/'.$file_name;
			}

			if($file_extension == 'jpg') {
				imagejpeg($image, $save_path, 100);
			}elseif($file_extension == 'gif') {
				imagegif($image, $save_path);
			}elseif($file_extension == 'png') {
				imagepng($image, $save_path, 0, null);
			}
			
			imagedestroy($image);
			imagedestroy($src);
			
			return array(
				'status' => true,
				'orgin_file' => array(
					'name' => basename($image_path),
					'path' => $image_path,
				),
				'resized_file' => array(
					'name' => basename($save_root),
					'path' => $save_path,
				)
			);
		}else{
			return false;
		}
	}

	public function multi_resize($image_paths, $width, $height) {
		$status = array();

		if (is_array($image_paths) === true) {
			foreach($image_paths as $path) {
				$status[] = self::single_resize($path, $width, $height);
			}
		}

		return $status;
	}

}
?>