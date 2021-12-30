<?php
(!defined('AUTHPATH'))?exit:'';

class option_library{
	protected function get_libs(){
		//pendaftaran Library
		$args = unserialize(_library);
		
		return $args;
	}
	
	protected function get_option_lib($libs=''){		
		$args = self::get_libs();
		if(array_key_exists($libs,$args)){
			return $args[$libs];
		}

		return '';
	}
}