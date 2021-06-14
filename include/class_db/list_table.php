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
				'ggk-detal'		=> self::ggk_detal(),
				'ggk-employee'		=> self::ggk_employee(),
				'ggk-module'		=> self::ggk_module(),
				'ggk-post'		=> self::ggk_post(),
				'ggk-production'		=> self::ggk_production(),
				'ggk-user'		=> self::ggk_user(),
		);
		
		return $table;
	}
		

		private static function ggk_detal(){
			$list = array(
				'ID'	=> 0,
				'process_id'	=> 0,
				'scan_detail'	=> '',
				'_inserted'	=> date('Y-m-d H:i:s'),	
			);
			
			return $list;
		}

		private static function ggk_employee(){
			$list = array(
				'name'	=> '',
				'nickname'	=> '',
				'picture'	=> 0,
				'divisi'	=> 0,
				'no_induk'	=> 0,
				'no_pasok'	=> 0,
				'status'	=> 0,	
			);
			
			return $list;
		}

		private static function ggk_module(){
			$list = array(
				'module_key'	=> '',
				'module_value'	=> '',
				'module_nominal'	=> 0,
				'module_unit'	=> '',
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
				'batch_id'	=> 0,
				'_reff'	=> 0,	
			);
			
			return $list;
		}

		private static function ggk_user(){
			$list = array(
				'username'	=> '',
				'password'	=> '',
				'name'	=> '',
				'status'	=> 0,	
			);
			
			return $list;
		}

}