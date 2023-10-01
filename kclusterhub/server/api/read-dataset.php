<?php
require_once '../../../server/dbconnect.php';
require '../../../server/globalContext.php';
require_once '../../../vendor/autoload.php';
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

if(!isset($_GET['dataset'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"dataset is required"]);
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

if(!isset($_GET['algorithm'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"algorithm is required"]);
    exit;
}

if(!($_GET['algorithm']=="auto"||$_GET['algorithm']=="k-means"||$_GET['algorithm']=="k-modes"||$_GET['algorithm']=="k-prototypes")){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>" The algorithm parameter must be set to one of the following values:auto, k-means, k-modes or k-prototypes."]);
    exit;
}



if(!checkApiKeyExists($_GET['apikey'])){
    header("HTTP/1.1 401 Unauthorized");
    print json_encode(['errormesg'=>"This Apikey does not exist."]);
    exit;
}

$path_parts = pathinfo($_GET['dataset']);
$folder=$path_parts['filename'];
$folder = preg_replace('/_clusters_.*/', '', $folder);
$ext = pathinfo($_GET['dataset'], PATHINFO_EXTENSION);
$dataset=basename($_GET['dataset']);

if($_GET['dataset-type']=='public'){
    if(!file_exists("../../../server/python/datasets/public_datasets/$folder/$dataset")){
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"dataset does not exist"]);
        exit();
    }
    $file_path="../../../server/python/datasets/public_datasets/$folder";
}else{
    $email=getEmail($_GET['apikey']);
    $identity=md5($email);
    if(!file_exists("../../../server/python/datasets/$identity/$folder/$dataset")){
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"dataset does not exist"]);
        exit();
    }
    $file_path="../../../server/python/datasets/$identity/$folder";
}

if($ext=="csv"){
    $file=fopen("$file_path/$dataset",'r');
    if ($file) {
        $firstLine = fgets($file); // Read the first line of the file
    
        // Try to detect the delimiter by analyzing the first line
        $possibleDelimiters = array(',', ';', "\t");
        $detectedDelimiter = null;
    
        foreach ($possibleDelimiters as $delimiter) {
            $fieldCount = count(str_getcsv($firstLine, $delimiter));
            if ($fieldCount > 1) {
                $detectedDelimiter = $delimiter;
                break;
            }
        }}
if(!$detectedDelimiter){
    $detectedDelimiter=',';
}
    fclose($file);
    $file=fopen("$file_path/$dataset",'r');        
    $headers = fgetcsv($file, 1024, $detectedDelimiter);
    $headers_=array();
    foreach($headers as $value){
    $value = preg_replace("/[^a-zA-Z0-9-_\.]+/", "", $value);
    array_push($headers_,$value);
}
    $filerow =0;
    
    while ($row = fgetcsv($file, 1024, $detectedDelimiter)){
        $full_csv[]=array_combine($headers_,$row);
        $filerow++;
    }
    fclose($file);

}
else{
    $reader = $ext=="xls" ? new \PhpOffice\PhpSpreadsheet\Reader\Xls()
                          : new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load("$file_path/$dataset");
    $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
    $data = $sheet->toArray();
    $headers=$data[0];
    $headers_=array();
    foreach($headers as $value){
    $value = preg_replace("/[^a-zA-Z0-9-_\.]+/", "", $value);
    array_push($headers_,$value);
}
    for($i=1;$i<count($data);$i++){
        $full_csv[]=array_combine($headers_,$data[$i]);
    }
}

for($i=0;$i<=count($full_csv);$i++){
    if($headers_[0]==0||$headers_[0]==1){
        unset($full_csv[$i][$headers_[0]]);
    }
}

if($headers_[0]==0||$headers_[0]==1){
    array_shift($headers_);
}


$numerical_columns=[];
$categorical_columns=[];
$available_columns=[];
foreach($headers_ as $value_){
    ${$value_}=array_column($full_csv, $value_);
    if ( count( ${$value_} ) === count( array_filter( ${$value_}, 'is_numeric' ) ) ) {
       array_push($numerical_columns,$value_);
    }else{
        array_push($categorical_columns,$value_);
    }
}

if($_GET['algorithm']=="k-means"){
    $available_columns=$numerical_columns;
}elseif($_GET['algorithm']=="k-modes"){
    $available_columns=$categorical_columns;
}else{
    $available_columns=$headers_;
}

print json_encode(["items"=>$full_csv,"available_columns"=>$available_columns,"numerical_columns"=>$numerical_columns,"categorical_columns"=>$categorical_columns],JSON_UNESCAPED_UNICODE);

?>