<?php
require_once '../../server/dbconnect.php';
require '../../server/globalContext.php';

$body = json_decode(file_get_contents("php://input"), true);

if(!isset($body['token'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"token is required"]);
    exit;
}

if(!isset($body['password'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"password is required"]);
    exit;
}

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

$hashed_passwd= password_hash($body['password'], PASSWORD_BCRYPT);

$sql = 'call update_password(?,?)';
$st = $mysqli->prepare($sql);
$st->bind_param('ss',$hashed_passwd,$body['token']);
$st->execute();

print json_encode(["message"=>"New password has been saved"]);
?>