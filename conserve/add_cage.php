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
    $cage_size = $_POST['cage_size'] ?? null;
    $cage_type = $_POST['cage_type'] ?? null;
    $details_cage = $_POST['details_cage'] ?? null;
    $location_cage = $_POST['location_cage'] ?? null;

    // เพิ่มข้อมูลกรงใหม่ในตาราง cages
    $sql = "INSERT INTO cage (size, type, details_cage, location_cage) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cage_size, $cage_type, $details_cage, $location_cage]);

    // แจ้งเตือนการสำเร็จและย้อนกลับ
    echo "<script>alert('เพิ่มข้อมูลกรงเลี้ยงสำเร็จ'); window.location='Manage_cage.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logo.png">
    <title>เพิ่มข้อมูลกรงเลี้ยง</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .wrapper {
            display: flex;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #2e7d32;
            color: white;
            padding: 15px;
            position: fixed;
        }
        .sidebar h4 {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 1px solid white;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 10px;
            text-decoration: none;
            margin-bottom: 5px;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background: #1b5e20;
        }
        .sidebar .active {
            background: #1b5e20;
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
    <!-- เมนูด้านซ้าย -->
    <div class="sidebar">
    <h4><i class="fas fa-cogs"></i> เมนูหลัก</h4>
        <a href="conserve.php"><i class="fas fa-home"></i> หน้าหลัก</a>
        <a href="Manage_animal.php"><i class="fas fa-baby"></i> จัดการข้อมูลสัตว์เข้าใหม่</a>
        <a href="Manage_newanimal.php"><i class="fas fa-baby-carriage"></i> จัดการข้อมูลสัตว์เกิดใหม่</a>
        <a href="Manage_cage.php"><i class="fas fa-box"></i> จัดการกรงเลี้ยง</a>
        <a href="Manage_type.php"><i class="fas fa-paw"></i> จัดการข้อมูลประเภทสัตว์</a>
        <a href="Manage_breeding.php"><i class="fas fa-heart"></i> จัดการข้อมูลผสมพันธุ์ ตั้งครรภ์ และคลอด</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
     </div>

    <!-- เนื้อหาหลัก -->
    <div class="content">
        <div class="form-container">
            <h2>เพิ่มข้อมูลกรงเลี้ยง</h2>
            <form method="POST">
                <div class="form-control">
                    <label for="cage_size">ขนาดกรง (ตารางเมตร)</label>
                    <input type="number" class="form-control" id="cage_size" name="cage_size" required>
                </div>
                <div class="form-control">
                    <label for="cage_type">ประเภทกรง</label>
                    <input type="text" class="form-control" id="cage_type" name="cage_type" required>
                </div>
                <div class="form-control">
                    <label for="details_cage">รายละเอียดกรงเลี้ยง</label>
                    <textarea class="form-control" id="details_cage" name="details_cage" rows="4"></textarea>
                </div>
                <div class="form-control">
                    <label for="location_cage">สถานที่ตั้งกรงเลี้ยง</label>
                    <input type="text" class="form-control" id="location_cage" name="location_cage" required>
                </div>
                <button type="submit" class="btn-custom">เพิ่มข้อมูลกรงเลี้ยง</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
