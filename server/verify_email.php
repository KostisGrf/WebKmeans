<?php

require_once 'dbconnect.php';
require 'globalContext.php';

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

$sql = 'call verifyAccount(?)';
$st = $mysqli->prepare($sql);
$st->bind_param('s',$_GET['token']);
$st->execute();

?>