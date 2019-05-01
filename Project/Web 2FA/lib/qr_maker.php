<?php

/**
 * 
 */
class QR_MAKER
{
    
    function __construct()
    {
        # code...
    }

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

    function make_qr_code($data, $QR_ECLEVEL = "M", $qr_size = 10, $size = 300)
    {
        require_once __DIR__ . "/phpqrcode/qrlib.php";

        //set it to writable location, a place for temp generated PNG files
        $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
        $PNG_TEMP_DIR = __DIR__ . "/../temp/";
        
        //html PNG location prefix
        $PNG_WEB_DIR = 'temp/';

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


        if (isset($data)) 
        { 
            //it's very important!
            if (trim($data) == '')
                die('secret cannot be empty! <a href="?">back</a>');

            $hash = hash_hmac("sha3-512", $data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize, $this->random_str(32));

            //$filename = $PNG_TEMP_DIR.md5($data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
            $filename = $PNG_TEMP_DIR.$hash.'.png';
            if (file_exists($filename)) 
            {
                //Dont make a new qr
            }
            else
            {
                QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 0);
            }  
        } 
        else 
        {    
            //default data
            echo 'You can provide data in GET parameter: <a href="?data=like_that">like that</a><hr/>';    
            QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 0);    
        }   

        return '<img src="'.$PNG_WEB_DIR.basename($filename).'" height=" ' . $size . ' px" width=" ' . $size . ' px"/>';
    }


    /**
     * Generate a random string, using a cryptographically secure 
     * pseudorandom number generator (random_int)
     * 
     * For PHP 7, random_int is a PHP core function
     * For PHP 5.x, depends on https://github.com/paragonie/random_compat
     * 
     * @param int $length      How many characters do we want?
     * @param string $keyspace A string of all possible characters
     *                         to select from
     * @return string
     */
    function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }
    /*
    Usage:
    $a = random_str(32);
    $b = random_str(8, 'abcdefghijklmnopqrstuvwxyz');
    */
}


?>