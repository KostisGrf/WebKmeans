<?php
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
// header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
error_reporting(0);

require_once '../../../server/dbconnect.php';
require '../../../server/globalContext.php';
require '../../../server/phpmailer.php';
require '../../../server/config.php';

$method=$_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents("php://input"), true);

if($method!= "POST") {
    header("HTTP/1.1 403 Forbidden");
    print json_encode(['errormesg'=>"Method $method not allowed here."]);
    exit;
}

if(!isset($body['email'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"email is required"]);
    exit;
}

if(!isset($body['password'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"password is required"]);
    exit;
}

if(!isset($body['fname'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"fname is required"]);
    exit;
}

if(!isset($body['lname'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"lname is required"]);
    exit;
}

$email=$body['email'];
$hashed_passwd= password_hash($body['password'], PASSWORD_BCRYPT);
$fname=$body['fname'];
$lname=$body['lname'];
$api_key=bin2hex(random_bytes(16));
$token=bin2hex(random_bytes(16));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"invalid email."]);
        exit;
}

$sql2='SELECT count(*) as c FROM users WHERE email=?';
$st2 = $mysqli->prepare($sql2);
$st2->bind_param('s',$email);
$st2->execute();
$res2 = $st2->get_result();
$r2 = $res2->fetch_all(MYSQLI_ASSOC);
	
if($r2[0]['c']>0){
	header("HTTP/1.1 400 Bad Request");
	print json_encode(['errormesg'=>"You are already registered."]);
	exit;
}

    $sql = 'call registerUser(?,?,?,?,?,?)';
    $st = $mysqli->prepare($sql);
    $st->bind_param('ssssss',$email,$hashed_passwd,$fname,$lname,$api_key,$token);
	$st->execute();
	$identity=md5($email);
	mkdir("../python/datasets/$identity");

    $domain = $_SERVER['HTTP_HOST'];
    $email_body="click <a href='$domain/verify-account.html?token=$token'>here</a> or paste this to your browser $domain/verify-account.html?token=$token";
    $alt_body="paste this to your browser $domain/verify-account.html?token=$token";
    $subject="K-Cluster Hub Account confirmation";
    send_mail($email,$fname,$subject,$email_body,$alt_body);
	print json_encode(['message'=>"user registered."]);

?>