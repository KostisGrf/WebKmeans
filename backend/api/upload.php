<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

    // $body=json_decode(file_get_contents("php://input"), true);
// print json_encode(['message'=>"file uploaded."]);

// print json_encode($body);
// print json_encode($_POST);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);



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



