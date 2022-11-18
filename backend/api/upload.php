<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');




if(isset($_FILES['sample_dataset'])){
    $file_name=$_FILES['sample_dataset']['name'];
    move_uploaded_file($_FILES['sample_dataset']['tmp_name'],'../python/datasets/' . $file_name);
    
    
    print json_encode(['message'=>"file uploaded."]);
}



