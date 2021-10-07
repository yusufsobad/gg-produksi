<?php

class _production{

	private static $default = array();

	private static function _default($args=array()){
		$def = gg_module::_gets('default_sc',array('module_note'));
		$def = $def[0]['module_note'];

		$data = array(
			'user_id'		=> 0, 		// ID user login
			'user_name'		=> '-', 	// name user login
			'user_no'		=> '-',		// NIK user login
			'id_pasok2'		=> false,
			'_input'		=> false,
			'pasok'			=> 0,
			'process'		=> '-',
			'operator'		=> '-',
			'operator_id'	=> 0,
			'picture'		=> _treacibility::_check_picture()
		);

		foreach ($args as $key => $val) {
			$data[$key] = isset($data[$key])?$val:'';
		}

		self::$default = $data;
	}

	private static function _check_pasok2($id=0){
		$y = date('Y');$m = date('m');$d = date('d');
		$where = "AND id_pasok2='$id' AND YEAR(inserted)='$y' AND MONTH(inserted)='$m' AND DAY(inserted)='$d'";

		$check = gg_login::get_all(array('ID'),$where);
		$check = array_filter($check);
		if(empty($check)){
			return false;
		}

		return true;
	}

	private static function get_noPasok($user=0){
		$y = date('Y');$m = date('m');$d = date('d');
		$where = "AND user_id='$user' AND YEAR(inserted)='$y' AND MONTH(inserted)='$m' AND DAY(inserted)='$d'";

		$pasok = gg_afkir::get_all(array('ID'),$where);
		return count($pasok);
	}

	public static function scan_login($scan=''){
		$data = self::$default;

		$year = date('Y');$month = date('m');$day = date('d');

		$div = _treacibility::_check_divisi($scan);
		if($div==9){
			$nik = (int) substr($scan,2,4);

			$module = gg_module::get_id($div,array('ID','module_value'));
			$module = $module[0];

			$user = gg_employee::get_all(array('ID','name','picture'),"AND divisi='$div' AND no_induk='$nik'");
			
			$check = array_filter($user);
			if(empty($check)){
				die(_error::_alert_db("User Tidak di temukan !!!"));
			}

			$user = $user[0];

			$data['id_pasok2'] = self::_check_pasok2($user['ID']);
			$data['user_id'] = $user['ID'];
			$data['user_name'] = $user['name'];
			$data['user_no'] = $scan;
			$data['picture'] = _treacibility::_check_picture($user['notes_pict']);

		}else{
			die(_error::_alert_db('ID user tidak terdaftar!!!'));
		}

		return $data;
	}

	public static function sync_operator($scan='',$args=array()){
		$y = date('Y');$m = date('m');$d = date('d');
		self::_default($args);

		$str = base64_decode($scan);
		$str = explode("&&",$str);

		$block = $str[1];
		$leader = $str[2];

		// Update pasok2
		$where = "id_user='$leader' AND id_block='$block' AND YEAR(inserted)='$y' AND MONTH(inserted)='$m' AND DAY(inserted)='$d'";
		$q = sobad_db::_update_multiple($where,'ggk-login-user',array('id_pasok2' => $args['user_id']));

		self::$default['id_pasok2'] = true;
		return self::$default;
	}

	public static function scan_operator($scan='',$data=array()){
		self::_default($data);

		if(!preg_match("/$nbk/i", $scan)){
			$index = _treacibility::_check_noTable($scan);
			$user = gg_employee::get_all($index,array('ID','name','divisi'));
		}else{
			$div = _treacibility::_check_divisi($scan);
			$induk = (int) substr($scan, 2,4);
			$user = gg_employee::get_all(array('ID','name','divisi'),"AND divisi='$div' AND no_induk='$induk'");
		}

		
		$check = array_filter($user);
		if(empty($check)){
			die(_error::_alert_db('Operator belum Terdaftar!!!'));
		}

		// Check pasok ke
		$pasok = self::get_noPasok($user[0]['ID']);
		$pasok += 1;

		self::$default['operator_id'] = $user[0]['ID'];
		self::$default['operator'] = $user[0]['name'];
		self::$default['process'] = $user[0]['module_value_divi'];
		self::$default['pasok'] = $pasok;

		return self::$default;
	}

	public static function send_data($afkir=0,$data=array()){
		self::_default($data);
		$data = self::$default;

		$check = array_filter($data);
		if(!empty($check)){
			// Check scan user id 
			if(!isset($data['user_id']) || empty($data['user_id'])){
				die(_error::_alert_db("Scan User terlebih dahulu !!!"));
			}
		}

		$product = gg_production::get_id($data['operator_id'],array('operator_id'));
		if($product[0]['operator_id']==0){
			die(_error::_alert_db("Scan Operator terlebih dahulu !!!"));
		}

		$pasok = self::get_noPasok($data['ID']);
		$pasok += 1;

		sobad_db::_insert_table('ggk-detail-afkir',array(
			'user_id'		=> $data['operator_id'],
			'scan_id'		=> $data['user_id'],
			'afkir'			=> $afkir,
			'pasok'			=> $pasok
		));

		$data['operator_id'] = '-';
		$data['operator'] = '-';
		$data['pasok'] = 0;
		$data['process'] = '-';
		$data['input'] = false;
		return $data;
	}
}