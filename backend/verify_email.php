<?php

require_once 'dbconnect.php';

$sql = 'call verifyAccount(?)';
$st = $mysqli->prepare($sql);
$st->bind_param('s',$_GET['token']);
$st->execute();

?>