<?php
/*
Version 1.1.2
*/
(!defined('DEFPATH'))?exit:'';

class sobad_asset{
	public static function _name_file($dir){
		if(is_dir($dir)){
			if($handle = opendir($dir)){
				$i = 0;
				while(($file = readdir($handle)) !== false){
					if($file == "."){
						continue;
					}
					if($file == ".."){
						continue;
					}
					
					$list[$i] = $file;
					$i += 1;
				}
				closedir($handle);
				
				return $list;
			}
		}
	}

	public function _js_file(){
		$dir = "asset/js/";
		$list = self::_name_file($dir);
		if(count($list)>0){
			for($i=0;$i<count($list);$i++){
				echo '<script src="'.$dir.$list[$i].'"></script>';
			}
		}
	}

	public function _css_file(){
		$dir = "asset/css/";
		$list = self::_name_file($dir);
		if(count($list)>0){
			for($i=0;$i<count($list);$i++){
				echo '<link rel="stylesheet" type="text/css" href="'.$dir.$list[$i].'">';
			}
		}
	}

	public function _url_set(){

		echo '<!-- --custom stylesheet-- -->';
		self::_css_file();
		echo '<!-- -function javascript- -->';
		self::_js_file();
	}

	public function _vendor_css(){
		global $reg_script_css;
		
		foreach($reg_script_css as $key => $val){
			echo "<!-- $key CSS -->";
			echo '<link id="'.$key.'" rel="stylesheet" type="text/css" href="'.$val.'">';
		}
	}

	public function _vendor_js(){
		global $reg_script_js;

		foreach($reg_script_js as $key => $val){
			echo "<!-- $key JS -->";
			echo '<script src="'.$val.'"></script>';
		}
	}

	public function _script_head(){
		global $reg_script_head;

		echo "<!-- Script Head Sobad -->";
		foreach($reg_script_head as $key => $val){
			echo $val;
		}
	}

	public function _script_foot(){
		global $reg_script_foot;

		echo "<!-- Script Foot Sobad -->";
		foreach($reg_script_foot as $key => $val){
			echo $val;
		}
	}

	public static function _pages($dir = "pages/"){
		$GLOBALS['reg_locFile'] = $dir;
		require_once 'routes/routes.php';

		self::_indexPages($dir);
	}

	public static function _allPages($dir=''){
		global $reg_page;

		foreach ($reg_page as $key => $val) {
			self::_loadPage($reg);
		}
	}

	protected static function _indexPages($dir=''){
		$pages = self::_name_file($dir);
		if(count($pages)>0){
			for($i=0;$i<count($pages);$i++){
				if(is_dir($dir.$pages[$i])){
					if(file_exists($dir.$pages[$i]."/index.php")){
						require_once $dir.$pages[$i]."/index.php";
					}
				}
			}
		}
	}

	public static function _loadPage($reg = array()){
		global $reg_locFile;

		$loc = $reg_locFile;
		$dir = isset($reg['view'])?$reg['view']:'folder.file';

		$loc = $reg_locFile;
		$dir = str_replace('.', '/', $dir);

		$_dirs = explode('/', $dir);
		$_cdir = count($_dirs);
		$_cdir -= 1;

		$file = $_dirs[$_cdir].'.php';

		unset($_dirs[$_cdir]);
		$dir = implode('/', $_dirs);

		$dir = $loc.$dir;
		if(is_dir($dir)){
			if(file_exists($dir."/".$file)){
				require_once $dir."/".$file;
			}
		}else{
			//die($file.'::Halaman gagal dimuat!!!');
		}
	}

	public static function _loadFile($dir = "pages"){
		global $reg_locFile;

		$loc = $reg_locFile;
		$dir = str_replace('.', '/', $dir);
		
		$_dirs = explode('/', $dir);
		$_cdir = count($_dirs);
		$_cdir -= 1;

		$file = $_dirs[$_cdir].'.php';

		unset($_dirs[$_cdir]);
		$dir = implode('/', $_dirs);

		$dir = $loc.$dir;
		if(is_dir($dir)){
			if(file_exists($dir."/".$file)){
				require_once $dir."/".$file;
			}
		}else{
			die($file.'::File gagal dimuat!!!');
		}
	}

	public static function _loadView($dir = "page_views", $data='',$lvtype=''){
		$loc = is_dir("page_views/")?"page_views/":"../page_views/";
		$dir = str_replace('.', '/', $dir);

		$_dirs = explode('/', $dir);
		$_cdir = count($_dirs);
		$_cdir -= 1;

		$nm_file = $_dirs[$_cdir];
		$file = empty($lvtype)?$nm_file:$nm_file.'.'.$lvtype;
		$file .= '.php';

		unset($_dirs[$_cdir]);
		$dir = implode('/', $_dirs);

		$dir = $loc.$dir;
		if(is_dir($dir)){
			if(file_exists($dir."/".$file)){
				if(gettype($data)=='array'){
					$check = array_filter($data);
					if(!empty($check)){
						extract($data);
					}
				}
				
				if($lvtype=='html'){
					ob_start();
					require_once $dir."/".$file;
					return ob_get_clean();
				}

				require_once $dir."/".$file;

				if($lvtype=='config'){
					return $config;
				}

				if($lvtype=='table'){
					return table_admin($config);
				}

				if($lvtype=='modal'){
					return modal_admin($config);
				}

				if($lvtype=='portlet'){
					return portlet_admin($config, $box);
				}

				if($lvtype=='tabs'){
					return tabs_admin($config, $box);
				}
			}
		}else{
			die($file.'::File not Exist!!!');
		}
	}

	public static function _reg_session($key='type',$var=''){
		if(empty($key)){
			die(_error::_alert_db('Key Session Kososng!!!'));
		}

		if(!isset($_SESSION[_prefix.$key])){
			return '';
		}

		$_SESSION[_prefix.$key] = $var;
	}

	public static function _get_post($type=''){
		if(!isset($_POST[$type])){
			return '';
		}

		return $_POST[$type];
	}

	public static function ascii_to_hexa($ascii=''){
		$ascii = strval($ascii);
		if(empty($ascii)){
			return '';
		}
		
		$hex = '';
		for ($i = 0; $i < strlen($ascii); $i++) {
			$byte = strtoupper(dechex(ord($ascii{$i})));
			$byte = str_repeat('0', 2 - strlen($byte)).$byte;
			$hex .= $byte;
		}
		return $hex;
	}

	public static function hexa_to_ascii($str=''){
		if(empty($str)){
			return '';
		}
		
		$html = '';
		$jml = strlen($str);
		for($i=0;$i<$jml;$i+=2){
			$hex = substr($str,$i,2);
			$html .= chr(hexdec($hex));
		}
		
		$html = urldecode($html);
		$html = str_replace('-plus-','+',$html);
		return $html;
	}

	public static function _check_required($key='',$value=''){
		if(isset($_SESSION[_prefix.'require_form'])){
			$_filter = $_SESSION[_prefix.'require_form'];
			
			if($_filter[$key]['status']==true && empty($value)){
				die(_error::_alert_db("This field ".$_filter[$key]['name']." is Required !!!"));
			}
		}
	}

	public static function ajax_conv_json($args){
		$args = json_decode($args,true);
		$data = array();

		$filter = false;
		if(isset($_SESSION[_prefix.'input_form'])){
			$_filter = $_SESSION[_prefix.'input_form'];
			$filter = true;
		}
		
		if (is_array($args) || is_object($args)){	
			foreach($args as $key => $val){
				$name = stripcslashes($val['name']);
				$data[$name] = self::hexa_to_ascii(stripcslashes($val['value']));

				if($filter){
					if(isset($_filter[$name])){
						self::_check_required($name,$data[$name]);
						$data[$name] = formatting::sanitize($data[$name],$_filter[$name]);
					}
				}
			}

			return $data;
		}
		
		return array();
	}

	public static function ajax_conv_array_json($args){
		$args = json_decode($args,true);
		$data = array();

		$filter = false;
		if(isset($_SESSION[_prefix.'input_form'])){
			$_filter = $_SESSION[_prefix.'input_form'];
			$filter = true;
		}
		
		if (is_array($args) || is_object($args)){	
			foreach($args as $key => $val){
				$name = stripcslashes($val['name']);
				$val['value'] = self::hexa_to_ascii($val['value']);

				if(!array_key_exists($name,$data)){
					$data[$name] = array();
				}

				if($filter){
					if(isset($_filter[$name])){
						$val['value'] = formatting::sanitize($val['value'],$_filter[$name]);
					}
				}
				
				array_push($data[$name],stripcslashes($val['value']));
			}
			
			return $data;
		}
		
		return array();
	}

	public static function handling_upload_file($name='',$target_dir='upload'){
		$err = new _error();

		if(empty($name))die($err->_alert_db("index FILE not found!!!"));

		$_name = basename($_FILES[$name]["name"]);
		$target_file = $target_dir . '/' . $_name;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

		// Check if image file is a actual image or fake image
		if(in_array($imageFileType,array('jpg','jpeg','bmp','png','gif'))) {
			$check = getimagesize($_FILES[$name]["tmp_name"]);
			
			if($check === false) {
				die($err->_alert_db("Fake Image Upload!!!"));
			}
		}

		// Check file size
		if ($_FILES[$name]["size"] > 2000000) {
			die($err->_alert_db("Ukuran File terlalu besar (2MB)!!!"));
		}

		// Check if file already exists
		$_files = self::_check_filename($target_dir,$_name);

		// if everything is ok, try to upload file
		if (move_uploaded_file($_FILES[$name]["tmp_name"], $_files['target'])) {
			return $_files['name'];
		} else {
			die($err->_alert_db("Sorry, there was an error uploading your file.!!!"));	
		}
	}

	private static function _check_filename($target_dir='',$name='',$extend=0){
		$_info = pathinfo($name);
		$_basename = $_info['basename'];
		$_name = $_info['filename'];
		$_ext = $_info['extension'];

		if(!empty($extend)){
			$_name .= '-'.$extend;
			$_basename = $_name.'.'.$_ext;
		}

		$target_file = $target_dir . '/' . $_basename;
		if(file_exists($target_file)){
			$extend += 1;
			return self::_check_filename($target_dir,$name,$extend);
		}

		return array(
			'name'		=> $_basename,
			'target'	=> $target_file
		);
	}	
}

class logout_system{
	// ----------------------------------------------
	// Function Logout Admin ------------------------
	// ----------------------------------------------

	public function _get(){

		unset($_SESSION[_prefix.'page']);
		unset($_SESSION[_prefix.'user']);
		unset($_SESSION[_prefix.'name']);

		setcookie('id','');
		setcookie('name','');		

		return '/'.URL;
	}	
}