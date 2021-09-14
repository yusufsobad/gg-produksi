<?php

class data_production{

	protected static function _data_layout(){
		$args = array();

		// Gilling
		for($i=0;$i<=5;$i++){
			for($j=0;$j<=2;$j++){
				$args[] = array(
					'type'		=> 'block',
					'top'		=> $j,
					'left'		=> $i,
					'width'		=> 1,
					'height'	=> 1,
					'color'		=> 'bg-gg-red'
				);
			}
		}

		// Push Cutter
		for($i=0;$i<=5;$i++){
			for($j=3;$j<=3;$j++){
				$args[] = array(
					'type'		=> 'block',
					'top'		=> $j,
					'left'		=> $i,
					'width'		=> 1,
					'height'	=> 1,
					'color'		=> 'bg-gg-blue-ocean'
				);
			}
		}

		// Inner
		for($i=0;$i<=5;$i++){
			if($i==2 || $i==3)continue;

			for($j=5;$j<=7;$j++){
				$args[] = array(
					'type'		=> 'block',
					'top'		=> $j,
					'left'		=> $i,
					'width'		=> 1,
					'height'	=> 1,
					'color'		=> 'bg-gg-green'
				);
			}
		}

		// Push Cutter
		for($i=0;$i<=5;$i++){
			for($j=9;$j<=9;$j++){
				$args[] = array(
					'type'		=> 'block',
					'top'		=> $j,
					'left'		=> $i,
					'width'		=> 1,
					'height'	=> 1,
					'color'		=> 'bg-gg-blue-ocean'
				);
			}
		}

		// Gilling
		for($i=0;$i<=5;$i++){
			for($j=10;$j<=12;$j++){
				$args[] = array(
					'type'		=> 'block',
					'top'		=> $j,
					'left'		=> $i,
					'width'		=> 1,
					'height'	=> 1,
					'color'		=> 'bg-gg-red'
				);
			}
		}

		// Banderol
		for($i=7;$i<=12;$i++){
			for($j=2;$j<=5;$j++){
				$args[] = array(
					'type'		=> 'block',
					'top'		=> $j,
					'left'		=> $i,
					'width'		=> 1,
					'height'	=> 1,
					'color'		=> 'bg-gg-blue'
				);
			}
		}

		// Pack
		for($i=7;$i<=12;$i++){
			for($j=9;$j<=12;$j++){
				$args[] = array(
					'type'		=> 'block',
					'top'		=> $j,
					'left'		=> $i,
					'width'		=> 1,
					'height'	=> 1,
					'color'		=> 'bg-gg-yellow'
				);
			}
		}

		// Leader Inspeksi
		$args[] = array(
			'type'		=> 'block',
			'top'		=> 7,
			'left'		=> 2,
			'width'		=> 2,
			'height'	=> 1,
			'color'		=> 'bg-gg-green',
			'text'		=> 'Leader Inspeksi'
		);

		// Ball / Box
		$args[] = array(
			'type'		=> 'block',
			'top'		=> 0,
			'left'		=> 8,
			'width'		=> 3,
			'height'	=> 1,
			'color'		=> 'bg-gg-darkblue',
			'text'		=> 'Ball / Box'
		);

		return $args;
	}

	public static function _get_data(){
		$args = array(
			'process'	=> self::_process(),
			'chart'		=> self::_chart(),
			'total'		=> self::_information(),
			'position'	=> self::_layout(),
			'list'		=> self::_detail()
		);

		return $args;
	}

	public static function _process(){
		$args = array();
	
		$args[] = array(
			'icon'		=> 'box.png',
			'title'		=> 'Gilling',
			'info'		=> array(
				0			=> array(
					'title'		=> 'total',
					'value'		=> 80,
					'color'		=> 'bg-gg-red'
				),
				1			=> array(
					'title'		=> 'reject',
					'value'		=> 5,
					'color'		=> 'bg-gg-red'
				),
			)
		);

		$args[] = array(
			'icon'		=> 'box.png',
			'title'		=> 'Push Cutter',
			'info'		=> array(
				0			=> array(
					'title'		=> 'total',
					'value'		=> 60,
					'color'		=> 'bg-gg-blue-ocean'
				),
				1			=> array(
					'title'		=> 'reject',
					'value'		=> 2,
					'color'		=> 'bg-gg-blue-ocean'
				),
			)
		);

		$args[] = array(
			'icon'		=> 'box.png',
			'title'		=> 'Inner',
			'info'		=> array(
				0			=> array(
					'title'		=> 'total',
					'value'		=> 40,
					'color'		=> 'bg-gg-green'
				),
				1			=> array(
					'title'		=> '&nbsp;',
					'value'		=> 100,
					'color'		=> 'bg-white'
				),
			)
		);

		$args[] = array(
			'icon'		=> 'box.png',
			'title'		=> 'Pack',
			'info'		=> array(
				0			=> array(
					'title'		=> 'total',
					'value'		=> 30,
					'color'		=> 'bg-gg-yellow'
				),
				1			=> array(
					'title'		=> '&nbsp;',
					'value'		=> 100,
					'color'		=> 'bg-white'
				),
			)
		);

		return $args;
	}

	public static function _chart(){
		$data = array();
		$label = array();

		$label = array('Jan','Feb','Mar','April','Mei','Jun');
		$data = array(
			0	=> array(
				'brdColor'	=> 'rgba(188, 27, 27, 1)',
				'bgColor'	=> 'rgba(188, 27, 27, 0.5)',
				'label'		=> 'Total Produksi',
				'data'		=> array(740,600,700,720,710,650)
			)
		);

		$args = array(
			'type'		=> 'line',
			'label'		=> $label,
			'data'		=> $data,
			'option'	=> ''
		);

		return $args;
	}

	public static function _information(){
		$args = array(
			0	=> array(
				'title'		=> 'Total Box',
				'total'		=> 500
			),
			1	=> array(
				'title'		=> 'Ball',
				'total'		=> 1000
			),
			2	=> array(
				'title'		=> 'Pack',
				'total'		=> 18000
			)
		);

		return $args;
	}

	public static function _layout(){
		return self::_data_layout();
	}

	public static function _detail(){
		$args = array();

		$args[] = array(
			'color'		=> 'bg-gg-red',
			'title'		=> 'Gilling'
		);

		$args[] = array(
			'color'		=> 'bg-gg-blue-ocean',
			'title'		=> 'Push Cutter'
		);

		$args[] = array(
			'color'		=> 'bg-gg-green',
			'title'		=> 'Inner'
		);

		$args[] = array(
			'color'		=> 'bg-gg-yellow',
			'title'		=> 'Pack'
		);

		$args[] = array(
			'color'		=> 'bg-gg-blue',
			'title'		=> 'Banderol'
		);

		$args[] = array(
			'color'		=> 'bg-gg-darkblue',
			'title'		=> 'Ball / Box'
		);

		return $args;
	}
}