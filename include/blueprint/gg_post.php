<?php

class gg_post extends _class{
	public static $table = 'ggk-post';

	public static function blueprint($type='order'){
	// Konsep Blueprint	Schema
		$args = array(
			'type'		=> $type,
			'table'		=> self::$table,
		);

		return $args;
	}
}