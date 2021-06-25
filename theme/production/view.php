<?php
(!defined('THEMEPATH'))?exit:'';

require dirname(__FILE__).'/script.php';

class production_layout extends production_template{

	private static $data = array();

	public static function load_here($data=array()){
		self::$data = $data;

		?>
			<div class="row">
				<div class="col-md-30 max-height">
					<?php self::information() ;?>
				</div>
				<div class="col-md-70 max-height">
					<?php self::position() ;?>
				</div>
			</div>
		<?php
	}

	private static function information(){
		$data = self::$data;
		
		?>
			<div class="box-information box-shadow">
				<div class="detail-information">
					<div class="title-production">
						<h2> Data <span>Produksi</span></h2>
					</div>
					<div class="detail-production">
						<?php self::_process($data['process']) ;?>
					</div>
					<div class="chart-production">
						<?php self::_chart($data['chart']) ;?>
					</div>
					<div class="total-production">
						<?php self::_information($data['total']) ;?>
					</div>
				</div>
			</div>
		<?php
	}

	private static function position(){
		$data = self::$data;

		?>
			<div class="box-position">
				<div id="layout-position">
					<?php self::_block($data['position']) ;?>
				</div>
				<div id="layout-information">
					<div class="box-info-detail box-shadow">
						<div class="title-production">
							<h3>
								<span>Urutan Proses</span>
							</h3>
						</div>
						<div class="detail-position">
							<?php self::_list($data['list']) ;?>
						</div>
					</div>
				</div>
			</div>
		<?php
	}
}