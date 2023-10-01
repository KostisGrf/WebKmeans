<?php

use PhpOffice\PhpSpreadsheet\Cell\DataType;

require_once '../../../server/dbconnect.php';
require '../../../server/globalContext.php';
require_once '../../../vendor/autoload.php';
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


$clusters=$body['clusters'];

$dataset=basename($body['dataset']);
$path_parts = pathinfo($dataset);
$folder=$path_parts['filename'];
$ext = pathinfo($body['dataset'], PATHINFO_EXTENSION);

if($body['dataset-type']=='public'){
    if(!file_exists("../../../server/python/datasets/public_datasets/$folder/{$folder}_clusters_{$clusters}.{$ext}")){
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"dataset does not exist"]);
        exit();
    }
    $folder_path="../../../server/python/datasets/public_datasets/$folder/";
    $file_path="../../../server/python/datasets/public_datasets/$folder/{$folder}_clusters_{$clusters}.{$ext}";
}else{
    $email=getEmail($body['apikey']);
    $identity=md5($email);
    if(!file_exists("../../../server/python/datasets/$identity/$folder/{$folder}_clusters_{$clusters}.{$ext}")){
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"dataset does not exist"]);
        exit();
    }
    $folder_path="../../../server/python/datasets/$identity/$folder/";
    $file_path="../../../server/python/datasets/$identity/$folder/{$folder}_clusters_{$clusters}.{$ext}";
}


$columns=$body['columns'];
$colums_string=implode("," ,$columns);


if($ext=="csv"){
    $file=fopen("$file_path",'r');
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
    $spreadsheet = $reader->load("$file_path");
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

if((empty($columns))||!(array_intersect($columns, $headers_) === $columns)){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"column doesn't exist"]);
    exit();
}

$numerical_columns=[];
$categorical_columns=[];
foreach($headers_ as $value_){
    ${$value_}=array_column($full_csv, "$value_");
    if ( count( ${$value_} ) === count( array_filter( ${$value_}, 'is_numeric' ) ) ) {
       array_push($numerical_columns,$value_);
    }else{
        array_push($categorical_columns,$value_);
    }
}

$filteredNumericalColumns = array_intersect($numerical_columns, $columns);
$filteredCategoricalColumns = array_intersect($categorical_columns, $columns);



if(!empty($filteredNumericalColumns) && empty($filteredCategoricalColumns)){
    $dataType="numerical";
    $path_to_save="{$folder_path}{$folder}_clusters_{$clusters}.png";
    $py="parallelPlot_module";
}elseif(empty($filteredNumericalColumns)&&!empty($filteredCategoricalColumns)){
    $dataType="categorical";
    $path_to_save="{$folder_path}{$folder}_clusters_{$clusters}.html";
    $py="parallel_plot_plotly";
}else{
    $dataType="mixed";
    $path_to_save="{$folder_path}{$folder}_clusters_{$clusters}.html";
    $py="parallel_plot_plotly";
}

echo shell_exec("python ../../../server/python/{$py}.py $file_path $colums_string $clusters $ext $path_to_save  2>&1");

$dataset_file=($body['dataset-type']=='public')?"public_datasets":$identity;


$res_ext=($dataType=='numerical')?"png":"html";

$plot=(gethostname()=='nireas')?"https://webkmeans.iee.ihu.gr/server/python/datasets/$dataset_file/$folder/{$folder}_clusters_{$clusters}.{$res_ext}"
:"http://webkmeans.localhost/server/python/datasets/$dataset_file/$folder/{$folder}_clusters_{$clusters}.{$res_ext}";

print json_encode(["file"=>$plot],JSON_UNESCAPED_SLASHES);


?>