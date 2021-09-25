<?php

class gg_login extends _class{
	public static $table = 'ggk-login-user';

	public static function blueprint($type='login'){
	// Konsep Blueprint	Schema
		$args = array(
			'type'		=> $type,
			'table'		=> self::$table,
		);

		return $args;
	}
}