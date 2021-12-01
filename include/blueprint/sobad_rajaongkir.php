<?php

class sobad_rajaongkir{
	private static $apiKey = '';

	private static $url = '';

	private static $data = '';

	private static $request = '';

	private static $header = array();

	public function __construct(){
		self::$apiKey = "6e017a3c62af6b88f5a0dea0bfe0bbdd";
		self::$url = '';
		self::$data = '';
		self::$request = 'POST';
		self::$header = array(
			"content-type: application/x-www-form-urlencoded",
			"key: ".self::$apiKey
		);
	}

	public static function _listCourier(){
		$list = array(
			'jne'		=> 'Jalur Nugraha Ekakurir (JNE)', 
			'pos'		=> 'POS Indonesia', 
			'tiki'		=> 'Citra Van Titipan Kilat (TIKI)', 
			'rpx'		=> 'RPX Holding', 
			'pandu'		=> 'Pandu Logistics', 
			'wahana'	=> 'Wahana Prestasi Logistik', 
			'sicepat'	=> 'SiCepat Express', 
			'jnt'		=> 'J&T Express', 
			'pahala'	=> 'Pahala Kencana Express', 
			'sap'		=> 'SAP Express', 
			'jet'		=> 'JET Express', 
			'indah'		=> 'Indah Logistik', 
			'dse'		=> '21 Express', 
			'slis'		=> 'Solusi Ekspres', 
			'first'		=> 'First Logistics', 
			'ncs'		=> 'Nusantara Card Semesta', 
			'star'		=> 'Star Cargo', 
			'ninja'		=> 'Ninja Xpress', 
			'lion'		=> 'Lion Parcel', 
			'idl'		=> 'IDL Cargo', 
			'rex'		=> 'Royal Express Indonesia', 
			'ide'		=> 'ID Express',
			'sentral'	=> 'Sentral Cargo',
			'anteraja'	=> 'Anter Aja'
		);

		return $list;
	}

	public static function _listCost(){
		$list = array();
		$courier = self::_listCourier();

		foreach ($courier as $key => $val) {
			$list[] = $key;
		}

		return $list;
	}
	
	public static function _listResi(){
		$list = array(
			'pos', 
			'wahana', 
			'jnt', 
			'sap', 
			'sicepat', 
			'jet', 
			'dse', 
			'first', 
			'ninja', 
			'lion', 
			'idl', 
			'rex', 
			'ide', 
			'sentral',
			'anteraja'
		);

		return $list;
	}

	private static function _curl(){
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => self::$url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => self::$request,
		  CURLOPT_POSTFIELDS => self::$data,
		  CURLOPT_HTTPHEADER => $header,
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			die(_error::_alert_db($err));
		} else {
			$data = json_decode($response,true);
			return $data;
		}
	}

	private static function _filter($data){
		if(isset($data['rajaongkir'])){
			$data = $data['rajaongkir'];
			if($data['status']['code']==200){
				$data = $data['result'];
				return $data;
			}else{
				$msg = $data['status']['code'].' : '.$data['status']['description'];
				die(_error::_alert_db($msg));
			}
		}

		return false;
	}

	public static function get_cost($origin=0,$args=array()){
		self::$url = 'https://pro.rajaongkir.com/api/cost';

		$courier = isset($args['courier'])?$args['courier']:'';
		if(is_array($courier)){
			$courier = implode(':', $courier);
		}

		$data = array(
			'origin'			=> $origin,
			'originType'		=> 'city',
			'destination'		=> isset($args['destination'])?$args['destination']:0,
			'destinationType'	=> 'subdistrict',
			'weight'			=> isset($args['weight'])?$args['weight']:0,
			'courier'			=> $courier
		);

		if(isset($args['length']) && !empty($args['length'])){
			$data['length'] = $args['length']; //cm
		}

		if(isset($args['width']) && !empty($args['width'])){
			$data['width'] = $args['width']; //cm
		}

		if(isset($args['height']) && !empty($args['height'])){
			$data['height'] = $args['height']; //cm
		}

		if(isset($args['diameter']) && !empty($args['diameter'])){
			$data['diameter'] = $args['diameter']; //cm
		}

		self::$data = implode('&', $data);

		$curl = self::_curl();
		$curl = self::_filter($curl);

		$data['result'] = $curl;
		return $data;
	}

	public static function get_country($origin=''){
		self::$url = 'https://pro.rajaongkir.com/api/v2/internationalDestination?id='.$origin;
		self::$request = 'GET';
		self::$header = array(
			"key: ".self::$apiKey
		);

		$curl = self::_curl();
		$curl = self::_filter($curl);

		$data['result'] = $curl;
		return $data;
	}

	public static function get_internationalCost($origin=0,$args=array()){
		self::$url = 'https://pro.rajaongkir.com/api/v2/internationalCost';

		$courier = isset($args['courier'])?$args['courier']:'';
		if(is_array($courier)){
			$courier = implode(':', $courier);
		}

		$data = array(
			'origin'			=> $origin,
			'destination'		=> isset($args['destination'])?$args['destination']:0,
			'weight'			=> isset($args['weight'])?$args['weight']:0,
			'courier'			=> $courier
		);

		if(isset($args['length']) && !empty($args['length'])){
			$data['length'] = $args['length']; //cm
		}

		if(isset($args['width']) && !empty($args['width'])){
			$data['width'] = $args['width']; //cm
		}

		if(isset($args['height']) && !empty($args['height'])){
			$data['height'] = $args['height']; //cm
		}

		self::$data = implode('&', $data);

		$curl = self::_curl();
		$curl = self::_filter($curl);

		$data['result'] = $curl;
		return $data;
	}

	public static function get_currency($nominal=0,$currency='IDR'){
		// Hanya nilai tukar rupiah terhadap dollar
		self::$url = 'https://pro.rajaongkir.com/api/currency';
		self::$request = 'GET';
		self::$header = array(
			"key: ".self::$apiKey
		);

		$curl = self::_curl();
		$curl = self::_filter($curl);
		if($curl){
			$default = $curl['value'];

			if($currency=='IDR'){
				$nominal /= $default;

				$curl['value'] = $nominal;
				$curl['currency'] = 'USD';
			}else if($currency=='USD'){
				$nominal *= $default;

				$curl['value'] = $nominal;
				$curl['currency'] = 'IDR';
			}
		}

		return $curl;
	}

	public static function get_waybill($resi='',$courier=array()){
		self::$url = "https://pro.rajaongkir.com/api/waybill";

		$courier = array_filter($courier);
		$courier = empty($courier)?self::_listResi():$courier;
		
		foreach ($courier as $key => $val) {
			self::$data = "waybill=".$resi."&courier=".$val;

			$data = self::_curl();
			$data = self::_filter($data);
			if($data){
				return array(
					'resi'			=> $resi,
					'courier'		=> $val,
					'result'		=> $data
				);
			}
		}

		return array();
	}
}