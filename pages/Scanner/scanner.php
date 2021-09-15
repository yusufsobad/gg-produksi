<?php

class _production{

	private static $default = array();

	private static function _default($args=array()){
		$data = array(
			'scanner'	=> true,
			'input'		=> false,
			'scan_id'	=> '-',
			'work_id'	=> 0,
			'user_id'	=> 0,
			'proses'	=> '-',
			'pasok'		=> 0,
			'meja'		=> '-',
			'divisi'	=> 0,
			'_operator'	=> '-',
			'_default'	=> 200,
			'_login'	=> false,
			'__number'	=> 0,
			'__smart'	=> '-',
			'__baki'	=> '-',
			'__banderol'=> '-',
			'__box'		=> '-',
		);

		foreach ($args as $key => $val) {
			$data[$key] = isset($data[$key])?$val:'';
		}

		self::$default = $data;
	}

	private static function _check_divisi($scan=''){
		$divisi = gg_module::_gets('divisi',array('ID','module_note'));
		$divisi = convToOption($divisi,'module_note','ID');

		$aw = substr($scan,0,2);
		$div = isset($divisi[$aw])?$divisi[$aw]:0;

		return $div;
	}

	private static function _check_codeScan($scan=''){
		$process = gg_module::_gets('scanner',array('module_code','module_note'));
		$code = 0;

		// Check id scan User
		foreach ($process as $ky => $vl) {
			$regx = $vl['module_note'];
			if(empty($regx)){
				$code = 0;
				break;
			}

			$regx = "/$regx/i";
			if(preg_match($regx, $scan)){
				$code = $vl['module_code'];
				break;
			}
		}

		return $code;
	}

	private static function _check_scPosition($scan=''){
		$data = self::$default;

		$sc_db = gg_module::_gets('smart_container',array('ID','module_reff'),"AND module_value='$scan'");
		$check = array_filter($sc_db);
		
		if(empty($check)){
			$q = sobad_db::_insert_table('ggk-module',array(
				'module_key' 		=> 'smart_container',
				'module_value'		=> $scan,
				'module_reff'		=> $data['work_id']
			));

			return array('index' => $q, 'position' => 0);
		}

		$sc_db = $sc_db[0];
		sobad_db::_update_single($sc_db['ID'],'ggk-module',array('module_reff' => $data['work_id']));

		return array('index' => $sc_db['ID'], 'position' => $sc_db['module_reff']);
	}

	private static function _add_detail($scan='',$max=1){
		$default = self::$default;

		// check max scann
		$product = gg_production::get_id($default['work_id'],array('ID','process_id'));
		$check = count($product);

		if($check==1 && empty($product[0]['process_id'])){
			$check = 0;
		}

		if($check>=$max){
			die(_error::_alert_db('Maximal scan!!!'));
		}

		self::$default['__number'] = $check;
		$q = sobad_db::_insert_table('ggk-detail',array(
				'process_id'	=> $default['work_id'],
				'scan_detail'	=> $scan,
		));

		return $q;
	}

	private static function _add_operator($scan=''){
		$default = self::$default;

		$div = self::_check_divisi($scan);
		$induk = (int) substr($scan, 2,4);
		$user = gg_employee::get_all(array('ID','name','divisi'),"AND divisi='$div' AND no_induk='$induk'");
		$check = array_filter($user);
		if(empty($check)){
			die(_error::_alert_db('Operator undefined!!!'));
		}

		self::$default['_operator'] = $user[0]['name'];
		self::$default['proses'] = $user[0]['meta_value_divi'];

		$q = sobad_db::_update_single($default['work_id'],'ggk-production',array(
			'ID'				=> $default['work_id'],
			'operator_id'		=> $user[0]['ID']
		));

		$q = self::_add_detail($scan);

		return $q;
	}

	private static function _add_production($scan=''){
		$default = self::$default;

		$user = gg_employee::get_id($default['user_id'],array('divisi'));
		$div = $user[0]['divisi'];

		$q = sobad_db::_insert_table('ggk-production',array(
				'user_id'		=> $default['user_id'],
				'divisi_id'		=> $div,
				'scan_id'		=> $scan
		));

		$default['work_id'] = $q;

		self::$default = $default;
		return $q;
	}

	public static function scan_login($scan=''){
		$data = self::$default;

		$year = date('Y');$month = date('m');$day = date('d');

		$div = self::_check_divisi($scan);
		if(!empty($div)){
			$nik = (int) substr($scan,2,4);

			$module = gg_module::get_id($div,array('ID','module_value'));
			$module = $module[0];

			$user = gg_employee::get_all(array('ID','name'),"AND divisi='$div' AND no_induk='$nik'");
			
			$check = array_filter($user);
			if(empty($check)){
				die(_error::_alert_db("User Tidak di temukan !!!"));
			}

			$user = $user[0];

			$data['user_id'] = $user['ID'];
			$data['operator'] = $user['name'];
			$data['proses'] = $module['module_value'];
			$data['divisi'] = $div;
			$data['_login'] = true;

		}else{
			die(_error::_alert_db('Bagian User Undefined !!!'));
		}

		$data['scan_id'] = $scan;
		return $data;
	}

	public static function scan_code($scan='',$data=array()){
		self::_default($data);
		$data = self::$default;

		$check = array_filter($data);
		if(!empty($check)){
			// Check scan user id
			if(!isset($data['user_id'])){
				$data['_login'] = false;
				return $data;
			}

			if($scan==$data['user_id']){
				die(_error::_alert_db("User sudah Scan !!!"));
			}

		}else{
			// Check user id sudah scan atau belum
			if(!preg_match("/[0-9]{6}/", $scan)){
				die(_error::_alert_db("Scan User terlebih dahulu !!!"));
			}
		}

		self::_check_scanid($scan);

		self::$default['scan_id'] = $scan;
		return self::$default;
	}

	public static function send_data($quantity=0,$afkir=0,$data=array()){
		self::_default($data);
		$data = self::$default;

		if(empty($quantity)){
			die(_error::_alert_db("Quantity tidak boleh kosong !!!"));
		}

		$check = array_filter($data);
		if(!empty($check)){
			// Check scan user id 
			if(!isset($data['user_id']) || empty($data['user_id'])){
				die(_error::_alert_db("Scan User terlebih dahulu !!!"));
			}
		}

		$product = gg_production::get_id($data['work_id'],array('operator_id'));
		if($product[0]['operator_id']==0){
			die(_error::_alert_db("Scan Operator terlebih dahulu !!!"));
		}

		sobad_db::_update_single($data['work_id'],'ggk-production',array(
			'p_total'		=> $quantity,
			'p_receh'		=> ($quantity-200),
			'p_afkir'		=> $afkir
		));

		$data['input'] = false;
		return $data;
	}

	private static function _check_scanid($scan=''){
		$default = self::$default;

		// Check id scan User
		if(strlen($scan)<=6){
			if(preg_match("/[0-9]{6}/", $scan)){
				self::_check_idCard($scan);
				return true;
			}
		}

		if(empty(self::$default['user_id'])){
			die(_error::_alert_db("Scan User terlebih dahulu !!!")); 
		}

		$divisi = gg_employee::get_id($default['user_id'],array('divisi'));
		$check = array_filter($divisi);

		$divisi = empty($check)?0:$divisi[0]['divisi'];
		$code = self::_check_codeScan($scan);

		// Check id scan Operator
		if($code=='OP' && in_array($divisi,array(6,7))){
			$idx = self::_add_operator($scan);
			
			self::$default['input'] = true;
			self::$default['pasok'] = substr($scan,6);
			//self::$default['meja'] = substr($scan,2,4);
		}

		// Check id scan Smart Container
		if($code=='SC' && in_array($divisi,array(6,7)){
			self::$default['__smart'] = $scan;
			$idx = self::_add_production($scan);
			$sc_db = self::_check_scPosition($scan);
		}

		// Check id scan Baki
		if($code=='IP' && in_array($divisi,array(3,4,5))){
			self::$default['__baki'] = $scan;
			self::$default['__number'] = 0;
			
			$idx = self::_add_production($scan);
			$sc_db = self::_check_scPosition($scan);
		}

		// Check id scan Smart Container
		if($code=='SC' && $divisi==3){
			self::$default['__smart'] = $scan;
			$idx = self::_add_detail($scan,6);
		}

		// Check id scan Banderoll
		if($code=='BP' && $divisi==5){
			self::$default['__banderol'] = $scan;
			$idx = self::_add_detail($scan,10);
		}

// Crash --> Check ulang
		// Check id scan Ball
		if($code=='BL' && $divisi==8){
			$idx = self::_add_production($scan);
		}

		// Check id scan Banderoll
		if($code=='BP' && $divisi==8){
			self::$default['__banderol'] = $scan;
			$idx = self::_add_detail($scan,20);
		}

		// Check id scan Box
		if($code=='BX' && $divisi==8){
			$idx = self::_add_production($scan);
		}
// End Crash
	}
}