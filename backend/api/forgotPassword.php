<?php

require_once '../dbconnect.php';
require '../config.php';
require '../phpmailer.php';

$method=$_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents("php://input"), true);

if(!isset($body['email'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"email is required"]);
    exit;
}

$token=bin2hex(random_bytes(16));

$sql = 'call forgotPassword(?,?)';
$st = $mysqli->prepare($sql);
$st->bind_param('ss',$body['email'],$token);
$st->execute();

$sql2 = 'SELECT fname FROM users WHERE email=?';
$st2 = $mysqli->prepare($sql2);
$st2->bind_param('s',$body['email']);
$st2->execute();
$res = $st2->get_result();
$res = $res->fetch_assoc();
$fname=$res['fname'];

$email_body="copy this to your browser $domain/www/password_reset.html?token=$token";
$alt_body="copy this to your browser $domain/www/password_reset.html?token=$token";
$subject="Password reset";
send_mail($body['email'],$fname,$subject,$email_body,$alt_body);
print json_encode(['message'=>"check your email for link confirmation"]);


?>