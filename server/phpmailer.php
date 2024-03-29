<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require __DIR__.'/../vendor/autoload.php';
// require '../../vendor/autoload.php';
require 'config.php';

//Create an instance; passing `true` enables exceptions

function send_mail($recipient,$r_name,$subject,$body,$altbody){

    require 'config.php';
$mail = new PHPMailer(true);



try {
    //Server settings
    $mail->SMTPDebug = 0;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp-mail.outlook.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->SMTPOptions=array('ssl'=>array(
        'verify_peer'=>false,
        'verify_peer_name'=>false,
        'allow_self_signed'=>true
    ));
    
    $mail->Username   = $username;                     //SMTP username
    $mail->Password   = $password;                               //SMTP password
    $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom($username, 'WebKmeans');
    $mail->addAddress($recipient, $r_name);     //Add a recipient
   
    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $body;
    $mail->AltBody = $altbody;

    $mail->send();

} catch (Exception $e) {
   //Server settings
   $mail->SMTPDebug = 0;                      //Enable verbose debug output
   $mail->isSMTP();                                            //Send using SMTP
   $mail->Host       = 'smtp-relay.sendinblue.com';                     //Set the SMTP server to send through
   $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
   $mail->SMTPOptions=array('ssl'=>array(
       'verify_peer'=>false,
       'verify_peer_name'=>false,
       'allow_self_signed'=>true
   ));
   
   $mail->Username   = $username;                     //SMTP username
   $mail->Password   = $password2;                               //SMTP password
   $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
   $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

   //Recipients
   $mail->setFrom("webkmeans@sendinblue.com", 'WebKmeans');
   $mail->addAddress($recipient, $r_name);     //Add a recipient
  
   //Content
   $mail->isHTML(true);                                  //Set email format to HTML
   $mail->Subject = $subject;
   $mail->Body    = $body;
   $mail->AltBody = $altbody;

   $mail->send();
}
}