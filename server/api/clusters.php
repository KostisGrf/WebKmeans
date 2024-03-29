<?php
ini_set("display_errors", "1");
ini_set('memory_limit', '1024M');
require_once '../dbconnect.php';
require '../globalContext.php';
require_once '../../vendor/autoload.php';
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

if(!isset($body['dataset'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"dataset is required"]);
    exit;
}

if(!isset($body['dataset-type'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"dataset-type is required"]);
    exit;
}

if(!($body['dataset-type']=="public"||$body['dataset-type']=="personal")){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"dataset-type value can only be personal or public"]);
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

if(!checkApiKeyExists($body['apikey'])){
    header("HTTP/1.1 401 Unauthorized");
    print json_encode(['errormesg'=>"This Apikey does not exist."]);
    exit;
}

if($body['clusters']>100){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"The maximum number of clusters is 100"]);
    exit();
}

$dataset=basename($body['dataset']);
$path_parts = pathinfo($dataset);
$folder=$path_parts['filename'];

if($body['dataset-type']=='public'){
    if(!file_exists("../python/datasets/public_datasets/$folder/$dataset")){
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"dataset does not exist"]);
        exit();
    }
    $file_path="../python/datasets/public_datasets/$folder";
}else{
    $email=getEmail($body['apikey']);
    $identity=md5($email);
    if(!file_exists("../python/datasets/$identity/$folder/$dataset")){
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"dataset does not exist"]);
        exit();
    }
    $file_path="../python/datasets/$identity/$folder";
}

$columns=$body['columns'];
$colums_string=implode("," ,$columns);
$ext = pathinfo($body['dataset'], PATHINFO_EXTENSION);

if($ext=="csv"){
    $file=fopen("$file_path/$dataset",'r');
    $headers = fgetcsv($file, 1024, ',');
    $headers_=array();
    foreach($headers as $value){
    $value = preg_replace("/[^a-zA-Z0-9-_\.]+/", "", $value);
    array_push($headers_,$value);
}
    $filerow =0;
    
    while ($row = fgetcsv($file, 1024, ',')){
        $full_csv[]=array_combine($headers_,$row);
        $filerow++;
    }
    fclose($file);
}else{
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

if($body['clusters']>count($full_csv)){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"The number of clusters must be less than or equal to the number of rows of the dataset"]);
    exit();
}

for($i=0;$i<=count($full_csv);$i++){
    if($headers_[0]==0||$headers_[0]==1){
        unset($full_csv[$i][$headers_[0]]);
    }
}

if($headers_[0]==0||$headers_[0]==1){
    array_shift($headers_);
}


if((empty($columns))||!(array_intersect($columns, $headers_) === $columns)){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"column doesn't exist"]);
    exit();
}

$numerical_columns=[];
foreach($headers_ as $value_){
    ${$value_}=array_column($full_csv, "$value_");
    if ( count( ${$value_} ) === count( array_filter( ${$value_}, 'is_numeric' ) ) ) {
       array_push($numerical_columns,$value_);
    }
}

if(!(array_intersect($columns, $numerical_columns) === $columns)){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"columns must be numerical"]);
    exit();
}

$clusters=$body['clusters'];

file_put_contents("$file_path/".$folder . "_clusters_$clusters.csv", '');
$path="$file_path/$dataset";
$path_to_save="$file_path/" .$folder .  "_clusters_$clusters.csv";


echo shell_exec("python ../python/clusters_module.py $path $colums_string $clusters $ext $path_to_save  2>&1");
$file=fopen("$path_to_save",'r');
$headers = fgetcsv($file, 1024, ',');
$filerow =0;
while (($row = fgetcsv($file, 1024, ','))) {
    $csv[] = array_combine($headers, $row);
    $filerow++;
}
fclose($file);
for($i=0;$i<=count($csv);$i++){
    if($headers[0]==0||$headers[0]==1){
        unset($csv[$i][$headers[0]]);
    }
}


print json_encode(["items"=>$csv],JSON_UNESCAPED_UNICODE);
