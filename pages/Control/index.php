<?php

$args = array();
$args['Login'] = array(
	'page'	=> 'control_gg',
	'home'	=> true,
	'theme'	=> 'control'
);
reg_hook('reg_page',$args);

class control_gg{

	public function _reg(){
		$url = get_page_url();
		$url = empty($url)?'login':$url;

		if(!isset($_SESSION[_prefix.'page'])){
			$pages = new sobad_page($url);
			$pages->_get();
		}
	}
}