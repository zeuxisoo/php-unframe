<?php
if (defined('IN_APP') === false) exit('Access Dead');

Secure::init(array(
	'csrf_token_ttl'   => 7200,
	'csrf_encrypt_key' => $config['init']['csrf_encrypt_key'],
));
