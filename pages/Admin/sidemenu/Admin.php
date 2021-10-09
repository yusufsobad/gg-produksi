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
		'func'		=> '#',
		'child'		=> report_admin()
	);

	$args['tracking-user'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-truck',
		'label'		=> 'Tracking',
		'func'		=> 'tracking_user',
		'child'		=> null
	);
	
	return $args;
}

function setting_admin(){
	$args = array();

	$args['grade'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Grade',
		'func'		=> 'grade_admin',
		'child'		=> null
	);
	
	$args['block'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Block',
		'func'		=> 'block_admin',
		'child'		=> null
	);

	$args['no-bangku'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'No Bangku',
		'func'		=> 'noTable_admin',
		'child'		=> null
	);

	$args['recahan'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Recahan',
		'func'		=> 'recahan_admin',
		'child'		=> null
	);

	$args['scanner'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Scanner',
		'func'		=> 'scanner_admin',
		'child'		=> null
	);

	$args['divisi'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Divisi',
		'func'		=> 'divisi_admin',
		'child'		=> null
	);

	$args['default-SC'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Default SC',
		'func'		=> 'defaultSC_admin',
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

function report_admin(){
	$args = array();

	$args['report-operator'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Operator',
		'func'		=> 'report_admin',
		'child'		=> null
	);
	
	$args['report-skt'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'SKT Online',
		'func'		=> 'reportSKT_admin',
		'child'		=> null
	);

	return $args;
}