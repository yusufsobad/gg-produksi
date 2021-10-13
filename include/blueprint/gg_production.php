<?php

class gg_production extends _class{
	public static $table = 'ggk-production';

	protected static $tbl_join = 'ggk-detail';

	protected static $join = "joined.user ";

	public static function blueprint($type='production'){
	// Konsep Blueprint	Schema
		$args = array(
			'type'		=> $type,
			'table'		=> self::$table,
			'detail'	=> array(
				'user_id'	=> array(
					'key'		=> 'ID',
					'table'		=> 'ggk-employee',
					'column'	=> array('name','divisi','no_induk','no_meja')
				),
				'divisi_id'	=> array(
					'key'		=> 'ID',
					'table'		=> 'ggk-module',
					'column'	=> array('module_value','module_note')
				),
			),
			'joined'	=> array(
				'key'		=> 'process_id',
				'table'		=> self::$tbl_join,
				'detail'	=> array(
					'operator_id'	=> array(
						'key'		=> 'ID',
						'table'		=> 'ggk-employee',
						'column'	=> array('name','nickname','divisi','no_induk','no_meja','grade','capacity')
					),
				)
			)
		);

		return $args;
	}

	public static function _tracking_by_user($user=0,$date=''){
		$track = array();
		$temp = array();
		$args = array('ID','user_id','divisi_id','operator_id','p_total','p_afkir','scan_id','scan_detail');

		$date = empty($date)?date('Y-m-d'):$date;
		$data = self::get_all($args,"AND operator_id='$user' AND scan_date='$date'");

		$divisi = array();
		$module = gg_module::_gets('divisi',array('ID','module_value','module_code'));
		foreach ($module as $key => $val) {
			$divisi[$val['ID']] = array(
				'name' => $val['module_value'],
				'code' => $val['module_code']
			);
		}

		$color = array(
			1 => '#ff3232ff',
			'#ff3232ff',
			'#ff3232ff',
			'#ff3232ff',
			'#ff3232ff',
			'#ff3232ff',
			'#ff3232ff',
			'#ff3232ff',
			'#ff3232ff'
		);


	// Set Gilling, Smart Container, Afkir
		$search = array();
		foreach ($data as $key => $val) {
			$track[$val['ID']] = array();
			$idp = $val['divisi_oper'];
			$idu = $val['divisi_id'];

			$track[$val['ID']][] = array(
				'id'		=> 1,
				'name'		=> $val['name_oper'],
				'column'	=> $idp,
				'swimlane'	=> $divisi[$idp]['code'],
				'text'		=> 'ID : '.$val['scan_detail'],
				'barColor'	=> $color[$idp]
			);

			$track[$val['ID']][] = array(
				'id'		=> 2,
				'name'		=> $val['p_total'] - $val['p_afkir'],
				'column'	=> $idp,
				'swimlane'	=> $divisi[$idp]['code'],
				'text'		=> 'total : '.$val['p_total'].'<br>afkir : '.$val['p_afkir'],
				'barColor'	=> $color[$idp]
			);

			$track[$val['ID']][] = array(
				'id'		=> 3,
				'name'		=> $val['name_user'],
				'column'	=> $idu,
				'swimlane'	=> $divisi[$idu]['code'],
				'text'		=> 'ID : '.$val['scan_id'],
				'barColor'	=> $color[$idp]
			);

			$temp[$val['scan_id']] = $val['ID'];
			$search[] = $val['scan_id'];
		}

	// Set Push Cutter, Afkir
		$search = implode(',', $search);
		$search = empty($search)?0:$search;

		$data = self::get_all($args,"AND operator_id!='$user' AND scan_date='$date' AND scan_id IN ($search)");
		$search = array();
		foreach ($data as $key => $val) {
			$idp = $val['divisi_oper'];
			$idu = $temp[$val['scan_id']];

			// Set Gilling, Smart Container, Afkir
			$track[$idu][] = array(
				'id'		=> 1,
				'name'		=> $val['name_oper'],
				'column'	=> $idp,
				'swimlane'	=> $divisi[$idp]['code'],
				'text'		=> 'ID : '.$val['scan_detail'],
				'barColor'	=> $color[$idp]
			);

			$track[$val['ID']][] = array(
				'id'		=> 2,
				'name'		=> $val['p_total'] - $val['p_afkir'],
				'column'	=> $idp,
				'swimlane'	=> $divisi[$idp]['code'],
				'text'		=> 'total : '.$val['p_total'].'<br>afkir : '.$val['p_afkir'],
				'barColor'	=> $color[$idp]
			);

			$search[] = $val['scan_id'];
		}

	// Set Inner
		$search = implode(',', $search);
		$search = empty($search)?0:$search;

		$data = self::get_all($args,"AND scan_date='$date' AND scan_detail IN ($search)");
		foreach ($data as $key => $val) {
			$idp = $val['divisi_oper'];
			$idu = $temp[$val['scan_detail']];

			// Set Gilling, Smart Container, Afkir
			$track[$idu][] = array(
				'id'		=> 1,
				'name'		=> $val['name_oper'],
				'column'	=> $idp,
				'swimlane'	=> $divisi[$idp]['code'],
				'text'		=> 'ID : '.$val['scan_detail'],
				'barColor'	=> $color[$idp]
			);

			$track[$val['ID']][] = array(
				'id'		=> 2,
				'name'		=> $val['p_total'] - $val['p_afkir'],
				'column'	=> $idp,
				'swimlane'	=> $divisi[$idp]['code'],
				'text'		=> 'total : '.$val['p_total'].'<br>afkir : '.$val['p_afkir'],
				'barColor'	=> $color[$idp]
			);
		}
	}
}