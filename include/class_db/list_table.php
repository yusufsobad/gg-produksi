<?php
(!defined('AUTHPATH'))?exit:'';

class sobad_table{

	public static function _get_table($func){
		$func = str_replace('-','_',$func);
				
		$obj = new self();
		if(is_callable(array($obj,$func))){
			$list = $obj::{$func}();
				return $list;
			}
		
		return false;
	}
		
	public static function _get_list($func=''){
		$list = array();
		$lists = self::_get_table($func);
		if($lists){
			foreach ($lists as $key => $val) {
				$list[] = $key;
			}
		}
		
		return $list;
	}
		

	private static function _list_table(){
		// Information data table
		
		$table = array(
				'ggk-detail'		=> self::ggk_detail(),
				'ggk-detail-afkir'		=> self::ggk_detail_afkir(),
				'ggk-employee'		=> self::ggk_employee(),
				'ggk-history-target'		=> self::ggk_history_target(),
				'ggk-login-user'		=> self::ggk_login_user(),
				'ggk-module'		=> self::ggk_module(),
				'ggk-post'		=> self::ggk_post(),
				'ggk-production'		=> self::ggk_production(),
				'ggk-user'		=> self::ggk_user(),
		);
		
		return $table;
	}
		

		private static function ggk_detail(){
			$list = array(
				'process_id'	=> 0,
				'operator_id'	=> 0,
				'pasok_ke'	=> 0,
				'scan_detail'	=> '',
				'_inserted'	=> date('Y-m-d H:i:s'),	
			);
			
			return $list;
		}

		private static function ggk_detail_afkir(){
			$list = array(
				'user_id'	=> 0,
				'scan_id'	=> 0,
				'afkir'	=> 0,
				'pasok'	=> 0,
				'inserted'	=> date('Y-m-d H:i:s'),	
			);
			
			return $list;
		}

		private static function ggk_employee(){
			$list = array(
				'name'	=> '',
				'nickname'	=> '',
				'picture'	=> 0,
				'divisi'	=> 0,
				'grade'	=> 0,
				'no_induk'	=> 0,
				'no_meja'	=> 0,
				'capacity'	=> 0,
				'under_capacity'	=> 0,
				'status'	=> 0,	
			);
			
			return $list;
		}

		private static function ggk_history_target(){
			$list = array(
				'user_id'	=> 0,
				'target'	=> 0,
				'grade'	=> 0,
				'_date'	=> date('Y-m-d'),	
			);
			
			return $list;
		}

		private static function ggk_login_user(){
			$list = array(
				'id_user'	=> 0,
				'id_pasok'	=> 0,
				'id_pasok2'	=> 0,
				'id_block'	=> 0,
				'inserted'	=> date('Y-m-d H:i:s'),
				'receh'	=> 0,	
			);
			
			return $list;
		}

		private static function ggk_module(){
			$list = array(
				'module_key'	=> '',
				'module_value'	=> '',
				'module_code'	=> '',
				'module_note'	=> '',
				'module_reff'	=> 0,	
			);
			
			return $list;
		}

		private static function ggk_post(){
			$list = array(
				'name'	=> '',
				'notes'	=> '',
				'inserted'	=> date('Y-m-d H:i:s'),
				'var'	=> '',	
			);
			
			return $list;
		}

		private static function ggk_production(){
			$list = array(
				'user_id'	=> 0,
				'divisi_id'	=> 0,
				'scan_date'	=> date('Y-m-d H:i:s'),
				'p_total'	=> 0,
				'p_afkir'	=> 0,
				'scan_id'	=> '',
				'status'	=> 0,	
			);
			
			return $list;
		}

		private static function ggk_user(){
			$list = array(
				'username'	=> '',
				'password'	=> '',
				'name'	=> '',
				'picture'	=> 0,
				'divisi'	=> 0,
				'status'	=> 0,	
			);
			
			return $list;
		}

}