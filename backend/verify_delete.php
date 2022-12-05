<?php

require_once 'dbconnect.php';

$sql2 = 'SELECT email FROM users as u JOIN verification_tokens as vt on u.id=vt.userid WHERE vt.token=?';
$st2 = $mysqli->prepare($sql2);
$st2->bind_param('s',$_GET['token']);
$st2->execute();
$res = $st2->get_result();
$res = $res->fetch_assoc();
$email=$res['email'];
$file_name=md5($email);



function rrmdir($directory)
{
    array_map(fn (string $file) => is_dir($file) ? rrmdir($file) : unlink($file), glob($directory . '/' . '*'));
    return rmdir($directory);
}

rrmdir("python/datasets/$file_name");

$sql = 'call verifyDelete(?)';
$st = $mysqli->prepare($sql);
$st->bind_param('s',$_GET['token']);
$st->execute();



?>