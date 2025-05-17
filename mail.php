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
        $mail->isHTML(true);
        $mail->Subject = 'Your Verification';

        // XSS protection
        $safeCode  = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');

        $mail->Body = "
        <html>
        <head>
            <style>
            .container {
                font-family: Arial, sans-serif;
                background-color: #f4f7fc;
                padding: 30px;
                border-radius: 10px;
                max-width: 600px;
                margin: auto;
                border: 1px solid #ddd;
            }
            .code-box {
                background-color: #ffffff;
                padding: 15px 25px;
                border: 2px dashed #4CAF50;
                font-size: 24px;
                font-weight: bold;
                color: #4CAF50;
                text-align: center;
                border-radius: 8px;
                margin: 20px 0;
            }
            .footer {
                font-size: 12px;
                color: #999999;
                margin-top: 30px;
                text-align: center;
            }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Hello,</h2>
                <p>Please use the verification code below to complete your registration:</p>
                <div class='code-box'>{$safeCode}</div>
                <p>This code is valid for <strong>10 minutes</strong>.</p>
                <p>If you did not request this, please ignore this email.</p>
                <div class='footer'>This is an automated message, please do not reply.</div>
            </div>
        </body>
        </html>
        ";

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
