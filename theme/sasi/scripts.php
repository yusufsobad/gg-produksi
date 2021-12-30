<?php
(!defined('THEMEPATH'))?exit:'';

class theme_script{
	public function _get_($func='',$idx=array()){
		if(is_callable(array($this,$func))){
			$script = self::$func($idx);
		}else{
			$script = array();
		}
		
		return $script;
	}
	
	private function lokasi(){
		return 'theme/sasi/asset/';
	}
// BEGIN PAGE LEVEL STYLES ---->
	
	private function _css_page_level($idx=array()){
		$loc = $this->lokasi();
		$css = array(
			'themes-login-soft'	=> $loc.'css/pages/login-soft.css',
			'themes-search'		=> $loc.'css/pages/search.css'
		);
		
		$check = array_filter($idx);
		if(!empty($check)){
			foreach($idx as $key){
				$css[$key];
			}
		}
		
		return $css;
	}
	
	
// BEGIN PAGE STYLES ---->	
	private function _css_page($idx=array()){
		$loc = $this->lokasi();
		$css = array(
			'themes-task'		=> $loc.'css/pages/tasks.css',
		);
		
		$check = array_filter($idx);
		if(!empty($check)){
			foreach($idx as $key){
				$css[$key];
			}
		}
		
		return $css;
	}
	
// BEGIN THEME STYLES ---->
	private function _css_theme($idx=array()){
		$loc = $this->lokasi();
		$css = array(
			'themes-bootstrap'		=> $loc.'css/sasi/bootstrap-sasi.css',
			'themes-primary'		=> $loc.'css/sasi/sasi.css',
			'themes-font'			=> $loc.'css/sasi/sasi-icon.css',
		);
		
		$check = array_filter($idx);
		if(!empty($check)){
			foreach($idx as $key){
				$js[$key];
			}
		}
		
		return $css;
	}	

// BEGIN PAGE LEVEL SCRIPTS ---->
	private function _js_page_level($idx=array()){
		$loc = $this->lokasi();
		$js = array( 
			'themes-sasi'			=> $loc.'js/sasi/sasi.js',
			'themes-login-soft'		=> $loc.'js/pages/login-soft.js',
			'themes-ui-toastr'		=> $loc.'js/pages/ui-toastr.js',
		);
		
		$check = array_filter($idx);
		if(!empty($check)){
			foreach($idx as $key){
				$js[$key];
			}
		}
		
		return $js;
	}	
}