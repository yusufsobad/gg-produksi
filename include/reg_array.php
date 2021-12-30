<?php
$sobad_data = array(
	'reg_page'			=> array(),
	'reg_sidebar'		=> array(),
	'reg_load'			=> array(),
	'reg_theme'			=> '',
	'reg_script_css'	=> array(),
	'reg_script_js'		=> array(),
	'reg_script_head'	=> array(),
	'reg_script_foot'	=> array(),
	'reg_ajax'			=> array(),
	'reg_exe'			=> ''
);

global_data($sobad_data);

// function registry array
function reg_hook($name,$arr = array()){
	global $sobad_data;
	
	if(isset($sobad_data[$name])){
		if($name=='reg_exe' || $name=='reg_theme'){
			$sobad_data[$name] = $arr;
		}else{
			foreach($arr as $key => $val){
				$sobad_data[$name][$key] = $val;
			}
		}
	}
	global_data($sobad_data);
}

function global_data($data){
	$GLOBALS['sobad_data'] = $data;
	// data array sidebar
	foreach($data as $key => $val){
		$GLOBALS[$key] = $val;
	}
}

class sobad_page extends _error{
	protected static $page = '';

	public function __construct($page=''){
		if(empty($page)){
			parent::_page404();
		}

		self::$page = $page;
	}

	public function _get(){
		global $reg_page;
		
		$_pages = $reg_page;
		$page = self::$page;

		$call_page = $page;
		foreach($_pages as $key => $val){
			if($val['home']==true){
				$call_page = $key;
				$_pages['Home'] = array(
					'page'	=> $val['page'],
					'theme'	=> isset($val['theme'])?$val['theme']:'default',
					'home'	=> true,
					'view'	=> isset($val['view'])?$val['view']:'folder.file'
				);
			}
		}

		$func = isset($_pages[$page])?$_pages[$page]['page']:'';
		if(isset($reg_page[$page]['theme'])){
			reg_hook('reg_theme',$_pages[$page]['theme']);
		}

		sobad_asset::_loadPage($_pages[$page]);
		if(class_exists($func) && is_callable(array($func,'_reg'))){			
			self::$page = $func;

			if(!isset($_SESSION[_prefix.'page'])){
				$_SESSION[_prefix.'page'] = $call_page;
			}

			$GLOBALS['reg_page'] = $_pages;
			sobad_themes();

			$object = new $func();
			$object->_reg();
		}else{
			session_destroy();
			parent::_page404();
		}
	}

	public function _execute(){
		$page = self::$page;

		if(class_exists($page) && is_callable(array($page,'_page'))){
			$object = new $page();
			$object->_page();
		}else{
			exit;
		}
	}
}