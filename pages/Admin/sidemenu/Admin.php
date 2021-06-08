<?php

function sidemenu_admin(){
	$args = array();
	$args['dashboard'] = array(
		'status'	=> 'active',
		'icon'		=> 'icon-home',
		'label'		=> 'Dashboard',
		'func'		=> 'dash_admin',
		'child'		=> null
	);
	
	$args['option'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-gear',
		'label'		=> 'Setting',
		'func'		=> 'setting_admin',
		'child'		=> null
	);

	$args['report'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-book',
		'label'		=> 'Report',
		'func'		=> 'report_admin',
		'child'		=> null
	);
	
	return $args;
}