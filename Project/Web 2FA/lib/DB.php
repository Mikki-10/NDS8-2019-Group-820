<?php


/**
* 
*/
class DB
{
	private $conn;

	function __construct()
	{
		$servername = 'mysql:dbname=' . DB_NAME . ';host=' . DB_SERVER;
		$username = DB_USER;
		$password = DB_PASS;

	    try 
		{
	    	$this->conn = new PDO($servername, $username, $password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	    } 
	    catch (PDOException $e) 
	    {
	    	//echo 'Connection failed: ' . $e->getMessage();
    		//debug_to_console('Connection failed: ' . $e->getMessage());
		}
	}

	function __destruct()
	{
		$this->conn = NULL;
	}









	// ------------------------------------------------------------------- //
    /**
    * Function to add data to the DB
	*
    * @param string     $sqlstruktur        	The column names for were the data shoud be added
    * @param string 	$sqlvalues				The value defined as names for binding 
    * @param array 		$data 					The sqlvalues with values as array
    * @param string 	$dbtable 				The DB table where the data shut go in
    *
    * Example
    * $dbtable = users
    * $sqlstruktur = 'name, email, username, password, ativation-code';
    * $sqlvalues = ':name, :email, :username, :password, :ativation-code';
    * $data = array(
			     ':name' 				=> $_POST["name"],
			     ':email' 				=> $_POST["email"], 
			     ':username'  			=> $_POST["username"],
			     ':password' 			=> password_hashing($_POST["password"]),
			     ':ativation-code' 		=> mt_rand(10000000, 99999999)
			   );
    * $DB = New DB();
    * $DB->add($sqlstruktur, $sqlvalues, $data, $dbtable);
    * 
    *
    * OLD NAME: add_data_to_db
    *
    * Call from
    * Function 										File
    * PHP_MIKROTIK_USERS::make_new					includes\lib\PHP_MIKROTIK_USERS.php
    * php_funktioner_opret_web_bruger()				includes\php_funktioner.php
    * Not in a function 							includes\request_handling.php
    * pushbullet_receive_gem_komando_og_besked()	pushbullet_receive.php
    * 				
    */
    // ------------------------------------------------------------------- //
	function add($sqlstruktur, $sqlvalues, $data, $dbtable)
	{
		//debugecho("<br>Stack trace<br>File: ".__FILE__." Function: ADD Line: ".__LINE__."<br>");
	    
	    //$this->conn = db_funktioner_make_conn_to_db();

		$sql = "INSERT INTO  " . $dbtable . " (" . $sqlstruktur . ") 
		VALUES (" . $sqlvalues . ")";

		// Klargør forespørgsel
		$statement = $this->conn->prepare( $sql );
		
		if ($statement->execute($data) === TRUE)
		{
			//debugecho("<br>Data added to DB <br>");
			//debugecho("<br>File: ".__FILE__." Line: ".__LINE__."<br>");
			return TRUE;
		}
		else
		{
			//include "sqlerror.php"; //Error logger
			//debugecho("<br>Error adding data to DB <br>");
			//debugecho("<br>File: ".__FILE__." Line: ".__LINE__."<br>");
			//echo "nope";
			return FALSE;
		}

	}

	// ------------------------------------------------------------------- //
	/**
	* Users
	* 
	* 
	* 
	*/
	// ------------------------------------------------------------------- //
	function get_user($username)
	{
		//$this->conn = db_funktioner_make_conn_to_db();

	    //echo $id_value;

	    $sql = 'SELECT * FROM `users` WHERE `username` = :username';

	    $stmt = $this->conn->prepare($sql);

	    $stmt->bindParam(':username', $username);

	    $stmt->execute();
	    $user = $stmt->fetchAll();

	    if (isset($user[0])) 
	    {
	    	return $user[0];
	    }	
	    else
	    {
	    	$dbtable = "users";
		    $sqlstruktur = 'username';
		    $sqlvalues = ':username';
		    $data = array(':username' => $username);
		    $this->add($sqlstruktur, $sqlvalues, $data, $dbtable);

		    return $this->get_user($username);
	    }
	}

	function set_voiceit_user($username, $voiceit)
	{
	    $sql = 'UPDATE `users` SET `voiceit` = :voiceit WHERE username = :username';

		$stmt = $this->conn->prepare($sql);

		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':voiceit', $voiceit);

		//echo "<pre>"; var_dump($stmt); echo "</pre>";

		$stmt->execute();
	}

	function get_voiceit_enrolled($voiceit)
	{
		$sql = 'SELECT * FROM `users` WHERE `voiceit` = :voiceit';

	    $stmt = $this->conn->prepare($sql);

	    $stmt->bindParam(':voiceit', $voiceit);

	    $stmt->execute();
	    $user = $stmt->fetchAll();

	    if (isset($user[0])) 
	    {
	    	return $user[0];
	    }	
	    else
	    {
	    	return false;
	    }
	}

	function set_voiceit_enrolled($voiceit, $voiceit_enrolled)
	{
	    $sql = 'UPDATE `users` SET `voiceit_enrolled` = :voiceit_enrolled WHERE voiceit = :voiceit';

		$stmt = $this->conn->prepare($sql);

		$stmt->bindParam(':voiceit', $voiceit);
		$stmt->bindParam(':voiceit_enrolled', $voiceit_enrolled);

		//echo "<pre>"; var_dump($stmt); echo "</pre>";

		$stmt->execute();
	}



	// ------------------------------------------------------------------- //
	/**
	* Logins
	* 
	* 
	* 
	*/
	// ------------------------------------------------------------------- //

	function get_ssh_login($username)
	{
	    $sql = 'SELECT * FROM `logins` WHERE `username` = :username ORDER BY id DESC LIMIT 1';

	    $stmt = $this->conn->prepare($sql);

	    $stmt->bindParam(':username', $username);

	    $stmt->execute();
	    $ssh_login = $stmt->fetchAll();

	    if (isset($ssh_login) && isset($ssh_login[0])) 
	    {
	    	return $ssh_login[0];
	    }
	    else
	    {
	    	return false;
	    }
	}

	function get_ssh_login_username($id)
	{
		$sql = 'SELECT * FROM `logins` WHERE `id` = :id ORDER BY id DESC LIMIT 1';

	    $stmt = $this->conn->prepare($sql);

	    $stmt->bindParam(':id', $id);

	    $stmt->execute();
	    $ssh_login = $stmt->fetchAll();

	    return $ssh_login[0];
	}

	function update_ssh_login($id, $bool)
	{
	    $sql = 'UPDATE `logins` SET `login_validated` = :bool WHERE id = :id';

		$stmt = $this->conn->prepare($sql);

		$stmt->bindParam(':bool', $bool);
		$stmt->bindParam(':id', $id);

		//echo "<pre>"; var_dump($stmt); echo "</pre>";

		$stmt->execute();
	}

	function ssh_validated($id)
	{
	    $sql = 'UPDATE `logins` SET `timestamp` = "0" WHERE id = :id';

		$stmt = $this->conn->prepare($sql);

		$stmt->bindParam(':id', $id);

		//echo "<pre>"; var_dump($stmt); echo "</pre>";

		$stmt->execute();
	}

}
?>