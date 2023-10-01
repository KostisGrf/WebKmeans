<?php
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

if(!isset($body['apikey'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"apikey is required"]);
    exit;
}

$changed_cols=[];
$changed_values=[];

if(isset($body['fname'])){
    array_push($changed_cols,"fname");
    array_push($changed_values,$body['fname']);
}

if(isset($body['lname'])){
    array_push($changed_cols,"lname");
    array_push($changed_values,$body['lname']);
}

if(isset($body['password'])){
    array_push($changed_cols,"password");
    array_push($changed_values,password_hash($body['password'], PASSWORD_BCRYPT));
}

if(count($changed_cols)==1){
    $sql="UPDATE users SET $changed_cols[0] = '$changed_values[0]' WHERE apiKey=?;";
}elseif(count($changed_cols)==2){
    $sql="UPDATE users SET $changed_cols[0] = '$changed_values[0]', $changed_cols[1] = '$changed_values[1]' WHERE apiKey=?;";
}elseif(count($changed_cols)==3){
    $sql="UPDATE users SET $changed_cols[0] = '$changed_values[0]', $changed_cols[1] = '$changed_values[1]',$changed_cols[2] = '$changed_values[2]' WHERE apiKey=?;";
}
else{
    header("HTTP/1.1 400 Bad Request");
}

$apikey=$body['apikey'];

$st = $mysqli->prepare($sql);
$st->bind_param('s',$apikey);
$st->execute();

?>