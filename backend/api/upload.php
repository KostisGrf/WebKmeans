<?php
$method=$_SERVER['REQUEST_METHOD'];


if($method!= "POST") {
    header("HTTP/1.1 403 Forbidden");
    print json_encode(['errormesg'=>"Method $method not allowed here."]);
    exit;
}

if(isset($_FILES['dataset'])){
    $file_name=$_FILES['dataset']['name'];
    $path_parts = pathinfo("$file_name");
    $filename=$path_parts['filename'];
    $email=$_POST['email'];
    $identity=md5($email);
    mkdir("../python/datasets/$identity/$filename");
    move_uploaded_file($_FILES['dataset']['tmp_name'],"../python/datasets/$identity/$filename/" . $file_name);
    print json_encode(['message'=>"file uploaded."]);
}

?>



