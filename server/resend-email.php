<?php

require_once './dbconnect.php';
require './globalContext.php';
require './phpmailer.php';

$method=$_SERVER['REQUEST_METHOD'];

if($method!= "GET") {
    header("HTTP/1.1 403 Forbidden");
    print json_encode(['errormesg'=>"Method $method not allowed here."]);
    exit;
}



if(!isset($_GET['type'])){
    header("HTTP/1.1 400 Bad Request");
    print json_encode(['errormesg'=>"type is required"]);
    exit;
}


$type=$_GET['type'];

if($type=="regenerate-token"){
    if(!isset($_GET['token'])){
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"token is required"]);
        exit;
    }
    if(!checkTokenExists($_GET['token'])){
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"Token does not exist."]);
        exit;
    }
    $old_token=$_GET['token']; 
    $sql5='SELECT email from users as u JOIN verification_tokens as vt on u.id=vt.userid where vt.token=?';
    $st5 = $mysqli->prepare($sql5);
    $st5->bind_param('s',$old_token);
    $st5->execute();
    $res5 = $st5->get_result();
    $r5 = $res5->fetch_assoc();
    $email=$r5['email']; 
}else{
    if(!isset($_GET['email'])){
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"email is required"]);
        exit;
    }
    $email=$_GET['email'];
    $sql4='SELECT count(*) as c FROM users WHERE email=?';
    $st4 = $mysqli->prepare($sql4);
    $st4->bind_param('s',$email);
    $st4->execute();
    $res4 = $st4->get_result();
    $r4 = $res4->fetch_all(MYSQLI_ASSOC);

    if($r4[0]['c']==0){
	    header("HTTP/1.1 400 Bad Request");
	    print json_encode(['errormesg'=>"You are not registered!"]);
	    exit;
}


}




$token=bin2hex(random_bytes(16));

$sql="SELECT created_at FROM verification_tokens as vt JOIN users as u on u.id=vt.userid where u.email=?";
    $st = $mysqli->prepare($sql);
    $st->bind_param('s',$email);
    $st->execute();
    $res = $st->get_result();
    $r = $res->fetch_assoc();
    if(mysqli_num_rows($res)>0){
        $created_at=$r['created_at'];
        if(strtotime($created_at) < strtotime("-2 minutes")) {
        $sql2 = 'call create_token(?,?)';
        $st2 = $mysqli->prepare($sql2);
        $st2->bind_param('ss',$email,$token);
	    $st2->execute();
        }else{
                header("HTTP/1.1 400 Bad Request");
                print json_encode(['errormesg'=>"You can resend email verification every 2 minutes"]);
                exit;
        }
    }else{
        $sql3 = 'call create_token(?,?)';
        $st3 = $mysqli->prepare($sql3);
        $st3->bind_param('ss',$email,$token);
	    $st3->execute();
    }

    $fname = substr($email, 0, strpos($email, '@'));
    $domain = $_SERVER['HTTP_HOST'];
    if($type=="email-verification" || $type == "regenerate-token"){
        $email_body="click <a href='$domain/verify-account.html?token=$token'>here</a> or paste this to your browser $domain/verify-account.html?token=$token";
        $alt_body="copy this to your browser $domain/verify-account.html?token=$token";
        $subject="WebKmeans Account confirmation";
    }elseif($type=="forgot-password"){
        $email_body="click <a href='$domain/password-reset.html?token=$token'>here</a> or paste this to your browser $domain/password-reset.html?token=$token";
        $alt_body="copy this to your browser $domain/password-reset.html?token=$token";
        $subject="Password reset";
    }elseif($type=="delete-user"){
        $email_body="click <a href='$domain/verify-account-deletion.html?token=$token'>here</a> or paste this to your browser $domain/verify-account-deletion.html?token=$token";
        $alt_body="copy this to your browser $domain/verify-account-deletion.html?token=$token";
        $subject="Request for account deletion";
    }else{
        exit;
    }

    send_mail($email,$fname,$subject,$email_body,$alt_body);

    if($type=="regenerate-token"){
        print json_encode(array(
            'message' => "email has been sent",
            'email' => $email
        ));
    }else{
        print json_encode(['message'=>"email has been sent"]);
    }
	


?>