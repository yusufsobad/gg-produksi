<?php

class gg_module extends _class{
	public static $table = 'ggk-module';

	public static function blueprint(){
		$args = array(
			'type'		=> 'module',
			'table'		=> self::$table
		);

		return $args;
	}

	private static function _check_type($type=''){
		if(!empty($type)){
			$args = array(
				'scanner',
				'divisi',
				'smart_container',
				'block',
				'default_sc',
				'no_meja',
				'location'
			);

			if(in_array($type, $args)){
				return true;
			}
		}

		return false;
	}
	
	public static function _gets($type='',$args=array(),$limit=''){
		if(self::_check_type($type)){
			$where = "WHERE module_key='$type' $limit";
			return self::_check_join($where,$args,$type);
		}

		return array();
	}

	public static function _max_noBangku(){
		$args = array("MAX(module_value) As max");
		$whr = "WHERE module_key='no_meja'";
		$data = self::_get_data($whr,$args);

		$check = array_filter($data);
		if(empty($check)){
			return 0;
		}

		return $data[0]['max'];
	}
}