<?php
require_once '../../../server/dbconnect.php';
require '../../../server/config.php';
require '../../../server/phpmailer.php';
require '../../../server/globalContext.php';

$method=$_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents("php://input"), true);

if(!isset($body['email'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"email is required"]);
    exit;
}

if(checkTokenByemail($body['email'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"You can resend email verification every 2 minutes"]);
    exit;
}

$token=bin2hex(random_bytes(16));

$email=$body['email'];
$sql3='SELECT count(*) as c FROM users WHERE email=?';
$st3 = $mysqli->prepare($sql3);
$st3->bind_param('s',$email);
$st3->execute();
$res3 = $st3->get_result();
$r3 = $res3->fetch_all(MYSQLI_ASSOC);

if($r3[0]['c']==0){
	header("HTTP/1.1 400 Bad Request");
	print json_encode(['errormesg'=>"You are not registered!"]);
	exit;
}

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

$domain = $_SERVER['HTTP_HOST'];
$email_body="click <a href='$domain/password-reset.html?token=$token'>here</a> or paste this link to your browser $domain/password-reset.html?token=$token";
$alt_body="paste this link to your browser $domain/password-reset.html?token=$token";
$subject="Password reset";
send_mail($body['email'],$fname,$subject,$email_body,$alt_body);
print json_encode(['message'=>"check your email for link confirmation"]);
?>