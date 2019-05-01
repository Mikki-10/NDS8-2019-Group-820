<?php

if (FORCE_DEBUG_OFF === "false") 
{
	error_reporting(E_ALL);
	ini_set("display_errors", true);
}

/*
*
* my_debug_print($value, __FILE__, __LINE__, "on");
* my_debug_print(debug_string_backtrace(), __FILE__, __LINE__, "on");
*
*/

function my_debug_print($value, $file, $line, $debug = "off")
{
	if (FORCE_DEBUG_OFF === "false") 
	{
		if ($debug === "on") 
		{
			echo "<pre>";
			echo "File: ".$file." Line: ".$line."<br>";
			echo "Debug id: ".microtime(true)."<br>";
			if (is_string($value)) 
			{
				echo $value;
			}
			else
			{
				var_dump($value);
			}
			echo "</pre>";
		}
		else
		{
			//Debug is off
		}
	}
}

function debug_string_backtrace() 
{ 
    if (FORCE_DEBUG_OFF === "false") 
	{

	    ob_start(); 
	    debug_print_backtrace(); 
	    $trace = ob_get_contents(); 
	    ob_end_clean(); 

	    // Remove first item from backtrace as it's this function which 
	    // is redundant. 
	    $trace = preg_replace ('/^#0\s+' . __FUNCTION__ . "[^\n]*\n/", '', $trace, 1); 

	    // Renumber backtrace items. 
	    //$trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace); //Do not work in php7
	}
	else
	{
		$trace = "";
	}

    return $trace; 
}

?>