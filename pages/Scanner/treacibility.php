<?php

class _treacibility{
	private static $default = array();

	public static function get_blocks(){
		$block = gg_module::_gets('block',array('ID','module_value'));
		$block = convToOption($block,'ID','module_value');

		return $block;
	}
}