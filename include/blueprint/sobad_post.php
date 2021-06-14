<?php

class sobad_post extends _class{
	public static $table = 'ggk-post';

	public static function blueprint($type='order'){
	// Konsep Blueprint	Schema
		$args = array(
			'type'		=> $type,
			'table'		=> self::$table,
		);

		return $args;
	}

	public static function get_profiles($args=array(),$limit=''){
		$args = parent::_check_array($args);
		
		$where = "WHERE var='profile' $limit";
		return parent::_get_data($where,$args);
	}
}