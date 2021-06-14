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

	$args['employee'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-users',
		'label'		=> 'Karyawan',
		'func'		=> 'employee_admin',
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
	$args['recahan'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Recahan',
		'func'		=> 'recahan_admin',
		'child'		=> null
	);

	$args['process'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Process',
		'func'		=> 'process_admin',
		'child'		=> null
	);

	$args['divisi'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Divisi',
		'func'		=> 'divisi_admin',
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