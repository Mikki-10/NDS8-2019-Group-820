<?php

/*
*
* $qr_eclevel = QR ECC level
* L = 1 - Minimum
* M = 2
* Q = 3
* H = 4 - Maximum
*
* $qr-size = QR reselution (opløsning)
* Value from 1 to 10 where 10 is biggest
*
* $size = size to show in a bowser
* size is in pixels
*
*/

function make_qr_code($data, $QR_ECLEVEL = "H", $qr_size = 10, $size = 300)
{
    require_once __DIR__ . "/phpqrcode/qrlib.php";

    //set it to writable location, a place for temp generated PNG files
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    
    //html PNG location prefix
    $PNG_WEB_DIR = __DIR__ . '/../temp/';

    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);
    
    
    $filename = $PNG_TEMP_DIR.'test.png';
    
    //processing form input
    //remember to sanitize user input in real-life solution !!!
    $errorCorrectionLevel = 'L';
    if (isset($QR_ECLEVEL) && in_array($QR_ECLEVEL, array('L','M','Q','H')))
        $errorCorrectionLevel = $QR_ECLEVEL;    

    $matrixPointSize = 4;
    if (isset($qr_size))
        $matrixPointSize = min(max((int)$qr_size, 1), 10);


    if (isset($data)) { 
    
        //it's very important!
        if (trim($data) == '')
            die('data cannot be empty! <a href="?">back</a>');
            
        // user data
        $filename = $PNG_TEMP_DIR.md5($data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
        if (file_exists($filename)) 
        {
        	//Dont make a new qr
        }
        else
        {
        	QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 0);
        }  
        
    } else {    
    
        //default data
        echo 'You can provide data in GET parameter: <a href="?data=like_that">like that</a><hr/>';    
        QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 0);    
        
    }   

    return '<img src="'.$PNG_WEB_DIR.basename($filename).'" height=" ' . $size . ' px" width=" ' . $size . ' px"/>';
}

?>