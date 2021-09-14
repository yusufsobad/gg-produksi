<?php

class _treacibility{
	private static $default = array();

	private static function _default($args=array()){
		$data = array(
			'scanner'		=> true,
			'scan_id'		=> '-',
			'work_id'		=> 0,
			'user_id'		=> 0,
			'operator'		=> '-',
			'proses'		=> '-',
			'pasok'			=> 0,
			'id_gilling'	=> '-',
			'id_pc'			=> '-',
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

	public static function scan_code($scan='',$data=array()){
		self::_default($data);
		$data = self::$default;

		self::_check_scanid($scan);

		self::$default['scan_id'] = $scan;
		return self::$default;
	}

	private static function _check_scanid($scan=''){
		$default = self::$default;

		$divisi = gg_employee::get_id($default['user_id'],array('divisi'));
		$check = array_filter($divisi);

		$divisi = empty($check)?0:$divisi[0]['divisi'];
		$code = self::_check_codeScan($scan);

		// Check id operator

		// Check id scan Smart Container
		if($code=='SC' && in_array($divisi,array(1,2,6))){
			$sc_db = self::_check_scPosition($scan);

			$idx = self::_add_production($scan);

			if($divisi!=1){
				sobad_db::_update_single($sc_db['position'],'ggk-production',array(
					'_reff'		=> $idx,
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

			$idx = self::_add_production($scan);

			if($divisi!=1){
				sobad_db::_update_single($sc_db['position'],'ggk-production',array(
					'_reff'		=> $idx,
				));
			}
		}
	}
}