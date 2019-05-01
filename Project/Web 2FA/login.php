<?php

error_reporting(E_ALL);
ini_set("display_errors", true);

require_once "lib/session.php";
require_once "lib/config.php";
require_once "lib/DB.php";
require_once "lib/LDAP.php";


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

$TOTP = new TOTP();
$secret = $TOTP->genSecret(24);
$secret = $secret["secret"];

var_dump($TOTP->getOTP($secret));

function user_login($username, $password)
{
	$LDAP = new LDAP();
	if ($LDAP->check_login($username, $password) === "login ok") 
	{
		

		php_session_set_session($_POST["username"]);

		php_session_redirect($msg = NULL);
	}
}

/*
function user_login($username, $password)
{
	$DB = new DB();
	$respons = $DB->get_user($username);

	//echo "<pre>"; var_dump($respons); echo "</pre>";
	
	if (isset($respons) && $respons != "" && $respons != null && $respons["username"] == $username) 
	{
		if (password_verify($password, $respons["password"])) 
		{
		    echo 'Password is valid!';
		    return "login ok";
		} 
		else
		{
		    echo 'Invalid password.';
		    return FALSE;
		}
	}
	else
	{
		echo "Error";
	}
}
*/

?>