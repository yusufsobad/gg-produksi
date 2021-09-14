<?php

class operator_layout{

	private static $curl = "https://gg.soloabadi.com/kartosura/include/curl.php";

	private static function _curl($func='',$args=array()){
		$args = json_decode($args,true);
		$args = array(
			'object'	=> '_production',
			'func'		=> $func,
			'data'		=> $args
		);

		$data = sobad_curl::get_data(self::$curl,$args);
		$data = json_decode($data,true);

		if($data['status']=='error'){
			die(_error::_alert_db($data['msg']));
		}

		return $data['msg'];
	}

	public static function _send($args=array()){
		return self::_curl('scan_code',$args);
	}

	public static function _production($args=array()){
		return self::_curl('send_data',$args);
	}

	public static function load_here(){
		?>
			<div class="row">
				<div class="col-md-12">
					<input id="qrscanner" style="width:25%" type="text" name="scanner">
				</div>
				<div class="col-md-12">
					<hr>
					<div>
						<label>Total</label>
						<input id="pTotal" style="width:25%" type="text" name="pTotal">
					</div>
					<div>
						<label>Afkir</label>
						<input id="pAfkir" style="width:25%" type="text" name="pAfkir">
					</div>
					<a href="javascript:" class="btn btn-xs green btn_data_malika" onclick="gg_button(this)">
						<i class="fa fa-send"></i> Send
					</a>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div id="data_json">
						
					</div>
				</div>
			</div>
		<?php
	}
}