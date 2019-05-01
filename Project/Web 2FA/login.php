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
	user_login($_POST["username"], $_POST["password"]);
}
else
{
	?>
	<form action="/login.php" method="post">
	  	<fieldset>
		    <input type="text" name="username" value="testuser">
		    <input type="password" name="password" value="test">
		    <input type="submit" name="login" value="Submit">
	  	</fieldset>
	</form>
	<?php
}


function user_login($username, $password)
{
	$LDAP = new LDAP();
	if ($LDAP->check_login($username, $password) === "login ok") 
	{
		//Get totp secret from ldap

		if (isset($secret) && $secret == "")  
		{
			if ($TOTP->verify_totp($secret, trim($_POST["totp"])))
			{
				php_session_set_session($_POST["username"]);

				php_session_redirect($msg = NULL);
			}
			else
			{
				die("2FA not ok");
			}
		}
		else
		{
			//make totp secret and save in ldap
		}
	}
}


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

?>