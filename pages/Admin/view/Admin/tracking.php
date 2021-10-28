<?php

class tracking_user{
	protected static $object = 'tracking_user';

	protected static $table = 'gg_production';

	private static function head_title(){
		$args = array(
			'title'	=> 'Tracking <small>data tracking</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'tracking'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected static function get_form(){		
		$box = array(
			'label'		=> 'Form Tracking',
			'tool'		=> '',
			'action'	=> '',
			'object'	=> self::$object,
			'func'		=> '_form',
			'data'		=> ''
		);

		return $box;
	}

	protected static function get_box(){		
		$box = array(
			'ID'		=> 'report_tracking',
			'label'		=> 'Tracking User',
			'tool'		=> '',
			'action'	=> '',
			'func'		=> 'sobad_table',
			'data'		=> array(
				'class'		=> '',
				'table'		=> array()
			)
		);

		return $box;
	}

	public static function _sidemenu(){
		$metronic = new metronic_layout();
	
		$title = self::head_title();
		$data = array();
	
		$data[] = array(
			'style'		=> array(),
			'script'	=> array(),
			'func'		=> '_portlet',
			'data'		=> self::get_form()
		);

		$data[] = array(
			'style'		=> array(),
			'script'	=> array(),
			'func'		=> '_portlet',
			'data'		=> self::get_box()
		);
	
		ob_start();
		$metronic->_head_content($title);
		$metronic->_content('_panel',$data);
		return ob_get_clean();
	}

	public static function _form(){
		$user = gg_employee::get_all(array('ID','name'),"AND divisi='1'");
		$user = convToOption($user,'ID','name');

		$data = array(
			array(
				'func'			=> 'opt_datepicker',
				'key'			=> '_date',
				'label'			=> 'Tanggal',
				'class'			=> 'input-circle',
				'value'			=> date('Y-m-d')
			),
			array(
				'func'			=> 'opt_select',
				'data'			=> $user,
				'key'			=> 'operator',
				'label'			=> 'Gilling',
				'class'			=> 'input-circle',
				'searching'		=> true,
				'select'		=> 0,
			),
		);

		?>
			<div class="row">
				<?php metronic_layout::sobad_form($data); ?>
				<div style="text-align: right;margin-right: 20px;">
					<button data-sobad="_view" data-load="report_tracking" data-type="" type="button" class="btn blue" data-dismiss="modal" onclick="sobad_submitLoad(this)" ><i class="fa fa-book"></i> Report</button>
				</div>
			</div>
		<?php
	}

	// ----------------------------------------------------------
	// Print data report ----------------------------------------
	// ----------------------------------------------------------

	public static function _view($data=array()){
		return 'aaaa';
	}
}