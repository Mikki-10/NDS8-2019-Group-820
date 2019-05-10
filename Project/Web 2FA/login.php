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

function show_login_form()
{
	?>
	<form action="/login.php" method="post">
	  	<fieldset>
		    <input type="text" name="username" placeholder="Username">
		    <input type="password" name="password">
		    <input type="text" name="2fa" placeholder="2FA code">
		    <input type="submit" name="login" value="Submit">
	  	</fieldset>
	</form>
	<?php
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
						echo $QR_MAKER->make_qr_code($url);	

						show_login_form();
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
					echo $QR_MAKER->make_qr_code($url);	

					show_login_form();
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
				echo $QR_MAKER->make_qr_code($url);

				show_login_form();
			}
		}
	}
}



/*
$TOTP = new TOTP();

$secret = $TOTP->createSecret();
$secret = "LHD2AHT2HGW55R5RVFERJQ75WWIVPWHY";
my_debug_print($secret, __FILE__, __LINE__, "on");

$code = $TOTP->get_totp_code($secret);
my_debug_print($code, __FILE__, __LINE__, "on");


my_debug_print($TOTP->verify_totp($secret, $code), __FILE__, __LINE__, "on");

$label = "Username";
$issuer = "Telenor.dk";
$url = $TOTP->make_totp_url($secret, $label, $issuer);

$QR_MAKER = new QR_MAKER();
echo $QR_MAKER->make_qr_code($url);
*/

?>