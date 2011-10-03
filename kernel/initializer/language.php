<?php
if (defined('IN_APP') === false) exit('Access Dead');

Language::init(array(
	'language_root' => LANGUAGE_ROOT,
	'language_name' => $config['init']['language']
));
?>