<?php

class gg_detail extends _class{
	public static $table = 'ggk-detail';

	public static function blueprint($type='detail'){
	// Konsep Blueprint	Schema
		$args = array(
			'type'		=> $type,
			'table'		=> self::$table,
			'detail'	=> array(
				'operator_id'	=> array(
					'key'		=> 'ID',
					'table'		=> 'ggk-employee',
					'column'	=> array('name','divisi','no_induk','no_meja')
				),
			),
		);

		return $args;
	}

	public static function _get_user($id=0,$limit=''){
		$where = "AND operator_id='$id' $limit";
		return self::get_all($where);
	}

	public static function _get_userNow($id=0,$limit=''){
		$y = date('Y');$m = date('m');$d = date('d');
		$where = "AND operator_id='$id' AND (YEAR(_inserted)='$y' AND MONTH(_inserted)='$m' AND DAY(_inserted)='$d') $limit";
		return self::get_all($where);
	}
}