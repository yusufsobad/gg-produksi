<?php

class gg_user extends _class{
	public static $table = 'ggk-user';

	public static function blueprint($type='user'){
	// Konsep Blueprint	Schema
		$args = array(
			'type'		=> $type,
			'table'		=> self::$table,
		);

		return $args;
	}

	public static function check_login($user='',$pass=''){
		$conn = conn::connect();
		$args = array('ID','name','divisi','picture');

		$user = $conn->real_escape_string($user);
		$pass = $conn->real_escape_string($pass);

		$where = "WHERE username='$user' AND password='$pass' AND status='1'";

		return parent::_get_data($where,$args);
	}

	public static function _conv_divisi($id=0){
		$args = array(
			'administrator',
		);

		return $args[$id];
	}
}