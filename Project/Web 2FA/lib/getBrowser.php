<?php

//error_reporting(0);

function getBrowser() 
{ 
    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
    $bname = null;
    $platform = null;
    $version = null;
    $bit = null;

    //First get the platform?

    //Find den rigte linux version
    if (preg_match('/linux/i', $u_agent)) 
    {
        $platform = 'Linux';

        if (preg_match('/Android/i', $u_agent)) 
	    {
	        $a = explode(";", $u_agent);
	        $platform = $a[1];
	    }
	    elseif (preg_match('/Ubuntu/i', $u_agent)) 
	    {
	    	$platform = "Ubuntu";
	    	
	    	if (preg_match('/x86_64/i', $u_agent) )
	    	{
	    		$bit = '64';
	    	}
	    	else
	    	{
	    		$bit = '32';
	    	}
	    	
	    }
	    

    }

    //Find den rigtige mac version
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) 
    {
        $platform = 'Mac';
    }
    
    //Find den rigtige windows version
    elseif (preg_match('/windows|win32/i', $u_agent)) 
    {
        $platform = 'Windows (ukendt)';
        
        if(preg_match('/windows NT 10|win32/i', $u_agent))
        {
        	$platform = 'Windows 10';
        	if (preg_match('/WOW64|win32/i', $u_agent)) {
        		$bit = '64';
        	}
        	elseif (preg_match('/Win64|win32/i', $u_agent)) 
        	{
        		$bit = '64';
        	}
        	elseif (preg_match('/x64|win32/i', $u_agent)) 
        	{
        		$bit = '64';
        	}
	        else
        	{
        		$bit = '32';
        	}
        }
        elseif (preg_match('/windows NT 6.3|win32/i', $u_agent)) 
        {
        	$platform = 'Windows 8.1';
        	if (preg_match('/WOW64|win32/i', $u_agent)) {
        		$bit = '64';
        	}
        	elseif (preg_match('/Win64|win32/i', $u_agent)) 
        	{
        		$bit = '64';
        	}
        	elseif (preg_match('/x64|win32/i', $u_agent)) 
        	{
        		$bit = '64';
        	}
	        else
        	{
        		$bit = '32';
        	}
	    }
	    elseif (preg_match('/windows NT 6.2|win32/i', $u_agent)) 
	    {
	        $platform = 'Windows 8';
	        if (preg_match('/WOW64|win32/i', $u_agent)) {
        		$bit = '64';
        	}
        	elseif (preg_match('/Win64|win32/i', $u_agent)) 
        	{
        		$bit = '64';
        	}
        	elseif (preg_match('/x64|win32/i', $u_agent)) 
        	{
        		$bit = '64';
        	}
	        else
        	{
        		$bit = '32';
        	}
	    }
	    elseif (preg_match('/windows NT 6.1|win32/i', $u_agent)) 
	    {
	        $platform = 'Windows 7';
	        if (preg_match('/WOW64|win32/i', $u_agent)) {
        		$bit = '64';
        	}
        	elseif (preg_match('/Win64|win32/i', $u_agent)) 
        	{
        		$bit = '64';
        	}
        	elseif (preg_match('/x64|win32/i', $u_agent)) 
        	{
        		$bit = '64';
        	}
	        else
        	{
        		$bit = '32';
        	}
	    }
	    elseif (preg_match('/windows NT 6.0|win32/i', $u_agent)) 
	    {
	        $platform = 'Windows Vista';
	        if (preg_match('/WOW64|win32/i', $u_agent)) {
        		$bit = '64';
        	}
        	elseif (preg_match('/Win64|win32/i', $u_agent)) 
        	{
        		$bit = '64';
        	}
        	elseif (preg_match('/x64|win32/i', $u_agent)) 
        	{
        		$bit = '64';
        	}
	        else
        	{
        		$bit = '32';
        	}
	    }
	    elseif (preg_match('/windows NT 5.2|win32/i', $u_agent)) 
	    {
	        $platform = 'Windows XP (NT 5.2)';
	        if (preg_match('/WOW64|win32/i', $u_agent)) {
        		$bit = '64';
        	}
        	elseif (preg_match('/Win64|win32/i', $u_agent)) 
        	{
        		$bit = '64';
        	}
        	elseif (preg_match('/x64|win32/i', $u_agent)) 
        	{
        		$bit = '64';
        	}
	        else
        	{
        		$bit = '32';
        	}
	    }
	    elseif (preg_match('/windows NT 5.1|win32/i', $u_agent)) 
	    {
	        $platform = 'Windows XP (NT 5.1)';
	        if (preg_match('/WOW64|win32/i', $u_agent)) {
        		$bit = '64';
        	}
        	elseif (preg_match('/Win64|win32/i', $u_agent)) 
        	{
        		$bit = '64';
        	}
        	elseif (preg_match('/x64|win32/i', $u_agent)) 
        	{
        		$bit = '64';
        	}
	        else
        	{
        		$bit = '32';
        	}
	    }
	    elseif (preg_match('/windows NT 5.0|win32/i', $u_agent)) 
	    {
	        $platform = 'Windows 2000';
	        if (preg_match('/WOW64|win32/i', $u_agent)) {
        		$bit = '64';
        	}
        	elseif (preg_match('/Win64|win32/i', $u_agent)) 
        	{
        		$bit = '64';
        	}
        	elseif (preg_match('/x64|win32/i', $u_agent)) 
        	{
        		$bit = '64';
        	}
	        else
        	{
        		$bit = '32';
        	}
	    }

    }
    
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Internet Explorer'; 
        $ub = "MSIE"; 
    } 
    elseif(preg_match('/Firefox/i',$u_agent)) 
    { 
        $bname = 'Mozilla Firefox'; 
        $ub = "Firefox"; 
    } 
    elseif(preg_match('/Chrome/i',$u_agent)) 
    { 
        $bname = 'Google Chrome'; 
        $ub = "Chrome"; 
    } 
    elseif(preg_match('/Safari/i',$u_agent)) 
    { 
        $bname = 'Apple Safari'; 
        $ub = "Safari"; 
    } 
    elseif(preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Opera'; 
        $ub = "Opera"; 
    } 
    elseif(preg_match('/Netscape/i',$u_agent)) 
    { 
        $bname = 'Netscape'; 
        $ub = "Netscape"; 
    }
    elseif(preg_match('/Googlebot/i',$u_agent)) 
    { 
        $bname = 'Googlebot'; 
        $ub = "Googlebot"; 
    }
    elseif(preg_match('/UptimeRobot/i',$u_agent)) 
    { 
        $bname = 'UptimeRobot'; 
        $ub = "UptimeRobot"; 
    }  
    
    // finally get the correct version number
    $known = @array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9\.|a-zA-Z\.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            @$version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
    
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    

	return @array(
    'userAgent' 		=> $u_agent,
    'name'      		=> $bname,
    'version'   		=> $version,
    'version_Android' 	=> $version_Android,
    'platform'  		=> $platform,
    'pattern'   		=> $pattern,
    'bit'				=> $bit
	);
    

} 

/*
$ua = getBrowser();
$os = $ua['platform'];
$browserandversion = $ua['name'] . " " . $ua['version'];
$browser = $ua['name'];
$browserversion = $ua['version'];
$fuldstreng = $ua['userAgent'];
$osbit = $ua['bit'];
*/

//visecho($yourbrowser);
//visecho("\n");
//visecho($yourbrowser2);

//error_reporting(1);

?>