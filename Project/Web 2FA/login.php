<?php

require_once "lib/config.php";
require_once "lib/debug.php";
require_once "lib/session.php";
require_once "lib/DB.php";
require_once "lib/LDAP.php";
require_once "lib/TOTP.php";
require_once "lib/qr_maker.php";


if (isset($_POST["login"]) && isset($_POST["username"]) && isset($_POST["password"])) 
{
	if (isset($_POST["2fa"]) && $_POST["2fa"] != "") 
	{
		user_login(trim($_POST["username"]), trim($_POST["password"]), trim($_POST["2fa"]));
	}
	else
	{
		user_login(trim($_POST["username"]), trim($_POST["password"]));
		//my_debug_print("Username and password set", __FILE__, __LINE__, "on");
	}
}
else
{
	show_login_form();
}


function user_login($username, $password, $totp = "")
{
	$LDAP = new LDAP();
	$TOTP = new TOTP();

	if ($LDAP->check_login($username, $password) === "login ok") 
	{
		//Get totp secret from ldap
		$secret = $LDAP->get_2fa_user_data($username);

		//my_debug_print("secret: " . $secret, __FILE__, __LINE__, "on");

		if (isset($secret) && $secret != "" && $secret != "false")  
		{
			if (isset($totp) && $totp != "") 
			{
				if ($TOTP->verify_totp($secret, $totp))
				{
					php_session_set_session($username);

					php_session_redirect($msg = NULL);
				}
				else
				{
					//my_debug_print("2FA not ok", __FILE__, __LINE__, "on");
					show_login_form();
				}
			}
			else
			{
				//Missing 2fa code but 2fa is set
				//my_debug_print("Missing 2fa code but 2fa is set", __FILE__, __LINE__, "on");
				show_login_form();
			}
		}
		else
		{
			if (php_session_get_tofa() != "" && php_session_get_tofa() != "false" && php_session_get_tofa() !== false) 
			{
				if (isset($totp) && $totp != "")
				{
					if ($TOTP->verify_totp(php_session_get_tofa(), $totp)) 
					{
						$LDAP->add_2fa($username, php_session_get_tofa());
						//my_debug_print("2fa set in ldap", __FILE__, __LINE__, "on");
						php_session_remove_tofa();

						//$secret = $LDAP->get_2fa_user_data($username);

						//my_debug_print("secret: " . $secret, __FILE__, __LINE__, "on");

						//user is set and allowed to login
						php_session_set_session($username);

						php_session_redirect($msg = NULL);
					}
					else
					{
						//2fa code did not match, user to slow?
						//my_debug_print("2fa code not corret", __FILE__, __LINE__, "on");

						$label = $username;
						$issuer = "Telenor.dk";
						$url = $TOTP->make_totp_url(php_session_get_tofa(), $label, $issuer);

						$QR_MAKER = new QR_MAKER();
						$QR_CODE_URL = $QR_MAKER->make_qr_code($url);	

						show_login_form($QR_CODE_URL);
					}
				}
				else
				{
					//Missing 2fa code but 2fa is set in session so we show it agrin
					//my_debug_print("Missing 2fa code but 2fa is set in session so we show it agrin", __FILE__, __LINE__, "on");

					$label = $username;
					$issuer = "Telenor.dk";
					$url = $TOTP->make_totp_url(php_session_get_tofa(), $label, $issuer);

					$QR_MAKER = new QR_MAKER();
					$QR_CODE_URL = $QR_MAKER->make_qr_code($url);	

					show_login_form($QR_CODE_URL);
				}
			}
			else
			{
				//my_debug_print("2fa not set in the session cookie", __FILE__, __LINE__, "on");

				$secret = $TOTP->createSecret();
				
				php_session_set_tofa($secret);

				$label = $username;
				$issuer = "Telenor.dk";
				$url = $TOTP->make_totp_url($secret, $label, $issuer);

				$QR_MAKER = new QR_MAKER();
				$qr_code_url = $QR_MAKER->make_qr_code($url);

				show_login_form($qr_code_url);
			}
		}
	}
}


function show_login_form($qr_code_url = null)
{
	/*
	<form action="/login.php" method="post">
	  	<fieldset>
		    <input type="text" name="username" placeholder="Username">
		    <input type="password" name="password">
		    <input type="text" name="2fa" placeholder="2FA code">
		    <input type="submit" name="login" value="Submit">
	  	</fieldset>
	</form>
	*/
	?>

	<!DOCTYPE html>
	<html lang="en">
	<head>
		<title>Login V4</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--===============================================================================================-->	
		<link rel="icon" type="image/png" href="login-html/images/icons/favicon.ico"/>
	<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="login-html/vendor/bootstrap/css/bootstrap.min.css">
	<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="login-html/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="login-html/fonts/iconic/css/material-design-iconic-font.min.css">
	<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="login-html/vendor/animate/animate.css">
	<!--===============================================================================================-->	
		<link rel="stylesheet" type="text/css" href="login-html/vendor/css-hamburgers/hamburgers.min.css">
	<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="login-html/vendor/animsition/css/animsition.min.css">
	<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="login-html/vendor/select2/select2.min.css">
	<!--===============================================================================================-->	
		<link rel="stylesheet" type="text/css" href="login-html/vendor/daterangepicker/daterangepicker.css">
	<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="login-html/css/util.css">
		<link rel="stylesheet" type="text/css" href="login-html/css/main.css">
	<!--===============================================================================================-->
	<style>
	img {
	  display: block;
	  margin-left: auto;
	  margin-right: auto;
	}
	</style>
	</head>
	<body>
		
		<div class="limiter">
			<div class="container-login100" style="background-image: url('login-html/images/bg-01.jpg');">
				<div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
					<form class="login100-form validate-form" action="/login.php" method="post">
						<span class="login100-form-title p-b-49">
							Login
						</span>

						<div>
						<?php
						echo "$qr_code_url";
						?>
						</div>
						<br>

						<div class="wrap-input100 validate-input m-b-23" data-validate = "Username is reauired">
							<span class="label-input100">Username</span>
							<input class="input100" type="text" name="username" placeholder="Type your username">
							<span class="focus-input100" data-symbol="&#xf206;"></span>
						</div>

						<div class="wrap-input100 validate-input m-b-23" data-validate="Password is required">
							<span class="label-input100">Password</span>
							<input class="input100" type="password" name="password" placeholder="Type your password">
							<span class="focus-input100" data-symbol="&#xf190;"></span>
						</div>

						<div class="wrap-input100 m-b-23">
							<span class="label-input100">2FA</span>
							<input class="input100" type="text" name="2fa" placeholder="Type your 2FA code">
							<span class="focus-input100" data-symbol="&#xf334;"></span>
						</div>
						
						
						<div class="container-login100-form-btn">
							<div class="wrap-login100-form-btn">
								<div class="login100-form-bgbtn"></div>
								<button class="login100-form-btn" name="login">
									Login
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		

		<div id="dropDownSelect1"></div>
		
	<!--===============================================================================================-->
		<script src="login-html/vendor/jquery/jquery-3.2.1.min.js"></script>
	<!--===============================================================================================-->
		<script src="login-html/vendor/animsition/js/animsition.min.js"></script>
	<!--===============================================================================================-->
		<script src="login-html/vendor/bootstrap/js/popper.js"></script>
		<script src="login-html/vendor/bootstrap/js/bootstrap.min.js"></script>
	<!--===============================================================================================-->
		<script src="login-html/vendor/select2/select2.min.js"></script>
	<!--===============================================================================================-->
		<script src="login-html/vendor/daterangepicker/moment.min.js"></script>
		<script src="login-html/vendor/daterangepicker/daterangepicker.js"></script>
	<!--===============================================================================================-->
		<script src="login-html/vendor/countdowntime/countdowntime.js"></script>
	<!--===============================================================================================-->
		<script src="login-html/js/main.js"></script>

	</body>
	</html>
	<?php
}

?>