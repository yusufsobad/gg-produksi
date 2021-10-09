<?php

class report_admin{
	protected static $object = 'report_admin';

	protected static $table = 'gg_production';

	protected static $date_report = '';

	private static function head_title(){
		$args = array(
			'title'	=> 'Report <small>data report</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'report'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected static function get_form(){		
		$box = array(
			'label'		=> 'Form Report',
			'tool'		=> '',
			'action'	=> '',
			'object'	=> self::$object,
			'func'		=> '_form',
			'data'		=> ''
		);

		return $box;
	}

	protected static function get_box(){		
		$box = array(
			'ID'		=> 'report_tracking',
			'label'		=> 'Report',
			'tool'		=> '',
			'action'	=> '',
			'func'		=> 'sobad_table',
			'data'		=> array(
				'class'		=> '',
				'table'		=> array()
			)
		);

		return $box;
	}

	public static function _sidemenu(){
		$metronic = new metronic_layout();
	
		$title = self::head_title();
		$data = array();
	
		$data[] = array(
			'style'		=> array(),
			'script'	=> array(),
			'func'		=> '_portlet',
			'data'		=> self::get_form()
		);

		$data[] = array(
			'style'		=> array(),
			'script'	=> array(),
			'func'		=> '_portlet',
			'data'		=> self::get_box()
		);
	
		ob_start();
		$metronic->_head_content($title);
		$metronic->_content('_panel',$data);
		return ob_get_clean();
	}

	public static function _form(){
		$block = array();
		$mod = gg_module::_gets('block',array('ID','module_value'));
		foreach ($mod as $key => $val) {
			$block[$val['ID']] = 'Block '.$val['module_value'];
		}

		$type = array(
			1 => 'Data Pasok dan Afkir LI',
			2 => 'Data Afkir Push Cutter',
			3 => 'Data Afkir Glling',
			4 => 'Total Afkir Operator'
		);

		$data = array(
			array(
				'func'			=> 'opt_datepicker',
				'key'			=> '_date',
				'label'			=> 'Tanggal',
				'class'			=> 'input-circle',
				'value'			=> date('Y-m-d')
			),
			array(
				'func'			=> 'opt_select',
				'data'			=> $block,
				'key'			=> 'block',
				'label'			=> 'Block',
				'searching'		=> true,
				'class'			=> 'input-circle',
				'select'		=> 0,
			),
			array(
				'func'			=> 'opt_select',
				'data'			=> $type,
				'key'			=> 'type',
				'label'			=> 'Jenis Laporan',
				'searching'		=> true,
				'class'			=> 'input-circle',
				'select'		=> 0,
			),
		);

		?>
			<div class="row">
				<?php metronic_layout::sobad_form($data); ?>
				<div style="text-align: right;margin-right: 20px;">
					<button data-sobad="_view" data-load="report_tracking" data-type="" type="button" class="btn blue" data-dismiss="modal" onclick="sobad_submitLoad(this)" ><i class="fa fa-book"></i> Report</button>
				</div>
			</div>
		<?php
	}

	// ----------------------------------------------------------
	// Print data report ----------------------------------------
	// ----------------------------------------------------------

	public function _view($data=array()){
		$data = sobad_asset::ajax_conv_json($data);
		$block = $data['block'];

		// Set Tanggal
		$date = empty($data['_date'])?date('Y-m-d'):$data['_date'];
		$date = strtotime($date);

		self::$date_report = $date;
		$y = date('Y',$date);$m = date('m',$date);$d = date('d',$date);
		$where = "AND id_block='$block' AND YEAR(inserted)='$y' AND MONTH(inserted)='$m' AND DAY(inserted)='$d'";

		// Get data block
		$data_block = gg_login::get_all(array('id_user','id_pasok','id_pasok2'),$where);
		$check = array_filter($data_block);
		if(empty($check)){
			$table = array();
			$table['class'] = '';
			$table['table'] = array();

			$table['table'][0]['tr'] = array('');
			$table['table'][0]['td'] = array(
				'Keterangan'	=> array(
					'center',
					'auto',
					'Tidak ada data yang ditemukan',
					false
				)
			);

			ob_start();
			metronic_layout::sobad_table($table);
			return ob_get_clean();
		}

		$data_block = $data_block[0];

		$pasok = $data_block['id_pasok'];
		$pasok2 = $data_block['id_pasok2'];
		$leader = $data_block['id_user'];

		switch ($data['type']) {
			case 1: // data Pasok dan Afkir LI 
				$afkir = self::_get_afkirTotalOperator($pasok,$pasok2,4);
				$table = self::_get_tableAfkir($afkir,true);
				break;

			case 2: // data Afkir Push Cutter
				$afkir = self::_get_afkirOperator($pasok2,2);
				$table = self::_get_tableAfkir($afkir);
				break;

			case 3: // data Afkir Gilling
				$afkir = self::_get_afkirOperator($pasok2,1);
				$table = self::_get_tableAfkir($afkir);
				break;

			case 4: // Total Afkir Operator
				$afkir = self::_get_afkirTotalOperator($pasok,$pasok2,1);
				$table1 = self::_get_tableAfkir($afkir);

				$afkir = self::_get_afkirTotalOperator($pasok,$pasok2,2);
				$table2 = self::_get_tableAfkir($afkir);

				$afkir = self::_get_afkirTotalOperator($pasok,$pasok2,3);
				$table3 = self::_get_tableAfkir($afkir);

				$table = '
					<div class="row"> '.$table1.' </div>
					<div class="row"> '.$table2.' </div>
					<div class="row"> '.$table3.' </div>
				';
				break;
			
			default:
				return _error::_alert_db("Jenis Laporan tidak Tersedia!!!");
				break;
		}

		return $table;
	}

	private static function _get_tableAfkir($data=array(),$pasok_ke=false){
		// Get jumlah pasok
		$pasok = 0;
		foreach ($data as $key => $val) {
			if($pasok >= count($val)){
				$pasok = count($val) - 1;
			}
		}

		// Buat Table
		ob_start();
		?>
			<div class="table_flexible">
				<table class="">
					<thead>
						<tr role="row">
							<th rowspan="2" style="width:10%;text-align:center;font-family: calibriBold;font-weight: bold;">
								NIK
							</th>
							<th rowspan="2" style="width:25%;text-align:center;font-family: calibriBold;font-weight: bold;">
								Nama
							</th>

							<?php if($pasok_ke): ?>
								<th colspan="<?php print($pasok) ;?>" style="text-align:center;font-family: calibriBold;font-weight: bold;">
									Pasok Ke
								</th>
								<th rowspan="2" style="width:10%;text-align:center;font-family: calibriBold;font-weight: bold;">
									Total
								</th>
							<?php endif; ?>
							
							<th colspan="<?php print($pasok) ;?>" style="text-align:center;font-family: calibriBold;font-weight: bold;">
								Afkir
							</th>
							<th rowspan="2" style="width:10%;text-align:center;font-family: calibriBold;font-weight: bold;">
								Total
							</th>
						</tr>
						<tr>
							<?php
								for($i=1;$i<=$pasok;$i++){
									echo '
									<th style="width:8%;text-align:center;">
										'.$i.'
									</th>';
								}
							?>
						</tr>
					</thead>
					<tbody>
						<?php 
							foreach($data as $key => $val):
								$user = gg_employee::get_id($key,array('name','divisi','no_induk'));
								$user = $user[0];

								$nik = employee_admin::_ID_card($user['module_note_divi'],$val['no_induk']);
							?>
							
							<td style="text-align: right">
								<?php print($nik) ?>
							</td>
							<td style="text-align: left">
								<?php print(ucwords($user['name'])) ?>
							</td>

							<?php
								if($pasok_ke):
									$total = 0;
									for($i=1;$i<=$pasok;$i++){
										$afk = isset($val[$i])?$val[$i]['total']:0;

										echo '
											<td style="text-align:center;">
												'.$afk.'
											</td>
										';

										$total += $afk;
									}
							?>
							<td style="text-align: right">
								<?php print(format_nominal($total)) ?>
							</td>
							<?php
								endif;

								$total = 0;
								for($i=1;$i<=$pasok;$i++){
									if($pasok_ke){
										$afk = isset($val[$i])?$val[$i]['afkir']:0;
									}else{
										$afk = isset($val[$i])?$val[$i]:0;
									}

									echo '
										<td style="text-align:center;">
											'.$afk.'
										</td>
									';

									$total += $afk;
								}
							?>
							<td style="text-align: right">
								<?php print(format_nominal($total)) ?>
							</td>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php

		return ob_get_clean();
	}

	private static function _get_groupOperator($id=0){
		$date = self::$date_report;

		$y = date('Y',$date);$m = date('m',$date);$d = date('d',$date);
		$where = "AND user_id='$id' AND (YEAR(scan_date)='$y' AND MONTH(scan_date)='$m' AND DAY(scan_date)='$d')";
		$flow = gg_production::get_all(array('ID','p_total','p_afkir','operator_id','pasok_ke'),$where);

		$push = array();$_temp = array();
		foreach ($flow as $key => $val) {
			$idx = $val['operator_id'];

		// Set data Operator	
			if(!isset($push[$idx])){
				$push[$idx] = $val;
			}

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

		// Set Grouping Operator
		$data = array();
		foreach ($_temp as $key => $val) {
			$idp = $val['parent'];
			if(!isset($_data[$idp])){
				$data[$idp] = $push;
			}

			$idc = $val['child'];
			if(!in_array($idc,$_data[$idp])){
				$pasok = $push[$idc]['pasok_ke'];
				$data[$idp]['pasok_ke'][$pasok][$idc] = $push[$idc];
			}
		}

		return $data;
	}

	private static function _get_detailAfkir($id=0,$pasok=0){
		$date = self::$date_report;
		$y = date('Y',$date);$m = date('m',$date);$d = date('d',$date);

		$where = empty($pasok)?"":"AND pasok='$pasok' ";
		$where .= "AND YEAR(inserted)='$y' AND MONTH(inserted)='$m' AND DAY(inserted)='$d'";

		$load = gg_afkir::get_id($id,array('afkir','pasok'),$where);
		$check = array_filter($load);

		if($pasok>0 && !empty($check)){
			return $load;
		}

		return empty($check)?0:$load[0]['afkir'];
	}

	private static function _get_afkirOperator($pasok2=0,$divisi=0){
		$date = self::$date_report;
		$y = date('Y',$date);$m = date('m',$date);$d = date('d',$date);
		$where = "AND _user_id.divisi='$divisi' AND scan_id='$pasok2' AND YEAR(inserted)='$y' AND MONTH(inserted)='$m' AND DAY(inserted)='$d'";
		
		$_temp = array();
		$load = gg_afkir::get_all(array('user_id','afkir','pasok'),$where);

		$data = array();
		foreach ($load as $key => $val) {
			$idx = $val['user_id'];
			if(!isset($data[$idx])){
				$data[$idx] = array();
			}

			$data[$idx][$val['pasok']] = $val['afkir'];
		}

		return $data;		
	}

	private static function _get_afkirTotalOperator($pasok=0,$pasok2=0,$divisi=0){
		$detail = self::_get_groupOperator($pasok);
		if($divisi==1){
			$gilling = self::_get_afkirOperator($pasok2,1);
		}else if($divisi==2){
			$push_cutter = self::_get_afkirOperator($pasok2,2);
		}else{
			$gilling = self::_get_afkirOperator($pasok2,1);
			$push_cutter = self::_get_afkirOperator($pasok2,2);
		}

		$data = array();
		foreach ($detail as $key => $val) {
			$pasok = $val['pasok_ke'];
			$pasok_grp = count($pasok);

			for($i=1;$i<=$pasok_grp;$i++){
				$idx = 0;
				$pasok_ke = $val['pasok_ke'];

				if($divisi==2 || $divisi==3){
					$idx = $val['user_id'];
					$afkir = 0;

					if(isset($pasok_ke[$i])){
						foreach ($pasok_ke[$i] as $ky => $vl) {
							$afkir += $vl['p_afkir'];
						}
					}
		
					$afkir_pc = isset($push_cutter[$idx][$i])?$push_cutter[$idx][$i]['_afkir']:0;
					$data[$idx][$i] = $afkir + $afkir_pc;
				}

				if($divisi==1 || $divisi==4){
					$afkir_gl[$i] = 0;
					foreach ($pasok_ke[$i] as $ky => $vl) {
						$idg = $vl['user_id'];

						$afkir = $vl['p_afkir'];
						$afkir_gil = isset($gilling[$idg][$i])?$gilling[$idg][$i]['_afkir']:0;

						$afkir_gl[$i] += $afkir_gil;

						if($divisi==4){
							$data[$idx][$i]['afkir'] = $afkir;
							$data[$idx][$i]['total'] = $vl['p_total'];
						}else{
							$data[$idg][$i] = $afkir + $afkir_gl;
						}
					}
				}

				if($divisi==3){
					$data[$idx][$i] += $afkir_gl[$i];
				}
			}
		}

		return $data;
	}
}