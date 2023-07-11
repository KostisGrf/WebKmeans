<?php
header("Access-Control-Allow-Origin: *");
ini_set('upload_max_filesize', '1M');

$method=$_SERVER['REQUEST_METHOD'];
require_once '../dbconnect.php';
require '../globalContext.php';

if($method!= "POST") {
    header("HTTP/1.1 403 Forbidden");
    print json_encode(['errormesg'=>"Method $method not allowed here."]);
    exit;
}

if(!isset($_POST['apikey'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"apikey is required"]);
    exit;
}

if(!isset($_POST['dataset-type'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"dataset-type is required"]);
    exit;
}

if(!($_POST['dataset-type']=="public"||$_POST['dataset-type']=="personal")){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"dataset-type value can only be personal or public"]);
    exit;
}

if(!checkApiKeyExists($_POST['apikey'])){
    header("HTTP/1.1 401 Unauthorized");
    print json_encode(['errormesg'=>"This Apikey does not exist."]);
    exit;
}

if(isset($_FILES['dataset'])){

    $allowed = array('csv','xlsx','xls');
    $file_name=$_FILES['dataset']['name'];
    $ext = pathinfo($file_name, PATHINFO_EXTENSION);
    if (!in_array($ext, $allowed)) {
        header("HTTP/1.1 415 Unsupported Media Type");
        print json_encode(['errormesg'=>"Only csv and xlsx are valid file types."]);
        exit;
    }

    $path_parts = pathinfo("$file_name");
    $filename=$path_parts['filename'];
    if($_POST['dataset-type']=="public"){
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
        $folder="../python/datasets/public_datasets/$filename";
        if(!file_exists($folder)){
            mkdir("$folder");
            move_uploaded_file($_FILES['dataset']['tmp_name'],"$folder/" . $file_name);
        }else{
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg'=>"This dataset already exists"]);
            exit;
        }
    }else{
        $email=getEmail($_POST['apikey']);
        $identity=md5($email);
        $folder="../python/datasets/$identity/$filename";

        if(!file_exists($folder)){
            mkdir("$folder");
            move_uploaded_file($_FILES['dataset']['tmp_name'],"$folder/" . $file_name);
        }else{
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg'=>"This dataset already exists"]);
            exit;
        }

    }
    print json_encode(['message'=>"file uploaded."]);
}

?>



