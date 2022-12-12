<?php

$method=$_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents("php://input"), true);

if($method!= "POST") {
    header("HTTP/1.1 403 Forbidden");
    print json_encode(['errormesg'=>"Method $method not allowed here."]);
    exit;
}

if(!isset($body['dataset'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"dataset is required"]);
    exit;
}

if(!isset($body['email'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"email is required"]);
    exit;
}

if(!isset($body['clusters'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"clusters field is required"]);
    exit;
}

if(!isset($body['columns'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"columns field is required"]);
    exit;
}

$dataset=$body['dataset'];
$email=$body['email'];
$identity=md5($email);
$clusters=$body['clusters'];

$path_parts = pathinfo($body['dataset']);
$folder=$path_parts['filename'];



$columns=['unleaded95','unleaded100'];
$colums_string=implode("," ,$columns);
$path="../python/datasets/$identity/$folder/$dataset";


$output=shell_exec("python ../python/elbow_module.py $path $colums_string $clusters  2>&1");
echo ($output);

?>
