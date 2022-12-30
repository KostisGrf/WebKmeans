<?php
$host='127.0.0.1';
$db = 'web_kmeans';
$user="root";
$pass="1234";
require_once "config.php";



mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if(gethostname()=='nireas') {
	$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
} else {
        $mysqli = new mysqli($host, $user, $pass, $db);
}

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . 
    $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
?>