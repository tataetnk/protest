<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลืมรหัสผ่าน</title>
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
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        p {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        input[type="email"] {
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="email"]:focus {
            border-color:rgb(0, 255, 13);
            outline: none;
        }
        button {
            padding: 12px;
            background-color:rgb(44, 86, 13);
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
       
    </style>
</head>
<body>
    <div class="container">
        <h2>ลืมรหัสผ่าน</h2>
        <p>ป้อนอีเมล์ เพื่อส่งลิงก์รีเซ็ตรหัสผ่าน</p>
        <form method="POST" action="send_reset_email.php">
            <label for="email">อีเมล์</label>
            <input type="email" id="email" name="email" placeholder="example@domain.com" required>
            <button type="submit">ส่งรหัสรีเซ็ต</button>
        </form>
        <div class="footer">
            <p>จำรหัสผ่านได้? <a href="login.php">เข้าสู่ระบบ</a>.</p>
        </div>
    </div>
</body>
</html>
