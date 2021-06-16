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
			'operator'	=> '-',
			'proses'	=> '-',
			'batch'		=> '-',
			'meja'		=> '-',
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

		// Check id scan User
		foreach ($process as $ky => $vl) {
			$regx = $vl['module_note'];
			if(empty($regx)){
				$code = 0;
				break;
			}

			$regx = "/^$regx/i";
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
	}

	public static function scan_code($scan='',$data=array()){
		self::_default($data);
		$data = self::$default;

		$check = array_filter($data);
		if(!empty($check)){
			// Check scan user id
			if(!isset($data['user_id'])){
				die(_error::_alert_db("Scan User terlebih dahulu !!!"));
			}

			if($scan==$data['user_id']){
				die(_error::_alert_db("User sudah Scan !!!"));
			}

		}else{
			// Check user id sudah scan atau belum
			if(!preg_match("/[0-9]{8}/", $scan)){
				die(_error::_alert_db("Scan User terlebih dahulu !!!"));
			}
		}

		self::_check_scanid($scan);

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


	}

	private static function _check_scanid($scan=''){
		$default = self::$default;

		// Check id scan User
		if(preg_match("/[0-9]{8}/", $scan)){
			self::_check_idCard($scan);
			return true;
		}

		if(empty(self::$default['user_id'])){
			die(_error::_alert_db("Scan User terlebih dahulu !!!"));
		}

		$divisi = gg_employee::get_id($default['user_id'],array('divisi'));
		$check = array_filter($divisi);

		$divisi = empty($check)?0:$divisi[0]['divisi'];
		$code = self::_check_codeScan($scan);

		// Check id scan Smart Container
		if($code=='SC' && in_array($divisi,array(1,2,6))){
			$sc_db = self::_check_scPosition($scan);

			self::_add_production($scan);

			if($divisi!=1){
				sobad_db::_update_single($sc_db['position'],'ggk-production',array(
					'_reff'		=> $default['work_id'],
				));
			}
		}

		// Check id scan Smart Container
		if($code=='SC' && $divisi==3){
			$sc_db = self::_check_scPosition($scan);

			//Check Jumlah Scan
			$qty = gg_production::get_all(array('ID'),"AND _reff='".$default['work_id']."'");
			$qty = count($qty);

			if($qty>=6){
				die(_error::_alert_db("Jumlah Smart Container Max !!!"));
			}

			sobad_db::_update_single($sc_db['position'],'ggk-production',array(
				'_reff'		=> $default['work_id'],
			));
		}

		// Check id scan Smart Container
		if($code=='IP' && in_array($divisi,array(3,4,6))){
			$sc_db = self::_check_scPosition($scan);

			self::_add_production($scan);

			if($divisi!=1){
				sobad_db::_update_single($sc_db['position'],'ggk-production',array(
					'_reff'		=> $default['work_id'],
				));
			}
		}
	}

	private static function _check_idCard($scan=''){
		$data = self::$default;

		$year = date('Y');$month = date('m');$day = date('d');

		$div = self::_check_divisi($scan);
		if(!empty($div)){
			$nik = (int) substr($scan,2,4);

			$module = gg_module::get_id($div,array('module_value','module_code'));
			$module = $process[0];

			$user = gg_employee::get_all(array('ID','name'),"AND divisi='$div' AND no_induk='$nik'");
			$user = $user[0];

			$data['scan_id'] = $scan;
			$data['user_id'] = $user['ID'];
			$data['operator'] = $user['name'];
			$data['proses'] = $module['module_value'];
			$data['input'] = $module['module_code']=='0'?false:true;

		}else{
			die(_error::_alert_db('Bagian User Undefined !!!'));
		}

		self::$default = $data;
	}
}