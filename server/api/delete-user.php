<?php
require_once '../dbconnect.php';
require '../phpmailer.php';
require '../globalContext.php';

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

if(!checkApiKeyExists($body['apikey'])){
    header("HTTP/1.1 401 Unauthorized");
    print json_encode(['errormesg'=>"This Apikey does not exist."]);
    exit;
}

$sql2 = 'SELECT email,fname FROM users WHERE apiKey=?';
$st2 = $mysqli->prepare($sql2);
$st2->bind_param('s',$body['apikey']);
$st2->execute();
$res = $st2->get_result();
$res = $res->fetch_assoc();
$email=$res['email'];
$fname=$res['fname'];

if(checkTokenByemail($email)){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"You can resend email verification every 2 minutes"]);
    exit;
}

$token=bin2hex(random_bytes(16));

$sql = 'call deleteUser(?,?)';
$st = $mysqli->prepare($sql);
$st->bind_param('ss',$body['apikey'],$token);
$st->execute();

$domain=getdomain();
<<<<<<< HEAD
$email_body="click <a href='$domain/verify-account-deletion.html?token=$token'>here</a> or paste this link to your browser $domain/verify-account-deletion.html?token=$token";
$alt_body="paste this link to your browser $domain/verify-account-deletion.html?token=$token";
=======
$email_body="copy this to your browser $domain/verify-account-deletion.html?token=$token";
$alt_body="copy this to your browser $domain/verify-account-deletion.html?token=$token";
>>>>>>> 549c676594f3b31b1fbceaece92a9e19c635fc8f
$subject="Request for account deletion";
send_mail($email,$fname,$subject,$email_body,$alt_body);
print json_encode(['message'=>"check your email for link confirmation"]);

?>