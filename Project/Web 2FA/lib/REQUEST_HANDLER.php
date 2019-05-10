<?php

/**
 * 
 */
class REQUEST_HANDLER
{
	
	function __construct()
	{
		$type = $this->check_browser();
		$this->check_request_type($type);
	}

	function check_browser()
	{
		require_once "getBrowser.php";
		$ua = getBrowser();
		$os = $ua['platform'];
		$browserandversion = $ua['name'] . " " . $ua['version'];
		$browser = $ua['name'];
		$browserversion = $ua['version'];
		$fuldstreng = $ua['userAgent'];
		$osbit = $ua['bit'];

		//file_put_contents(__DIR__ . "/bowser.txt", $fuldstreng);
		
		if ($fuldstreng == "python-requests/2.18.4") 
		{
			return "server";
		}
		elseif (isset($_GET["force"]) && $_GET["force"] === "server") 
		{
			return "server-cheat";
		}
		else
		{
			return "client";
		}
	}

	function check_request_type($type)
	{
		if ($type == "server") 
		{
			$this->server_handler();
		}
		elseif ($type == "server-cheat") 
		{
			$this->server_handler("true");
		}
		elseif ($type == "client") 
		{
			$this->client_handler();
		}
		else
		{
			die("Type not supported");
		}
	}

	function server_handler($cheat = "false")
	{
		if ($cheat === "false") 
		{
			if (isset($_POST["make"]) && $_POST["make"] === "login") 
			{
				$LOGIN_VALIDATION = new LOGIN_VALIDATION();
				$data = $LOGIN_VALIDATION->make_login($_POST["username"]);

				header("Content-type:application/json");
				echo json_encode($data);
			}
			elseif (isset($_POST["check"]) && $_POST["check"] === "login") 
			{
				$LOGIN_VALIDATION = new LOGIN_VALIDATION();
				$LOGIN_VALIDATION->check_login($_POST["username"], $_POST["id"]);
			}
			else
			{
				#Disallow using http response code 404
				http_response_code(404);
				my_debug_print("die()", __FILE__, __LINE__, "on");
				die("Missing peremters");
			}
		}
		elseif ($cheat === "true") 
		{
			php_session_beskyt();

			if (isset($_GET["type"]) && $_GET["type"] === "make-login") 
			{
				$LOGIN_VALIDATION = new LOGIN_VALIDATION();
				$data = $LOGIN_VALIDATION->make_login($_SESSION["username"]);

				header("Content-type:application/json");
				echo json_encode($data);
			}
			elseif (isset($_GET["type"]) && $_GET["type"] === "check-login" && isset($_GET["id"]) && $_GET["id"] != "") 
			{
				$LOGIN_VALIDATION = new LOGIN_VALIDATION();
				$LOGIN_VALIDATION->check_login($_SESSION["username"], $_GET["id"]);
			}
			else
			{
				my_debug_print("die()", __FILE__, __LINE__, "on");
				die("Missing peremters");
			}
		}
		else
		{
			my_debug_print("die()", __FILE__, __LINE__, "on");
			die("Server handler error");
		}
	}

	function client_handler()
	{
		php_session_beskyt();
		
		//my_debug_print($_POST, __FILE__, __LINE__, "on");

		if (isset($_POST["login-ssh"]) && isset($_POST["id"]) && isset($_POST["type"]) && $_POST["type"] == "verification") 
		{
			$DB = new DB();
			$data = $DB->get_ssh_login_username($_POST["id"]);

			if (strtoupper($data["username"]) == strtoupper($_SESSION["username"])) 
			{
				$input = $_FILES['audio_data']['tmp_name']; //temporary name that PHP gave to the uploaded file
				$recording = $input;
				
				$LDAP = new LDAP();
				$user_data = $LDAP->get_voiceit_user_data($data["username"]);
				$voiceit_user_id = $user_data["voiceit"];
				$contentLanguage = "en-US";

				$login_data = $DB->get_ssh_login($data["username"]);
				$phrase = $login_data["random_text"];

				$TOFA = new TOFA();
				if ($TOFA->validate_voice($voiceit_user_id, $contentLanguage, $phrase, $recording, $data["id"]))
				{
					$DB->update_ssh_login($_POST["id"], true);
				}
			}
			else
			{
				my_debug_print("die()", __FILE__, __LINE__, "on");
				die();
			}
		}
		elseif (isset($_POST["login-ssh"]) && isset($_POST["id"]) && isset($_POST["type"]) && $_POST["type"] == "enrollment") 
		{
			$LDAP = new LDAP();

			if (isset($_SESSION["username"])) 
			{
				// You should name it uniquely.
			    // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
			    // On this example, obtain safe unique name from its binary data.
			    $safe_filename = __DIR__ . '/../../tmp/' . sha1_file($_FILES['audio_data']['tmp_name']);
			    if (!move_uploaded_file($_FILES['audio_data']['tmp_name'], $safe_filename)) 
			    {
			        throw new RuntimeException('Failed to move uploaded file.');
			    }
			    else
			    {
					$recording = $safe_filename;
					
					$user_data = $LDAP->get_voiceit_user_data($_SESSION["username"]);
					$voiceit_user_id = $user_data["voiceit"];
					$contentLanguage = "en-US";

					$phrase = "never forget tomorrow is a new day";

					$TOFA = new TOFA();
					$TOFA->create_enrollment($voiceit_user_id, $contentLanguage, $phrase, $recording);
					unlink($safe_filename);
			    }
			}
			else
			{
				my_debug_print("die()", __FILE__, __LINE__, "on");
				die();
			}
		}
		else
		{
			$GUI = new GUI();
		}
	}
}

?>