<?php

require_once '../dbconnect.php';
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


$sql2 = 'SELECT grandPublicDataset FROM users WHERE apiKey=?';
$st2 = $mysqli->prepare($sql2);
$st2->bind_param('s',$_POST['apikey']);
$st2->execute();
$res = $st2->get_result();
$res = $res->fetch_assoc();
$grandpublicdataset=$res['grandPublicDataset'];




if($grandpublicdataset==0){
    header("HTTP/1.1 403 Forbidden");
    print json_encode(['errormesg'=>"You dont have the permission to upload public dataset."]);
    exit;
}



if(isset($_FILES['dataset'])){
    $file_name=$_FILES['dataset']['name'];
    $path_parts = pathinfo("$file_name");
    $filename=$path_parts['filename'];
    mkdir("../python/datasets/public_datasets/$filename");
    move_uploaded_file($_FILES['dataset']['tmp_name'],"../python/datasets/public_datasets/$filename/" . $file_name);
    print json_encode(['message'=>"file uploaded."]);
}
?>