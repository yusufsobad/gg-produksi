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
			'action'	=> self::print_action(),
			'func'		=> 'sobad_table',
			'data'		=> array(
				'class'		=> '',
				'table'		=> array()
			)
		);

		return $box;
	}

	private static function print_action(){
		$export = array(
			'ID'	=> 'excel_0',
			'func'	=> '_export_excel',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-file-excel-o',
			'label'	=> 'Export',
			'type'	=> '',
			'spin'	=> true
		);		

		$xls = print_button($export);

		$print = array(
			'ID'	=> 'preview_0',
			'func'	=> '_preview',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-print',
			'label'	=> 'Print',
			'type'	=> ''
		);	

		$print = '';//print_button($print);

		return $xls." ".$print;
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

		$type = self::_convert_type();

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

	private static function _convert_type($id=0){
		$type = array(
			1 => 'Data Pasok dan Afkir LI',
			2 => 'Data Afkir Push Cutter',
			3 => 'Data Afkir Glling',
			4 => 'Total Afkir Operator'
		);

		if(empty($id)){
			return $type;
		}

		return isset($type[$id])?$type[$id]:'';
	}

	// ----------------------------------------------------------
	// Print data report ----------------------------------------
	// ----------------------------------------------------------

	public function _export_excel($data=array()){
		$args = sobad_asset::ajax_conv_json($data);

		$date = $args['_date'];
		$title = self::_convert_type($args['type']);

		ob_start();
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=".$title." ".$_date.".xls");

		$header = ob_get_clean();
		$view = self::_view($data);

		return $header.$view;
	}

	public function _preview($data=array()){
		$args = sobad_asset::ajax_conv_json($data);

		$date = $args['_date'];
		$title = self::_convert_type($args['type']);

		$args = array(
			'data'		=> $data,
			'style'		=> array(),
			'object'	=> self::$object,
			'html'		=> '_html',
			'setting'	=> array(
				'posisi'	=> 'landscape',
				'layout'	=> 'A4',
			),
			'name save'	=> $title.' '.$date
		);

		return sobad_convToPdf($args);
	}

	public static function _html($data=array()){
		?>
			<page backtop="5mm" backbottom="5mm" backleft="5mm" backright="5mm" pagegroup="new">
			<?php
				echo self::_view($data);
			?>
			</page>
		<?php
	}

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
				$title = array(
					'title'		=> self::_convert_type(1),
					'note'		=> ''
				);

				$afkir = self::_get_afkirTotalOperator($pasok,$pasok2,4);
				$table = self::_get_tableAfkir($afkir,$title,true);
				break;

			case 2: // data Afkir Push Cutter
				$title = array(
					'title'		=> self::_convert_type(2),
					'note'		=> ''
				);

				$afkir = self::_get_afkirOperator($pasok2,2);
				$table = self::_get_tableAfkir($afkir,$title);
				break;

			case 3: // data Afkir Gilling
				$title = array(
					'title'		=> self::_convert_type(3),
					'note'		=> ''
				);

				$afkir = self::_get_afkirOperator($pasok2,1);
				$table = self::_get_tableAfkir($afkir,$title);
				break;

			case 4: // Total Afkir Operator
				$title1 = array(
					'title'		=> "Data Afkir Gilling",
					'note'		=> "total afkir dari LI dan Gilling"
				);

				$title2 = array(
					'title'		=> "Data Afkir Push Cutter",
					'note'		=> "total afkir dari LI dan PC"
				);

				$title3 = array(
					'title'		=> "Data Afkir Total",
					'note'		=> "total afkir dari LI, PC dan Gilling"
				);

				$afkir = self::_get_afkirTotalOperator($pasok,$pasok2,1);
				$table1 = self::_get_tableAfkir($afkir,$title1);

				$afkir = self::_get_afkirTotalOperator($pasok,$pasok2,2);
				$table2 = self::_get_tableAfkir($afkir,$title2);

				$afkir = self::_get_afkirTotalOperator($pasok,$pasok2,3);
				$table3 = self::_get_tableAfkir($afkir,$title3);

				$table = '
					<div style="margin-bottom:20px;">
						'.$table1.' 
					</div>
					<div style="margin-bottom:20px;">
						'.$table2.' 
					</div>
					<div style="margin-bottom:20px;">
						'.$table3.' 
					</div>
				';
				break;
			
			default:
				return _error::_alert_db("Jenis Laporan tidak Tersedia!!!");
				break;
		}

		return $table;
	}

	private static function _get_tableAfkir($data=array(),$title=array(),$pasok_ke=false){
		// Get jumlah pasok
		$pasok = 0;
		foreach ($data as $key => $val) {
			if($pasok <= count($val)){
				$pasok = count($val);
			}
		}

		$cols = $pasok + 3;
		if($pasok_ke){
			$cols += ($pasok + 1);
		}

		// Buat Table
		ob_start();
		?>
			<div class="table_flexible">
				<table style="width:100%;" class="table table-striped table-bordered table-hover dataTable no-footer ">
					<thead>
						<tr>
							<th colspan="<?php print($cols) ;?>">
								<label style="font-weight:bold;display:block;text-align: center;">
									<?php print($title['title']) ;?>
								</label>
							</th>
						</tr>
						<tr>
							<th colspan="<?php print($cols) ;?>">
								<small style="text-align: center;"><?php print($title['note']) ;?></small>
							</th>
						</tr>
						<tr>
							<th colspan="<?php print($cols) ;?>">&nbsp;</th>
						</tr>
						<tr role="row">
							<th rowspan="2" style="width:100px;text-align:center;font-family: calibriBold;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
								NIK
							</th>
							<th rowspan="2" style="width:250px;text-align:center;font-family: calibriBold;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
								Nama
							</th>

							<?php if($pasok_ke): ?>
								<th colspan="<?php print($pasok) ;?>" style="text-align:center;font-family: calibriBold;font-weight: bold;border: 1px solid #ddd;">
									Pasok Ke
								</th>
								<th rowspan="2" style="width:100px;text-align:center;font-family: calibriBold;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
									Total
								</th>
							<?php endif; ?>
							
							<th colspan="<?php print($pasok) ;?>" style="text-align:center;font-family: calibriBold;font-weight: bold;border: 1px solid #ddd;">
								Afkir
							</th>
							<th rowspan="2" style="width:100px;text-align:center;font-family: calibriBold;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
								Total
							</th>
						</tr>
						<tr>
							<?php
								if($pasok_ke){
									for($j=1;$j<=$pasok;$j++){
										echo '
										<th style="width:70px;text-align:center;border: 1px solid #ddd;">
											'.$j.'
										</th>';
									}
								}

								for($i=1;$i<=$pasok;$i++){
									echo '
									<th style="width:70px;text-align:center;border: 1px solid #ddd;">
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

								$nik = employee_admin::_ID_card($user['module_note_divi'],$user['no_induk']);
							?>
						<tr>
							<td style="text-align: right;border: 1px solid #ddd;">
								<?php print($nik) ?>
							</td>
							<td style="text-align: left;border: 1px solid #ddd;">
								<?php print(ucwords($user['name'])) ?>
							</td>

							<?php
								if($pasok_ke):
									$total = 0;
									for($j=1;$j<=$pasok;$j++){
										$afk = isset($val[$j])?$val[$j]['total']:0;

										echo '
											<td style="text-align:center;border: 1px solid #ddd;">
												'.$afk.'
											</td>
										';

										$total += $afk;
									}
							?>
							<td style="text-align: right;border: 1px solid #ddd;">
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
										<td style="text-align:center;border: 1px solid #ddd;">
											'.$afk.'
										</td>
									';

									$total += $afk;
								}
							?>
							<td style="text-align: right;border: 1px solid #ddd;">
								<?php print(format_nominal($total)) ?>
							</td>
						</tr>
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
				$push[$idx] = array();
			}

			$pask = $val['pasok_ke'];
			if(!isset($push[$idx][$pask])){
				$push[$idx][$pask] = $val;
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
			if(!isset($data[$idp])){
				$data[$idp] = $push[$idp][0];
				$data[$idp]['pasok_ke'] = array();
			}

			$idc = $val['child'];
			foreach ($push[$idc] as $ky => $vl) {
				if(!isset($data[$idp]['pasok_ke'][$ky])){
					$data[$idp]['pasok_ke'][$ky] = array();
				}

				$data[$idp]['pasok_ke'][$ky][$idc] = $push[$idc][$ky];
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

	private static function _get_afkirOperator($pasok=0,$pasok2=0,$divisi=0){
		$date = self::$date_report;
		$y = date('Y',$date);$m = date('m',$date);$d = date('d',$date);
		$where = "AND _user_id.divisi='$divisi' AND scan_id IN ('$pasok','$pasok2') AND YEAR(inserted)='$y' AND MONTH(inserted)='$m' AND DAY(inserted)='$d'";
		
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
			$gilling = self::_get_afkirOperator($pasok,$pasok2,1);
		}else if($divisi==2){
			$push_cutter = self::_get_afkirOperator($pasok,$pasok2,2);
		}else{
			$gilling = self::_get_afkirOperator($pasok,$pasok2,1);
			$push_cutter = self::_get_afkirOperator($pasok,$pasok2,2);
		}

		$data = array();
		foreach ($detail as $key => $val) {
			$pasok = $val['pasok_ke'];
			$pasok_grp = count($pasok);

			for($i=1;$i<=$pasok_grp;$i++){
				$idx = 0;
				$pasok_ke = $val['pasok_ke'];

				if($divisi==2 || $divisi==3){
					$idx = $val['operator_id'];
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
						$idg = $vl['operator_id'];

						$afkir = $vl['p_afkir'];
						$afkir_gil = isset($gilling[$idg][$i])?$gilling[$idg][$i]['_afkir']:0;

						$afkir_gl[$i] += $afkir_gil;

						if($divisi==4){
							$data[$idg][$i]['afkir'] = $afkir;
							$data[$idg][$i]['total'] = $vl['p_total'];
						}else{
							$data[$idg][$i] = $afkir + $afkir_gil;
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