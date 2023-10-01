<?php

require_once '../../server/dbconnect.php';
require '../../server/globalContext.php';

if(!isset($_GET['token'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"token is required"]);
    exit;
}

if(!checkTokenExists($_GET['token'])){
    header("HTTP/1.1 400 Bad Request");
	print json_encode(['errormesg'=>"Token does not exist."]);
	exit;
}


if(checkTokenExpired($_GET['token'])){
    header("HTTP/1.1 401 Unauthorized ");
    print json_encode(['errormesg'=>"token has expired"]);
    exit;
}

$sql2 = 'SELECT email FROM users as u JOIN verification_tokens as vt on u.id=vt.userid WHERE vt.token=?';
$st2 = $mysqli->prepare($sql2);
$st2->bind_param('s',$_GET['token']);
$st2->execute();
$res = $st2->get_result();
$res = $res->fetch_assoc();
$email=$res['email'];
$file_name=md5($email);

rrmdir("../../server/python/datasets/$file_name");

$sql = 'call verifyDelete(?)';
$st = $mysqli->prepare($sql);
$st->bind_param('s',$_GET['token']);
$st->execute();



?>