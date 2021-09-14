<?php
(!defined('DEFPATH'))?exit:'';
require dirname(__FILE__).'/operator.php';

$args = array();
$args['operator'] = array(
	'page'		=> 'operator_gg',
	'home'		=> false,
);
reg_hook('reg_page',$args);

class operator_gg{
	public function _reg(){
		$GLOBALS['body'] = 'operator';

		self::_script();
		reg_hook('reg_sidebar',array(
				'operator'	=> array(
					'status'	=> 'active',
					'icon'		=> '',
					'label'		=> 'Layout',
					'func'		=> 'operator_layout',
					'child'		=> null
				)
			)
		);

		reg_hook('reg_language',array());
	}

	public function _page(){
		operator_layout::load_here();
	}

	private function _script(){
		$script = new vendor_script();

		// url script jQuery - Vendor
		$get_jquery = $script->_get_('_js_core',array('jquery-core'));
		$head[0] = '<script src="'.$get_jquery['jquery-core'].'"></script>';

		// url script css ----->
		$css = array_merge(
				$script->_get_('_css_global'),
				$script->_get_('_css_page_level',array('bootstrap-toastr')),
				$script->_get_('_css_chart')
			);
		
		// url script css ----->
		$js = array_merge(
				$script->_get_('_js_core'),
				$script->_get_('_js_page_level',array('bootstrap-toastr')),
				$script->_get_('_js_chart')
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
				var data_json = {};

				$("#qrscanner").focus();
				$("#qrscanner").on('change',function(){
					data = "ajax=_send&object="+object+"&data="+JSON.stringify([this.value,data_json]);

					this.value = '';
					sobad_ajax('#data_json',data,set_data,false);
				});

				function set_data(data,id){
					data_json = data;
					$(id).html(JSON.stringify(data_json));
				}

				function gg_button(val){
					var ptotal = $('#pTotal').val();
					var pafkir = $('#pAfkir').val();

					$('#pTotal').val(0);
					$('#pAfkir').val(0);

					data = "ajax=_production&object="+object+"&data="+JSON.stringify([ptotal,pafkir,data_json]);
					sobad_ajax('#data_json',data,set_data,false);
				}
			</script>
		<?php
	}
}