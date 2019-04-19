<?php

/**
 * 
 */
class GUI
{
	function __construct()
	{
		$this->show();
	}

	function show()
	{
		$this->is_there_a_login($_SESSION["username"]);
	}

	function is_there_a_login($username)
	{
		$DB = new DB();
		$data = $DB->get_ssh_login($username);


		//echo "<pre>"; var_dump($data); echo "</pre>";


		$TOFA = new TOFA();
		$voiceit = $TOFA->voiceit_user_exists($username);
		if ($TOFA->voiceit_user_is_enrolled($voiceit) == false) 
		{
			?>
			<div id="response-div"></div>
			<?php
			$TOFA->voiceit($username, "0", "enrollment");
		}
		elseif ($data["login_validated"] == "0" && $data["timestamp"] + LOGIN_SESION_TIME >= time()) 
		{
			//echo "<pre>"; var_dump($data); echo "</pre>";
			?>
			<div id="response-div"></div>
			<?php

			$TOFA = new TOFA();
			$TOFA->voiceit($username, $data["id"], "verification");
		}
		else
		{
			$auto = 1 * 1000;
			?>	
			<script type="text/javascript">
			setTimeout(function()
			{
			   location.href = "/";
			}, <?php echo $auto; ?>);
			</script>
			<?php
		}
	}
}

?>