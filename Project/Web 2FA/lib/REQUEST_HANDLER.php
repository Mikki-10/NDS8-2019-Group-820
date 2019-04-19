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

		//$dump = "fuld: " . $fuldstreng . "\n bowser: " . $browser;

		//file_put_contents("test.txt", $dump);

		if ($fuldstreng == "python-requests/2.18.4") 
		{
			return "server";
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
			$LOGIN_VALIDATION = new LOGIN_VALIDATION();
			$data = $LOGIN_VALIDATION->check_login($_POST["username"]);

			echo $data;
		}
		elseif ($type == "client") 
		{
			php_session_beskyt();
			
			if (isset($_POST["login-ssh"]) && isset($_POST["id"]) && isset($_POST["type"]) && $_POST["type"] == "verification") 
			{
				$DB = new DB();
				$data = $DB->get_ssh_login_username($_POST["id"]);

				//echo "<pre>"; var_dump($data); echo "</pre>";
				//echo "<pre>"; var_dump($_SESSION["username"]); echo "</pre>";

				if ($data["username"] == $_SESSION["username"]) 
				{
					$input = $_FILES['audio_data']['tmp_name']; //temporary name that PHP gave to the uploaded file
					$recording = $input;

					//move_uploaded_file($tmp_name, "$uploads_dir/$name");
					
					$user_data = $DB->get_user($data["username"]);
					$voiceit_user_id = $user_data["voiceit"];
					$contentLanguage = "en-US";

					$login_data = $DB->get_ssh_login($data["username"]);
					$phrase = $login_data["random_text"];

					$TOFA = new TOFA();
					if ($TOFA->validate_voice($voiceit_user_id, $contentLanguage, $phrase, $recording))
					{
						$DB->update_ssh_login($_POST["id"], true);
					}
				}
				else
				{
					die();
				}
			}
			elseif (isset($_POST["login-ssh"]) && isset($_POST["id"]) && isset($_POST["type"]) && $_POST["type"] == "enrollment") 
			{
				$DB = new DB();
				//$data = $DB->get_ssh_login_username($_POST["id"]);

				//echo "<pre>"; var_dump($data); echo "</pre>";
				//echo "<pre>"; var_dump($_SESSION["username"]); echo "</pre>";

				if (isset($_SESSION["username"])) 
				{
					//$input = $_FILES['audio_data']['tmp_name']; //temporary name that PHP gave to the uploaded file

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
				    	//$recording = file_get_contents($safe_filename);
					    //var_dump($recording);

						$recording = $safe_filename;

						//move_uploaded_file($tmp_name, "$uploads_dir/$name");
						
						$user_data = $DB->get_user($_SESSION["username"]);
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
					die();
				}
			}
			else
			{
				$GUI = new GUI();
			}
		}
		else
		{
			die("Type not supported");
		}
	}
}

?>