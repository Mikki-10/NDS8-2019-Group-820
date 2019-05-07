<?php

/**
 * 
 */
class LDAP
{
	Private $ldap_link;
	Private $ldapconfig;

	Private $admin_username = "admin";
	Private	$admin_password = "admin";

	function __construct()
	{
		$this->connect();
	}

	function __destruct()
	{
		ldap_close($this->ldap_link);
	}

	function connect()
	{
		//cn=readonly,dc=example,dc=org
		$this->ldapconfig['host'] = '192.168.20.4';//CHANGE THIS TO THE CORRECT LDAP SERVER
		$this->ldapconfig['port'] = '389';
		$this->ldapconfig['basedn'] = 'dc=example,dc=org';//CHANGE THIS TO THE CORRECT BASE DN
		$this->ldapconfig['usersdn'] = 'ou=People';//CHANGE THIS TO THE CORRECT USER OU/CN
		$this->ldap_link=ldap_connect($this->ldapconfig['host'], $this->ldapconfig['port']);
		if (!$this->ldap_link) 
		{
		    exit('Connection failed');
		}

		ldap_set_option($this->ldap_link, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->ldap_link, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($this->ldap_link, LDAP_OPT_NETWORK_TIMEOUT, 10);
	}

	// ----------------------------------------------------------------------------------- //
	//
	// LDAP login
	//
	// ----------------------------------------------------------------------------------- //

	function check_login($username, $password)
	{
		$dn="cn=".$username.",".$this->ldapconfig['usersdn'].",".$this->ldapconfig['basedn'];
		
		if ($bind=ldap_bind($this->ldap_link, $dn, $password))
		{
			//my_debug_print("Login corret", __FILE__, __LINE__, "on");
			
			//ldap_close($this->ldap_link);
			return "login ok";
		} 
		else 
		{
			//my_debug_print("Login Failed: Please check your username or password", __FILE__, __LINE__, "on");
			//my_debug_print($this->ldap_link, __FILE__, __LINE__, "on");
			//my_debug_print($dn, __FILE__, __LINE__, "on");
			//my_debug_print($bind, __FILE__, __LINE__, "on");
			
			//ldap_close($this->ldap_link);
			return FALSE;
		}
	}




	// ----------------------------------------------------------------------------------- //
	//
	// Voiceit
	//
	// ----------------------------------------------------------------------------------- //

	function get_voiceit_user_data($username)
	{
		//my_debug_print(debug_string_backtrace(), __FILE__, __LINE__, "on");

		//Admin login
		$dn="cn=".$this->admin_username.",".$this->ldapconfig['basedn'];
		$bind=ldap_bind($this->ldap_link, $dn, $this->admin_password);

		//User to read value from
		$dn="cn=".$username.",".$this->ldapconfig['usersdn'].",".$this->ldapconfig['basedn'];

		$filter = "(objectclass=*)"; // this command requires some filter
		$filter_types = array("description");
		$sr = ldap_read($this->ldap_link, $dn, $filter, $filter_types);
		$entry = ldap_get_entries($this->ldap_link, $sr);

		if (isset($entry[0]["description"])) 
		{
			$ldap_description_write;
			foreach ($entry[0]["description"] as $key => $value) 
			{
				if ($key === "count") 
				{
					//do not do anyting for count
				}
				else
				{
					$description = json_decode($value, true);
					if (json_last_error() === JSON_ERROR_NONE) 
					{
					    // Valid JSON
						if (isset($description["voiceit"]) && $description["voiceit"] != "") 
						{
							//my_debug_print($description, __FILE__, __LINE__, "on");

							return array(
										'voiceit' 			=> $description["voiceit"],
										'voiceit_enrolled' 	=> $description["voiceit_enrolled"],
										);
						}
						else
						{
							return "false";
						}
					}
				}
			}
		}
	}

	function set_voiceit_enrolled($username, $voiceit_value)
	{
		$voiceit_enrolled = "1";
		$update = true;
		$this->add_voiceit($username, $voiceit_value, $voiceit_enrolled, $update);
	}

	function add_voiceit($username, $voiceit_value, $voiceit_enrolled = "0", $update = false)
	{
		//Admin login
		$dn="cn=".$this->admin_username.",".$this->ldapconfig['basedn'];
		$bind=ldap_bind($this->ldap_link, $dn, $this->admin_password);

		//User to change value on
		$dn="cn=".$username.",".$this->ldapconfig['usersdn'].",".$this->ldapconfig['basedn'];

		$filter = "(objectclass=*)"; // this command requires some filter
		$filter_types = array("description");
		$sr = ldap_read($this->ldap_link, $dn, $filter, $filter_types);
		$entry = ldap_get_entries($this->ldap_link, $sr);

		if (isset($entry[0]["description"])) 
		{
			$ldap_description_write;
			foreach ($entry[0]["description"] as $key => $value) 
			{
				if ($key === "count") 
				{
					//do not do anyting for count
				}
				else
				{
					$description = json_decode($value, true);
					if (json_last_error() === JSON_ERROR_NONE) 
					{
					    // Valid JSON
						if (isset($description["2fa"]) && $description["2fa"] != "") 
						{
							if (isset($description["voiceit"]) && $description["voiceit"] != "")
							{
								if ($update === true) 
								{
									$description = array(
														"voiceit" => $voiceit_value,
														"voiceit_enrolled" => $voiceit_enrolled,
														"2fa" => $description["2fa"],
														);
									$json = json_encode($description);
									$ldap_description_write[$key] = $json;
								}
							}
							else
							{
								$description = array(
													"voiceit" => $voiceit_value,
													"voiceit_enrolled" => $voiceit_enrolled,
													"2fa" => $description["2fa"],
													);
								$json = json_encode($description);
								$ldap_description_write[$key] = $json;
							}
						}
						else
						{
							$ldap_description_write[$key] = $value;
						}
					}
					else
					{
						// Not valid JSON
						$ldap_description_write[$key] = $value;
					}
				}
			}
			//Value to change
			$le = array("description" => array_values($ldap_description_write));
			//Change the value
			$result = ldap_modify($this->ldap_link, $dn, $le);
		}
		else
		{
			my_debug_print("Can not set voice settings before 2fa on web", __FILE__, __LINE__, "on");
			die();
			//Shoud never go down here as web 2fa will be set using json before voiceit
		}
	}


	// ----------------------------------------------------------------------------------- //
	//
	// Web 2FA
	//
	// ----------------------------------------------------------------------------------- //

	function get_2fa_user_data($username)
	{
		//my_debug_print(debug_string_backtrace(), __FILE__, __LINE__, "on");

		//Admin login
		$dn="cn=".$this->admin_username.",".$this->ldapconfig['basedn'];
		$bind=ldap_bind($this->ldap_link, $dn, $this->admin_password);

		//User to read value from
		$dn="cn=".$username.",".$this->ldapconfig['usersdn'].",".$this->ldapconfig['basedn'];

		$filter = "(objectclass=*)"; // this command requires some filter
		$filter_types = array("description");
		$sr = ldap_read($this->ldap_link, $dn, $filter, $filter_types);
		$entry = ldap_get_entries($this->ldap_link, $sr);

		if (isset($entry[0]["description"])) 
		{
			$ldap_description_write;
			foreach ($entry[0]["description"] as $key => $value) 
			{
				if ($key === "count") 
				{
					//do not do anyting for count
				}
				else
				{
					$description = json_decode($value, true);
					if (json_last_error() === JSON_ERROR_NONE) 
					{
					    // Valid JSON
						if (isset($description["2fa"]) && $description["2fa"] != "") 
						{
							//my_debug_print($description, __FILE__, __LINE__, "on");

							return $description["2fa"];
						}
						else
						{
							return "false";
						}
					}
				}
			}
		}
	}

	function add_2fa($username, $secret, $update = false)
	{
		//Admin login
		$dn="cn=".$this->admin_username.",".$this->ldapconfig['basedn'];
		$bind=ldap_bind($this->ldap_link, $dn, $this->admin_password);

		//User to change value on
		$dn="cn=".$username.",".$this->ldapconfig['usersdn'].",".$this->ldapconfig['basedn'];

		$filter = "(objectclass=*)"; // this command requires some filter
		$filter_types = array("description");
		$sr = ldap_read($this->ldap_link, $dn, $filter, $filter_types);
		$entry = ldap_get_entries($this->ldap_link, $sr);

		if (isset($entry[0]["description"])) 
		{
			$ldap_description_write;
			foreach ($entry[0]["description"] as $key => $value) 
			{
				if ($key === "count") 
				{
					//do not do anyting for count
				}
				else
				{
					$description = json_decode($value, true);
					if (json_last_error() === JSON_ERROR_NONE) 
					{
					    // Valid JSON
						if (isset($description["2fa"]) && $description["2fa"] != "") 
						{
							if ($update === true) 
							{
								$description = array(
													"2fa" => $secret,
													);
								$json = json_encode($description);
								$ldap_description_write[$key] = $json;
							}
							else
							{
								$ldap_description_write[$key] = $value;
							}
						}
						else
						{
							$description = array(
												"2fa" => $secret,
												);
							$json = json_encode($description);
							$ldap_description_write[$key] = $json;
						}
					}
					else
					{
						// Not valid JSON
						$ldap_description_write[$key] = $value;
					}
				}
			}
			//Value to change
			$le = array("description" => array_values($ldap_description_write));
			//Change the value
			$result = ldap_modify($this->ldap_link, $dn, $le);
		}
		else
		{
			//Add value 
			$description = array(
								"2fa" => $secret,
								);
			$json = json_encode($description);
			$ldap_description_write[0] = $json;

			//Value to change
			$le = array("description" => array_values($ldap_description_write));
			//Change the value
			$result = ldap_modify($this->ldap_link, $dn, $le);
		}
	}














	// ----------------------------------------------------------------------------------- //
	//
	// Other
	//
	// ----------------------------------------------------------------------------------- //

	//Dangerous to use as is
	function modify_value($username, $key, $value)
	{
		//Admin login
		$dn="cn=".$this->admin_username.",".$this->ldapconfig['basedn'];
		$bind=ldap_bind($this->ldap_link, $dn, $this->admin_password);

		//User to change
		$dn="cn=".$username.",".$this->ldapconfig['usersdn'].",".$this->ldapconfig['basedn'];
		//Value to change
		$le = array($key => array($value));
		//Change the value
		$result = ldap_modify($this->ldap_link, $dn, $le);
	}
}

?>