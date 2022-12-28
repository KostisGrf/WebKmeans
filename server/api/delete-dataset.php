<?php

require_once '../dbconnect.php';
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

if(!isset($body['dataset'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"dataset is required"]);
    exit;
}

if(!isset($body['dataset-type'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"dataset-type is required"]);
    exit;
}

if(!($body['dataset-type']=="public"||$body['dataset-type']=="personal")){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"dataset-type value can only be personal or public"]);
    exit;
}

if(!checkApiKeyExists($body['apikey'])){
    header("HTTP/1.1 401 Unauthorized");
    print json_encode(['errormesg'=>"This Apikey does not exist."]);
    exit;
}

$sql2 = 'SELECT email,grandPublicDataset FROM users WHERE apiKey=?';
$st2 = $mysqli->prepare($sql2);
$st2->bind_param('s',$body['apikey']);
$st2->execute();
$res = $st2->get_result();
$res = $res->fetch_assoc();
$email=$res['email'];
$grandPublicDataset=$res['grandPublicDataset'];

$dataset=$body['dataset'];
$path_parts = pathinfo($body['dataset']);
$file_name=$path_parts['filename'];

if($body['dataset-type']=="public"){
    if($grandPublicDataset==1){
        $folder="../python/datasets/public_datasets/$file_name";
        if(file_exists($folder)){
            rrmdir($folder);
            print json_encode(['message'=>"dataset deleted"]);
        }else{
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg'=>"dataset does not exist"]);
            exit;
        }
    }else{
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"You dont have the permission to delete public dataset."]);
        exit;
    }
   
}else{
    $identity=md5($email);
    $folder="../python/datasets/$identity/$file_name";
    if(file_exists($folder)){
        rrmdir($folder);
        print json_encode(['message'=>"dataset deleted"]);
    }else{
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"dataset does not exist"]);
        exit;
    }
}

?>