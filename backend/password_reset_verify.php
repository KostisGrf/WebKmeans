<?php
require_once 'dbconnect.php';

$body = json_decode(file_get_contents("php://input"), true);

$hashed_passwd= password_hash($body['password'], PASSWORD_BCRYPT);


$sql = 'call update_password(?,?)';
$st = $mysqli->prepare($sql);
$st->bind_param('ss',$hashed_passwd,$body['token']);
$st->execute();

print json_encode(["message"=>"New password has been saved"]);
?>