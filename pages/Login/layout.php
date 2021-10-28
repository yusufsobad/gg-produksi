<?php

class login_sasi extends create_form{
	public static function login($func,$args=array()){
		$check = array_filter($args);
		if(empty($check)){
			$data = 'placeholder="username" autofocus required';
			$args[0] = array(
				'option' 	=> 'input',
				'data'		=> array(
					'type'		=> 'text',
					'key'		=> 'user',
					'class'		=> '',
					'value'		=> '',
					'data'		=> $data
				)
			);

			$data = 'placeholder="password" required';
			$args[1] = array(
				'option' 	=> 'input',
				'data'		=> array(
					'type'		=> 'password',
					'key'		=> 'pass',
					'class'		=> '',
					'value'		=> '',
					'data'		=> $data
				)
			);
		}
	?>

	<style type="text/css">
		.login .content {
		    padding: 60px 400px 10px;
		    width: 1200px;
		    background-color: #fff;
		    border-radius: 25px !important;
		}

		button#btn_login_submit {
		    background-color: #a0237a;
		    color: #fff;
		    padding: 5px 35px;
		    border-radius: 20px !important;
		    font-size: 20px;
		    font-weight: 600;
		}

		button#btn_login_submit:hover {
		    opacity: 0.7;
		}

		.login .form-logo,
		.form-logo>img {
		    width: 65px;
		}

		.login .content .form-title {
			color: #333 !important;
		    font-weight: 600;
		    margin-bottom: 25px;
		    font-size: 38px;
		    line-height: 1;
		}

		form.login-form input.form-control {
		    background-color: #e0dddd !important;
		}

		.input-icon > .form-control {
		    padding-left: 15px !important;
		}

		::placeholder {
		    color: #666 !important;
		}

		.login .copyright {
		    position: absolute;
		    width: 100%;
		    bottom: 20px;
		    color: #333;
		}

		@media only screen and (max-width: 600px){
			.login .content {
			    padding: 30px;
			    width: 300px;
			    border-radius: 15px !important;
			}

			.login .content .form-title {
				font-size: 22px;
			}
		}
	</style>

	<!-- BEGIN LOGIN FORM -->
	<form class="login-form" data-sobad="<?php print($func) ;?>" action="javascript:void(0)" method="post">
		<div class="form-logo">
			<img src="asset/img/Sasi Logo.png">
		</div>
		<h3 class="form-title">Login to your account</h3>
		<div class="alert alert-danger display-hide">
			<button class="close" data-close="alert"></button>
			<span>
			Enter any username and password. </span>
		</div>
		<div class="form-group">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<label class="control-label visible-ie8 visible-ie9">Username</label>
			<div class="input-icon">
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Password</label>
			<div class="input-icon">
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password"/>
			</div>
		</div>
		<div class="form-actions">
			<label class="checkbox" style="display: block !important;color:#666;">
			<input type="checkbox" name="remember" value="1"/> <?php print(__e('remember me')) ;?> </label>
			<div style="text-align: center;margin-top: 25px;">
				<button id="btn_login_submit" data-sobad="<?php print($func) ;?>" type="submit" class="btn">
					Login
				</button>
			</div>
		</div>
	</form>
	<!-- END LOGIN FORM -->
		<?php
	}
}