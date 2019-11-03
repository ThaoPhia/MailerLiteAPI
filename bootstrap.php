<?php  
require_once('vendor/autoload.php'); 

//NOTE: Yes, I know I shouldn't put these sensitive info here, but this is not a real project. :) 
//Define constant variables for environment.  
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_DATABASE', 'mailerlite');     
define('DB_USERNAME', 'root');          //NOTE: Change this to whatever it is in your environment 
define('DB_PASSWORD', 'password');      //NOTE: Change this to whatever it is in your environment  
define('TOKEN', '2SnFjARLtB5yidvZoPV7DpiLk4oNXx6C');    

$db = \system\database\DB::getInstance()->getConnection(); 