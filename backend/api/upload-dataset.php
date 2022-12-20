<?php
$method=$_SERVER['REQUEST_METHOD'];
require_once '../dbconnect.php';


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


if(isset($_FILES['dataset'])){
    $file_name=$_FILES['dataset']['name'];
    $path_parts = pathinfo("$file_name");
    $filename=$path_parts['filename'];
    $email=$_POST['email'];
    $identity=md5($email);
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



