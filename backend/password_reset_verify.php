<?php
require_once 'dbconnect.php';
require 'token.php';

$body = json_decode(file_get_contents("php://input"), true);

$hashed_passwd= password_hash($body['password'], PASSWORD_BCRYPT);

if(!checkTokenExists($body['token'])){
    header("HTTP/1.1 400 Bad Request");
	print json_encode(['errormesg'=>"Token does not exist."]);
	exit;
}


if(checkTokenExpired($body['token'])){
    header("HTTP/1.1 401 Unauthorized ");
    print json_encode(['errormesg'=>"token has expired"]);
    exit;
}

$sql = 'call update_password(?,?)';
$st = $mysqli->prepare($sql);
$st->bind_param('ss',$hashed_passwd,$body['token']);
$st->execute();

print json_encode(["message"=>"New password has been saved"]);
?>