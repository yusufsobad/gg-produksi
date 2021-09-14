<?php

class sContainer_admin extends _page{
	protected static $object = 'sContainer_admin';

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
			'module_reff'
		);

		return $args;
	}

	protected static function table(){
		$data = array();
		$args = self::_array();
		
		$kata = '';$where = "AND module_key='smart_container'";
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
			
			$hapus = array(
				'ID'	=> 'del_'.$id,
				'func'	=> '_delete',
				'color'	=> 'red',
				'icon'	=> 'fa fa-trash',
				'label'	=> 'hapus',
			);

			$divisi = '-';$nik = '-';$name = '-';$time = '-';
			if($val['module_reff']>0){
				$work = gg_production::get_id($val['module_reff'],array('user_id','divisi_id','scan_date'));
				$work = $work[0];

				$divisi = $work['module_value_divi'];
				$nik = employee_admin::_ID_card($work['divisi_user'],$work['no_induk_user'],$work['no_pasok_user']);
				$name = $work['name_user'];
				
				$date = strtotime($work['scan_date']);
				$time = date('H:i',$date);
			}
			
			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'No'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Kode'		=> array(
					'left',
					'10%',
					$val['module_value'],
					true
				),
				'Bagian'		=> array(
					'left',
					'10%',
					$divisi,
					true
				),
				'ID card'		=> array(
					'left',
					'10%',
					$nik,
					true
				),
				'Nama'		=> array(
					'left',
					'auto',
					$name,
					true
				),
				'Waktu Scan'	=> array(
					'left',
					'10%',
					$time,
					true
				),
				'Edit'			=> array(
					'center',
					'10%',
					edit_button($edit),
					false
				),
				'Hapus'			=> array(
					'center',
					'10%',
					hapus_button($hapus),
					false
				)
				
			);
		}
		
		return $data;
	}

	private static function head_title(){
		$args = array(
			'title'	=> 'Smart Container <small>data smart container</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'smart container'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected static function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Smart Container',
			'tool'		=> '',
			'action'	=> parent::action(),
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
	public static function add_form($func='',$load='sobad_portlet'){
		$vals = array(0,'',0,'smart_container',0);
		$vals = array_combine(self::_array(),$vals);

		if($func=='add_0'){
			$func = '_add_db';
		}
		
		$args = array(
			'title'		=> 'Tambah data SC',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> $func,
				'load'		=> $load
			)
		);
		
		return self::_data_form($args,$vals);
	}

	protected static function edit_form($vals=array()){
		$check = array_filter($vals);
		if(empty($check)){
			return '';
		}
		
		$args = array(
			'title'		=> 'Edit data SC',
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
				'label'			=> 'kode SC',
				'class'			=> 'input-circle',
				'value'			=> $vals['module_value'],
				'data'			=> 'placeholder="kode SC"'
			)
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}
}