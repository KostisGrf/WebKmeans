<?php
require_once '../../server/dbconnect.php';
require '../../server/globalContext.php';
$method=$_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents("php://input"), true);

if($method!= "POST") {
    header("HTTP/1.1 403 Forbidden");
    print json_encode(['errormesg'=>"Method $method not allowed here."]);
    exit;
}
if(!isset($body['apikey'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"apikey is required"]);
    exit;
}

if(!checkApiKeyExists($body['apikey'])){
    header("HTTP/1.1 401 Unauthorized");
    print json_encode(['errormesg'=>"This Apikey does not exist."]);
    exit;
}


$old_apikey=$body['apikey'];
$api_key=bin2hex(random_bytes(16));



$sql="UPDATE users SET apiKey = ? WHERE apiKey = ?";
$st = $mysqli->prepare($sql);
$st->bind_param('ss',$api_key,$old_apikey);
$st->execute();

print json_encode(['apikey'=>$api_key]);

?>