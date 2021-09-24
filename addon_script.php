<?php
(!defined('DEFPATH'))?exit:'';

abstract class addon_script{

	protected static function _js_swimlane($idx=array()){
			$js = array(
				'dailypilot'			=> 'vendor/dailypilot/scripts/daypilot-all.min.js',
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$js[$key];
				}
			}
		
			return $js;
		}
}