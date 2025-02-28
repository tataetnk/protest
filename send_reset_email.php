<?php
require 'connect.php'; // เชื่อมต่อฐานข้อมูล
require 'vendor/autoload.php'; // โหลด PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Bangkok'); // ตั้งค่าโซนเวลา

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // ตรวจสอบว่าอีเมลมีอยู่ในระบบหรือไม่
    $stmt = $pdo->prepare("SELECT staff_id FROM personnel WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // สร้าง token และวันหมดอายุ
        $token = bin2hex(random_bytes(32)); // สร้างโทเค็นสุ่ม
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // หมดอายุใน 1 ชั่วโมง

        // บันทึกโทเค็นลงในฐานข้อมูล
        $update_stmt = $pdo->prepare("UPDATE personnel SET reset_token = ?, token_expiry = ? WHERE staff_id = ?");
        $update_stmt->execute([$token, $expiry, $user['staff_id']]);

        // สร้างลิงก์รีเซ็ตรหัสผ่าน
        $reset_link = "http://http://localhost:8080/Pro/reset_password.php?token=$token";

        // ส่งอีเมล
        $mail = new PHPMailer(true);
        try {
            // กำหนดค่าเซิร์ฟเวอร์ SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'sukanyajpy2509@gmail.com';  // อีเมลของคุณ
            $mail->Password = 'uwbp ihgr hkow luhn';  // รหัสผ่านอีเมลหรือ App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // กำหนดผู้ส่งและผู้รับ
            $mail->setFrom('sukanyajpy2509@gmail.com', 'Mailer');
            $mail->addAddress($email, 'User'); // เพิ่มผู้รับ

            // กำหนดหัวข้อและเนื้อหา
            $mail->isHTML(true);
            $mail->Subject = 'รีเซ็ตรหัสผ่านของคุณ';
            $mail->Body    = "คลิกลิงก์นี้เพื่อรีเซ็ตรหัสผ่านของคุณ: $reset_link";

            // ส่งอีเมล
            $mail->send();
            $message = "ลิงก์รีเซ็ตรหัสผ่านถูกส่งไปที่อีเมลของคุณแล้ว.";
        } catch (Exception $e) {
            $message = "ไม่สามารถส่งอีเมลได้. Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = "ไม่พบอีเมลนี้ในระบบ.";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logo.png">
    <title>ส่งลิงก์รีเซ็ตรหัสผ่าน</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e8f5e9;
            color: #333;
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
            animation: fadeIn 1s ease-in-out;
            text-align: center;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        h2 {
            color: #333;
            margin-bottom: 15px;
        }
        p {
            font-size: 14px;
            color: #777;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            text-align: left;
            font-weight: 500;
            margin-bottom: 8px;
            color: #555;
        }
        input[type="email"] {
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="email"]:focus {
            border-color: rgb(0, 255, 13);
            outline: none;
        }
        button {
            padding: 12px;
            background-color: rgb(44, 86, 13);
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: rgb(34, 66, 10);
        }
        .message {
            font-size: 14px;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .success {
            background-color: #28a745;
            color: white;
        }
        .error {
            background-color: #dc3545;
            color: white;
        }
        .footer p {
            font-size: 14px;
            color: #555;
            margin-top: 15px;
        }
        .footer a {
            color: rgb(44, 86, 13);
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>ส่งลิงก์รีเซ็ตรหัสผ่าน</h2>
        <p>กรุณาป้อนอีเมลของคุณเพื่อรับลิงก์รีเซ็ตรหัสผ่าน</p>

        <?php if (isset($message)): ?>
            <div class="message <?= strpos($message, 'สำเร็จ') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="email">อีเมลของคุณ:</label>
            <input type="email" id="email" name="email" placeholder="example@domain.com" required>
            <button type="submit">ส่งรหัสรีเซ็ต</button>
        </form>

        <div class="footer">
            <p>จำรหัสผ่านได้? <a href="login.php">เข้าสู่ระบบ</a></p>
        </div>
    </div>
</body>
</html>
