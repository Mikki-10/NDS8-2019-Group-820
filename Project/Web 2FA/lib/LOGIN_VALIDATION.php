<?php

/**
 * 
 */
class LOGIN_VALIDATION
{
	#Allow using http response code 240 (made up)
	#Disallow using http response code 403 (403 Forbidden)
	#User not respoting in the time fream http response code 408 (408 Request Timeout)

	function __construct()
	{
		
	}

	function make_login($username)
	{
		$random_text = "never forget tomorrow is a new day";
		$random_id = mt_rand(100000, 999999);

		$dbtable = "logins";
	    $sqlstruktur = 'username, random_text, random_id, login_validated, timestamp';
	    $sqlvalues = ':username, :random_text, :random_id, :login_validated, :timestamp';
	    $data = array(
				    ':username'  			=> $username,
				    ':random_text' 			=> $random_text,
				    ':random_id' 			=> $random_id,
				    ':login_validated' 		=> false,
					':timestamp'			=> time()
				   );

		$DB = new DB();
		$id = $DB->add($sqlstruktur, $sqlvalues, $data, $dbtable);

		//my_debug_print($id, __FILE__, __LINE__, "on");

		if ($id != "") 
		{
			//my_debug_print($id, __FILE__, __LINE__, "on");
			$value = array(
						"random_text" => $random_text,
						"random_id" => $random_id,
						"id" => $id,
						);

			return $value;
		}
		else
		{
			return "id error";
		}
	}

	function check_login($username, $id)
	{			
		$DB = new DB();
		$login_validated = false;
		while ($login_validated === false) 
		{
			$time_start = microtime(true);
			while (microtime(true) - $time_start < LOGIN_SESION_TIME) 
			{
				$timeout = true;

				$data = $DB->get_ssh_login_username($id);

				if ($data["username"] !== $username) 
				{
					$timeout = false;
					break;
				}

				if ($data["timestamp"] === "0") 
				{
					$timeout = false;
					break;
				}

				if ($data["login_validated"] === "1" && $data["timestamp"] + LOGIN_SESION_TIME >= time()) 
				{
					$timeout = false;

					$DB->ssh_clear_timestamp($data["id"]);

					$login_validated = true;

					break;
				}
			}
			break;
		}

		if ($timeout === true) 
		{
			#User not respoting in the time fream http response code 408
			#(408 Request Timeout)
			http_response_code(408);
		}
		elseif ($login_validated === true) 
		{
			#Allow using http response code 240
			#(made up)
			http_response_code(240);
		}
		else
		{
			#Disallow using http response code 403
			#403 Forbidden
			http_response_code(403);
		}
	}

}

?>