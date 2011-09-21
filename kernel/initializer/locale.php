<?php
if (defined('IN_APP') === false) exit('Access Dead');

Locale::init(array(
	'locale_root' => LOCALE_ROOT,
	'locale_name' => $config['init']['locale']
));
?>