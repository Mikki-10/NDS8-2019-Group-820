<?php

/**
 * 
 */
class LOGIN_VALIDATION
{
	#Allow using http response code 240
	#Disallow using http response code 404
	#User not respoting in the time fream http response code 418

	function __construct()
	{
		
	}

	function check_login($username)
	{
		if ($username == "testuser")
		{
			$random_text = "never forget tomorrow is a new day";

			$dbtable = "logins";
		    $sqlstruktur = 'username, random_text, login_validated, timestamp';
		    $sqlvalues = ':username, :random_text, :login_validated, :timestamp';
		    $data = array(
					    ':username'  			=> $username,
					    ':random_text' 			=> $random_text,
					    ':login_validated' 		=> false,
						':timestamp'			=> time()
					   );


			$DB = new DB();
			$DB->add($sqlstruktur, $sqlvalues, $data, $dbtable);
			
			$login_validated = false;
			while ($login_validated === false) 
			{
				$time_start = microtime(true);
				while (microtime(true) - $time_start < LOGIN_SESION_TIME) 
				{
					$timeout = true;

					$data = $DB->get_ssh_login($username);

					if ($data["timestamp"] == "0") 
					{
						break;
					}

					//$data = file_get_contents( __DIR__ . "/../db/ssh-logins/" . $username . ".json");

					//$data = json_decode($data, true);

					if ($data["login_validated"] == "1" && $data["timestamp"] + LOGIN_SESION_TIME >= time()) 
					{
						$timeout = false;

						/*
						$data = array(
							'username' => $username,
							'random_text' => $random_text,
							'login_validated' => false,
							'timestamp' => 0,
							);
						file_put_contents( __DIR__ . "/../db/ssh-logins/" . $username . ".json" , json_encode($data));
						*/
						//$DB->update_ssh_login($data["id"], false);
						$DB->ssh_validated($data["id"]);

						$login_validated = true;

						break;
					}
				}
				break;
			}

			if ($timeout === true) 
			{
				#User not respoting in the time fream http response code 418
				http_response_code(418);
			}
			elseif ($login_validated === true) 
			{
				#Allow using http response code 240
				http_response_code(240);
			}
			else
			{
				#Disallow using http response code 404
				http_response_code(404);
			}
		}
		else
		{
			http_response_code(404);
		}

		return $data;
	}

}

?>