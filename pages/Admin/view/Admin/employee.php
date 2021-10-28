<?php

class employee_admin extends _file_manager{
	protected static $object = 'employee_admin';

	protected static $table = 'gg_employee';

	protected static $file_type = 'profile';

	protected static $url = '../asset/img/user';

	// ----------------------------------------------------------
	// Layout category  -----------------------------------------
	// ----------------------------------------------------------

	public static function _array(){
		$args = array(
			'ID',
			'name',
			'no_induk',
			'grade',
			'no_meja',
			'divisi',
			'picture',
			'nickname',
			'capacity',
			'under_capacity'
		);

		return $args;
	}

	public static function _ID_card($div=0,$ind=0){
		$ind = sprintf('%06d',$ind);
		return $div.$ind;
	}

	public static function _filter_search($field='',$search=''){
		if(in_array($field,array('name','no_induk','grade','no_meja','divisi','picture','nickname','capacity','under_capacity'))){
			return "`ggk-employee`.$field LIKE '%$search%'";
		}
	}

	protected static function table(){
		$data = array();
		$args = self::_array();

		$start = intval(self::$page);
		$nLimit = intval(self::$limit);

		$tab = parent::$type;
		$type = str_replace('employee_', '', $tab);

		$where = "AND divisi='$type'";
		$kata = '';$_search = '';
		if(self::$search){
			$src = self::like_search($args,$where);
			$cari = $src[0];
			$where = $src[0];
			$kata = $src[1];
			$_search = $src[2];
		}else{
			$cari=$where;
		}
		
		$limit = 'ORDER BY no_induk ASC LIMIT '.intval(($start - 1) * $nLimit).','.$nLimit;
		$where .= $limit;

		$object = self::$table;
		$sum_data = $object::count("1=1 ".$cari,$args);
		$args = $object::get_all($args,$where);

		$data['data'] = array('data' => $kata, 'value' => $_search ,'type' => $tab);
		$data['search'] = array('Semua','nama','no induk','grade');
		$data['class'] = '';
		$data['table'] = array();
		$data['page'] = array(
			'func'	=> '_pagination',
			'data'	=> array(
				'start'		=> $start,
				'qty'		=> $sum_data,
				'limit'		=> $nLimit,
				'type'		=> $tab
			)
		);

		$no = ($start-1) * $nLimit;
		$now = time();
		foreach($args as $key => $val){
			$no += 1;

			$color = 'green';$title = 'normal';$func = '_underCapacity';
			if($val['under_capacity']==1){
				$color = 'yellow';$title = 'under';$func = '_normalCapacity';
			}

			$status = array(
				'ID'	=> 'status_'.$val['ID'],
				'func'	=> $func,
				'color'	=> $color,
				'icon'	=> 'fa fa-book',
				'label'	=> $title,
				'type'	=> $tab
			);

			$edit = array(
				'ID'	=> 'edit_'.$val['ID'],
				'func'	=> '_edit',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-edit',
				'label'	=> 'edit',
				'type'	=> $tab
			);

			$image = empty($val['notes_pict'])?'no-profile.jpg':$val['notes_pict'];

			$target = $val['module_note_grad'];
			if($val['under_capacity']==1){
				$target = !empty($val['capacity'])?$val['capacity']:$val['module_note_grad'];
			}
			
			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'No'		=> array(
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
				'Grade'		=> array(
					'left',
					'10%',
					"Grade ".$val['module_value_grad'],
					true
				),
				'No Induk'		=> array(
					'right',
					'10%',
					self::_ID_card($val['module_note_divi'],$val['no_induk']),
					true
				),
				'Nama'		=> array(
					'left',
					'auto',
					$val['name'],
					true
				),
				'Panggilan'		=> array(
					'left',
					'15%',
					$val['nickname'],
					true
				),
				'Target'		=> array(
					'right',
					'12%',
					format_nominal($target)." batang",
					true
				),
				'Meja'		=> array(
					'right',
					'10%',
					'NBK'.sprintf('%04d',$val['module_value_no_m']),
					true
				),
				'Status'	=> array(
					'center',
					'8%',
					_click_button($status),
					false
				),
				'Edit'		=> array(
					'center',
					'8%',
					edit_button($edit),
					false
				)
			);
		}
		
		return $data;
	}

	private static function head_title(){
		$args = array(
			'title'	=> 'Karyawan <small>data Karyawan</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'karyawan'
				)
			),
			'date'	=> false,
			'modal'	=> 3
		); 
		
		return $args;
	}

	protected static function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Karyawan',
			'tool'		=> '',
			'action'	=> self::action(),
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected static function layout(){
		$divisi = gg_module::_gets('divisi',array('ID','module_value'));
		$check = array_filter($divisi);
		if(empty($check)){
			$status = 'Go To Option - <a href="/'.URL.'/divisi"> Divisi</a>'; 
			return $status;
		}

		parent::$type = 'employee_'.$divisi[0]['ID'];
		$box = self::get_box();

		$tabs = array();$no = -1;
		foreach ($divisi as $key => $val) {
			$no += 1;
			$tabs[$no] = array(
				'key'	=> 'employee_'.$val['ID'],
				'label'	=> $val['module_value'],
				'qty'	=> gg_employee::count("divisi='".$val['ID']."'")
			);
		}

		$tabs = array(
			'tab'	=> $tabs,
			'func'	=> '_portlet',
			'data'	=> $box
		);

		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(),
			'script'	=> array('')
		);
		
		return tabs_admin($opt,$tabs);
	}

	protected static function action(){
		$import = array(
			'ID'	=> 'import_0',
			'func'	=> 'import_form',
			'color'	=> 'btn-default',
			'load'	=> 'here_modal2',
			'icon'	=> 'fa fa-file-excel-o',
			'label'	=> 'Import',
			'spin'	=> false,
			'type'	=> self::$type
		);
		
		$import = apply_button($import);

		$add = array(
			'ID'	=> 'add_0',
			'func'	=> 'add_form',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-plus',
			'label'	=> 'Tambah',
			'type'	=> self::$type
		);
		
		$add = edit_button($add);

		return $import.$add;
	}

	public static function _conv_status($id=0){
		$label = gg_module::get_id($id,array('module_value'));
		$check = array_filter($label);
		if(empty($check)){
			return 'Undefined';
		}

		return $label[0]['module_value'];
	}

	// ----------------------------------------------------------
	// Form data category ---------------------------------------
	// ----------------------------------------------------------

	public static function import_form(){
		$data = array(
			'id'	=> 'importForm',
			'cols'	=> array(3,8),
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ajax',
				'value'			=> '_import'
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'object',
				'value'			=> self::$object
			),
			array(
				'id'			=> 'file_import',
				'func'			=> 'opt_file',
				'type'			=> 'file',
				'key'			=> 'data',
				'label'			=> 'Filename',
				'accept'		=> '.csv',
				'data'			=> ''
			)
		);
		
		$args = array(
			'title'		=> 'Import Karyawan',
			'button'	=> '_btn_modal_import',
			'status'	=> array(
				'id'		=> 'importForm',
				'link'		=> 'import_file',
				'load'		=> 'sobad_portlet',
				'type'		=> $_POST['type']
			)
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	public static function add_form($func='',$load='sobad_portlet'){
		$no = gg_employee::get_maxNIK();
		$no = sprintf("%04d",$no+1);

		$div = str_replace('employee_', '', $_POST['type']);

		$vals = array(0,'',$no,0,1,$div,0,0,0);
		$vals = array_combine(self::_array(), $vals);

		if($func=='add_0'){
			$func = '_add_db';
		}
		
		$args = array(
			'title'		=> 'Tambah data karyawan',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> $func,
				'load'		=> $load,
				'type'		=> $_POST['type']
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
			'title'		=> 'Edit data karyawan',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_update_db',
				'load'		=> 'sobad_portlet',
				'type'		=> $_POST['type']
			)
		);
		
		return self::_data_form($args,$vals);
	}

	private static function _data_form($args=array(),$vals=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		$idm = $vals['no_meja'];
		$meja = array();
		$mdl = gg_module::_gets('no_meja',array('ID','module_value'),"AND (module_reff='0' OR ID='$idm')");
		foreach ($mdl as $key => $val) {
			$meja[$val['ID']] = "NBK".sprintf("%04d",$val['module_value']);
		}

		$meja[0] = 'Tidak Ada';

		$divisi = gg_module::_gets('divisi',array('ID','module_value'));
		$divisi = convToOption($divisi,'ID','module_value');

		$grade = array();
		$grades = gg_module::_gets('grade',array('ID','module_value'));
		foreach ($grades as $key => $val) {
			$grade[$val['ID']] = "Grade ".$val['module_value'];
		}

		reset($grade);
		$target = self::option_grade(key($grade));

		$tab1 = array(
			0	=> array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $vals['ID']
			),
			array(
				'id'			=> 'picture-employee',
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'picture',
				'value'			=> $vals['picture']
			),
			array(
				'func'			=> 'opt_select',
				'data'			=> $grade,
				'key'			=> 'grade',
				'label'			=> 'Grade',
				'class'			=> 'input-circle',
				'select'		=> $vals['grade'],
				'status'		=> 'data-sobad="option_grade" data-load="grade_user" data-attribute="val" '
			),
			array(
				'ID'			=> 'grade_user',
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> '_target',
				'label'			=> 'Target (Default)',
				'class'			=> 'input-circle',
				'value'			=> $target,
				'data'			=> 'readonly'
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'no_induk',
				'label'			=> 'NIK',
				'class'			=> 'input-circle',
				'value'			=> $vals['no_induk'],
				'data'			=> ''
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'name',
				'label'			=> 'Nama',
				'class'			=> 'input-circle',
				'value'			=> $vals['name'],
				'data'			=> 'placeholder="Nama Karyawan"'
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'nickname',
				'label'			=> 'Nama Panggilan',
				'class'			=> 'input-circle',
				'value'			=> $vals['nickname'],
				'data'			=> 'placeholder="Nama Panggilan"'
			),
			array(
				'func'			=> 'opt_select',
				'data'			=> $meja,
				'key'			=> 'no_meja',
				'label'			=> 'No Bangku',
				'searching'		=> true,
				'class'			=> 'input-circle',
				'select'		=> $vals['no_meja'],
			),
			array(
				'id'			=> 'divisi',
				'func'			=> 'opt_select',
				'data'			=> $divisi,
				'key'			=> 'divisi',
				'label'			=> 'Bagian',
				'class'			=> 'input-circle',
				'select'		=> $vals['divisi'],
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'price',
				'key'			=> 'capacity',
				'label'			=> 'Target',
				'class'			=> 'input-circle',
				'value'			=> $vals['capacity'],
				'data'			=> ''
			),
		);	

		$data = array(
			'menu'		=> array(
				0	=> array(
					'key'	=> '',
					'icon'	=> 'fa fa-bars',
					'label'	=> 'General'
				),
			),
			'content'	=> array(
				0	=> array(
					'func'	=> '_layout_form',
					'object'=> self::$object,
					'data'	=> array($tab1,$vals['picture'])
				),
			)
		);
		
		$args['func'] = array('_inline_menu');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	public static function _layout_form($args=array()){
		$picture = $args[1];
		$args = $args[0];

		$image = 'no-profile.jpg';
		if($picture!=0){
			$image = sobad_post::get_id($picture,array('notes'));
			$image = $image[0]['notes'];
		}

		?>
			<style type="text/css">
				.col-md-3.box-image-show:hover > a.remove-image-show {
				    opacity: 1;
				}

				.col-md-3.box-image-show:hover > a.change-image-show {
				    opacity: 1;
				}

				a.change-image-show {
				    position: absolute;
				    opacity: 0;
				    top: 50%;
				    left: 40%;
				}

				a.change-image-show>i {
				    font-size: 50px;
				    color: #333;
				}

				a.remove-image-show {
				    position: absolute;
				    right: 7px;
				    top: -7px;
				    opacity: 0;
				}

				a.change-image-show:hover > i {
				    opacity: 0.8;
				}

				a.remove-image-show:hover {
				    border: 1px solid #dfdfdf;
				    padding: 3px;
				}

				.box-image-show{
					cursor:default;
				}

				.box-image-show>img {
				    border-radius: 20px !important;
				}
			</style>

			<div class="row" style="padding-right: 20px;">
				<div class="col-md-3 box-image-show">
					<a class="remove-image-show" href="javascript:" onclick="remove_image_profile()">
						<i style="font-size: 24px;color: #e0262c;" class="fa fa-trash"></i>
					</a>

					<a data-toggle="modal" data-sobad="_form_upload" data-load="here_modal2" data-type="" data-alert="" href="#myModal2" class="change-image-show" onclick="sobad_button(this,0)">
						<i class="fa fa-upload"></i>
					</a>

					<img src="asset/img/user/<?php print($image) ;?>" style="width:100%" id="profile-employee">
				</div>
				<div class="col-md-9">
					<?php metronic_layout::sobad_form($args) ;?>
				</div>
			</div>

			<script type="text/javascript">
				function remove_image_profile(){
					$('#profile-employee').attr('src',"asset/img/user/no-profile.jpg");
					$('#picture-employee').val(0);
				}

				function set_file_list(val){
					select_file_list(val,false);
					$("#myModal2").modal('hide');

					$('#profile-employee').attr('src',_select_file_list[0]['url']);
					$('#picture-employee').val(_select_file_list[0]['id']);
				}
			</script>
		<?php
	}

	public static function option_grade($id=0){
		$grade = gg_module::get_id($id,array('module_note'));
		$check = array_filter($grade);

		if(!empty($check)){
			$grade = $grade[0]['module_note'];
		}else{
			$grade = 0;
		}

		return format_nominal($grade);
	}

	public static function _form_upload(){

		$args = array(
			'title'		=> 'Select Photo Profile',
			'button'	=> '',
			'status'	=> array(
				'link'		=> '',
				'load'		=> ''
			)
		);

		return parent::_item_form($args);
	}

	// ----------------------------------------------------------
	// Function category to database -----------------------------
	// ----------------------------------------------------------

	public static function _normalCapacity($id=0){
		return self::_changeCapacity($id,0);
	}

	public static function _underCapacity($id=0){
		return self::_changeCapacity($id,1);
	}

	protected static function _changeCapacity($id=0,$status=0){
		self::$type = $_POST['type'];
		$id = str_replace('status_', '', $id);

		$q = sobad_db::_update_single($id,'ggk-employee',array('ID' => $id,'under_capacity' => $status));

		if($q>0){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return self::_get_table($pg);
		}
	}

	protected static function _callback($args=array(),$_args=array()){
		// Update module meja
		if($args['no_meja']>0){
			sobad_db::_update_single($args['no_meja'],'ggk-module',array('module_reff' => 1));
		}

		$args['status'] = 1;
		return $args;
	}

	// ----------------------------------------------------------
	// Function Import Data -------------------------------------
	// ----------------------------------------------------------

	protected static function _check_import($files=array()){
		$divisi = gg_module::_gets('divisi',array('ID','module_value'));
		self::$type = 'employee_'.$divisi[0]['ID'];

		$check = array_filter($files);
		if(empty($check)){
			return array(
				'status'	=> false,
				'data'		=> $files,
				'insert'	=> false
			);
		}

		$files = self::_convert_column($files);

		if(!isset($files['status'])){
			$files['status'] = 1;
		}

		return self::_conv_import($files);
	}

	public static function _conv_import($files=array()){
		$files['ID'] = 0;
		if(isset($files['no_induk']) && !empty($files['no_induk'])){
			$status = false;
			$no_idk = $files['no_induk'];
			$user = gg_employee::get_all(array('ID'),"AND no_induk='$no_idk'");

			$check = array_filter($user);
			if(!empty($check)){
				$files['ID'] = $user[0]['ID'];
				$status = true;
			}

			return array(
				'status'	=> $status,
				'data'		=> $files,
				'insert'	=> true
			);
		}else{
			$status = false;
			$name = $files['name'];
			$user = gg_employee::get_all(array('ID'),"AND name='$name'");
			
			$check = array_filter($user);
			if(!empty($check)){
				$files['ID'] = $user[0]['ID'];
				$status = true;
			}

			return array(
				'status'	=> $status,
				'data'		=> $files,
				'insert'	=> true
			);
		}
	}

	private static function _convert_column($files=array()){
		$data = array();

		$args = array(
			'no_induk'		=> array(
				'data'			=> array('nik','no induk','induk karyawan','no induk karyawan'),
				'type'			=> 'number'
			),
			'name'			=> array(
				'data'			=> array('nama','nama lengkap','nama karyawan'),
				'type'			=> 'text'
			),
			'nickname'		=> array(
				'data'			=> array('nama pendek','nama panggilan','panggilan','nickname'),
				'type'			=> 'text'
			),
			'no_meja'		=> array(
				'data'			=> array('nbk','no meja','no bangku'),
				'type'			=> 'number'
			),
			'grade'			=> array(
				'data'			=> array('grade','grd','tingkatan'),
				'type'			=> 'number'
			),
		);

		foreach ($args as $key => $val) {
			foreach ($files as $ky => $vl) {
				$_data = '';
				if(in_array($ky, $val['data'])){
					$_data = self::_filter_column($key,$vl,$val['type']);
					$data = array_merge($data,$_data);

					unset($files[$ky]);
					break;
				}
			}
		}

		return $data;
	}

	private static function _filter_column($key='',$_data='',$type=''){
		$data = array();
		switch ($key) {
			case 'name':
			case 'nickname':
				$_data = strtolower($_data);
				$_data = ucwords($_data);
				
				break;

			case 'no_meja':
				$args = gg_module::_gets('no_meja',array('ID','module_reff'),"AND module_value='$_data'");
				
				$check = array_filter($args);
				if(empty($check)){
					$_data = sobad_db::_insert_table('ggk-module',array('module_key' => 'no_meja','module_value' => $_data,'module_reff' => 1 ));
				}else{
					if($args[0]['module_reff']==0){
						sobad_db::_update_single($args[0]['ID'],'ggk-module',array('module_key' => 'no_meja','module_reff' => 1 ));

						$_data = $args[0]['ID'];
					}else{
						$_data = 0;
					}
				}
				
				break;

			case 'grade':
				$args = gg_module::_gets('grade',array('ID'),"AND module_value='$_data'");
				
				$check = array_filter($args);
				if(empty($check)){
					$_data = sobad_db::_insert_table('ggk-module',array('module_key' => 'grade','module_value' => strtoupper($_data) ));
				}else{
					$_data = $args[0]['ID'];
				}
				
				break;

			case 'no_induk':
				$div = _treacibility::_check_divisi($_data);
				$_data = (int) substr($_data, 2,6);
				
				$data['divisi'] = formatting::sanitize($div,'number');
				break;
			
			default:
				// default
				break;
		}

		$data[$key] = formatting::sanitize($_data,$type);

		return $data;
	}

}