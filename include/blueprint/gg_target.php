<?php

class gg_target extends _class{
	public static $table = 'ggk-history-target';

	public static function blueprint($type='target'){
	// Konsep Blueprint	Schema
		$args = array(
			'type'		=> $type,
			'table'		=> self::$table,
		);

		return $args;
	}
}