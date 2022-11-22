<?php

require_once '../dbconnect.php';

$body = json_decode(file_get_contents("php://input"), true);
$dataset=$body['dataset'];
$email=$body['email'];
$identity=md5($email);

$path_parts = pathinfo($body['dataset']);
$folder=$path_parts['filename'];



$columns=['unleaded95','unleaded100'];
$colums_string=implode("," ,$columns);
$path="../python/datasets/$identity/$folder/$dataset";


echo shell_exec("python ../python/elbow_module.py $path $colums_string 2>&1");

?>
