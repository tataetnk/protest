<?php
session_start();
require_once '../connect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// เมื่อผู้ใช้ส่งฟอร์มมา
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม (กำหนดค่าให้เป็นค่าว่างหากไม่ได้กรอก)
    $breeding_date = $_POST['breeding_date'] ?? null;
    $breeding_status = $_POST['breeding_status'] ?? null;
    $pregnancy_status = $_POST['pregnancy_status'] ?? null;
    $offspring_count = $_POST['offspring_count'] ?? null;
    $birth_date = $_POST['birth_date'] ?? null;
    $animal_father_id = $_POST['animal_father_id'] ?? null;
    $animal_mother_id = $_POST['animal_mother_id'] ?? null;

    // เพิ่มข้อมูลในตาราง breeding_data (ให้ชื่อเหมาะสมตามโครงสร้างฐานข้อมูลของคุณ)
    $sql = "INSERT INTO breeding (breeding_date, breeding_status, pregnancy_status, offspring_count, birth_date, Animal_father_id, Animal_mother_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$breeding_date, $breeding_status, $pregnancy_status, $offspring_count, $birth_date, $animal_father_id, $animal_mother_id]);

    // แจ้งเตือนการสำเร็จและย้อนกลับ
    echo "<script>alert('เพิ่มข้อมูลการผสมพันธุ์สำเร็จ'); window.location='Manage breeding.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มข้อมูลการผสมพันธุ์</title>
    <link rel="icon" type="image/png" href="logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .wrapper {
            display: flex;
        }
        .content {
            margin-left: 260px;
            padding: 30px;
            width: calc(100% - 260px);
            display: flex;
            justify-content: center;
        }
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }
        .form-container h2 {
            margin-bottom: 20px;
            color: #2e7d32;
        }
        .form-control {
            margin-bottom: 15px;
        }
        .form-control label {
            font-weight: bold;
        }
        .btn-custom {
            background-color: #2e7d32;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        .btn-custom:hover {
            background-color: #43a047;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- เนื้อหาหลัก -->
    <div class="content">
        <div class="form-container">
            <h2>เพิ่มข้อมูลการผสมพันธุ์</h2>
            <form method="POST">
                <div class="form-control">
                    <label for="breeding_date">วันที่ผสมพันธุ์</label>
                    <input type="date" class="form-control" id="breeding_date" name="breeding_date" required>
                </div>
                <div class="form-control">
                    <label for="breeding_status">สถานะการผสมพันธุ์</label>
                    <select class="form-control" id="breeding_status" name="breeding_status" required>
                        <option value="ผสมพันธุ์ผ่าน">ผสมพันธุ์ผ่าน</option>
                        <option value="ผสมพันธุ์ไม่ผ่าน">ผสมพันธุ์ไม่ผ่าน</option>
                    </select>
                </div>
                <div class="form-control">
                    <label for="pregnancy_status">สถานะการตั้งครรภ์</label>
                    <select class="form-control" id="pregnancy_status" name="pregnancy_status" required>
                        <option value="ตั้งครรภ์แล้ว">ตั้งครรภ์แล้ว</option>
                        <option value="ยังไม่ตั้งครรภ์">ยังไม่ตั้งครรภ์</option>
                    </select>
                </div>
                <div class="form-control">
                    <label for="offspring_count">จำนวนลูกสัตว์</label>
                    <input type="number" class="form-control" id="offspring_count" name="offspring_count" required>
                </div>
                <div class="form-control">
                    <label for="birth_date">วันที่คลอด</label>
                    <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                </div>
                <div class="form-control">
                    <label for="animal_father_id">รหัสสัตว์พ่อ</label>
                    <input type="text" class="form-control" id="animal_father_id" name="animal_father_id" required>
                </div>
                <div class="form-control">
                    <label for="animal_mother_id">รหัสสัตว์แม่</label>
                    <input type="text" class="form-control" id="animal_mother_id" name="animal_mother_id" required>
                </div>
                <button type="submit" class="btn-custom">เพิ่มข้อมูล</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
