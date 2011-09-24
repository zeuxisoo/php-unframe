<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Form {

	public static function open($url, $attributes = array()) {
		$default_attributes = array(
			'action' => $url,
			'method' => 'post',
		);

		if (isset($attributes['multipart']) === true) {
			$attributes['enctype'] = 'multipart/form-data';
			unset($attributes['multipart']);
		}

		printf("<form %s>", self::create_attributes(array_merge($default_attributes, $attributes)));
	}
	
	public static function input($name, $value = "", $attributes = array()) {
		$attributes = array_merge(array(
			'type' => 'text',
			'name' => $name,
			'value' => $value,
		), $attributes);

		printf("<input %s />", self::create_attributes($attributes));
	}

	public static function hidden($name, $value = "", $attributes = array()) {
		$default_attributes['type'] = 'hidden';

		self::input($name, $value, array_merge($default_attributes, $attributes));
	}	

	public static function password($name, $value = "", $attributes = array()) {
		$default_attributes['type'] = 'password';

		self::input($name, $value, array_merge($default_attributes, $attributes));
	}

	public static function select($name, $option_tags, $attributes = array()) {
		if (isset($attributes['first_option']) === true) {
			$option_tags = self::option(array('' => $attributes['first_option'])).$option_tags;
			unset($attributes['first_option']);
		}

		$new_select[] = sprintf("<select %s>", self::create_attributes($attributes));
		$new_select[] = $option_tags;
		$new_select[] = "</select>";

		echo join("\n", $new_select);
	}

	public static function option($collections, $selected_key = null) {
		$new_collections = array();
		foreach($collections as $key => $value) {
			if ($key == $selected_key) {
				$new_collections[] = sprintf('<option value="%s" selected="selected">%s</option>', $key, $value);
			}else{
				$new_collections[] = sprintf('<option value="%s">%s</option>', $key, $value);
			}
		}
		return join("\n", $new_collections);
	}

	public static function checkbox($name, $value = "1", $checked = false, $attributes = array()) {
		$default_attributes['type'] = "checkbox";

		if ($checked === true) {
			$default_attributes['checked'] = "checked";
		}

		self::input($name, $value, array_merge($default_attributes, $attributes));
	}

	public static function textarea($name, $value = "", $attributes = array()) {
		$attributes = array_merge(array(
			'name' => $name,
			'rows' => 5
		), $attributes);

		printf("<textarea %s >%s</textarea>", self::create_attributes($attributes), $value);
	}

	public static function submit($value, $attributes = array()) {
		$default_attributes['type'] = 'submit';

		self::input("commit", $value, array_merge($default_attributes, $attributes));

		echo "</form>";
	}

	private static function create_attributes($attributes) {
		$new_options = array();
		foreach($attributes as $option_name => $option_value) {
			$new_options[] = sprintf('%s="%s"', $option_name, $option_value);
		}
		return join(" ", $new_options);
	}

}
?>