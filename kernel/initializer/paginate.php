<?php
if (defined('IN_APP') === false) exit('Access Dead');

Paginate::init(array(
	'per_page' => $config['init']['per_page']
));
?>