<?php

class _treacibility{
	private static $picture = "https://gg.soloabadi.com/kartosura/asset/img/user/no-profile.jpg";

	private static $default = array();

	private static function _check_user($div=0,$scan=""){
		$nik = (int) substr($scan,2,4);

		$module = gg_module::get_id($div,array('ID','module_value'));
		$module = $module[0];

		$user = gg_employee::get_all(array('ID','name'),"AND divisi='6' AND no_induk='$nik'");
			
		$check = array_filter($user);
		if(empty($check)){
			die(_error::_alert_db("User Tidak di temukan !!!"));
		}

		return $user;
	}

	public static function get_blocks(){
		$block = gg_module::_gets('block',array('ID','module_value'));
		$block = convToOption($block,'ID','module_value');

		return $block;
	}

	public static function get_leaderBlock($scan='',$block=0){
		$div = _production::_check_divisi($scan);
		if($div!=6){
			$user = self::_check_user(6,$scan);

			// Insert Login User
			sobad_db::_insert_table("ggk-login-user",array('id_user' => $user[0]['ID'], 'id_block' => $block));

		}else{
			die(_error::_alert_db('Bagian User Undefined !!!'));
		}

		return array(
			'picture'	=> self::$picture,
			'name'		=> $user[0]['name'],
			'nik'		=> $scan
		);
	}

	public static function get_pasok($scan='',$block=0){
		$y = date('Y');$m = date('m');$d = date('d');
		$div = _production::_check_divisi($scan);

		if($div!=9){
			$user = self::_check_user(9,$scan);

			// Update Login User
			$where = "id_user='$block' AND YEAR(inserted)='$y' AND MONTH(inserted)='$m' AND DAY(inserted)='$d'";
			sobad_db::_update_multiple($where,"ggk-login-user",array('id_pasok' => $user[0]['ID'], 'id_user' => $block));

		}else{
			die(_error::_alert_db('Bagian User Undefined !!!'));
		}

		return array(
			'picture'	=> self::$picture,
			'name'		=> $user[0]['name'],
			'nik'		=> $scan
		);
	}
}