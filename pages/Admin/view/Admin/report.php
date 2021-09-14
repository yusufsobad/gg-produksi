<?php

class report_admin extends _page{
	
	protected static $object = 'report_admin';

	protected static $table = 'gg_production';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected static function _array(){
		$args = array(
			'ID',
			'user_id',
			'divisi_id',
			'scan_date'
		);

		return $args;
	}

	protected function table($date=''){
		$data = array();
		$args = self::_array();

		$now = empty($date)?date('Y-m-d'):$date;
		$now = strtotime($now);
		$y = date('Y',$now); $m = date('m',$now); $d = date('d',$now);

		$where = "AND divisi_id='6' AND YEAR(scan_date)='$y' AND MONTH(scan_date)='$m' AND DAY(scan_date)='$d'";

		$object = self::$table;
		$args = $object::get_all($args,$where);
		
		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach($args as $key => $val){
			$no += 1;
			$id = $val['ID'];

			$time = strtotime($val['scan_date']);
			$time = date('H:i',$time);

			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'No'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Jam Ke'		=> array(
					'left',
					'auto',
					$time,
					true
				),
			);
		}
		
		return $data;
	}

	private function head_title(){
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

	protected function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Report',
			'tool'		=> '',
			'action'	=> self::action(),
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected function layout(){
		$box = self::get_box();
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(),
			'script'	=> array()
		);
		
		return portlet_admin($opt,$box);
	}

	protected function action(){
		$date = date('Y-m');
		ob_start();
		?>
			<div style="display: inline-flex;" class="input-group input-medium date date-picker" data-date-format="yyyy-mm" data-date-viewmode="months">
				<input id="monthpicker" type="text" class="form-control" value="<?php print($date); ?>" data-sobad="_filter" data-load="sobad_portlet" data-type="" name="filter_date" onchange="sobad_filtering(this)">
			</div>
			<script type="text/javascript">
				if(jQuery().datepicker) {
		            $("#monthpicker").datepicker( {
					    format: "yyyy-mm-dd",
					    viewMode: "days", 
					    minViewMode: "days",
					    rtl: Metronic.isRTL(),
			            orientation: "right",
			            autoclose: true
					});
		        };
			</script>
		<?php
		$date = ob_get_clean();	

		return $date;
	}

	public function _filter($date=''){
		ob_start();

		$table = self::table($date);
		metronic_layout::sobad_table($table);
		return ob_get_clean();
	}
}