<?php

require_once '../dbconnect.php';

$body = json_decode(file_get_contents("php://input"), true);

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
	print json_encode(['message'=>"user registered."]);
?>