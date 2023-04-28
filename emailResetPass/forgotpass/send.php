<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
// require 'vendor/autoload.php';
// require 'vendor/phpmailer/src/Exception.php';
// require 'vendor/phpmailer/src/PHPMailer.php';
// require 'vendor/phpmailer/src/SMTP.php';
require 'vendor/autoload.php';
$mail = new PHPMailer(true);

try {
    // Set up SMTP
    $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'verify.your.mail.01@gmail.com';
    $mail->Password = 'fgkncgkpranrxuso';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Set up email content
    $mail->setFrom('verify.your.mail.01@gmail.com', 'Your Name');
    $mail->addAddress('bearp1024@gmail.com', 'Recipient Name');
    $mail->Subject = 'Test email';
    // $mail->AddEmbeddedImage('header.png', 'logo_2u');
    // Embed image and add to email body
    $mail->isHTML(true);
    $image_url = 'https://anonsharing.com/cache/plugins/filepreviewer/11342/77b371f8bb2040bee0f43600fd006836603a4c03c402a9d355debbe561096482/1100x800_cropped.jpg';
    $mail->Body = '
    <html>
    <body>
        <p>This is a test email sent via PHPMailer.</p>
        <div style="width: 100%; height: 250px; width:80%; object-fit: cover; background-image: url(' . $image_url . '); background-repeat: no-repeat; background-size: cover;"></div>
    </body>
</html>
    ';
    // Send the email
    $mail->send();
    echo 'Email sent successfully.';
} catch (Exception $e) {
    echo 'Email could not be sent. Error message: ', $mail->ErrorInfo;
}