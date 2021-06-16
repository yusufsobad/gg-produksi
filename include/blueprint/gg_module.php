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
}