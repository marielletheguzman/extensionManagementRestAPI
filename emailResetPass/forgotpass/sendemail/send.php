<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
// require 'vendor/autoload.php';
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require '../vendor/autoload.php';

// if(isset($_POST['send'])){
//     $mail = new PHPMailer(true);

//     $mail->isSMTP();
//     $mail->Host = 'stmp.gmail.com';
//     $mail->SMTPAuth = true;
//     $mail->Username = 'verify.your.mail.01@gmail.com';
//     $mail->Password = 'fxhkooojgzilbzzt';
//     $mail->SMTPSecure = 'tls';
//     $mail->Port = 587;

//     $mail->setFrom('verify.your.mail.01@gmail.com');
//     $mail->address($_POST['email']);

//     $mail->isHTML(true);
//     $mail->Subject = $_POST['subject'];
//     $mail->Body = $_POST['message'];

//     $mail->send();
//     echo
//     "<script>
//     alert('Sent Successfully!')
//     document.location.href = 'index.php'
//     </script>
    
//     ";

// }
$mail = new PHPMailer(true);
try {
  //Server settings
  $mail->isSMTP();                                            //Send using SMTP
  $mail->Host        = "smtp.gmail.com";                     //Set the SMTP server to send through
  $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
  $mail->Username   = "verify.your.mail.01@gmail.com";                 //SMTP username
  $mail->Password   = 'fgkncgkpranrxuso';                               //SMTP password
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
  $mail->Port       = 587;                               //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

  //Recipients
  $mail->setFrom('verify.your.mail.01@gmail.com', 'Mailer');
  $mail->addAddress('verify.your.mail.01@gmail.com', 'Joe User');     //Add a recipient
  $mail->addAddress('verify.your.mail.01@gmail.com');               //Name is optional
  $mail->addReplyTo('verify.your.mail.01@gmail.com', 'Information');
  // $mail->addCC('cc@example.com');
  // $mail->addBCC('bcc@example.com');

  //Attachments
  // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
  // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

  //Content
  $mail->isHTML(true);                                  //Set email format to HTML
  $mail->Subject = 'Here is the subject';
  $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
  $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

  $mail->send();
  echo 'Message has been sent';
} catch (Exception $e) {
  echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}


// if(isset($_POST['send'])){
//     $mail = new PHPMailer();
//     $mail->IsSMTP();
  
//     $mail->SMTPDebug  = 0;  
//     $mail->SMTPAuth   = TRUE;
//     $mail->SMTPSecure = "tls";
//     $mail->Port       = 587;
//     $mail->Host       = "smtp.gmail.com";
//     //$mail->Host       = "smtp.mail.yahoo.com";
//     $mail->Username   = "verify.your.mail.01@gmail.com";
//     $mail->Password   = "fgkncgkpranrxuso";
  
    
//     $mail->IsHTML(true);
//     $mail->addAddress($_POST['email']);
//     $mail->setFrom('verify.your.mail.01@gmail.com');
//     $mail->Subject = $_POST['subject'];
//     $mail->Body = $_POST['message'];
  
//     $mail->send();
//     echo
//     "<script>
//     alert('Sent Successfully!')
//     document.location.href = 'index.php'
//     </script>
    
//     ";
  
//   }



