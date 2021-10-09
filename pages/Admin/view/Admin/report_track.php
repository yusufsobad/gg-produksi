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
			'style'		=> array(self::$object,'_style'),
			'script'	=> array(self::$object,'_script')
		);
		
		return portlet_admin($opt,$box);
	}

	public function _style(){
		?>
			<style type="text/css">
				.kanban_default_corner>div {
				    display: none;
				}
			</style>
		<?php
	}

	public function _script(){
		?>
			<script type="text/javascript">

			    var dp = new DayPilot.Kanban("sobad_portlet");
			    dp.columns.list = [
			        {name: "Gilling", id: "1"},
			        {name: "Leader Block", id: "6"},
			        {name: "Push Cutter", id: "2"},
			        {name: "Leader Inpeksi", id: "7"},
			        {name: "Inner", id: "3"},
			        {name: "Packing", id: "4"},
			        {name: "Banderol", id: "5"},
			        {name: "Ball", id: "8"},
			        {name: "Box", id: "9"}
			    ];

			    dp.swimlanes.list = [
			    	{name: "Checking", id: "C"},
				    {name: "Operator", id: "O"},
				    {name: "Produksi", id: "P"},
				    {name: "-", id: "B"},
				    {name: "Tracking", id: "T"}
				];

			    dp.cards.list = [
			        {id: 1, "name": "Yusuf Eko", column: "6", swimlane: "C", text: "ID : 612006", barColor: "#999"},
			        {id: 2, "name": "Eko Nugroho", column: "1", swimlane: "O", text: "ID : 112001", barColor: "#ff3232ff"},
			        {id: 3, "name": "199", column: "1", swimlane: "P", text: "Afkir : 1<br>Jumlah : 200"}
			    ];
			    dp.cardMoveHandling = "Disabled";
			    dp.init();

			</script>
		<?php
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