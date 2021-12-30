<?php

(!defined('DEFPATH'))?exit:'';

// registry page
$args = array();
$args['login'] = array(
	'home'	=> true,
	'view'	=> 'Login.login',
	'page'	=> 'login_system'
);

reg_hook('reg_page',$args);