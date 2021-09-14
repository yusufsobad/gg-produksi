<?php

class gg_production extends _class{
	public static $table = 'ggk-production';

	protected static $tbl_join = 'ggk-detail';

	protected static $join = "joined.user ";

	public static function blueprint($type='production'){
	// Konsep Blueprint	Schema
		$args = array(
			'type'		=> $type,
			'table'		=> self::$table,
			'detail'	=> array(
				'user_id'	=> array(
					'key'		=> 'ID',
					'table'		=> 'ggk-employee',
					'column'	=> array('name','divisi','no_induk','no_meja')
				),
				'divisi_id'	=> array(
					'key'		=> 'ID',
					'table'		=> 'ggk-module',
					'column'	=> array('module_value','module_note')
				)
			),
			'joined'	=> array(
				'key'		=> 'process_id',
				'table'		=> self::$tbl_join
			)
		);

		return $args;
	}
}