<?php
session_start();
require_once '../connect.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// ตรวจสอบว่าได้รับ delete_id หรือไม่
if (isset($_POST['delete_id'])) {
    $staff_id = $_POST['delete_id'];

    // ลบข้อมูลบุคลากรจากฐานข้อมูล
    $sql = "DELETE FROM personnel WHERE staff_id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$staff_id])) {
        echo "ลบข้อมูลบุคลากรสำเร็จ";
    } else {
        echo "เกิดข้อผิดพลาดในการลบข้อมูลบุคลากร";
    }

    exit; // ป้องกันไม่ให้แสดง HTML ส่วนอื่น ๆ หลังการลบ
}

// ส่วนของ HTML แสดงข้อมูลบุคลากรตามปกติ
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logo.png">
    <title>จัดการบุคลากร</title>
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
     <a href="admin.php"><i class="fas fa-home"></i> หน้าหลัก</a>
    <a href="Manage personnel.php"><i class="fas fa-users"></i> จัดการบุคลากร</a>
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
     </div>

    <!-- เนื้อหาหลัก -->
    <div class="content">
        <h2 class="text-success">จัดการบุคลากร</h2>

        <!-- ปุ่มเพิ่มข้อมูล + ช่องค้นหา -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="add_personnel.php" class="btn btn-success">+ เพิ่มข้อมูล</a>
            <input type="text" id="searchInput" class="form-control w-25" placeholder="🔍 ค้นหา...">
        </div>

        <table class="table table-bordered table-striped bg-white">
            <thead class="table-success">
                <tr>
                    <th>รหัสบุคลากร</th>
                    <th>ชื่อบุคลากร</th>
                    <th>ตำแหน่ง</th>
                    <th>ชื่อผู้ใช้</th>
                    <th>เบอร์โทรศัพท์</th>
                    <th>อีเมล</th>
                    <th>ภาพบุคลากร</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // ดึงข้อมูลบุคลากรจากฐานข้อมูล
                $sql = "SELECT * FROM personnel";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($result) > 0) {
                    foreach ($result as $row) {
                        // แสดงผลในตาราง
                        echo "<tr>";
                        echo "<td>{$row['staff_id']}</td>";
                        echo "<td>{$row['full_name']}</td>";
                        echo "<td>{$row['position']}</td>";
                        echo "<td>{$row['username']}</td>";
                        echo "<td>{$row['phone_number']}</td>";
                        echo "<td>{$row['email']}</td>";
                        echo "<td><img src='{$row['picture_staff']}' alt='Staff Picture' width='50'></td>";
                        echo "<td>
                                <div class='btn-group'>
                                    <a href='edit_personnel.php?staff_id={$row['staff_id']}' class='btn btn-warning btn-sm'>แก้ไข</a>
                                    <button class='btn btn-danger btn-sm' onclick='confirmDelete({$row['staff_id']})'>ลบ</button>
                                </div>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>ไม่มีข้อมูลบุคลากร</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // ค้นหาข้อมูลแบบเรียลไทม์
    $("#searchInput").on("keyup", function () {
        let value = $(this).val().toLowerCase();
        $("tbody tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

// ลบข้อมูลแบบ AJAX
function confirmDelete(staffId) {
    if (confirm("คุณแน่ใจหรือไม่ที่จะลบข้อมูลบุคลากรนี้?")) {
        $.post("Manage personnel.php", { delete_id: staffId }, function(response) {
            alert(response); // แสดงข้อความที่ส่งกลับจาก PHP (เช่น "ลบข้อมูลสำเร็จ")
            location.reload(); // รีเฟรชหน้าเพื่อตรวจสอบข้อมูลที่ถูกลบ
        });
    }
}

</script>

</body>
</html>
