<?php

class gg_post extends _class{
	public static $table = 'gg-post';

	public static function blueprint($type='order'){
	// Konsep Blueprint	Schema
		$args = array(
			'type'		=> $type,
			'table'		=> self::$table,
		);

		return $args;
	}

	public static function flowchart(){
		$flow = array(
			array(
				'process'	=> 10,
			),
			array(
				'process'	=> 16,
			),
			array(
				'process'	=> 11,
			),
			array(
				'process'	=> 16
			),
			array(
				'process'	=> 17
			),
			array(
				'process'	=> 12,
				'max_scan'	=> 6
			),
			array(
				'process'	=> 16
			),
			array(
				'process'	=> 13,
			),
			array(
				'process'	=> 16
			),
			array(
				'process'	=> 14,
				'max_scan'	=> 10
			),
			array(
				'process'	=> 16
			),
			array(
				'process'	=> 15,
				'max_scan'	=> 20
			),
			array(
				'process'	=> 18,
				'max_scan'	=> 4
			),
		);

		return $flow;
	}

	public static function scan_code($scan='',$data=array()){
		$scanner = true;
		$_input = false;
		$batch = '-';
		$process = '-';
		$meja = '-';
		$user_id = 0;
		$user_name = '-';

		$date = date('Y-m-d');
		$year = date('Y');
		$month = date('m');
		$day = date('d');

		$check = array_filter($data);
		if(!empty($check)){
			// Check scan user id sama atau tidak 
			if(!isset($data['user_id']) && empty($data['user_id'])){
				return _error::_alert_db("Scan User terlebih dahulu !!!");
			}

			if($scan==$data['user_id']){
				return _error::_alert_db("User sudah Scan !!!");
			}

			$user_id = $data['user_id'];
			$user_name = $data['operator'];
		}else{
			// Check user id sudah scan atau belum
			if(!preg_match("/[0-9]{8}/", $scan)){
				return _error::_alert_db("Scan User terlebih dahulu !!!");
			}
		}

		$divisi = gg_module::_gets('divisi',array('ID','module_note'));
		$divisi = convToOption($divisi,'module_note','ID');

		// Chack id scan User
		if(preg_match("/[0-9]{8}/", $scan)){
			$aw = substr($scan,0,2);
			if(isset($divisi[$aw])){
				$div = $divisi[$aw];
				$nik = (int) substr($scan,2,4);

				$user = gg_employee::get_all(array('ID','name'),"AND divisi='$div' AND no_induk='$nik'");
				$user = $user[0];

				$user_id = $user['ID'];
				$user_name = $user['name'];

				$_check = gg_production::get_all(array('ID'),"AND user_id='$user_id' AND YEAR(scan_date)='$year' AND MONTH(scan_date)='$month' AND DAY(scan_date)='$day'");

				$_check = array_filter($_check);
				if(!empty($_check)){
					return _error::_alert_db("User sudah Scan !!!");
				}

				$q = sobad_db::_insert_table('ggk-production',array(
					'user_id'		=> $user_id,
					'divisi_id'		=> $div
				));
			}
		}

		return array(
			'scanner'	=> $scanner,
			'user_id'	=> $user_id,
			'operator'	=> $user_name,
			'scan_id'	=> $scan,
			'batch'		=> $batch,
			'meja'		=> $meja,
			'proses'	=> $process,
			'input'		=> $_input
		);

		foreach ($process as $ky => $vl) {
			if(empty($val)){
			$args[] = 0;
				break;
			}

			$regx = "/$vl/i";
			if(preg_match($regx, $val)){
				$args[] = $ky+1;
				break;
			}
		}
	}

	public static function send_data($actual=0,$reject=0){
		$length = strlen($scan);
	}
}