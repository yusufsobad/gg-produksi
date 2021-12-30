<?php
(!defined('AUTHPATH'))?exit:'';

require dirname(__FILE__).'/defined.php';
require dirname(__FILE__).'/../err.php';
require dirname(__FILE__).'/../conn.php';
require dirname(__FILE__).'/../class_db/sync_db.php';

function get_uri(){
	$uri = $_SERVER['REQUEST_URI'];
	$uri = str_replace('/' . URL . '/', '', $uri);

	if(empty($uri)){
		return '';
	}

	$uri = explode('/', $uri);
	return $uri[0];
}

function get_home(){
	return HOSTNAME.'/'.URL;
}

function get_page_url(){
	$url = '';
	if(defined('load_menu')){
		$url = constant('load_menu');
	}

	return $url;
}

function load_first_page($key=''){
	global $reg_page;

	// include pages
	$page = $_SESSION[_prefix.'page'];
	sobad_asset::_loadPage($reg_page[$page]);
}

function get_home_func($key=''){

	if(class_exists($key)){
		return $key;
	}

	global $reg_page;

	$prefix = constant("_prefix");
	$page = $_SESSION[$prefix.'page'];

	$func = $reg_page[$page]['page'];
	$object = new $func();
	$object->_reg();

	global $reg_sidebar;

	$child = get_side_active($reg_sidebar,$key);
	if($child!=false){
		if(isset($child['loc'])){
			sobad_asset::_loadFile($child['loc'].'.'.$child['func']);
		}
	}

	return $child['func'];
}

function get_side_active($args=array(),$func=''){	
	foreach ($args as $key => $val) {
		if(empty($func)){
			if($val['status']=='active'){
				return $val;
				break;
			}
		}else{	
			if($key==$func){
				return $val;
				break;
			}
		}

		if($val['child']!=null){
			$child = get_side_active($val['child'],$func);

			if(!empty($child)){
				return $child;
			}
		}
	}

	return false;
}

class hostname{
	public function __construct(){

		if(!empty(ABOUT)){
			$server = get_home();
			$host = self::get_hostname();

			if($server!=$host){
				die('Halaman ini di akses tidak dengan semestinya!!!');
			}
		}

	// Include File Component	
		require dirname(__FILE__).'/session.php';
		require dirname(__FILE__).'/option.php';
		require dirname(__FILE__).'/../../function.php';

	// Get Define
		new _config_define();	
	}

	private function get_hostname(){
		$where = "WHERE config_name='siteurl'";
		$host = self::_get_about($where,array('config_value'));
		return $host[0]['config_value'];
	}

	private function _get_about($where='',$args=array()){
		$email = array();
		$q = sobad_db::_select_table($where,ABOUT,$args);
		if($q!==0){
			while($r=$q->fetch_assoc()){
				$item = array();
				foreach($r as $key => $val){
					$item[$key] = $val;
				}
				
				$email[] = $item;
			}
		}
		
		return $email;
	}
}