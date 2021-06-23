<?php

class production_layout extends production_template{

	private static $data = array();

	public static function load_here($data=array()){
		self::$data = $data;

		?>
			<div class="row">
				<div class="col-md-3">
					<?php self::information() ;?>
				</div>
				<div class="col-md-9">
					<?php self::position() ;?>
				</div>
			</div>
		<?php
	}

	private static function information(){
		?>
			<div class="box-information">
				<div class="detail-information">
					<div class="title-production">
						<h1> Data <span>Produksi</span></h1>
					</div>
					<div class="detail-production">
						
					</div>
					<div class="total-production">
						
					</div>
				</div>
			</div>
		<?php
	}

	private static function position(){
		?>
			<div class="box-position">
				<div id="layout-position">
					
				</div>
				<div id="layout-information">
					<div class="title-production">
						<h3>
							<span>Urutan Proses</span>
						</h3>
					</div>
					<div class="detail-position">
						
					</div>
				</div>
			</div>
		<?php
	}
}