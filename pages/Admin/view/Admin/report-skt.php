<?php

class reportSKT_admin{
	protected static $object = 'reportSKT_admin';

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

		return print_button($export);
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

		$data = array(
			array(
				'func'			=> 'opt_datepicker',
				'key'			=> '_date',
				'label'			=> 'Tanggal',
				'to'			=> '_date2',
				'data'			=> date('Y-m-d'),
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

	public function _export_excel($data=array()){
		$args = sobad_asset::ajax_conv_json($data);

		$date = $args['_date'];

		ob_start();
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=Report SKT Online ".$_date.".xls");

		$header = ob_get_clean();
		$view = self::_view($data);

		return $header.$view;
	}

	public function _view($data=array()){
		$data = sobad_asset::ajax_conv_json($data);
		$block = $data['block'];

		// Set Tanggal
		$date = empty($data['_date'])?date('Y-m-d'):$data['_date'];
		$date = strtotime($date);

		$y = date('Y',$date);$m = date('m',$date);$d = date('d',$date);
		$where = "AND id_block='$block' AND YEAR(inserted)='$y' AND MONTH(inserted)='$m' AND DAY(inserted)='$d'";

		// Get data block
		$data_block = gg_login::get_all(array('id_user','id_pasok','id_pasok2','id_block'),$where);
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

		$table = array();
		$table['head'] = self::_get_head($data_block[0],$data);
		$table['body'] = self::_get_body($data_block[0],$data);

		ob_start();
		self::_header_table($table);
		return ob_get_clean();
	}

	private static function _get_head($user=array(),$data=array()){
		$args = array();

		// get Periode
		$date1 = format_date_id($data['_date']);
		$date2 = format_date_id($data['_date2']);

		$periode = $date1;
		if($date1!=$date2){
			$periode .= ' S/D '.$date2;
		}

		$args['periode'] = $periode;

		// get Leader
		$leader = gg_employee::get_id($user['id_user'],array('name'));
		$leader = $leader[0]['name'];

		$args['leader'] = $leader;

		// get Block
		$block = gg_module::get_id($user['id_block'],array('module_value'));
		$block = $block[0]['module_value'];

		$args['block'] = $block;

		return $args;
	}

	private static function _get_body($user=array(),$data=array()){
		$args = array();

		$cnt = self::_calc_date($data['_date'],$data['_date2']);
		$now = strtotime($data['_date']);

		$no = 0;
		$pasok = $user['id_pasok'];
		for($i=0;$i<=$cnt;$i++){
			$date = date('Y-m-d',strtotime('+'.$i.' days',$now));
			$date = strtotime($date);

			$y = date('Y',$date);$m = date('m',$date);$d = date('d',$date);
			$where = "AND user_id='$pasok' AND YEAR(scan_date)='$y' AND MONTH(scan_date)='$m' AND DAY(scan_date)='$d'";

			$produksi = gg_production::get_all(array('p_total','operator_id','_inserted'),$where);
			foreach ($produksi as $ky => $vl) {
				$idx = $vl['operator_id'];
				$div = $vl['divisi_oper'];

				if(!isset($args[$div])){
					$args[$div] = array();
				}

				if(!isset($args[$div][$idx])){

					// get Data Grade
					$grade = gg_module::get_id($vl['grade_oper'],array('module_value'));
					$grade = $grade[0]['module_value'];

					// Get Kode Divisi
					$divisi = gg_module::get_id($vl['divisi_oper'],array('module_note'));
					$divisi = $divisi[0]['module_note'];

					$args[$div][$idx] = array(
						'nik'		=> employee_admin::_ID_card($divisi,$vl['no_induk_oper']),
						'nbk'		=> $vl['no_meja_oper'],
						'name'		=> $vl['name_oper'],
						'grd'		=> $grade,
						'_detail'	=> array()
					);
				}

				if(!isset($args[$div][$idx]['_detail'][$i])){
					// get Data Grade
					$grade = gg_module::get_id($vl['grade_oper'],array('module_note'));
					$grade = $grade[0];

					$plan = empty($vl['capacity_oper'])?$grade['module_note']:$vl['capacity_oper'];

					$args[$div][$idx]['_detail'][$i] = array(
						'plan'		=> $plan,
						'actual'	=> 0,
						'time_est'	=> 7,
						'time_act'	=> 0
					);
				}

				$_time = strtotime($vl['_inserted']);
				$_time = date('H:i:s',$_time);

				$time_act = _conv_time('06:00:00',$_time,2);
				$time_act = round($time_act / 60,1);

				$bef_time = $args[$div][$idx]['_detail'][$i]['time_act'];
				if($time_act>=$bef_time){
					$bef_time = $time_act;
				}

				$args[$div][$idx]['_detail'][$i]['actual'] += $vl['p_total'];
				$args[$div][$idx]['_detail'][$i]['time_act'] = $bef_time;
			}
		}


		return array(
			'date1'	=> $data['_date'],
			'date2'	=> $data['_date2'],
			'data'	=> array(
				1		=> $args[1],
				2		=> $args[2]
			)
		);
	}

	private static function _calc_date($date1='',$date2=''){
		$tgl1 = new DateTime($date1);
		$tgl2 = new DateTime($date2);
		$jarak = $tgl2->diff($tgl1);

		return $jarak->d;
	}

	private static function _header_table($head=array()){
		$args = array(
			'periode'		=> date('d M Y'),
			'leader'		=> '-',
			'supervisor'	=> '-',
			'block'			=> '-',
			'kasi_produksi'	=> '-'
		);

		$data = $head['head'];
		foreach ($args as $key => $val) {
			$data[$key] = isset($data[$key])?$data[$key]:$val;
		}

		?>
			<div class="table_flexible">
				<table style="width:100%;" class="table table-striped table-bordered table-hover dataTable no-footer ">
					<thead>
						<tr>
							<th colspan="14" style="width:100px;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
								EBLEK
							</th>
						</tr>
						<tr>
							<th colspan="2" style="text-align:left;width:100px;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;border-top: 1px solid #ddd;">
								PERIODE
							</th>
							<th colspan="3" style="text-align:left;width:100px;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;border-top: 1px solid #ddd;">
								: <?php print($data['periode']) ;?>
							</th>
							<th colspan="2" style="text-align:left;width:100px;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;border-top: 1px solid #ddd;">
								FINISHED GOOD
							</th>
							<th colspan="2" style="text-align:left;width:100px;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">
								: 12ASP
							</th>
							<th colspan="2" style="text-align:left;width:100px;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;border-top: 1px solid #ddd;">
								1. LEADER BLOCK
							</th>
							<th colspan="3" style="text-align:left;width:100px;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">
								: <?php print($data['leader']) ;?>
							</th>
						</tr>
						<tr>
							<th colspan="2" style="text-align:left;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;">
								PROSES
							</th>
							<th colspan="3" style="text-align:left;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;">
								: GILLING
							</th>
							<th colspan="2" style="text-align:left;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;">
								LEMBAGA
							</th>
							<th colspan="2" style="text-align:left;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;border-right: 1px solid #ddd;">
								: UNIT 1 (NS)
							</th>
							<th colspan="2" style="text-align:left;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;">
								2. SUPERVISOR
							</th>
							<th colspan="3" style="text-align:left;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;border-right: 1px solid #ddd;">
								: <?php print($data['supervisor']) ;?>
							</th>
						</tr>
						<tr>
							<th colspan="2" style="text-align:left;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;">
								OUTPUT PRODUK
							</th>
							<th colspan="3" style="text-align:left;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;">
								: BATANGAN 12ASP
							</th>
							<th colspan="2" style="text-align:left;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;">
								WILKER
							</th>
							<th colspan="2" style="text-align:left;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;border-right: 1px solid #ddd;">
								: BLOCK <?php print($data['block']) ;?> (NS)
							</th>
							<th colspan="2" style="text-align:left;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;">
								3. KASI PRODUKSI
							</th>
							<th colspan="3" style="text-align:left;font-family: 'Arial Narrow';font-size:7pt;font-weight: bold;vertical-align: middle;border-right: 1px solid #ddd;">
								: <?php print($data['kasi_produksi']) ;?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php self::_body_table($head['body']) ;?>
					</tbody>
				</table>
			</div>
		<?php
	}

	private static function _body_table($data=array()){
		$date = self::_calc_date($data['date1'],$data['date2']);
		$date1 = strtotime($data['date1']);

		?>
			<tr>
				<td colspan="5" style="font-family: 'Arial Narrow';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
					TANGGAL
				</td>
				<?php
					for($i=0;$i<=$date;$i++){
						echo '
							<td colspan="5" style="font-family: \'Arial Narrow\';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
								'.date('d M Y',strtotime('+'.$i.' days',$date1)).'
							</td>
						';
					}
				?>
			</tr>
			<tr>
				<td colspan="5" style="font-family: 'Arial Narrow';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
					JAM KERJA
				</td>
				<?php
					for($i=0;$i<=$date;$i++){
						echo '
							<td colspan="5" style="font-family: \'Arial Narrow\';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
								7
							</td>
						';
					}
				?>
			</tr>
			<tr>
				<td colspan="5" style="font-family: 'Arial Narrow';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
					PLANNING TPK
				</td>
				<?php
					for($i=0;$i<=$date;$i++){
						echo '
							<td colspan="5" style="font-family: \'Arial Narrow\';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
								&nbsp;
							</td>
						';
					}
				?>
			</tr>
			<tr>
				<td style="width:50px;font-family: 'Arial Narrow';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
					NO
				</td>
				<td style="width:100px;font-family: 'Arial Narrow';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
					NIK
				</td>
				<td style="width:50px;font-family: 'Arial Narrow';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
					NBK
				</td>
				<td style="width:250px;font-family: 'Arial Narrow';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
					NAMA
				</td>
				<td style="width:50px;font-family: 'Arial Narrow';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
					GRD
				</td>
				<?php
					for($i=0;$i<=$date;$i++){
						echo '
							<td style="width:80px;font-family: \'Arial Narrow\';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
								PLAN
							</td>
							<td style="width:80px;font-family: \'Arial Narrow\';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
								ACTUAL
							</td>
							<td style="width:80px;font-family: \'Arial Narrow\';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
								JK
							</td>
							<td style="width:80px;font-family: \'Arial Narrow\';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
								JKE
							</td>
						';
					}
				?>
			</tr>
		<?php

		foreach ($data['data'] as $ky => $dt_val) {
			$no = 0;
			foreach ($dt_val as $key => $val) {
				$no += 1;
				?>
					<tr>
						<td style="font-family: 'Arial Narrow';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
							<?php print($no) ;?>
						</td>
						<td style="font-family: 'Arial Narrow';font-size:7pt;text-align:left;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
							<?php print($val['nik']) ;?>
						</td>
						<td style="font-family: 'Arial Narrow';font-size:7pt;text-align:left;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
							<?php print($val['nbk']) ;?>
						</td>
						<td style="font-family: 'Arial Narrow';font-size:7pt;text-align:left;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
							<?php print($val['name']) ;?>
						</td>
						<td style="font-family: 'Arial Narrow';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
							<?php print($val['grd']) ;?>
						</td>
						<?php
							for($i=0;$i<=$date;$i++){
								$plan = isset($val['_detail'][$i])?$val['_detail'][$i]['plan']:'&nbsp;';
								$actual = isset($val['_detail'][$i])?$val['_detail'][$i]['actual']:'&nbsp;';
								$time_act = isset($val['_detail'][$i])?$val['_detail'][$i]['time_act']:'&nbsp;';
								$time_est = isset($val['_detail'][$i])?$val['_detail'][$i]['time_est']:'&nbsp;';

								echo '
									<td style="width:80px;font-family: \'Arial Narrow\';font-size:7pt;text-align:right;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
										'.$plan.'
									</td>
									<td style="width:80px;font-family: \'Arial Narrow\';font-size:7pt;text-align:right;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
										'.$actual.'
									</td>
									<td style="width:80px;font-family: \'Arial Narrow\';font-size:7pt;text-align:left;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
										'.$time_est.'
									</td>
									<td style="width:80px;font-family: \'Arial Narrow\';font-size:7pt;text-align:left;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
										'.$time_act.'
									</td>
								';
							}
						?>
					</tr>
				<?php
			}

			$_cols = 5 + (($date + 1) * 4);
			?>
				<tr>
					<td colspan="<?php print($_cols) ;?>" style="font-family: 'Arial Narrow';font-size:7pt;text-align:center;font-weight: bold;vertical-align: middle;border: 1px solid #ddd;">
							&nbsp;
					</td>
				</tr>
			<?php
		}
	}
}