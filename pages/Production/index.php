<?php
require dirname(__FILE__).'/production.php';

$args = array();
$args['production'] = array(
	'page'		=> 'production_gg',
	'home'		=> false,
	'theme'		=> 'production'
);
reg_hook('reg_page',$args);

class production_gg{
	public function _reg(){
		$GLOBALS['body'] = 'production';

		self::_script();
		reg_hook('reg_language',array());
	}

	public function _page(){
		production_layout::load_here();
	}

	private function _script(){
		$script = new vendor_script();
		$theme = new production_script;

		// url script jQuery - Vendor
		$get_jquery = $script->_get_('_js_core',array('jquery-core'));
		$head[0] = '<script src="'.$get_jquery['jquery-core'].'"></script>';

		// url script css ----->
		$css = array_merge(
				$script->_get_('_css_global'),
				$theme->_get_('_css_page_style')
			);
		
		// url script css ----->
		$js = array_merge(
				$script->_get_('_js_core')
			);
			
		ob_start();
		self::load_script();
		$custom['login'] = ob_get_clean();

		reg_hook("reg_script_head",$head);
		reg_hook("reg_script_css",$css);
		reg_hook("reg_script_js",$js);
		reg_hook("reg_script_foot",$custom);
	}

	private function load_script(){
		?>
			<script>
				$("body.production").css("height",$(window).height());
			</script>
		<?php
	}
}