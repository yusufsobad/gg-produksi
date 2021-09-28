<?php

class gg_afkir extends _class{
	public static $table = 'ggk-detail-afkir';

	public static function blueprint($type='login'){
	// Konsep Blueprint	Schema
		$args = array(
			'type'		=> $type,
			'table'		=> self::$table,
		);

		return $args;
	}
}