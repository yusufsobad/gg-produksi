<?php

class recahan_admin extends _page{
	protected static $object = 'recahan_admin';

	protected static $table = 'gg_module';

	// ----------------------------------------------------------
	// Layout ---------------------------------------------------
	// ----------------------------------------------------------

	protected static function _array(){
		$args = array(
			'ID',
			'module_value',
			'module_note',
			'module_key',
		);

		return $args;
	}

	protected static function table(){
		$data = array();
		$args = self::_array();
		
		$kata = '';$where = "AND module_key='block'";
		if(parent::$search){
			$src = parent::like_search($args,$where);	
			$cari = $src[0];
			$where = $src[0];
			$kata = $src[1];
		}else{
			$cari=$where;
		}
	
		$limit = '';
		$where .= $limit;

		$object = self::$table;
		$sum_data = $object::count("1=1 ".$cari,$args);
		$args = $object::get_all($args,$where);
		
		$data['data'] = array('data' => $kata);
		$data['search'] = array('Semua','nama','kode');
		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach($args as $key => $val){
			$no += 1;
			$id = $val['ID'];

			$edit = array(
				'ID'	=> 'edit_'.$id,
				'func'	=> '_edit',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-edit',
				'label'	=> 'edit'
			);
			
			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'No'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Nama'		=> array(
					'left',
					'auto',
					'Block '.$val['module_value'],
					true
				),
				'Jumlah'		=> array(
					'right',
					'15%',
					format_nominal($val['module_note']).' Batang',
					true
				),
				'Edit'			=> array(
					'center',
					'10%',
					edit_button($edit),
					false
				),
			);
		}
		
		return $data;
	}

	private static function head_title(){
		$args = array(
			'title'	=> 'Recahan <small>data recahan</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'recahan'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected static function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data recahan',
			'tool'		=> '',
			'action'	=> '',
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected static function layout(){
		$box = self::get_box();
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(),
			'script'	=> array('')
		);
		
		return portlet_admin($opt,$box);
	}

	// ----------------------------------------------------------
	// Form data category -----------------------------------
	// ----------------------------------------------------------
	protected static function edit_form($vals=array()){
		$check = array_filter($vals);
		if(empty($check)){
			return '';
		}
		
		$args = array(
			'title'		=> 'Edit data recahan',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_update_db',
				'load'		=> 'sobad_portlet'
			)
		);
		
		return self::_data_form($args,$vals);
	}

	private static function _data_form($args=array(),$vals=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		$data = array(
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $vals['ID']
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'module_key',
				'value'			=> $vals['module_key']
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'module_value',
				'label'			=> 'Nama',
				'class'			=> 'input-circle',
				'value'			=> $vals['module_value'],
				'data'			=> 'placeholder="name stock"'
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'price',
				'key'			=> 'module_note',
				'label'			=> 'Recahan',
				'class'			=> 'input-circle',
				'value'			=> format_nominal($vals['module_note']),
				'data'			=> 'placeholder="recahan"'
			)
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}
}