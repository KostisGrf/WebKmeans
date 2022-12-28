<?php
require_once '../dbconnect.php';
require '../globalContext.php';

if(!isset($_GET['dataset'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"dataset is required"]);
    exit;
}

if(!isset($_GET['apikey'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"apikey is required"]);
    exit;
}

if(!isset($_GET['dataset-type'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"dataset-type is required"]);
    exit;
}

if(!($_GET['dataset-type']=="public"||$_GET['dataset-type']=="personal")){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"dataset-type value can only be personal or public"]);
    exit;
}

if(!checkApiKeyExists($_GET['apikey'])){
    header("HTTP/1.1 401 Unauthorized");
    print json_encode(['errormesg'=>"This Apikey does not exist."]);
    exit;
}

$dataset=basename($_GET['dataset']);
$path_parts = pathinfo($dataset);
$folder=$path_parts['filename'];
$folder = preg_replace('/_clusters_.*/', '', $folder);


if($_GET['dataset-type']=='public'){
    if(!file_exists("../python/datasets/public_datasets/$folder/$dataset")){
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"dataset does not exist"]);
        exit();
    }
    $file="../python/datasets/public_datasets/$folder/$dataset";
}else{
    $email=getEmail($_GET['apikey']);
    $identity=md5($email);
    if(!file_exists("../python/datasets/$identity/$folder/$dataset")){
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"dataset does not exist"]);
        exit();
    }
    $file="../python/datasets/$identity/$folder/$dataset";
}

    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header('Content-Disposition: filename="'.$dataset.'"');
    header("Content-Type:application/zip");
    header("Content-Transfer-Encoding:binary");
    readfile($file);
?>