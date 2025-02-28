<?php
session_start();
require_once '../connect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// ดึงข้อมูลประเภทสัตว์
$sql_type = "SELECT animal_type_id, type_name FROM animal_type";
$stmt_type = $pdo->query($sql_type);
$animal_types = $stmt_type->fetchAll(PDO::FETCH_ASSOC);

// เมื่อผู้ใช้ส่งฟอร์มมา
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $animal_name = $_POST['animal_name'] ?? null;
    $gender = $_POST['gender'] ?? null;
    $entry_date = $_POST['entry_date'] ?? null;
    $color = $_POST['color'] ?? null;
    $nature = $_POST['nature'] ?? null;
    $chip = $_POST['chip'] ?? null;
    $details_animals = $_POST['details_animals'] ?? null;
    $source = $_POST['source'] ?? null;
    $cage_id = $_POST['cage_id'] ?? null;
    $animal_type_id = $_POST['animal_type_id'] ?? null;
    
    // ดึงชื่อประเภทสัตว์จาก animal_type_id
    $stmt = $pdo->prepare("SELECT type_name FROM animal_type WHERE animal_type_id = ?");
    $stmt->execute([$animal_type_id]);
    $type_name = $stmt->fetchColumn();

    // เพิ่มข้อมูลสัตว์เข้าใหม่
    $sql = "INSERT INTO animal (animal_name, gender, entry_date, color, nature, chip, details_animals, source, cage_id, animal_type_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$animal_name, $gender, $entry_date, $color, $nature, $chip, $details_animals, $source, $cage_id, $animal_type_id]);

    $animal_id = $pdo->lastInsertId();

        // อัปโหลดรูปภาพ (สูงสุด 3 รูป)
        if (isset($_FILES['picture_animal'])) {
            $total_files = count($_FILES['picture_animal']['name']);
            $max_files = min($total_files, 3); // จำกัด 3 รูป
    
            for ($i = 0; $i < $max_files; $i++) {
                if ($_FILES['picture_animal']['error'][$i] == 0) {
                    $picture_name = $_FILES['picture_animal']['name'][$i];
                    $picture_tmp_name = $_FILES['picture_animal']['tmp_name'][$i];
                    $file_extension = pathinfo($picture_name, PATHINFO_EXTENSION);
                    $new_picture_name = uniqid() . '.' . $file_extension;
                    $picture_path = 'uploads/' . $new_picture_name;
    
                    if (move_uploaded_file($picture_tmp_name, $picture_path)) {
                        $sql_picture = "INSERT INTO pictureanimals (animal_id, file_animals, details, filepicture_animals) 
                                        VALUES (?, ?, ?, ?)";
                        $stmt_picture = $pdo->prepare($sql_picture);
                        $stmt_picture->execute([$animal_id, $new_picture_name, null, $picture_path]);
                    }
                }
            }
        }

    echo "<script>alert('เพิ่มข้อมูลสัตว์สำเร็จ'); window.location='Manage_animal.php';</script>";
    exit;
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logo.png">
    <title>เพิ่มข้อมูลสัตว์เข้าใหม่</title>
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
            <h2>เพิ่มข้อมูลสัตว์</h2>
            <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="animal_name" class="form-label">ชื่อสัตว์</label>
                <input type="text" class="form-control" id="animal_name" name="animal_name" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">เพศ</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="">-- เลือกเพศ --</option>
                    <option value="male">เพศผู้</option>
                    <option value="female">เพศเมีย</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="entry_date" class="form-label">วันที่เข้ามา</label>
                <input type="date" class="form-control" id="entry_date" name="entry_date" required>
            </div>
            <div class="mb-3">
                <label for="color" class="form-label">สี</label>
                <input type="text" class="form-control" id="color" name="color" required>
            </div>
            <div class="mb-3">
                <label for="nature" class="form-label">ลักษณะนิสัย</label>
                <textarea class="form-control" id="nature" name="nature" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="chip" class="form-label">หมายเลขชิป</label>
                <input type="text" class="form-control" id="chip" name="chip">
            </div>
            <div class="mb-3">
                <label for="details_animals" class="form-label">รายละเอียดสัตว์</label>
                <textarea class="form-control" id="details_animals" name="details_animals" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="source" class="form-label">ที่มา</label>
                <input type="text" class="form-control" id="source" name="source">
            </div>
            <div class="mb-3">
                <label for="cage_id" class="form-label">หมายเลขกรง</label>
                <input type="text" class="form-control" id="cage_id" name="cage_id">
            </div>
            <div class="mb-3">
                <label for="animal_type_id" class="form-label">ประเภทสัตว์</label>
                <select class="form-control" id="animal_type_id" name="animal_type_id" required>
                    <option value="">-- เลือกประเภทสัตว์ --</option>
                    <?php foreach ($animal_types as $type): ?>
                        <option value="<?php echo $type['animal_type_id']; ?>"><?php echo $type['type_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
            <label>อัปโหลดรูปสัตว์ (สูงสุด 3 รูป):</label>
            <input type="file" name="picture_animal[]" multiple accept="image/*"><br>
            </div>
            <button type="submit" class="btn btn-primary">เพิ่มข้อมูล</button>
       
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
