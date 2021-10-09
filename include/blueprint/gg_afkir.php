<?php

class gg_afkir extends _class{
	public static $table = 'ggk-detail-afkir';

	public static function blueprint($type='login'){
	// Konsep Blueprint	Schema
		$args = array(
			'type'		=> $type,
			'table'		=> self::$table,
			'detail'	=> array(
				'user_id'	=> array(
					'key'		=> 'ID',
					'table'		=> 'ggk-employee',
					'column'	=> array('name','nickname','divisi')
				),
				'scan_id'	=> array(
					'key'		=> 'ID',
					'table'		=> 'ggk-employee',
					'column'	=> array('name','nickname','divisi')
				),
			),
		);

		return $args;
	}
}