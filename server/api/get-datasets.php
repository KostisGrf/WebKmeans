<?php
require_once '../dbconnect.php';
require '../globalContext.php';
$method=$_SERVER['REQUEST_METHOD'];

if($method!= "GET") {
    header("HTTP/1.1 403 Forbidden");
    print json_encode(['errormesg'=>"Method $method not allowed here."]);
    exit;
}

if(!isset($_GET['apikey'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"apikey is required"]);
    exit;
}

if(!checkApiKeyExists($_GET['apikey'])){
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

function getDatasets($folder){
    $dirs = glob($folder . '/*' , GLOB_ONLYDIR);

$directories=[];
foreach($dirs as $dir){
    array_push($directories,basename($dir));
}

$datasets=[];
foreach($directories as $directory){
    $dataset_folder="$folder/$directory";
    $files=glob($dataset_folder . '/*');
    foreach($files as $file){
        $path_parts = pathinfo($file);
        $filename=$path_parts['filename'];
        if($filename==$directory){
            array_push($datasets,basename($file));
        }
    }
}
    return $datasets;
}

$email=getEmail($_GET['apikey']);
$identity=md5($email);

$personal_folder="../python/datasets/$identity";
$public_folder="../python/datasets/public_datasets";

$personal_datasets=getDatasets($personal_folder);
$public_datasets=getDatasets($public_folder);
print json_encode(['personal_datasets'=>$personal_datasets,"public_datasets"=>$public_datasets]);
?>