<?php

require_once '../dbconnect.php';
$method=$_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents("php://input"), true);

if(!isset($body['apikey'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"apikey is required"]);
    exit;
}

if(!isset($body['dataset'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"dataset is required"]);
    exit;
}

$sql2 = 'SELECT email FROM users WHERE apiKey=?';
$st2 = $mysqli->prepare($sql2);
$st2->bind_param('s',$body['apikey']);
$st2->execute();
$res = $st2->get_result();
$res = $res->fetch_assoc();
$email=$res['email'];
$user_file=md5($email);

$path_parts = pathinfo($body['dataset']);
$folder=$path_parts['filename'];


$dataset=$body['dataset'];
$file=fopen("../python/datasets/$user_file/$folder/$dataset",'r');
$headers = fgetcsv($file, 1024, ',');
$filerow =0;
while (($row = fgetcsv($file, 1024, ','))&&($filerow<=99)) {
    $csv[] = array_combine($headers, $row);
    $filerow++;

}
fclose($file);

$numerical_columns=[];
foreach($headers as $value){
    ${$value}=array_column($csv, "$value");
    if ( count( ${$value} ) === count( array_filter( ${$value}, 'is_numeric' ) ) ) {
       array_push($numerical_columns,$value);
    }
}

print json_encode(["items"=>$csv,"numerical_columns"=>$numerical_columns],JSON_UNESCAPED_UNICODE);


?>