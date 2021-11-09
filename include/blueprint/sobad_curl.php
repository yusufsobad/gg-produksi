<?php

class sobad_curl{

	public function get_data($url='',$data=array()){
		if(empty($url)){
			return '';
		}
		
		$check = array_filter($data);
		if(empty($check)){
			return '';
		}
		
		$ch = curl_init( $url );
		# Setup request to send json via POST.
		$payload = json_encode( $data );

		curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt( $ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
		# Return response instead of printing.
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		# Send request.
		$result = curl_exec($ch);
		if ($result === false){
			$err = new _error();	
			$result = curl_error($ch);
			
			$msg = $err->_alert_db($result);
			die($msg);
		}
		
		curl_close($ch);
//print($result);
		return $result;
	}
}

?>