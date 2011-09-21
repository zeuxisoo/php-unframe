<?php
if (defined('IN_APP') === false) exit('Access Dead');

Cache::init(array(
	'adapter' => new File_Adapter(array(
		'cache_root' => CACHE_ROOT.'/file_adapter'
	))
));
?>