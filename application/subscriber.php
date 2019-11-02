<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE"); 
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once("../bootstrap.php");
 
$id = (isset($_REQUEST["val"]) && is_numeric($_REQUEST["val"]))? $_REQUEST["val"] : 0;
$str = (isset($_REQUEST["val"]) && !is_numeric($_REQUEST["val"])) ? $_REQUEST["val"] : ""; 

$SubscribeController = new system\controllers\SubscriberController($db);

$data = (array) json_decode(file_get_contents("php://input"), TRUE);
if(isset($data["headers"])){
    $headers = $data["headers"];
}else{
    $headers = apache_request_headers(); 
}

// echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
// print_r($data); exit();
//Make sure user is authorized to do request
if ($SubscribeController->authenticated($headers) == false) {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}
$SubscribeController->doRequest($_SERVER["REQUEST_METHOD"], (int)$id, $str, $data); 