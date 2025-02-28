<?php
require 'connect.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // ตรวจสอบว่าโทเค็นถูกต้องและยังไม่หมดอายุ
    $stmt = $pdo->prepare("SELECT staff_id FROM personnel WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
 
    if ($user) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = $_POST['password']; // เก็บรหัสผ่านแบบ plain text

            // อัปเดตรหัสผ่านใหม่ และล้างโทเค็น
            $update_stmt = $pdo->prepare("UPDATE personnel SET password = ?, reset_token = NULL, token_expiry = NULL WHERE staff_id = ?");
            $update_stmt->execute([$new_password, $user['staff_id']]);

            $message = "รหัสผ่านถูกเปลี่ยนเรียบร้อยแล้ว.";
        }
    } else {
        $message = "ไม่สามารถรีเซ็ตรหัสผ่านได้. โปรดลองใหม่อีกครั้ง.";
    }
} else {
    $message = "ลิงก์ไม่ถูกต้อง.";
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logo.png">
    <title>รีเซ็ตรหัสผ่าน</title>
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
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            text-align: left;
        }
        input[type="password"] {
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="password"]:focus {
            border-color: rgb(0, 255, 13);
            outline: none;
        }
        button {
            padding: 12px;
            background-color: rgb(82, 152, 33);
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: rgb(146, 218, 91);
        }
        .back-link {
            margin-top: 15px;
            display: inline-block;
            font-size: 14px;
            color: rgb(46, 105, 9);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .message {
            margin-top: 20px;
            font-size: 16px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            font-weight: bold;
        }
        .success {
            background-color: #28a745;
            color: white;
        }
        .error {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>รีเซ็ตรหัสผ่าน</h2>
        <?php if (isset($message)): ?>
            <div class="message <?= strpos($message, 'สำเร็จ') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="password">กรอกรหัสผ่านใหม่:</label>
            <input type="password" id="password" name="password" placeholder="รหัสผ่านใหม่" required>
            <button type="submit">เปลี่ยนรหัสผ่าน</button>
        </form>
        <a href="login.php" class="back-link">กลับไปหน้าเข้าสู่ระบบ</a>
    </div>
</body>
</html>
