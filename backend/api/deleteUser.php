<?php

require_once '../dbconnect.php';
require '../config.php';
require '../phpmailer.php';

$method=$_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents("php://input"), true);

if($method!='DELETE'){
    header("HTTP/1.1 403 Forbidden");
    print json_encode(['errormesg'=>"Method $method not allowed here."]);
    exit;
}

if(!isset($body['apikey'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"apikey is required"]);
    exit;
}


$token=bin2hex(random_bytes(16));

$sql = 'call deleteUser(?,?)';
$st = $mysqli->prepare($sql);
$st->bind_param('ss',$body['apikey'],$token);
$st->execute();

$sql2 = 'SELECT email,fname FROM users WHERE apiKey=?';
$st2 = $mysqli->prepare($sql2);
$st2->bind_param('s',$body['apikey']);
$st2->execute();
$res = $st2->get_result();
$res = $res->fetch_assoc();
$email=$res['email'];
$fname=$res['fname'];



$email_body="copy this to your browser $domain/backend/verify_delete.php?token=$token";
$alt_body="copy this to your browser $domain/backend/verify_delete.php?token=$token";
$subject="Request for account deletion";
send_mail($email,$fname,$subject,$email_body,$alt_body);
print json_encode(['message'=>"check your email for link confirmation"]);



?>