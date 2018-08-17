<?php
/**
* @author      John Graham <iampapagray@gmail.com> Enderr Studios
* @copyright   Copyright (C), 2018 John Graham
* @license     MIT LICENSE (https://opensource.org/licenses/MIT)
*              Refer to the LICENSE file distributed within the package.
*              
* @todo functions and error handling
* @category  functions
*
*/
require_once 'init.php';
$url = APP_URL;

//connect to database
function connectDB(){
  $con = new mysqli(HOST, USER, PASS, HOST_DB);
  // check connection
  if ($con->connect_error) {
    trigger_error('Database connection failed: '  . $con->connect_error, E_USER_ERROR);
  }else{
    return $con;
  }
}

// escaping html special characters
function h($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
}

// returns true if $needle is a substring of $haystack
function contains($needle, $haystack){
    return strpos($haystack, $needle) !== false;
}
