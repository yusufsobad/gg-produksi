<?php
(!defined('THEMEPATH'))?exit:'';

abstract class production_template{

	public static function _process($data=array()){
		foreach ($data as $key => $val) {
			?>
			<div class="gg-process">
				<div class="ggbox-process box-shadow">
					<div class="row">
						<div class="col-md-5">
							<div class="process-icon">
								<img class="process-image" scr="asset/img/upload/<?php echo $val['icon'] ;?>">
							</div>
						</div>
						<div class="col-md-7">
							<div class="process-title">
								<h3>
									<?php echo $val['title'] ;?>
								</h3>
							</div>
						</div>
						<div class="col-md-12">
							<div class="process-info">
								<div class="row">
									<?php foreach ($val['info'] as $ky => $vl): ?>
										<?php
											$width = isset($vl['value'])?$vl['value']:0;
											$color = isset($vl['color'])?$vl['color']:'';
										?>
										<div class="col-md-12" style="padding:3px;">
											<label>
												<?php echo $vl['title'] ;?>
											</label>
											<div class="process-chart-info">
												<div class="bg-chart-info" style="width:100%;background-color: #cfcfcf;">
													&nbsp;
												</div>
												<div style="width:<?php echo $width ;?>%" class="bg-chart-info <?php echo $color ;?>">
													&nbsp;
												</div>
											</div>
										</div>
									<?php endforeach ;?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}

	public static function _chart($data=array()){
		$data = json_encode($data);
		?>
			<div class="box-chart box-shadow">
				<div id="chart_content_chart-view">
					<canvas id="chart-view" style="height: 228px;">
					</canvas>
				</div>
			</div>
			<script type="text/javascript">
				function gg_chart(){
					var gg_data = JSON.parse('<?php print_r($data) ;?>');
					load_chart_dash(gg_data,'chart-view');
				}
			</script>
		<?php
	}

	public static function _information($data=array()){
		?>
		<div class="box-info-total box-shadow">
			<div class="row">
				<div class="col-md-7">
					<label class="primary-production"><?php echo $data[0]['title'] ;?></label>
					<div class="primary-production">
						<?php echo $data[0]['total'] ;?>
					</div>
				</div>
				<div class="col-md-5">
					<div class="row">
						<div class="col-md-12">
							<label class="second-production"><?php echo $data[1]['title'] ;?></label>
							<div class="second-production">
								<?php echo $data[1]['total'] ;?>
							</div>
						</div>
						<div class="col-md-12">
							<label class="second-production"><?php echo $data[2]['title'] ;?></label>
							<div class="second-production">
								<?php echo $data[2]['total'] ;?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public static function _block($data=array()){
		$y = $x = 50;

		foreach ($data as $key => $val) {
			$top = "top:". (int) ($val['top'] * $y) ."px;";
			$left = "left:". (int) ($val['left'] * $x) ."px;";
			$height = "height:". (int) ($val['height'] * $y) ."px;";
			$width = "width:". (int) ($val['width'] * $x) ."px;";

			$style = $top.$left.$width.$height;
			$color = isset($val['color'])?$val['color']:'';
			$text = isset($val['text'])?$val['text']:'';

			if($val['type']=='block'){
				?>
					<div style="<?php echo $style ;?>" class="block-box-pos">
						<div class="block-box-color <?php echo $color ;?>">
							<div class="display-table">
								<div class="display-column">
									<label><?php echo $text ;?></label>
								</div>
							</div>
						</div>
					</div>
				<?php
			}

			if($val['type']=='text'){
				?>
					<div style="<?php echo $style ;?>" class="text-box-pos">
						<label> <?php echo $text ;?> </label>
					</div>
				<?php
			}
		}
	}

	public static function _list($data=array()){
		?>
			<div class="row">
				<?php foreach ($data as $key => $val): ?>
					<div class="detail-listcolor">
						<div class="box-listcolor <?php echo $val['color'] ;?>"></div>
						<label> <?php echo $val['title'] ;?> </label>
					</div>
				<?php endforeach ;?>
			</div>
		<?php
	}
}