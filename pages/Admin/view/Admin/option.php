<?php

class setting_admin extends _page{
	protected static $object = 'setting_admin';

	protected static $table = 'sobad_module';

	// ----------------------------------------------------------
	// Layout ---------------------------------------------------
	// ----------------------------------------------------------

	protected static function _array(){
		$args = array(
			'ID',
			'name',
			'meta_name',
			'detail'
		);

		return $args;
	}

	protected static function table(){
		global $reg_page;

		$data = array();
		$args = self::_array();

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach($reg_page as $key => $vl){
			if(in_array(strtolower($key),array('login','administrator'))){
				continue;
			}

			$module = sobad_module::get_all(array('ID','name','meta_name','detail'),"AND meta_name='$key'");
			$check = array_filter($module);
			if(empty($check)){
				$idx = sobad_db::_insert_table('sdn-module',array(
					'name'		=> ucwords($key),
					'meta_name'	=> strtolower($key)
				));

				$module[0] = array(
					'ID'		=> $idx,
					'name'		=> ucwords($key),
					'meta_name'	=> strtolower($key),
					'user'		=> ''
				);
			}

			$val = $module[0];

			$detail = '';
			if(!empty($val['detail'])){
				$user = unserialize($val['detail']);
				$user = $user['access'];
				$user = implode(',', $user);

				if(!empty($user)){
					$user = kmi_user::get_all(array('name','status'),"AND ID IN ($user)");
					$check = array_filter($user);
					if(!empty($check)){
						$detail = array();
						foreach ($user as $_ky => $_vl) {
							$_status = $_vl['status']==0?'Non Aktif':'Aktif';
							$detail[] = '<strong>'.$_vl['name'].'</strong> ('. $_status . ') ';
						}

						$detail = implode(', ', $detail);
					}
				}
			}

			$no += 1;
			$edit = array(
				'ID'	=> 'edit_'.$val['ID'],
				'func'	=> '_edit',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-edit',
				'label'	=> 'edit'
			);

			$hapus = array(
				'ID'	=> 'del_'.$val['ID'],
				'func'	=> '_delete',
				'color'	=> 'red',
				'icon'	=> 'fa fa-trash',
				'label'	=> 'hapus'
			);
			
			$data['table'][$no-1]['tr'] = array('');
			$data['table'][$no-1]['td'] = array(
				'no'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Nama'		=> array(
					'left',
					'15%',
					$val['name'],
					true
				),
				'Akses'		=> array(
					'left',
					'15%',
					$key,
					true
				),
				'user'		=> array(
					'left',
					'auto%',
					$detail,
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
			'title'	=> 'Option <small>data akses</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'option'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected static function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Akses',
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

		if(!empty($vals['detail'])){
			$user = unserialize($vals['detail']);
			$user = $user['access'];
		}else{
			$user = array();
		}

		$vals['user'] = $user;
		
		$args = array(
			'title'		=> 'Edit akses',
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

		$user = kmi_user::get_all(array('ID','name'),"AND status NOT IN ('0','7') ");	
		$user = convToOption($user,'ID','name');

		$data = array(
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $vals['ID']
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'name',
				'label'			=> 'Nama',
				'class'			=> 'input-circle',
				'value'			=> $vals['name'],
				'data'			=> 'placeholder="Nama"'
			),
			array(
				'func'			=> 'opt_select_tags',
				'data'			=> $user,
				'key'			=> 'user',
				'label'			=> 'Nama',
				'class'			=> 'input-circle',
				'select'		=> $vals['user']
			),
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	// ----------------------------------------------------------
	// Function to database -------------------------------------
	// ----------------------------------------------------------

	public static function _callback($args=array()){
		$user = explode(',',$args['user']);
		$user = serialize(array('access' => $user));
		$args['detail'] = $user;

		return $args;
	}
}