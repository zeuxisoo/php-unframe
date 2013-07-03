<?php
if (defined('IN_APP') === false) exit('Access Dead');

if ($config['init']['use_database'] === true) {
	Database::init($config['db']);
}
