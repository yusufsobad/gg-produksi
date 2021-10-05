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
			'no_meja',
			'divisi',
			'picture',
			'nickname'
		);

		return $args;
	}

	public static function _ID_card($div=0,$ind=0){
		$ind = sprintf('%04d',$ind);
		return $div.$ind;
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
		$data['search'] = array('Semua','nama','no induk','no pasok');
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
			$edit = array(
				'ID'	=> 'edit_'.$val['ID'],
				'func'	=> '_edit',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-edit',
				'label'	=> 'edit',
				'type'	=> $tab
			);

			$image = empty($val['notes_pict'])?'no-profile.jpg':$val['notes_pict'];
			
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
				'Bagian'		=> array(
					'left',
					'12%',
					self::_conv_status($val['divisi']),
					true
				),
				'No Induk'		=> array(
					'right',
					'10%',
					$val['no_induk'],
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
					'17%',
					$val['nickname'],
					true
				),
				'Meja'		=> array(
					'right',
					'10%',
					'NBK'.sprintf('%04d',$val['module_value_no_m']),
					true
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

	private function head_title(){
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

	protected function get_box(){
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

	protected function layout(){
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
			'spin'	=> false
		);
		
		$import = '';//apply_button($import);

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

	public function _conv_status($id=0){
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

	public function import_form(){
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

	public function add_form($func='',$load='sobad_portlet'){
		$no = gg_employee::get_maxNIK();
		$no = sprintf("%04d",$no+1);

		$div = str_replace('employee_', '', $_POST['type']);

		$vals = array(0,'',$no,1,$div,0);
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

	protected function edit_form($vals=array()){
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

	private function _data_form($args=array(),$vals=array()){
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

	public function _form_upload(){

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

	protected function _callback($args=array(),$_args=array()){
		// Update module meja
		if($args['no_meja']>0){
			sobad_db::_update_single($args['no_meja'],'ggk-module',array('module_reff' => 1));
		}

		$args['status'] = 1;
		return $args;
	}

}