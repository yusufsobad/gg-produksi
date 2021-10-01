<?php

class gg_employee extends _class{
	public static $table = 'ggk-employee';

	public static function blueprint(){
		$args = array(
			'type'		=> 'employee',
			'table'		=> self::$table,
			'detail'	=> array(
				'divisi'	=> array(
					'key'		=> 'ID',
					'table'		=> 'ggk-module',
					'column'	=> array('module_value','module_note')
				),
				'picture'	=> array(
					'key'		=> 'ID',
					'table'		=> 'ggk-post',
					'column'	=> array('name')
				),
				'no_meja'	=> array(
					'key'		=> 'ID',
					'table'		=> 'ggk-module',
					'column'	=> array('module_value','module_reff')
				)
			)
		);

		return $args;
	}

	public static function get_maxNIK(){
		$args = array('MAX(no_induk) as nik');
		$where = "WHERE 1=1";
		
		$data = parent::_get_data($where,$args);
		$check = array_filter($data);
		if(empty($check)){
			return 0;
		}

		return $data[0]['nik'];
	}
}