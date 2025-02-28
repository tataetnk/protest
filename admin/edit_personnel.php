<?php
session_start();
require_once '../connect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// ตรวจสอบว่ามีการส่งข้อมูลฟอร์มมา
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $full_name = $_POST['full_name'];
    $position = $_POST['position'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $staff_id = $_POST['staff_id'];  // รหัสพนักงาน

    // การอัปโหลดไฟล์รูปภาพ
    if (isset($_FILES['picture_staff']) && $_FILES['picture_staff']['error'] == 0) {
        $picture_name = $_FILES['picture_staff']['name'];
        $picture_tmp_name = $_FILES['picture_staff']['tmp_name'];
        $picture_size = $_FILES['picture_staff']['size'];

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($picture_tmp_name);

        if (in_array($file_type, $allowed_types)) {
            $new_picture_name = uniqid() . '.' . pathinfo($picture_name, PATHINFO_EXTENSION);
            $picture_path = 'uploads/' . $new_picture_name;

            if ($picture_size <= 2 * 1024 * 1024) { // ตรวจสอบขนาดไฟล์ไม่เกิน 2MB
                if (move_uploaded_file($picture_tmp_name, $picture_path)) {
                    // ตรวจสอบว่ามี staff_id หรือไม่ ถ้าไม่มีแสดงว่าเป็นการเพิ่มข้อมูล
                    if (empty($staff_id)) {
                        // เพิ่มข้อมูลใหม่
                        $sql = "INSERT INTO personnel (full_name, position, username, password, phone_number, email, picture_staff) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$full_name, $position, $username, $password, $phone_number, $email, $picture_path]);

                        echo "<script>alert('เพิ่มข้อมูลพนักงานสำเร็จ'); window.location='Manage personnel.php';</script>";
                        exit;
                    } else {
                        // อัปเดตข้อมูลในฐานข้อมูลรวมถึงภาพใหม่
                        $sql = "UPDATE personnel SET full_name = ?, position = ?, username = ?, password = ?, phone_number = ?, email = ?, picture_staff = ? WHERE staff_id = ?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$full_name, $position, $username, $password, $phone_number, $email, $picture_path, $staff_id]);

                        echo "<script>alert('อัปเดตข้อมูลพนักงานสำเร็จ'); window.location='Manage personnel.php';</script>";
                        exit;
                    }
                } else {
                    echo "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ";
                }
            } else {
                echo "ขนาดไฟล์เกิน 2MB";
            }
        } else {
            echo "ไฟล์ที่อัปโหลดไม่ใช่รูปภาพที่รองรับ";
        }
    } else {
        // หากไม่อัปโหลดภาพใหม่ ให้ทำการอัปเดตโดยไม่เปลี่ยนรูปภาพ
        if (empty($staff_id)) {
            // เพิ่มข้อมูลใหม่
            $sql = "INSERT INTO personnel (full_name, position, username, password, phone_number, email) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$full_name, $position, $username, $password, $phone_number, $email]);

            echo "<script>alert('เพิ่มข้อมูลพนักงานสำเร็จ'); window.location='Manage personnel.php';</script>";
            exit;
        } else {
            // อัปเดตข้อมูลในฐานข้อมูลโดยไม่เปลี่ยนแปลงภาพ
            $sql = "UPDATE personnel SET full_name = ?, position = ?, username = ?, password = ?, phone_number = ?, email = ? WHERE staff_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$full_name, $position, $username, $password, $phone_number, $email, $staff_id]);

            echo "<script>alert('อัปเดตข้อมูลพนักงานสำเร็จ'); window.location='Manage personnel.php';</script>";
            exit;
        }
    }
}

// ดึงข้อมูลพนักงานที่ต้องการแก้ไขจากฐานข้อมูล
if (isset($_GET['staff_id'])) {
    $staff_id = $_GET['staff_id'];
    $sql = "SELECT * FROM personnel WHERE staff_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$staff_id]);
    $employee = $stmt->fetch();

    if ($employee) {
        $full_name = $employee['full_name'];
        $position = $employee['position'];
        $username = $employee['username'];
        $phone_number = $employee['phone_number'];
        $email = $employee['email'];
    } else {
        echo "ไม่พบข้อมูลพนักงาน";
        exit;
    }
}
?>

<!-- ฟอร์ม HTML -->
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logo.png">
    <title>แก้ไขข้อมูลพนักงาน</title>
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
        <a href="admin.php"><i class="fas fa-home"></i> หน้าหลัก</a>
        <a href="Manage personnel.php"><i class="fas fa-users"></i> จัดการบุคลากร</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
    </div>

    <!-- เนื้อหาหลัก -->
    <div class="content">
        <div class="form-container">
            <h2>แก้ไขข้อมูลพนักงาน</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="staff_id" value="<?= isset($staff_id) ? $staff_id : '' ?>">
                <div class="form-control">
                    <label for="full_name">ชื่อเต็ม</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?= isset($full_name) ? $full_name : '' ?>" required>
                </div>

                <div class="form-control">
                    <label for="position">ตำแหน่ง</label>
                    <select class="form-select" id="position" name="position" required>
                        <option value="ผู้ดูแลระบบ" <?= isset($position) && $position == 'ผู้ดูแลระบบ' ? 'selected' : '' ?>>ผู้ดูแลระบบ</option>
                        <option value="เจ้าหน้าที่ส่วนจัดแสดง" <?= isset($position) && $position == 'เจ้าหน้าที่ส่วนจัดแสดง' ? 'selected' : '' ?>>เจ้าหน้าที่ส่วนจัดแสดง</option>
                        <option value="เจ้าหน้าที่แผนกอนุรักษ์สัตว์" <?= isset($position) && $position == 'เจ้าหน้าที่แผนกอนุรักษ์สัตว์' ? 'selected' : '' ?>>เจ้าหน้าที่แผนกอนุรักษ์สัตว์</option>
                        <option value="ผู้อำนวยการ" <?= isset($position) && $position == 'ผู้อำนวยการ' ? 'selected' : '' ?>>ผู้อำนวยการ</option>
                    </select>
                </div>

                <div class="form-control">
                    <label for="username">ชื่อผู้ใช้</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= isset($username) ? $username : '' ?>" required>
                </div>

                <div class="form-control">
                    <label for="password">รหัสผ่าน</label>
                    <input type="password" class="form-control" id="password" name="password" value="<?= isset($password) ? $password : '' ?>" required>
                </div>

                <div class="form-control">
                    <label for="phone_number">เบอร์โทรศัพท์</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= isset($phone_number) ? $phone_number : '' ?>" required>
                </div>

                <div class="form-control">
                    <label for="email">อีเมล</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= isset($email) ? $email : '' ?>" required>
                </div>

                <div class="form-control">
                    <label for="picture_staff">รูปภาพพนักงาน</label>
                    <input type="file" class="form-control" id="picture_staff" name="picture_staff">
                </div>

                <button type="submit" class="btn-custom">บันทึกการแก้ไข</button>
            </form>
        </div>
    </div>

</div>

</body>
</html>
