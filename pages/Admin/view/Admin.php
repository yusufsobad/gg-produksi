<?php
require 'Admin/include.php';

class dash_admin{
	private static function head_title(){
		$args = array(
			'title'	=> 'Dashboard <small>reports & statistics</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> 'dash_marketing',
					'label'	=> 'dashboard'
				)
			),
			'date'	=> false
		);
		
		return $args;
	}

	public static function _sidemenu(){
		$label = array();
		$data = array();
		$metronic = new metronic_layout();
		
		$data[] = array(
			'style'		=> array(),
			'script'	=> array(),
			'func'		=> 'sobad_dashboard',
			'data'		=> self::_data()
		);
		
		$title = self::head_title();
		
		ob_start();
		$metronic->_head_content($title);
		$metronic->_content('_panel',$data);
		return ob_get_clean();
	}

	private static function _data(){
		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> '',
				'color'		=> 'red-intense',
				'qty'		=> '',
				'desc'		=> '',
				'func'		=> ''
			)
		);
		
		return $dash;
	}
}