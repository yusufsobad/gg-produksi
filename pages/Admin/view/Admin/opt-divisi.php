<?php

class divisi_admin extends _page{
	protected static $object = 'divisi_admin';

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
		
		$kata = '';$where = "AND module_key='divisi'";
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

			$qty = gg_employee::count("divisi='$id'");

			$edit = array(
				'ID'	=> 'edit_'.$id,
				'func'	=> '_edit',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-edit',
				'label'	=> 'edit'
			);

			$detail = array(
				'ID'	=> 'detail_'.$id,
				'func'	=> '_detail',
				'color'	=> '',
				'icon'	=> '',
				'label'	=> $qty.' Orang'
			);

			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'No'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Name'		=> array(
					'left',
					'auto',
					$val['module_value'],
					true
				),
				'Kode'	=> array(
					'center',
					'10%',
					$val['module_note'],
					true
				),
				'Jumlah'	=> array(
					'center',
					'15%',
					_modal_button($detail),
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
			'title'	=> 'Bagian <small>data bagian</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'bagian'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected static function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Bagian',
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
			'title'		=> 'Edit data bagian',
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
				'data'			=> 'placeholder="Nama Bagian"'
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'module_note',
				'label'			=> 'Kode',
				'class'			=> 'input-circle',
				'value'			=> $vals['module_note'],
				'data'			=> 'placeholder="Kode Bagian"'
			)
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	public static function _detail($id=0){
		$id = str_replace('detail_', '', $id);
		intval($id);

		$args = gg_employee::get_all(array('picture','no_induk','name','divisi','no_meja'),"AND divisi='$id'");

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach ($args as $key => $val) {
			$no += 1;

			$image = empty($val['notes_pict'])?'no-profile.jpg':$val['notes_pict']; 

			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'no'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Profile'	=> array(
					'left',
					'5%',
					'<img src="asset/img/user/'.$image.'" style="width:100%">',
					true
				),
				'ID'		=> array(
					'left',
					'10%',
					employee_admin::_ID_card($val['divisi'],$val['no_induk']),
					true
				),
				'Nama'		=> array(
					'left',
					'auto',
					$val['name'],
					true
				),
				'Bagian'		=> array(
					'left',
					'15%',
					$val['module_value_divi'],
					true
				),
				'No Meja'		=> array(
					'left',
					'10%',
					'NBK'.sprintf('%04d',$val['module_value_no_m']),
					true
				)
			);
		}

		$args = array(
			'title'		=> 'Detail data',
			'button'	=> '_btn_modal_save',
			'status'	=> array(),
			'func'		=> array('sobad_table'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}
}