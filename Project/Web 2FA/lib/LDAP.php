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
		//ldap_close($this->ldap_link);
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

		//return $this->ldap_link;
	}

	function check_login($username, $password)
	{
		$dn="cn=".$username.",".$this->ldapconfig['usersdn'].",".$this->ldapconfig['basedn'];
		
		if ($bind=ldap_bind($this->ldap_link, $dn, $password))
		{
			echo("Login correct");//REPLACE THIS WITH THE CORRECT FUNCTION LIKE A REDIRECT;
			
			ldap_close($this->ldap_link);
			return "login ok";
		} 
		else 
		{
			echo "Login Failed: Please check your username or password";
			echo "<pre>"; var_dump($this->ldap_link); echo "</pre>";
			echo "<pre>"; var_dump($dn); echo "</pre>";
			echo "<pre>"; var_dump($bind); echo "</pre>";
			
			ldap_close($this->ldap_link);
			return FALSE;
		}
	}
	function add_voiceit($username, $voiceit_value, $update = false)
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

		//echo "<br>File: ".__FILE__." Line: ".__LINE__."<br>";
		//echo "<pre>"; var_dump($entry); echo "</pre>";

		if (isset($entry[0]["description"])) 
		{
			$ldap_description_write;
			foreach ($entry[0]["description"] as $key => $value) 
			{
				//echo "<br>File: ".__FILE__." Line: ".__LINE__."<br>";
				//echo "<pre>"; var_dump($key); echo "</pre>";
				//echo $key;
				if ($key === "count") 
				{
					//do not do anyting for count
					//echo "<br>File: ".__FILE__." Line: ".__LINE__."<br>";
				}
				else
				{
					$description = json_decode($value, true);
					if (json_last_error() === JSON_ERROR_NONE) 
					{
					    // Valid JSON
					    //echo "<br>File: ".__FILE__." Line: ".__LINE__."<br>";
						if (isset($description["voiceit"]) && $description["voiceit"] != "") 
						{
							if ($update === true) 
							{
								//echo "<br>File: ".__FILE__." Line: ".__LINE__."<br>";
								$description = array(
													"voiceit" => $voiceit_value,
													"voiceit_enrolled" => "0",
													);
								$json = json_encode($description);
								$ldap_description_write[$key] = $json;
							}
							else
							{
								//echo "<br>File: ".__FILE__." Line: ".__LINE__."<br>";
								$ldap_description_write[$key] = $value;
							}
						}
						else
						{
							//echo "<br>File: ".__FILE__." Line: ".__LINE__."<br>";
							$description = array(
												"voiceit" => $voiceit_value,
												"voiceit_enrolled" => "0",
												);
							$json = json_encode($description);
							$ldap_description_write[$key] = $json;
						}
					}
					else
					{
						// Not valid JSON
						//echo "<br>File: ".__FILE__." Line: ".__LINE__."<br>";
						$ldap_description_write[$key] = $value;
					}
				}
			}
			//echo "<br>File: ".__FILE__." Line: ".__LINE__."<br>";
			//echo "<pre>"; var_dump($ldap_description_write); echo "</pre>";
			//Value to change
			$le = array("description" => array_values($ldap_description_write));
			//$le = array("description" => array("1", "2"));
			//Change the value
			$result = ldap_modify($this->ldap_link, $dn, $le);

			//echo "<br>File: ".__FILE__." Line: ".__LINE__."<br>";
			//echo "<pre>"; var_dump($result); echo "</pre>";
		}
		else
		{
			//Add value 
			$description = array(
								"voiceit" => $voiceit_value,
								"voiceit_enrolled" => "0",
								);
			//echo "<br>File: ".__FILE__." Line: ".__LINE__."<br>";
			//echo "<pre>"; var_dump($description); echo "</pre>";
			$json = json_encode($description);
			//echo "<br>File: ".__FILE__." Line: ".__LINE__."<br>";
			//echo "<pre>"; var_dump($json); echo "</pre>";
			$ldap_description_write[0] = $json;

			//Value to change
			$le = array("description" => array_values($ldap_description_write));
			//$le = array("description" => array("3", "4"));
			//Change the value
			$result = ldap_modify($this->ldap_link, $dn, $le);

			//echo "<br>File: ".__FILE__." Line: ".__LINE__."<br>";
			//echo "<pre>"; var_dump($result); echo "</pre>";
		}

		ldap_close($this->ldap_link);
	}

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
		
		//echo "<pre>"; var_dump($result); echo "</pre>";

		ldap_close($this->ldap_link);
	}
}

?>