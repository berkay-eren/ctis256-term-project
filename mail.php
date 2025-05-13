<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/Exception.php';
require './PHPMailer/src/SMTP.php';

function sendVerificationCode($toEmail, $code) {
    $mail = new PHPMailer(true);
    try {

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->Username = '256termproject@gmail.com';
        $mail->Password = 'sdpj tyxo zbbd lzaj';

        $mail->setFrom('256termproject@gmail.com', 'Verification System');
        $mail->addAddress($toEmail);
        $mail->Subject = 'Verification Code';
        $mail->Body = "Your verification code is: $code";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
