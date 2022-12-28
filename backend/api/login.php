<?php
require_once '../dbconnect.php';

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

$email=$body['email'];
$passwd=$body['password'];

$get_user_by_email = "SELECT * FROM users WHERE email=?";
$st = $mysqli->prepare($get_user_by_email);
$st->bind_param("s",$email);
$st->execute();
$res = $st->get_result();
$res = $res->fetch_assoc();


if(!empty($res)){
    
    $sql='SELECT verified FROM users WHERE email=?';
    $st2 = $mysqli->prepare($sql);
    $st2->bind_param('s',$body['email']);
    $st2->execute();
    $res2 = $st2->get_result();
    $r = $res2->fetch_all(MYSQLI_ASSOC);


if(!$r[0]['verified']){
    header("HTTP/1.1 400 Bad Request");
	print json_encode(['errormesg'=>"You need to verify your email in order to login."]);
    exit;
}
    $check_password = password_verify($passwd, $res['password']);
}else{
        header("HTTP/1.1 400 Bad Request");
	    print json_encode(['errormesg'=>"Wrong username/password."]);
        exit;
}

if($check_password){
    $data=array("email"=>$res['email'],
    "fname"=>$res["fname"],
    "lname"=>$res["lname"],
    "apiKey"=>$res["apiKey"]);
    print json_encode($data, JSON_PRETTY_PRINT);
}else{
    header("HTTP/1.1 400 Bad Request");
	print json_encode(['errormesg'=>"Wrong username/password."]);
}

?>