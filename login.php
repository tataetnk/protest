<?php
session_start();
require 'connect.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
    $stmt = $pdo->prepare("SELECT * FROM personnel WHERE username = :username AND password = :password");
    $stmt->execute(['username' => $username, 'password' => $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) { 
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['position'] = $user['position'];
        $_SESSION['staff_id'] = $user['staff_id'];
    
        // กำหนดเส้นทางตามสิทธิ์
        switch ($user['position']) {
            case 'ผู้ดูแลระบบ':
                header("Location: admin/admin.php");
                break;
            case 'เจ้าหน้าที่ส่วนจัดแสดง':
                header("Location: Exhibition/Exhibition.php");
                break;
            case 'เจ้าหน้าที่แผนกอนุรักษ์สัตว์':
                header("Location: conserve/conserve.php");
                break;
            case 'ผู้อำนวยการ':
                header("Location: director/director.php");
                break;
            default:
                header("Location: homepang.html"); // ถ้าสิทธิ์ไม่ตรงเงื่อนไข
                break;
        }
        exit();
    } else {
        echo "<script>alert('ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!'); window.location='login.php';</script>";
    }
    
}
?>

<!-- ฟอร์ม Login -->
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logo.png">
    <title>เข้าสู่ระบบ - สวนสัตว์เขาสวนกวาง</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #e8f5e9;
            color: #333;
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #2e7d32;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .form-control {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-control label {
            display: block;
            margin-bottom: 8px;
        }

        .form-control input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .btn {
            background-color: #2e7d32;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn:hover {
            background-color: #43a047;
        }

        .error-message {
            color: red;
            margin-bottom: 20px;
        }

        .forgot-password {
            margin-top: 10px;
            font-size: 0.9rem;
        }

        .forgot-password a {
            color: #2e7d32;
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>เข้าสู่ระบบ</h2>
        <form method="POST">
            <div class="form-control">
                <label for="username">ชื่อผู้ใช้</label>
                <input type="text" name="username" id="username" placeholder="Username" required>
            </div>
            <div class="form-control">
                <label for="password">รหัสผ่าน</label>
                <input type="password" name="password" id="password" placeholder="Password" required>
            </div>
            <button type="submit" name="login" class="btn">Login</button>
        </form>
        <div class="forgot-password">
            <a href="forgot_password.php">ลืมรหัสผ่าน?</a>
        </div>

    </div>
</body>
</html>
