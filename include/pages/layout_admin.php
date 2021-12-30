<?php

function portlet_admin($opt = array(),$args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$title = isset($opt['title'])?$opt['title']:'';
	$data = array();
	
	$data[] = array(
		'style'		=> isset($opt['style'])?$opt['style']:'',
		'script'	=> isset($opt['script'])?$opt['script']:'',
		'func'		=> '_portlet',
		'data'		=> $args
	);
	
	ob_start();
	theme_layout('_head_content',$title);
	theme_layout('_panel',$data);
	return ob_get_clean();
}

function tabs_admin($opt = array(),$args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$title = isset($opt['title'])?$opt['title']:'';
	$data = array();
	
	$data[] = array(
		'style'		=> isset($opt['style'])?$opt['style']:'',
		'script'	=> isset($opt['script'])?$opt['script']:'',
		'func'		=> '_tabs',
		'data'		=> $args
	);
	
	ob_start();
	theme_layout('_head_content',$title);
	theme_layout('_panel',$data);
	return ob_get_clean();
}

function modal_admin($args = array()){	
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}

	ob_start();
	theme_layout('_modal_content',$args);
	return ob_get_clean();
}

function table_admin($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		$args = array(
			'class'	=> '',
			'table'	=> array()
		);
	}

	ob_start();
	theme_layout('sobad_table',$args);
	return ob_get_clean();
}