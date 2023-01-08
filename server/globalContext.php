<?php

date_default_timezone_set('Europe/Athens');


function checkTokenExpired($token){
    global $mysqli;

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
global $mysqli;
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
    global $mysqli;

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

function getEmail($apikey){
        global $mysqli;
        $sql = 'SELECT email FROM users WHERE apiKey=?';
        $st = $mysqli->prepare($sql);
        $st->bind_param('s',$apikey);
        $st->execute();
        $res = $st->get_result();
        $res = $res->fetch_assoc();
        $email=$res['email'];
        return $email;
}

function checkApiKeyExists($apikey){
    global $mysqli;
    $sql='SELECT count(*) as count FROM users WHERE apiKey=?';
    $st = $mysqli->prepare($sql);
    $st->bind_param('s',$apikey);
    $st->execute();
    $res = $st->get_result();
    $res = $res->fetch_assoc();

    return $res['count']>0;
}

function rrmdir($directory)
{
    array_map(fn (string $file) => is_dir($file) ? rrmdir($file) : unlink($file), glob($directory . '/' . '*'));
    return rmdir($directory);
}

function getdomain(){
    if(gethostname()=='nireas'){
       $domain_="https://nireas.iee.ihu.gr/webkmeans";
    }else{
        $domain_="webkmeans.localhost";
    }
    return $domain_;
}




?>