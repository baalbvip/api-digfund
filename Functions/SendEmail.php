<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require('./vendor/autoload.php');

// Crear una instancia de PHPMailer
$mail = new PHPMailer(true);

function sendEmail($affair, $body, $to)
{
    global $mail;
    try {
        // Configuración del servidor SMTP
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Cambia a SMTP::DEBUG_SERVER para ver mensajes de depuración
        $mail->isSMTP();
        $mail->Host = 'mail.dig-fund.com'; // Cambia esto al servidor SMTP que desees utilizar
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@dig-fund.com';
        $mail->Password = '4p61q1L5j$l2';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Usa PHPMailer::ENCRYPTION_STARTTLS si es necesario
        $mail->Port = 465; // Cambia al puerto SMTP correcto

        // Destinatario y remitente
        $mail->setFrom('noreply@dig-fund.com', '');
        $mail->addAddress($to, 'Destinatario');

        // Contenido del correo electrónico
        $mail->isHTML(true);
        $mail->Subject = $affair;
        $mail->Body = $body;

        // Enviar el correo electrónico
        $mail->send();
    } catch (Exception $e) {
    }
}
