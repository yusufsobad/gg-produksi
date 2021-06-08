<?php

(!defined('DEFPATH'))?exit:'';

class _sync_db{
	public static function _blueprint(){
		$dir = "include/blueprint/";
		$list = sobad_asset::_name_file($dir);

		if(empty($list)){
			$dir = "blueprint/";
			$list = sobad_asset::_name_file($dir);
		}

		if(count($list)>0){
			for($i=0;$i<count($list);$i++){
				require $dir.$list[$i];
			}
		}
	}
}

require 'class_db/_class.php';

_sync_db::_blueprint();
