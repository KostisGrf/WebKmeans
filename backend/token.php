<?php

date_default_timezone_set('Europe/Athens');

function checkTokenExpired($token){
    require  'dbconnect.php';

    $sql="SELECT created_at FROM verification_tokens where token=?";
    $st = $mysqli->prepare($sql);
    $st->bind_param('s',$token);
    $st->execute();
    $res = $st->get_result();
    $res = $res->fetch_assoc();
    $created_at=$res['created_at'];

    if(strtotime($created_at) < strtotime("-20 minutes")) {
        
        $sql2="DELETE FROM verification_tokens where token=?";
        $st2 = $mysqli->prepare($sql2);
        $st2->bind_param('s',$token);
        $st2->execute();
        return true;
    }else{
        return false;
    }

}

function checkTokenExists($token){
require  'dbconnect.php';
$sql='SELECT count(*) as count FROM verification_tokens WHERE token=?';
$st = $mysqli->prepare($sql);
$st->bind_param('s',$token);
$st->execute();
$res = $st->get_result();
$res = $res->fetch_assoc();

	
if($res['count']>0){
    return true;
}else{
    return false;
}
}

function checkTokenByemail($email){
    require  'dbconnect.php';

    $sql="SELECT created_at FROM verification_tokens as vt JOIN users as u on u.id=vt.userid where u.email=?";
    $st = $mysqli->prepare($sql);
    $st->bind_param('s',$email);
    $st->execute();
    $res = $st->get_result();
    $r = $res->fetch_assoc();
    if(mysqli_num_rows($res)>0){
        $created_at=$r['created_at'];
        if(strtotime($created_at) < strtotime("-2 minutes")) {
        
            $sql2="DELETE verification_tokens FROM verification_tokens  
            JOIN users ON users.id=verification_tokens.userid
            WHERE users.email=?";
            $st2 = $mysqli->prepare($sql2);
            $st2->bind_param('s',$email);
            $st2->execute();
            return false;
        }else{
            return true;
        }
    }
    
}

?>