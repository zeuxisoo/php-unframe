<?php
if (defined('IN_APP') === false) exit('Access Dead');

View::init(array(
	"debug" => $config['init']['show_view_error'],
	"view_folder" => VIEW_ROOT,
	"view_cache_folder" => CACHE_ROOT."/view",
	"theme" => $config['init']['default_view_theme'],
));
