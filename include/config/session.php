<?php
(!defined('AUTHPATH'))?exit:'';

function check_language($lang=''){
	if(empty($lang)){
		return '';
	}

	$args = array(
		'id_ID'		=> 'Indonesia',
		'en_US'		=> 'US Amerika',
	);

	if(isset($args[$lang])){
		return true;
	}

	return false;
}

function get_id_user(){
	$prefix = constant('_prefix');
	$user = isset($_SESSION[$prefix.'id'])?$_SESSION[$prefix.'id']:0;
	$user = empty($user)?isset($_COOKIE['id'])?$_COOKIE['id']:$user:$user;

	return $user;
}

function get_picture_user(){
	$picture = isset($_SESSION[_prefix.'picture'])?$_SESSION[_prefix.'picture']:'';
	return $picture;
}

function get_name_user(){
	$user = isset($_SESSION[_prefix.'name'])?$_SESSION[_prefix.'name']:'';
	$user = empty($user)?isset($_COOKIE['name'])?$_COOKIE['name']:$user:$user;

	return $user;
}

function get_divisi_user(){
	$dept = isset($_SESSION[_prefix.'divisi'])?$_SESSION[_prefix.'divisi']:'-';

	return $dept;
}

class _config_define{
	public function __construct(){
		$prefix = constant('_prefix');
		
		// Set Language default
		if(!isset($_SESSION[$prefix.'language'])){
			$_SESSION[constant('_prefix').'language'] = 'id_ID';
		}else{
			$url = get_page_url();
			$url = explode('/', $url);

			if(check_language($url[0])){
				$_SESSION[$prefix.'language'] = $url[0];
			}
		}
	}
}