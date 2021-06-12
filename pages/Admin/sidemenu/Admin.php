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
		'func'		=> '#',
		'child'		=> setting_admin()
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

function setting_admin(){
	$args = array();
	$args['process'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Proses',
		'func'		=> 'process_admin',
		'child'		=> null
	);

	$args['smart-container'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Smart Container',
		'func'		=> 'sContainer_admin',
		'child'		=> null
	);

	return $args;
}