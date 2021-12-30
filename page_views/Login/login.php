<?php

// Layout Login

?>
	<!-- BEGIN LOGO -->
	<div class="logo">
		<img src="asset/img/logo-big.png" alt=""> 
	</div>
	<!-- END LOGO -->
	<!-- BEGIN LOGIN -->
	<div class="content">
	<?php
		print(user_login::login('login_system'));
	?>
	</div>
	<!-- END LOGIN -->
	<!-- BEGIN COPYRIGHT -->
	<div class="copyright">
		<?php print(date('Y')) ;?> © System <?php print(constant('company')) ;?>
	</div>
	<!-- END COPYRIGHT -->
<?php