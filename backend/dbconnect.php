<?php
$host='127.0.0.1';
$db = 'web_kmeans';
$user="localhost";
$pass="1234";

// $user=$DB_USER;
// $pass=$DB_PASS;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if(gethostname()=='users.iee.ihu.gr') {
	$mysqli = new mysqli($host, $user, $pass, $db,null,'/home/student/it/2018/it185174/mysql/run/mysql.sock');
} else {
		$pass=null;
        $mysqli = new mysqli($host, $user, $pass, $db);
}

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . 
    $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
?>