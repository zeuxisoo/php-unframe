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
		'use_database'		=> false,
		'locale'			=> 'zh_HK',
		'per_page'			=> 12,
		'csrf_encrypt_key'	=> 'N._,?Yr3:VCBXpp6--ZIze9+9UcWKsej|f=U[l"/x"_!3:@E',
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

	'mail' => array(
		'smtp_enable'	=> false,
		'smtp_secure' 	=> 'ssl',
		'smtp_auth'   	=> true,
		'smtp_host'  	=> 'gmail.com',
		'smtp_user'   	=> 'user@gmail.com',
		'smtp_pass'  	=> '',
		'smtp_port'   	=> 465,
		'charset'	 	=> 'utf-8',
		'from_address'	=> 'no-reply@no-reply.com',
		'from_username'	=> 'uMailer',
	),
);
?>