<?php

class _treacibility{
	private static $picture = "https://gg.soloabadi.com/kartosura/asset/img/user/";

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
			'push_id'			=> 0,
			'gilling_id'		=> 0,
			'smart_container'	=> '-',
			'gilling'			=> '-',
			'push_cutter'		=> '-',
			'no_pasok'			=> '-',
			'_total'			=> 0,
			'_afkir'			=> 0,
			'_default'			=> 0,
			'_totGilling'		=> 0,
			'_totKereta'		=> 0,
			'_totRecehan'		=> 0,
			'_totAfkir'			=> 0,
			'_detail'			=> array()
		);

		foreach ($args as $key => $val) {
			$data[$key] = isset($data[$key])?$val:'';
		}

		self::$default = $data;
	}

	public static function _check_picture($name=''){
		$loc = self::$picture;
		$default = "no-profile.jpg";
		return empty($name)?$loc.$default:$loc.$name;
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

	public static function _check_div_induk($scan=''){
		$aw = substr($scan,2,6);
		$where = "AND no_induk='$aw' AND status='1'";
		$user = gg_employee::get_all(array('divisi'),$where);
		
		$check = array_filter($user);
		if(empty($check)){
			return 0;
		}

		return $user[0]['divisi'];
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
		$nik = (int) substr($scan,2,6);

		$module = gg_module::get_id($div,array('ID','module_value'));
		$module = $module[0];

		$user = gg_employee::get_all(array('ID','picture','name','nickname'),"AND divisi='$div' AND no_induk='$nik'");
			
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

	public static function _check_afkirOperator($pasok=0,$pasok2=0){
		$_data = self::_get_dataOperator($pasok);
		$detail = $_data['user'];

		$data = array(
			'status'	=> false,
			'data'		=> array()
		);

		$y = date('Y');$m = date('m');$d = date('d');
		$where = "AND (scan_id='$pasok2' OR scan_id='$pasok') AND YEAR(inserted)='$y' AND MONTH(inserted)='$m' AND DAY(inserted)='$d'";
		
		$_temp = array();
		$load = gg_afkir::get_all(array('user_id','afkir'),$where);
		foreach ($load as $key => $val) {
			$idx = $val['user_id'];
			if(!isset($_temp[$idx])){
				$_temp[$idx] = 0;
			}

			$_temp[$idx] += $val['afkir'];
		}

		$check = array_filter($_temp);
		$data['status'] = empty($check)?false:true;

		foreach ($_temp as $key => $val) {
			if(isset($detail[$key])){
				$data['data'][] = $detail[$key];
			}
		}

		return $data;
	}

	private static function _check_double_divisi($user_id=0){
		$default = self::$default;
		$check = gg_production::get_id($default['work_id'],array('ID','operator_id'));
		if(!empty($check[0]['operator_id'])){
			$divisi = $check[0]['operator_id'];

			// Get data User
			$user = gg_employee::get_id($user_id,array('divisi'));
			$proses = $user[0]['module_value_divi'];
			$user = $user[0]['divisi'];

			if($divisi==$user){
				die(_error::_alert_db("Double Scan Proses( ".$proses." )!!!"));
			}
		}

		return true;
	}

	private static function _check_group_gilling($user_id=0,$pasok=0){
		$id_push = self::$default['push_id'];
		$data = self::_get_dataOperator($pasok);

		$user = $data['user'];
		$flow = $data['flow'];

		$outGroup = array();
		foreach ($flow as $key => $val) {
			if($val['parent']!=$id_push){
				$outGroup[] = $val['child'];
			}
		}

		if(in_array($user_id, $outGroup)){
			die(_error::_alert_db("Operator tidak termasuk dalam Group Push Cutter ini!!!"));
		}

		return true;
	}

	private static function _check_group_pushCutter($user_id=0,$pasok=0){
		$id_gilling = self::$default['gilling_id'];

		$data = self::_get_dataOperator($pasok);

		$user = $data['user'];
		$flow = $data['flow'];

		$outGroup = array();
		foreach ($flow as $key => $val) {
			if($val['parent']!=$user_id){
				$outGroup[] = $val['child'];
			}
		}

		if(in_array($id_gilling, $outGroup)){
			die(_error::_alert_db("Push Cutter tidak membawahi Operator ini!!!"));
		}

		return true;
	}

	private static function _add_detail($scan='',$user_id=0,$pasok=0,$max=1){
		$default = self::$default;
		$y = date('Y');$m = date('m');$d = date('d');

		// Check Double Scan
		$check = gg_production::get_id($default['work_id'],array('ID','scan_detail'),"AND scan_detail='$scan'");
		$check = array_filter($check);
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

	public static function get_afkirUser($pasok=0){
		$data = self::_get_dataOperator($pasok);
		$flow = $data['flow'];
		$user = $data['user'];

		$args = array(
			'gilling' => array(),
			'push_cutter' => array()
		);

		$check = array();
		foreach ($flow as $key => $val) {

			// Set Push Cutter
			$idp = $val['parent'];
			if(!in_array($idp, $check)){
				$check[] = $idp;
				$divi_p = gg_module::get_id($user[$idp]['divisi'],array('module_note'));
				$divi_p = $divi_p[0]['module_note'];

				$args['push_cutter'][] = array(
					'id'		=> $idp,
					'name'		=> $user[$idp]['name'],
					'no_induk'	=> employee_admin::_ID_card($divi_p,$user[$idp]['no_induk'])
				);
			}

			// Set Gilling
			$idg = $val['child'];
			if(!in_array($idg, $check)){
				$check[] = $idg;
				$divi_g = gg_module::get_id($user[$idg]['divisi'],array('module_note'));
				$divi_g = $divi_g[0]['module_note'];

				$args['gilling'][] = array(
					'id'		=> $idg,
					'name'		=> $user[$idg]['name'],
					'no_induk'	=> employee_admin::_ID_card($divi_g,$user[$idg]['no_induk'])
				);
			}
		}

		$defGilling = 17; // Settingan disamakan layout di Desktop
		$defPush = 3; // Settingan disamakan layout di Desktop

		$_gill = array();
		foreach ($args['gilling'] as $key => $val) {
			$cnt = ($key + 1) / ($defGilling + 1);
			$idx = floor($cnt);
			if(!isset($_gill[$idx])){
				$_gill[$idx] = array(
					'data'		=> array()
				);
			}

			$_gill[$idx]['data'][] = $val;
		}

		$_push = array();
		foreach ($args['push_cutter'] as $key => $val) {
			$cnt = ($key + 1) / ($defPush + 1);
			$idx = floor($cnt);
			if(!isset($_push[$idx])){
				$_push[$idx] = array(
					'data'		=> array()
				);
			}

			$_push[$idx]['data'][] = $val;
		}

		$args['gilling'] = $_gill;
		$args['push_cutter'] = $_push;
		return $args;
	}

	public static function get_underCapacity($pasok=0){
		$data = self::_get_dataOperator($pasok);
		$flow = $data['flow'];
		$user = $data['user'];

		$args = array(
			'gilling' => array(),
			'push_cutter' => array()
		);

		$args = array();
		foreach ($flow as $key => $val) {

			// Set Push Cutter
			$idp = $val['parent'];
			if($user[$idp]['under_sts']==1){
				if(!in_array($idp, $check)){
					$check[] = $idp;
					$divi_p = gg_module::get_id($user[$idp]['divisi'],array('module_note'));
					$divi_p = $divi_p[0]['module_note'];

					$args['push_cutter'][] = array(
						'id'		=> $idp,
						'name'		=> $user[$idp]['name'],
						'no_induk'	=> employee_admin::_ID_card($divi_p,$user[$idp]['no_induk']),
						'target'	=> $user[$idp]['capacity']
					);
				}
			}

			// Set Gilling
			$idg = $val['child'];
			if($user[$idg]['under_sts']==1){
				if(!in_array($idg, $check)){
					$check[] = $idg;
					$divi_g = gg_module::get_id($user[$idg]['divisi'],array('module_note'));
					$divi_g = $divi_g[0]['module_note'];

					$args['gilling'][] = array(
						'id'		=> $idg,
						'name'		=> $user[$idg]['name'],
						'no_induk'	=> employee_admin::_ID_card($divi_g,$user[$idg]['no_induk']),
						'target'	=> $user[$idg]['capacity']
					);
				}
			}
		}

		$defGilling = 17; // Settingan disamakan layout di Desktop
		$defPush = 3; // Settingan disamakan layout di Desktop

		$_gill = array();
		foreach ($args['gilling'] as $key => $val) {
			$cnt = ($key + 1) / ($defGilling + 1);
			$idx = floor($cnt);
			if(!isset($_gill[$idx])){
				$_gill[$idx] = array(
					'data'		=> array()
				);
			}

			$_gill[$idx]['data'][] = $val;
		}

		$_push = array();
		foreach ($args['push_cutter'] as $key => $val) {
			$cnt = ($key + 1) / ($defPush + 1);
			$idx = floor($cnt);
			if(!isset($_push[$idx])){
				$_push[$idx] = array(
					'data'		=> array()
				);
			}

			$_push[$idx]['data'][] = $val;
		}

		$args['gilling'] = $_gill;
		$args['push_cutter'] = $_push;
		return $args;
	}

	public static function _get_dataOperator($id=0,$date='',$limit=''){
		$date = empty($date)?date('Y-m-d'):$date;
		$date = strtotime($date);

		$y = date('Y',$date);$m = date('m',$date);$d = date('d',$date);
		$where = "AND user_id='$id' AND (YEAR(scan_date)='$y' AND MONTH(scan_date)='$m' AND DAY(scan_date)='$d') ".$limit;
		$flow = gg_production::get_all(array('ID','p_total','p_afkir','operator_id'),$where);

		$push = array();$_temp = array();
		foreach ($flow as $key => $val) {
			if(is_null($val['operator_id']) || empty($val['operator_id'])){
				continue;
			}

			$idx = $val['operator_id'];

		// Get data picture
			$_user = gg_employee::get_id($idx,array('picture'));
			$_user = $_user[0]['notes_pict'];

		// Set data Operator	
			if(!isset($push[$idx])){

				// Get Afkir Operator
				$afkir = 0;
				$_where = "AND user_id='$idx' AND (YEAR(inserted)='$y' AND MONTH(inserted)='$m' AND DAY(inserted)='$d')";

				$load = gg_afkir::get_all(array('afkir'),$_where);
				foreach ($load as $ky => $vl) {
					$afkir += $vl['afkir'];
				}

				$push[$idx] = array(
					'divisi'	=> $val['divisi_oper'],
					'user_id'	=> $idx,
					'name'		=> $val['nickname_oper'],
					'no_induk'	=> $val['no_induk_oper'],
					'picture'	=> self::_check_picture($_user),
					'under_sts'	=> $val['under_capacity_oper'],
					'capacity'	=> $val['capacity_oper'],
					'_total'	=> 0,
					'_afkir'	=> $afkir,
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

		return array('user' => $push,'flow' => $_temp);
	}

	private static function group_operators($id=0){
		$_data = self::_get_dataOperator($id);
		$push = $_data['user'];
		$_temp = $_data['flow'];

	// Set data flow Construct
		$idm = -1;
		$data = array();$_data = array();$_idpr = array();
		foreach ($_temp as $key => $val) {
			$idp = $val['parent'];
			if(!isset($_data[$idp])){
				$idm += 1;
				$_data[$idp] = array();

				$push[$idp]['_total'] = format_nominal($push[$idp]['_total']);
				$push[$idp]['_afkir'] = format_nominal($push[$idp]['_afkir']);

				$data[$idm] = $push[$idp];
				$_idpr[$idp] = $idm;
			}

			$idc = $val['child'];
			if(!in_array($idc,$_data[$idp])){
				$_data[$idp][] = $idc;

				$push[$idc]['_total'] = format_nominal($push[$idc]['_total']);
				$push[$idc]['_afkir'] = format_nominal($push[$idc]['_afkir']);

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

		$baki = gg_module::_gets('default_sc',array('module_reff'),"AND module_code='KRT'");
		$baki = $baki[0]['module_reff'];

		$kereta = count($product) / $baki;
		$receh = self::_get_recehan($block);

		self::$default['_totGilling'] = format_nominal($total);
		self::$default['_totKereta'] = format_nominal($kereta);
		self::$default['_totAfkir'] = format_nominal($afkir);
		self::$default['_totRecehan'] = format_nominal($receh);
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

		$_inp = array();
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
					$user = gg_employee::get_id($idxm,array('ID','nickname','divisi'));

					if($user[0]['divisi']==1){
						$_inp['gilling'] = $user[0]['nickname'];
						$_inp['no_pasok'] = $val['pasok_ke'];
					}else if($user[0]['divisi']==2){
						$_inp['push_cutter'] = $user[0]['nickname'];
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
			$user = gg_employee::get_id($idxm,array('ID','nickname','divisi'));

			if($user[0]['divisi']==1){
				$_hist[$idx]['gilling'] = $user[0]['nickname'];
				$_hist[$idx]['no_pasok'] = $val['pasok_ke'];
			}else if($user[0]['divisi']==2){
				$_hist[$idx]['push_cutter'] = $user[0]['nickname'];
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
			'_totKereta'		=> self::$default['_totKereta'],
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
		$div = self::_check_div_induk($scan);

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
			'picture'	=> self::_check_picture($user['notes_pict']),
			'name'		=> $user['nickname'],
			'nik'		=> $scan
		);
	}

	public static function get_pasok($scan='',$block=0){
		$y = date('Y');$m = date('m');$d = date('d');
		$div = self::_check_div_induk($scan);

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
			'picture'	=> self::_check_picture($user['notes_pict']),
			'name'		=> $user['nickname'],
			'nik'		=> $scan
		);
	}

	public static function _check_pasok2($leader=0){
		$y = date('Y');$m = date('m');$d = date('d');
		$data = array(
			'status'	=> false,
			'data'		=> array()
		);

	// Check Pasok 2 sudah login atau belum	
		$whr = "AND id_user='$leader' AND YEAR(inserted)='$y' AND MONTH(inserted)='$m' AND DAY(inserted)='$d'";
		$login = gg_login::get_all(array("id_pasok2"),$whr);
		$check = array_filter($login);
		if(!empty($check)){
			$login = $login[0]['id_pasok2'];

			if($login>0){
				$data['status'] = true;

			// Get data Pasok2	
				$user = gg_employee::get_id($login,array('ID','name','nickname','picture','divisi','no_induk'));
				$check = array_filter($user);
				if(!empty($user)){
					$user = $user[0];
					$_data = array(
						'ID'		=> $user['ID'],
						'picture'	=> self::_check_picture($user['notes_pict']),
						'name'		=> $user['nickname'],
						'nik'		=> $user['module_note_divi'].sprintf("%04d",$user['no_induk'])
					);

					$data['data'] = $_data;
					return $data;
				}
			}
		}

		return $data;
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

	public static function get_operator($scan='',$id_pasok=0,$args=array()){
		self::_default($args);

		$idx = self::_check_noTable($scan);
		$user = gg_employee::get_id($idx,array('ID','nickname','divisi','no_induk'));
		$check = array_filter($user);
		if(empty($check)){
			die(_error::_alert_db('Operator belum Terdaftar!!!'));
		}

		// Check Double scan Divisi
		self::_check_double_divisi($user[0]['ID']);

		$pasok = self::get_noPasok($user[0]['ID']);
		if($user[0]['divisi']==1){
			$pasok += 1;
			self::$default['gilling'] = $user[0]['nickname'];
			self::$default['no_pasok'] = $pasok;
			self::$default['gilling_id'] = $user[0]['ID'];

			if(self::$default['push_id']>0){
				// Check operator Gilling in Push Cutter
				self::_check_group_gilling($user[0]['ID'],$id_pasok);
			}
	
		}else if($user[0]['divisi']==2){
			$pasok = 0;
			self::$default['push_cutter'] = $user[0]['nickname'];
			self::$default['push_id'] = $user[0]['ID'];

			if(self::$default['gilling_id']>0){
				// Check operator Gilling in Push Cutter
				self::_check_group_pushCutter($user[0]['ID'],$id_pasok);
			}
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

		$data['_total'] = format_nominal($quantity);
		$data['_afkir'] = format_nominal($afkir);
		$data['input'] = false;

		self::$default = $data;
		self::flow_operator($data['user_id'],$data['work_id']);

		// Get Status Produksi
		self::get_statusProduction($data['user_id'],$block);
		return self::$default;
	}

	public static function send_afkir($pasok=0,$data=array()){
		foreach ($data as $key => $val) {
			$args = array(
				'user_id'		=> $pasok,
				'operator_id'	=> $val['Key']
			);

			_production::send_data($val['Value'],$args);
		}

		return array('data' => "Data Berhasil Disimpan!!!");
	}

	public static function send_target($data=array()){
		foreach ($data as $key => $val) {
			$q = sobad_db::_update_single($val['Key'],'ggk-employee',array(
				'ID' 		=> $val['Key'],
				'capacity' 	=> $val['Value']
			));
		}

		// Update History Target
		$date = date('Y-m-d');
		foreach ($data as $key => $val) {
			$idx = $val['Key'];
			$capacity = $val['Value'];

			// Check History
			$target = gg_target::get_all(array('ID'),"AND user_id='$idx' AND _date='$date'");
			$check = array_filter($target);
			if(empty($check)){
				// Get Grade
				$user = gg_employee::get_id($idx,array('grade'));
				$grade = $user[0]['grade'];

				$q = sobad_db::_insert_table('ggk-history-target',array(
					'user_id'	=> $idx,
					'target'	=> $capacity,
					'grade'		=> $grade,
					'_date'		=> $date
				));
			}else{
				$q = sobad_db::_update_single($target[0]['ID'],'ggk-history-target',array(
					'user_id'	=> $idx,
					'target'	=> $target,
				));
			}
		}

		return array('data' => "Data Berhasil Disimpan!!!");
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
				$val['pos_x'] = 0;
				$val['pos_y'] = $key;
				$pos[] = $val;
			}

			foreach ($val['_detail'] as $ky => $vl) {
				// position Gilling
				if($val['user_id']==$idp){
					$vl['pos_x'] = $ky + 1;
					$vl['pos_y'] = $key;
					$pos[] = $vl;
				}
			}
		}

		self::$default['_detail'] = $pos;
	}
}