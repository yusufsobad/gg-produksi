<?php

class _treacibility{
	private static $picture = "https://gg.soloabadi.com/kartosura/asset/img/user/no-profile.jpg";

	private static $default = array();

	private static function _get_recehan($block=0){
		$receh = gg_module::get_id($block,array('module_note'));
		$check = array_filter($receh);

		if(!empty($receh)){
			return (int) $receh[0]['module_note'];
		}

		return 0;
	}

	private static function _default($args=array()){
		$data = array(
			'work_id'			=> 0,
			'user_id'			=> 0,
			'smart_container'	=> '-',
			'gilling'			=> '-',
			'push_cutter'		=> '-',
			'no_pasok'			=> '-',
			'_total'			=> 0,
			'_afkir'			=> 0,
			'_default'			=> 0,
			'_totGilling'		=> 0,
			'_totPushCutter'	=> 0,
			'_totRecehan'		=> 0,
			'_totAfkir'			=> 0,
			'_detail'			=> array()
		);

		foreach ($args as $key => $val) {
			$data[$key] = isset($data[$key])?$val:'';
		}

		self::$default = $data;
	}

	private static function get_noPasok($user=0){
		$y = date('Y');$m = date('m');$d = date('d');
		$where = "AND operator_id='$user' AND YEAR(scan_date)='$y' AND MONTH(scan_date)='$m' AND DAY(scan_date)='$d'";

		$pasok = gg_production::get_all(array('ID','operator_id'),$where);
		return count($pasok);
	}

	public static function _check_divisi($scan=''){
		$divisi = gg_module::_gets('divisi',array('ID','module_note'));
		$divisi = convToOption($divisi,'module_note','ID');

		$aw = substr($scan,0,2);
		$div = isset($divisi[$aw])?$divisi[$aw]:0;

		return $div;
	}

	public static function _check_noTable($scan=''){
		// Get Regex No Bangku
		$nbk = gg_module::_gets('scanner',array('module_note'),"AND module_code='NBK'");
		$nbk = $nbk[0]['module_note'];

		if(!preg_match("/$nbk/i", $scan)){
			die(_error::_alert_db("ID bukan no Meja!!!"));
		}

		preg_match_all("/$nbk/i", $scan,$no_meja);
		$scan = (int) $no_meja[1][0];

		$meja = gg_module::_gets('no_meja',array('ID'),"AND module_value='$scan' AND module_reff='1'");
		$check = array_filter($meja);
		if(empty($check)){
			die(_error::_alert_db("No Meja Belum terpakai!!!"));
		}

		$meja = $meja[0]['ID'];
		$user = gg_employee::get_all(array('ID'),"AND no_meja='$meja'");

		return $user[0]['ID'];
	}

	private static function _check_user($div=0,$scan=""){
		$nik = (int) substr($scan,2,4);

		$module = gg_module::get_id($div,array('ID','module_value'));
		$module = $module[0];

		$user = gg_employee::get_all(array('ID','name'),"AND divisi='$div' AND no_induk='$nik'");
			
		$check = array_filter($user);
		if(empty($check)){
			die(_error::_alert_db("User Tidak di temukan !!!"));
		}

		return $user[0];
	}

	public static function _check_codeScan($scan=''){
		$process = gg_module::_gets('scanner',array('module_code','module_note'));
		$code = 0;

		// Check id scan User
		foreach ($process as $ky => $vl) {
			$regx = $vl['module_note'];
			if(empty($regx)){
				$code = 0;
				break;
			}

			$regx = "/$regx/i";
			if(preg_match($regx, $scan)){
				$code = $vl['module_code'];
				break;
			}
		}

		return $code;
	}

	private static function _check_scPosition($scan=''){
		$data = self::$default;

		$sc_db = gg_module::_gets('smart_container',array('ID','module_reff'),"AND module_value='$scan'");
		$check = array_filter($sc_db);
		
		if(empty($check)){
			$q = sobad_db::_insert_table('ggk-module',array(
				'module_key' 		=> 'smart_container',
				'module_value'		=> $scan,
				'module_reff'		=> $data['work_id']
			));

			return array('index' => $q, 'position' => 0);
		}

		$sc_db = $sc_db[0];
		sobad_db::_update_single($sc_db['ID'],'ggk-module',array('module_reff' => $data['work_id']));

		return array('index' => $sc_db['ID'], 'position' => $sc_db['module_reff']);
	}

	private static function _add_detail($scan='',$user_id=0,$pasok=0,$max=1){
		$default = self::$default;
		$y = date('Y');$m = date('m');$d = date('d');

		// Check Double Scan
		$check = gg_production::get_id($default['work_id'],array('ID','scan_detail'),"AND scan_detail='$scan'");
		$check = array_filter($product);
		if(!empty($check)){
			die(_error::_alert_db('Double Scan ID!!!'));
		}

		// check max scann
		$product = gg_production::get_id($default['work_id'],array('ID','process_id'));
		$check = count($product);

		if($check==1 && empty($product[0]['process_id'])){
			$check = 0;
		}

		if($check>=$max){
			die(_error::_alert_db('Maximal scan!!!'));
		}

		$q = sobad_db::_insert_table('ggk-detail',array(
				'process_id'	=> $default['work_id'],
				'pasok_ke'		=> $pasok,
				'operator_id'	=> $user_id,
				'scan_detail'	=> $scan,
		));
	}

	private static function _add_production($scan='',$note=''){
		$default = self::$default;
		$y = date('Y');$m = date('m');$d = date('d');

		// Check Scan
		$where = "AND user_id='".$default['user_id']."' AND scan_id='$scan' AND YEAR(scan_date)='$y' AND MONTH(scan_date)='$m' AND DAY(scan_date)='$d'";
		$check = gg_production::get_all(array('ID'),$where);
		$check = array_filter($check);
		if(!empty($check)){
			die(_error::_alert_db("Double Scan ID ".$note." !!!"));
		}

		$user = gg_employee::get_id($default['user_id'],array('divisi'));
		$div = $user[0]['divisi'];

		$q = sobad_db::_insert_table('ggk-production',array(
				'user_id'		=> $default['user_id'],
				'divisi_id'		=> $div,
				'scan_id'		=> $scan
		));

		$default['work_id'] = $q;

		self::$default = $default;
	}

	public static function get_block($id=0){
		$block = gg_module::get_id($id,array('ID','module_value'));
		$check = array_filter($block);
		if(empty($check)){
			return array('name' => '-');
		}

		return array('name' => $block[0]['module_value']);
	}

	public static function get_location(){
		$loc = gg_module::_gets('location',array('ID','module_value'));
		$check = array_filter($loc);
		if(empty($check)){
			return array('name' => '-');
		}

		return array('name' => $loc[0]['module_value']);
	}

	private static function group_operators($id=0){
		$y = date('Y');$m = date('m');$d = date('d');
		$where = "AND user_id='$id' AND (YEAR(scan_date)='$y' AND MONTH(scan_date)='$m' AND DAY(scan_date)='$d')";
		$flow = gg_production::get_all(array('ID','p_total','p_total','operator_id'),$where);

		$push = array();$_temp = array();
		foreach ($flow as $key => $val) {
			$idx = $val['operator_id'];

		// Set data Operator	
			if(!isset($push[$idx])){
				$push[$idx] = array(
					'divisi'	=> $val['divisi_oper'],
					'user_id'	=> $idx,
					'name'		=> $val['name_oper'],
					'picture'	=> self::$picture,
					'_total'	=> 0,
					'_afkir'	=> 0,
					'_detail'	=> array()
				);
			}

			$push[$idx]['_total'] += $val['p_total'];
			$push[$idx]['_afkir'] += $val['p_afkir'];

		// Set data flow
			$idg = $val['ID'];
			if(!isset($_temp[$idg])){
				$_temp[$idg] = array();
			}

			if($val['divisi_oper']==2){
				$_temp[$idg]['parent'] = $val['operator_id'];
			}else{
				$_temp[$idg]['child'] = $val['operator_id'];
			}
		}

	// Set data flow Construct
		$idm = -1;
		$data = array();$_data = array();$_idpr = array();
		foreach ($_temp as $key => $val) {
			$idp = $val['parent'];
			if(!isset($_data[$idp])){
				$idm += 1;
				$_data[$idp] = array();
				$data[$idm] = $push[$idp];

				$_idpr[$idp] = $idm;
			}

			$idc = $val['child'];
			if(!in_array($idc,$_data[$idp])){
				$_data[$idp][] = $idc;
				$data[$_idpr[$idp]]['_detail'][] = $push[$idc];
			}
		}

		return $data;
	}

	public static function get_blocks(){
		$block = gg_module::_gets('block',array('ID','module_value'));
		$block = convToOption($block,'ID','module_value');

		return $block;
	}

	public static function get_statusProduction($pasok=0,$block=0){
		$y = date('Y');$m = date('m');$d = date('d');

		// Get Total 
		$where = "AND user_id='$pasok' AND YEAR(scan_date)='$y' AND MONTH(scan_date)='$m' AND DAY(scan_date)='$d'";
		$product = gg_production::get_all(array('p_total','p_afkir'),$where);

		$afkir = 0;$total = 0;
		foreach ($product as $key => $val) {
			$afkir += $val['p_afkir'];
			$total += $val['p_total'];
		}

		self::$default['_totGilling'] = $total;
		self::$default['_totPushCutter'] = $total;
		self::$default['_totAfkir'] = $afkir;
		self::$default['_totRecehan'] = self::_get_recehan($block);
	}

	public static function get_loadData($pasok=0,$block=0){
		$y = date('Y');$m = date('m');$d = date('d');
		$where = "AND user_id='$pasok' AND YEAR(scan_date)='$y' AND MONTH(scan_date)='$m' AND DAY(scan_date)='$d'";

		// Default Data
		$args = array(
			'input'		=> array(
				'status'	=> false,
				'data'		=> array()
			),
			'history'	=> array(
				'status'	=> false,
				'data'		=> array()
			),
			'total'		=> array(
				'status'	=> true,
				'data'		=> array()
			),
			'layout'	=> array(
				'status'	=> false,
				'data'		=> array()
			)
		);

		// Get input --------------------------------------------------------------
		$input = gg_production::get_all(array('scan_id','pasok_ke','scan_detail'),$where." AND status='0'");
		$check = array_filter($input);

		if(!empty($check)){
			$def = gg_module::_gets('default_sc',array('module_reff'),"AND module_code='SC'");
			$def = $def[0]['module_reff'];
	
			$args['input']['status'] = true;
			
			$_inp = array(
				'status'			=> 0,
				'smart_container'	=> '-',
				'gilling'			=> '-',
				'push_cutter'		=> '-',
				'no_pasok'			=> '-',
				'_default'			=> 0
			);

			$_inp['smart_container'] = $input[0]['scan_id'];
			$_inp['_default'] = $def;

			if(empty($input[0]['scan_detail'])){
				$_inp['status'] = 1;
			}else{
				$_inp['status'] = count($input) + 1;

				foreach ($input as $key => $val) {
					$scan = $val['scan_detail'];
					$idxm = self::_check_noTable($scan);
					$user = gg_employee::get_id($idxm,array('ID','name','divisi'));

					if($user[0]['divisi']==1){
						$_inp['gilling'] = $user[0]['name'];
						$_inp['no_pasok'] = $val['pasok_ke'];
					}else if($user[0]['divisi']==2){
						$_inp['push_cutter'] = $user[0]['name'];
					}
				}
			}
		}		
		// End Get input ---------------------------------------------------------

		// Get History -----------------------------------------------------------
		$history = gg_production::get_all(array('ID','scan_id','p_total','p_afkir','pasok_ke','scan_detail'),$where." AND status='1'");
		$args['history']['status'] = count($history)<=0?false:true;

		$_hist = array();
		foreach ($history as $key => $val) {
			$idx = $val['ID'];
			if(!isset($_hist[$idx])){
				$_hist[$idx] = array(
					'smart_container'	=> $val['scan_id'],
					'no_pasok'			=> '-',
					'_total'			=> $val['p_total'],
					'_afkir'			=> $val['p_afkir']
				);
			}

			$scan = $val['scan_detail'];
			$idxm = self::_check_noTable($scan);
			$user = gg_employee::get_id($idxm,array('ID','name','divisi'));

			if($user[0]['divisi']==1){
				$_hist[$idx]['gilling'] = $user[0]['name'];
				$_hist[$idx]['no_pasok'] = $val['pasok_ke'];
			}else if($user[0]['divisi']==2){
				$_hist[$idx]['push_cutter'] = $user[0]['name'];
			}
		}

		$_histy = array();
		foreach ($_hist as $key => $val) {
			$_histy[] = $val;
		}
		// End Get History ----------------------------------------------------------

		// Get Status Produksi ------------------------------------------------------
		self::get_statusProduction($pasok,$block);
		$args['total']['data'] = array(
			'_totGilling'		=> self::$default['_totGilling'],
			'_totPushCutter'	=> self::$default['_totPushCutter'],
			'_totRecehan'		=> self::$default['_totRecehan'],
			'_totAfkir'			=> self::$default['_totAfkir'],
		);
		// End Get Status Produksi --------------------------------------------------

		// Get Status Layout --------------------------------------------------------
		$lay = self::group_operators($pasok);
		$check = array_filter($lay);
		if(!empty($check)){
			$args['layout']['status'] = true;
			$args['layout']['data'] = $lay;
		}
		// End Get Status Layout ----------------------------------------------------

		$args['input']['data'] = $_inp;
		$args['history']['data'] = $_histy;

		return $args;
	}

	public static function get_leaderBlock($scan='',$block=0){
		$y = date('Y');$m = date('m');$d = date('d');
		$div = self::_check_divisi($scan);

		if($div==6){
			$user = self::_check_user(6,$scan);
			$idx = $user['ID'];

			// Check Leader Block Login
			$where = "AND id_user='$idx' AND YEAR(inserted)='$y' AND MONTH(inserted)='$m' AND DAY(inserted)='$d'";
			$check = gg_login::get_all(array('ID'),$where);
			$check = array_filter($check);
			if(empty($check)){
				// Insert Login User
				sobad_db::_insert_table("ggk-login-user",array('id_user' => $user['ID'], 'id_block' => $block));
			}

		}else{
			die(_error::_alert_db('User Bukan Leader Block !!!'));
		}

		return array(
			'ID'		=> $idx,
			'picture'	=> self::$picture,
			'name'		=> $user['name'],
			'nik'		=> $scan
		);
	}

	public static function get_pasok($scan='',$block=0){
		$y = date('Y');$m = date('m');$d = date('d');
		$div = self::_check_divisi($scan);

		if($div==9){
			$user = self::_check_user(9,$scan);

			// Update Login User
			$where = "id_user='$block' AND YEAR(inserted)='$y' AND MONTH(inserted)='$m' AND DAY(inserted)='$d'";
			sobad_db::_update_multiple($where,"ggk-login-user",array('id_pasok' => $user['ID'], 'id_user' => $block));

		}else{
			die(_error::_alert_db('User Bukan Pasok !!!'));
		}

		return array(
			'ID'		=> $user['ID'],
			'picture'	=> self::$picture,
			'name'		=> $user['name'],
			'nik'		=> $scan
		);
	}

	public static function get_smartContainer($scan='',$user=0){
		self::_default(array('user_id' => $user));
		$code = self::_check_codeScan($scan);

		// Check id scan Smart Container
		if($code=='SC'){
			$idx = self::_add_production($scan,"Smart Container");
		}else{
			die(_error::_alert_db("ID bukan Smart Container!!!"));
		}

		self::_check_scPosition($scan);
		self::$default['smart_container'] = $scan;
		return self::$default;
	}

	public static function get_operator($scan='',$args=array()){
		self::_default($args);

		$idx = self::_check_noTable($scan);
		$user = gg_employee::get_id($idx,array('ID','name','divisi','no_induk'));
		$check = array_filter($user);
		if(empty($check)){
			die(_error::_alert_db('Operator belum Terdaftar!!!'));
		}

		$pasok = self::get_noPasok($user[0]['ID']);
		if($user[0]['divisi']==1){
			$pasok += 1;
			self::$default['gilling'] = $user[0]['name'];
			self::$default['no_pasok'] = $pasok;
		}else if($user[0]['divisi']==2){
			$pasok = 0;
			self::$default['push_cutter'] = $user[0]['name'];
		}

		$def = gg_module::_gets('default_sc',array('module_reff'),"AND module_code='SC'");
		$def = $def[0]['module_reff'];
		self::$default['_default'] = $def;

		self::_add_detail($scan,$user[0]['ID'],$pasok,2);
		return self::$default;
	}

	public static function send_data($quantity=0,$afkir=0,$block=0,$data=array()){
		self::_default($data);
		$data = self::$default;

		if(empty($quantity)){
			die(_error::_alert_db("Quantity tidak boleh kosong !!!"));
		}

		sobad_db::_update_single($data['work_id'],'ggk-production',array(
			'p_total'		=> $quantity,
			'p_afkir'		=> $afkir,
			'status'		=> 1
		));

		// Pengurangan Recehan
		if($afkir > 0){
			$receh = self::_get_recehan($block);
			$receh -= $afkir;

			sobad_db::_update_single($block,'ggk-module',array('ID' => $block, 'module_note' => $receh));
		}

		$data['_total'] = $quantity;
		$data['_afkir'] = $afkir;
		$data['input'] = false;

		self::$default = $data;
		self::flow_operator($data['user_id'],$data['work_id']);

		// Get Status Produksi
		self::get_statusProduction($data['user_id'],$block);
		return self::$default;
	}

	Public static function flow_operator($pasok=0,$work=0){
		$detail = self::group_operators($pasok);

		// Get operator
		$opr = gg_production::get_id($work,array('ID','operator_id'));
		foreach ($opr as $key => $val) {
			if($val['divisi_oper']==2){
				$idp = $val['operator_id'];
			}else{
				$idg = $val['operator_id'];
			}
		}

		// Check position
		$pos = array();
		foreach ($detail as $key => $val) {
			// position Push Cutter
			if($val['user_id']==$idp){
				$val['pos_x'] = $key;
				$val['pos_y'] = 0;
				$pos[] = $val;
			}

			foreach ($val['_detail'] as $ky => $vl) {
				// position Gilling
				if($val['user_id']==$idp){
					$vl['pos_x'] = $key;
					$vl['pos_y'] = $ky + 1;
					$pos[] = $vl;
				}
			}
		}

		self::$default['_detail'] = $pos;
	}
}