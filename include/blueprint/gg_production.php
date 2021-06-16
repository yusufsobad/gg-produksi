<?php

class gg_production extends _class{
	public static $table = 'ggk-production';

	public static function blueprint($type='production'){
	// Konsep Blueprint	Schema
		$args = array(
			'type'		=> $type,
			'table'		=> self::$table,
			'detail'	=> array(
				'user_id'	=> array(
					'key'		=> 'ID',
					'table'		=> 'ggk-employee',
					'column'	=> array('name','divisi','no_induk','no_pasok')
				),
				'divisi_id'	=> array(
					'key'		=> 'ID',
					'table'		=> 'ggk-module',
					'column'	=> array('module_value','module_note')
				)
			)
		);

		return $args;
	}
}