<?php

class _treacibility{
	private static $picture = "https://gg.soloabadi.com/kartosura/asset/img/user/no-profile.jpg";

	private static $default = array();

	private static function _default($args=array()){
		$data = array(
			'work_id'			=> 0,
			'user_id'			=> 0,
			'smart_container'	=> '-',
			'gilling'			=> '-',
			'push_cutter'		=> '-',
			'no_pasok'			=> '-',
			'_default'			=> 0
		);

		foreach ($args as $key => $val) {
			$data[$key] = isset($data[$key])?$val:'';
		}

		self::$default = $data;
	}

	public static function _check_divisi($scan=''){
		$divisi = gg_module::_gets('divisi',array('ID','module_note'));
		$divisi = convToOption($divisi,'module_note','ID');

		$aw = substr($scan,0,2);
		$div = isset($divisi[$aw])?$divisi[$aw]:0;

		return $div;
	}

	private static function _check_user($div=0,$scan=""){
		$nik = (int) substr($scan,2,4);

		$module = gg_module::get_id($div,array('ID','module_value'));
		$module = $module[0];

		$user = gg_employee::get_all(array('ID','name'),"AND divisi='$div' AND no_induk='$nik'");
			
		$check = array_filter($user);
		if(empty($check)){
			die(_error::_alert_db("User Tidak di temukan !!!"));
		}

		return $user[0];
	}

	public static function _check_codeScan($scan=''){
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

	private static function _add_detail($scan='',$max=1){
		$default = self::$default;
		$y = date('Y');$m = date('m');$d = date('d');

		// Check Double Scan
		$check = gg_production::get_id($default['work_id'],array('ID','scan_detail'),"AND scan_detail='$scan'");
		$check = array_filter($product);
		if(!empty($check)){
			die(_error::_alert_db('Double Scan ID!!!'));
		}

		// check max scann
		$product = gg_production::get_id($default['work_id'],array('ID','process_id'));
		$check = count($product);

		if($check==1 && empty($product[0]['process_id'])){
			$check = 0;
		}

		if($check>=$max){
			die(_error::_alert_db('Maximal scan!!!'));
		}

		$q = sobad_db::_insert_table('ggk-detail',array(
				'process_id'	=> $default['work_id'],
				'scan_detail'	=> $scan,
		));
	}

	private static function _add_production($scan='',$note=''){
		$default = self::$default;
		$y = date('Y');$m = date('m');$d = date('d');

		// Check Scan
		$where = "AND user_id='".$default['user_id']."' AND scan_id='$scan' AND YEAR(scan_date)='$y' AND MONTH(scan_date)='$m' AND DAY(scan_date)='$d'";
		$check = gg_production::get_all(array('ID'),$where);
		$check = array_filter($check);
		if(!empty($check)){
			die(_error::_alert_db("Double Scan ID ".$note." !!!"));
		}

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

	public static function get_block($id=0){
		$block = gg_module::get_id($id,array('ID','module_value'));
		$check = array_filter($block);
		if(empty($check)){
			return array('name' => '-');
		}

		return array('name' => $block[0]['module_value']);
	}

	public static function get_blocks(){
		$block = gg_module::_gets('block',array('ID','module_value'));
		$block = convToOption($block,'ID','module_value');

		return $block;
	}

	public static function get_leaderBlock($scan='',$block=0){
		$y = date('Y');$m = date('m');$d = date('d');
		$div = self::_check_divisi($scan);

		if($div==6){
			$user = self::_check_user(6,$scan);
			$idx = $user['ID'];

			// Check Leader Block Login
			$where = "AND id_user='$idx' AND YEAR(inserted)='$y' AND MONTH(inserted)='$m' AND DAY(inserted)='$d'";
			$check = gg_login::get_all(array('ID'),$where);
			$check = array_filter($check);
			if(empty($check)){
				// Insert Login User
				sobad_db::_insert_table("ggk-login-user",array('id_user' => $user['ID'], 'id_block' => $block));
			}

		}else{
			die(_error::_alert_db('User Bukan Leader Block !!!'));
		}

		return array(
			'ID'		=> $idx,
			'picture'	=> self::$picture,
			'name'		=> $user['name'],
			'nik'		=> $scan
		);
	}

	public static function get_pasok($scan='',$block=0){
		$y = date('Y');$m = date('m');$d = date('d');
		$div = self::_check_divisi($scan);

		if($div==9){
			$user = self::_check_user(9,$scan);

			// Update Login User
			$where = "id_user='$block' AND YEAR(inserted)='$y' AND MONTH(inserted)='$m' AND DAY(inserted)='$d'";
			sobad_db::_update_multiple($where,"ggk-login-user",array('id_pasok' => $user['ID'], 'id_user' => $block));

		}else{
			die(_error::_alert_db('User Bukan Pasok !!!'));
		}

		return array(
			'ID'		=> $user['ID'],
			'picture'	=> self::$picture,
			'name'		=> $user['name'],
			'nik'		=> $scan
		);
	}

	public static function get_smartContainer($scan='',$user=0){
		self::_default(array('user_id' => $user));
		$code = self::_check_codeScan($scan);

		// Check id scan Smart Container
		if($code=='SC'){
			$idx = self::_add_production($scan,"Smart Container");
		}else{
			die(_error::_alert_db("ID bukan Smart Container!!!"));
		}

		self::$default['smart_container'] = $scan;
		return self::$default;
	}

	public static function get_operator($scan='',$args=array()){
		self::_default($args);

		$div = self::_check_divisi($scan);
		$induk = (int) substr($scan, 2,4);
		$user = gg_employee::get_all(array('ID','name','divisi'),"AND divisi='$div' AND no_induk='$induk'");
		$check = array_filter($user);
		if(empty($check)){
			die(_error::_alert_db('Operator belum Terdaftar!!!'));
		}

		if($div==1){
			self::$default['gilling'] = $scan;
			self::$default['pasok'] = (int) substr($scan, 6,2);
		}else if($div==2){
			self::$default['gilling'] = $scan;
		}

		self::_add_detail($scan,2);
		return self::$default;
	}
}