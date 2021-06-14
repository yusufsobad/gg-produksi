<?php

class gg_production extends _class{
	public static $table = 'ggk-production';

	public static function blueprint($type='production'){
	// Konsep Blueprint	Schema
		$args = array(
			'type'		=> $type,
			'table'		=> self::$table,
		);

		return $args;
	}
}