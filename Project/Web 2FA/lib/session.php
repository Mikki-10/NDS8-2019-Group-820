<?php

// **PREVENTING SESSION HIJACKING**
// Prevents javascript XSS attacks aimed to steal the session ID
ini_set('session.cookie_httponly', 1);

// **PREVENTING SESSION FIXATION**
// Session ID cannot be passed through URLs
ini_set('session.use_only_cookies', 1);

// Uses a secure connection (HTTPS) if possible
//ini_set('session.cookie_secure', 1);

// PHP function to start session
session_start();

// ------------------------------------------------------------------- //
// Set session til logget ind
// ------------------------------------------------------------------- //
function php_session_set_session($username)
{
	$_SESSION["logged_in"] = "ja";
	$_SESSION["username"] = $username;
	$_SESSION["user_ip"] = getUserIP();
}


// ------------------------------------------------------------------- //
//	Log brugeren ud
// ------------------------------------------------------------------- //
function php_session_log_ud()
{
	$_SESSION['logged_in'] = "nej";
	$_SESSION["username"] = NULL;
	$_SESSION["user_ip"] = NULL;
}


// ------------------------------------------------------------------- //
// Er brugeren logget ind
// ------------------------------------------------------------------- //
function php_session_er_session_sat()
{
	if(isset($_SESSION['logged_in']))
	{
		if($_SESSION['logged_in'] == "ja" && $_SESSION["username"] != NULL && $_SESSION["user_ip"] == getUserIP())
		{
			return true;
		}
	}
	return FALSE;
}


// ------------------------------------------------------------------- //
// Det kræver at man er logget ind for at se denne side
// ------------------------------------------------------------------- //
function php_session_beskyt()
{
	if(php_session_er_session_sat() == FALSE)
	{
		php_session_redirect("/login.php");
		?>
	    <script>
	        window.location.replace("https://" + window.location.hostname + "/login.php");
	    </script>
	    <?php
		die("Adgang nægtet<br>Aktiver javascript");
	}
}

// ------------------------------------------------------------------- //
// Funktion til at redirect
// ------------------------------------------------------------------- //
function php_session_redirect($msg = NULL)
{
    $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    header("Location: " . $actual_link . $msg);
    die("Adgang nægtet");
}


// ------------------------------------------------------------------- //
// Function to get the users ip
// ------------------------------------------------------------------- //
function getUserIP()
{
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}


?>