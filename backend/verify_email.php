<?php

require_once 'dbconnect.php';

$sql2='UPDATE users 
JOIN verification_tokens ON users.id=verification_tokens.userid
SET users.verified=? where verification_tokens.token=?';
$st2 = $mysqli->prepare($sql2);
$n=1;
$st2->bind_param('is',$n,$_GET['token']);
$st2->execute();


?>