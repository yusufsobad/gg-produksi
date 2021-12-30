<?php

class sasi_header{
	protected static $menu = array();

	public static function _create(){
		global $reg_sidebar;
		?>
			<!-- BEGIN HEADER INNER -->
			<div class="page-header-inner">
				<?php
					self::_logo();
					self::_menu_toggle();
					self::_hor_menu($reg_sidebar);
					self::menu_user();
				?>
			</div>
			<!-- END HEADER INNER -->
		<?php

		return self::$menu;
	}
	
	public static function _logo(){
		?>
			<!-- BEGIN LOGO -->
			<a class="navbar-brand" href="">
				<img src="asset/img/sasi-logo.png" width="50" height="50" class="d-inline-block align-top" alt="">
            </a>
			<!-- END LOGO -->
		<?php
	}
	
	public static function _menu_toggle(){
		?>
			<!-- BEGIN RESPONSIVE MENU TOGGLER -->
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
            </button>
			<!-- END RESPONSIVE MENU TOGGLER -->
		<?php
	}

	public static function _hor_menu($args=array()){
		?>
			<!-- BEGIN TOP NAVIGATION MENU -->
			<div class="navbar-sasi collapse navbar-collapse " id="navbarNav">
				<ul class="navbar-nav">
					<?php
						$check = array_filter($args);
						if(!empty($check)){
							$menu = self::sidemenu_horizontal($args);
							self::$menu = $menu;
						}
					?>
				</ul>
			</div>
			<!-- END TOP NAVIGATION MENU -->
		<?php
	}

	public static function sidemenu_horizontal($args=array()){
		$req = array();
		$check = array_filter($args);
		if(!empty($check)){
			foreach($args as $key => $val){
				// Check active Sidemenu
				if($val['status']=='active'){
					$req['func'] = $val['func'];
					$req['label'] = $val['label'];

					$status = 'active';
				}else{
					$status = '';
				}
				
				echo '<li class="nav-item pl-3 pr-2 '.$status.'">';
				
				echo '<a id="sobad_'.$val['func'].'" class="sobad_sidemenu nav-link" href="javascript:void(0)">
					<h5 class="font-weight-light">'.$val['label'].'</h5></a>';

				echo '</li>';
			}
			
			return $req;
		}
	}

	private static function menu_language($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		$loc = 'asset/img/';
		$lang = get_language();

		$flag = $lang[get_locale()]['flag'];
		$t_lang = $lang[get_locale()]['name'];

		?>
			<li class="dropdown dropdown-language">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<img alt="" src="<?php print($loc.$flag) ;?>">
					<span class="langname">
					<?php print($t_lang) ;?> </span>
					<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<?php 
							foreach ($args as $ky => $val) {
								if($val==get_locale()){
									continue;
								}

								$flag = $loc.$lang[$val]['flag'];
								$t_lang = $lang[$val]['name'];

								echo '
									<li>
										<a href="'.get_home().'/'.$val.'"> 
											<img alt="" src="'.$flag.'"> '.$t_lang.' 
										</a>
									</li>
								';
							}

						?>
					</ul>
				</li>
		<?php
	}
	
	public static function menu_user($args=''){
		$name = get_name_user();
		$id = get_id_user();

		$user = get_picture_user();
		$dept = get_divisi_user();
		$image = empty($user)?'asset/img/user/no-profile.jpg':$user;

		?>
			<div class="d-flex justify-content-end dropdown-topbar">
	            <div class="col text-right pt-2 pr-0">
	                <h4 class="color-light font-weight-bold mobile-none"><?php print($name) ;?></h4>
	                <p class="color-light font-weight-lighter mobile-none"><?php print($dept) ;?></p>
	            </div>
	            <div class="dropdown">
	                <button class="btn sasi-btn-dark" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                    <img src="<?php print($image) ;?>">
	                </button>
	                <div class="sasi-dropdown dropdown-menu border-white radius-sm shadow p-3 mb-5 bg-white rounded" aria-labelledby="dropdownMenuButton">
	                    <div class="col">
	                        <div class="row">
	                            <div class="text-right pl-4 pr-2 pt-1">
	                                <h4 class="font-weight-600"><?php print($name) ;?></h4>
	                                <h6 class="font-weight-100"><?php print($dept) ;?></h6>
	                            </div>
	                            <div class="pr-4">
	                                <img src="<?php print($image) ;?>">
	                            </div>
	                        </div>
	                    </div>
	                    <div class="col">
	                        <div class="dropdown-divider"></div>
	                    </div>
	                    <div class="text-left">
	                        <a class="dropdown-item" href="javascrip:void(0)">
	                            <h5>My Profile</h5>
	                        </a>
	                        <a class="dropdown-item" href="javascrip:void(0)">
	                            <h5>My Calender</h5>
	                        </a>
	                    </div>
	                    <div class="col">
	                        <div class="dropdown-divider"></div>
	                    </div>
	                    <div class="text-left">
	                        <a class="dropdown-item" href="javascrip:void(0)">
	                            <h5>Lock Screen</h5>
	                        </a>
	                    </div>
	                    <div class="col">
	                        <div class="dropdown-divider"></div>
	                    </div>
	                    <div class="col">
	                        <a href="#myModal" data-toggle="modal" class="btn red color-light btn-sm radius-sm sobad_logout" tabindex="-1" role="button" aria-disabled="true">Logout</a>
	                    </div>
	                </div>
	            </div>
	        </div>
		<?php
	}
	
	public static function side_toggle($args=''){
		?>
			<li class="dropdown dropdown-quick-sidebar-toggler">
				<a href="javascript:;" class="dropdown-toggle">
					<i class="icon-logout"></i>
				</a>
			</li>
		<?php
	}
	
	private static function _scroll_bar(){
		?>
			<div class="slimScrollBar" style="background: rgb(99, 114, 131); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 147.994px;"></div>
			<div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(234, 234, 234); opacity: 0.2; z-index: 90; right: 1px;"></div>
		<?php
	}
}