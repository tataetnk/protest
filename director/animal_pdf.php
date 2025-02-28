<?php
// รวมไฟล์การเชื่อมต่อฐานข้อมูล
require_once '../connect.php';

// ดึงข้อมูลสัตว์จากฐานข้อมูลด้วย PDO
$sql = "SELECT * FROM animal";
$stmt = $pdo->prepare($sql);  // เตรียมคำสั่ง SQL
$stmt->execute();  // เริ่มการทำงานของคำสั่ง SQL
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logo.png">
    <title>รายงานข้อมูลสัตว์เข้าใหม่</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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

        .btn-blue {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }
        .btn-blue:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }

        /* ปรับให้ข้อความ "ข้อมูลสัตว์เข้าใหม่" อยู่ตรงกลาง */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        /* ปรับให้ปุ่มดาวน์โหลดอยู่มุมขวา */
        .download-btn {
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="wrapper">
     <!-- เมนูด้านซ้าย -->
     <div class="sidebar">
     <h4><i class="fas fa-cogs"></i> เมนูหลัก</h4>
        <a href="director.php"><i class="fas fa-home"></i> หน้าหลัก</a>
        <a href="staff_pdf.php"><i class="fas fa-file-alt"></i> รายงานข้อมูลบุคลากร</a>
        <a href="animainew_pdf.php"><i class="fas fa-file-alt"></i> รายงานข้อมูลสัตว์แรกเกิด</a>
        <a href="animal_pdf.php"><i class="fas fa-file-alt"></i> รายงานข้อมูลสัตว์เข้าใหม่</a>
        <a href="cage_pdf.php"><i class="fas fa-file-alt"></i> รายงานข้อมุลกรงเลี้ยง</a>
        <a href="animal_type_pdf.php"><i class="fas fa-file-alt"></i> รายงานข้อมูลประเภทสัตว์</a>
        <a href="breeding_report.php"><i class="fas fa-file-alt"></i> รายงานประวัติการผสมพันธุ์ ตั้งครรภ์และคลอด</a>
        <a href="care_report.php"><i class="fas fa-file-alt"></i> รายงานข้อมูลการดูแล</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
     </div>

    <!-- เนื้อหาหลัก -->
    <div class="content">
        <!-- ข้อความหัวเรื่อง -->
        <div class="header">
            <h2>รายงานข้อมูลสัตว์เข้าใหม่</h2>
        </div>

        <!-- ปุ่มสำหรับดาวน์โหลด PDF -->
        <div class="download-btn">
            <a href="animal_report_pdf.php" target="_blank" class="btn-blue">ดาวน์โหลดรายงาน PDF</a>
        </div>

        <!-- แสดงตารางข้อมูลสัตว์ -->
        <table>
            <tr>
                <th width="20%">รหัสสัตว์</th>
                <th width="25%">ชื่อสัตว์</th>
                <th width="15%">เพศ</th>
                <th width="20%">วันที่เข้า</th>
                <th width="20%">รายละเอียด</th>
                <th width="20%">มาจาก</th>
            </tr>

            <?php
            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>
                            <td>' . $row['animal_id'] . '</td>
                            <td>' . $row['animal_name'] . '</td>
                            <td>' . $row['gender'] . '</td>
                            <td>' . $row['entry_date'] . '</td>
                            <td>' . $row['nature'] . '</td>
                            <td>' . $row['source'] . '</td>
                        </tr>';
                }
            } else {
                echo '<tr><td colspan="6">ไม่พบข้อมูลสัตว์เข้าใหม่</td></tr>';
            }
            ?>
        </table>
    </div>
</div>

</body>
</html>
