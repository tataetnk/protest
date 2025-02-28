<?php
require 'vendor/autoload.php';  // โหลด PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// สร้างอ็อบเจ็กต์ PHPMailer
$mail = new PHPMailer(true);

try {
    // ตั้งค่าเซิร์ฟเวอร์ SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // ใช้ Gmail SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'sukanyajpy2509@gmail.com';  // อีเมลของคุณ
    $mail->Password = '0831438919';  // รหัสผ่านอีเมลของคุณ หรือ App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // ใช้ STARTTLS
    $mail->Port = 587;  // พอร์ตที่ Gmail ใช้ (587 สำหรับ STARTTLS)

    // กำหนดผู้รับและผู้ส่ง
    $mail->setFrom('sukanyajpy2509@gmail.com', 'Mailer');
    $mail->addAddress($email, 'User');  // เพิ่มผู้รับที่ส่งลิงก์

    // กำหนดเนื้อหาของอีเมล
    $mail->isHTML(true);
    $mail->Subject = 'รีเซ็ตรหัสผ่านของคุณ';
    $mail->Body    = "คลิกลิงก์นี้เพื่อรีเซ็ตรหัสผ่านของคุณ: $reset_link";

    // ส่งอีเมล
    $mail->send();
    $message = "ลิงก์รีเซ็ตรหัสผ่านถูกส่งไปที่อีเมลของคุณแล้ว.";
} catch (Exception $e) {
    $message = "ไม่สามารถส่งอีเมลได้. Error: {$mail->ErrorInfo}";
}
?>
