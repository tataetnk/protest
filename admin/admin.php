<?php
session_start(); // เริ่มต้น session เพื่อดึงข้อมูลจาก session

// ตรวจสอบว่าได้ล็อกอินหรือยัง
if (!isset($_SESSION['username'])) {
    // ถ้ายังไม่ได้ล็อกอินให้รีไดเร็กไปหน้าล็อกอิน
    header("Location: login.php");
    exit;
}

$staff_id = $_SESSION['staff_id']; // ดึง staff_id จาก session

// เชื่อมต่อฐานข้อมูลเพื่อดึงชื่อเต็ม (full_name)
require_once '../connect.php';
$sql = "SELECT full_name FROM personnel WHERE staff_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$staff_id]); // ส่ง staff_id เป็นพารามิเตอร์

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $full_name = $row['full_name']; // ดึงชื่อเต็มของผู้ใช้
} else {
    // ถ้าไม่พบข้อมูลให้แสดงข้อความแจ้งเตือน
    $full_name = "ผู้ใช้ไม่พบ";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logo.png">
    <title>ผู้ดูแลระบบ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

        .content {
            margin-left: 260px;
            padding: 20px;
            width: calc(100% - 260px);
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- เมนูด้านซ้าย -->
    <div class="sidebar">
        <h4><i class="fas fa-cogs"></i> เมนูหลัก</h4>
        <!-- ไม่ต้องใช้ PHP สำหรับการเปลี่ยนสีพื้นหลังที่นี่ -->
    <a href="admin.php"><i class="fas fa-home"></i> หน้าหลัก</a>
    <a href="Manage personnel.php"><i class="fas fa-users"></i> จัดการบุคลากร</a>
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
</div>

    <!-- เนื้อหาหลัก -->
    <div class="content">
        <h1>ยินดีต้อนรับคุณ <?php echo htmlspecialchars($full_name); ?>!</h1> <!-- แสดงชื่อเต็มของผู้ใช้ -->
        <p>กรุณาเลือกเมนูจากด้านซ้ายมือเพื่อดำเนินการที่ต้องการ</p>
    </div>

</div>

</body>
</html>
