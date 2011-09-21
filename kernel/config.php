<?php
if (defined('IN_APP') === false) exit('Access Dead');

$config = array(
	'init' => array(
		'site_url' 			=> 'http://localhost/labs/unframework',
		'attachment_folder'	=> 'attachment',
		'admin_folder'		=> 'admin',
		'timezone'			=> 'Asia/Hong_Kong',
		'header_no_cache'	=> true,
		'show_php_error'	=> true,
		'show_view_error'	=> true,
		'default_view_theme'=> 'default',
		'use_database'		=> true,
	),

	'db' => array(
		'adapter'	=> "mysql",
		'host'		=> "localhost",
		'username'	=> "root",
		'password'	=> "root",
		'database'	=> "test",
		'charset'	=> 'utf-8',
		'port'		=> "3306",
		'prefix'	=> "ufw_",
		'debug'		=> true,
	),

	'view' => array(
		'site_title' => 'Undefined Title',
	),
);
?>