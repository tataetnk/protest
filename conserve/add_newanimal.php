<?php 
session_start();
require_once '../connect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// ดึงข้อมูลประเภทสัตว์จากฐานข้อมูล
$sql = "SELECT * FROM animal_type";
$stmt = $pdo->query($sql);
$animal_types = $stmt->fetchAll();

// เมื่อผู้ใช้ส่งฟอร์มมา
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $animal_name = $_POST['animal_name']; // ชื่อสัตว์
    $details_animal = $_POST['details_animal'] ?? null;
    $gender = $_POST['gender'] ?? null; // เพศ
    $weight = $_POST['weight'] ?? null;
    $animalsiz = $_POST['animalsiz'] ?? null;
    $birthday_animal = $_POST['birthday_animal'] ?? null;
    $breeding_id = $_POST['breeding_id'] ?? null;
    $cage_id = $_POST['cage_id'] ?? null; 
    $color = $_POST['color'] ?? null;
    $chip = $_POST['chip'] ?? null; 
    $animal_type_id = $_POST['animal_type_id'] ?? null; // รับค่าประเภทสัตว์

    // จัดการการอัปโหลดรูปภาพ
    $image_paths = [];
    if (isset($_FILES['picture_animal']) && $_FILES['picture_animal']['error'][0] == 0) {
        $total_files = count($_FILES['picture_animal']['name']);
        $max_files = min($total_files, 3); // จำกัด 3 รูป
    
        // เริ่มการอัปโหลดไฟล์
        for ($i = 0; $i < $max_files; $i++) {
            if ($_FILES['picture_animal']['error'][$i] == 0) {
                $picture_name = $_FILES['picture_animal']['name'][$i];
                $picture_tmp_name = $_FILES['picture_animal']['tmp_name'][$i];
                $file_extension = pathinfo($picture_name, PATHINFO_EXTENSION);
                $new_picture_name = uniqid() . '.' . $file_extension;
                $picture_path = 'uploads/' . $new_picture_name;
    
                // ย้ายไฟล์ไปยังโฟลเดอร์ที่กำหนด
                if (move_uploaded_file($picture_tmp_name, $picture_path)) {
                    $sql_picture = "INSERT INTO pictureanimals (animal_id, file_animals, details, filepicture_animals) 
                                    VALUES (?, ?, ?, ?)";
                    $stmt_picture = $pdo->prepare($sql_picture);
                    $stmt_picture->execute([$animal_id, $new_picture_name, null, $picture_path]);
                }
            }
        }
    }

    // ตรวจสอบว่า cage_id ที่ได้รับมีอยู่ในตาราง cage
    $sql = "SELECT * FROM cage WHERE cage_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cage_id]);

    // ตรวจสอบว่า breeding_id มีอยู่ในตาราง breeding หรือไม่
    $sql = "SELECT * FROM breeding WHERE breeding_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$breeding_id]);

    if ($stmt->rowCount() == 0) {
        die("Error: breeding_id ที่ระบุไม่มีอยู่ในตาราง breeding.");
    }

    // ถ้าไม่พบ cage_id ให้แสดงข้อผิดพลาด
    if ($stmt->rowCount() == 0) {
        die("Error: cage_id ที่ระบุไม่มีอยู่ในตาราง cage.");
    }

    // ตรวจสอบว่า animal_type_id ถูกส่งมาจากฟอร์ม
    // ดึงชื่อประเภทสัตว์จาก animal_type_id
    $stmt = $pdo->prepare("SELECT type_name FROM animal_type WHERE animal_type_id = ?");
    $stmt->execute([$animal_type_id]);
    $type_name = $stmt->fetchColumn();

    // เริ่มต้น Transaction เพื่อป้องกันข้อผิดพลาด
    $pdo->beginTransaction();

    try {
        // 1️⃣ เพิ่มข้อมูลสัตว์ใหม่ลงใน animal
        $sql = "INSERT INTO animal (animal_name, gender, color, chip, cage_id, animal_type_id) 
        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$animal_name, $gender, $color, $chip, $cage_id, $animal_type_id]);

    

        // 3️⃣ เพิ่มข้อมูลลงใน newborn_animals
        $breeding_id = !empty($_POST['breeding_id']) ? $_POST['breeding_id'] : null;
    // 2️⃣ ดึง animal_id ที่เพิ่งเพิ่ม
        $animal_id = $pdo->lastInsertId();
        $sql = "INSERT INTO newborn_animals (details_animal, birthday_animal, weight, animalsiz, animal_id, animal_name, gender, breeding_id, cage_id, animal_type_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$details_animal, $birthday_animal, $weight, $animalsiz, $animal_id, $animal_name, $gender, $breeding_id, $cage_id, $animal_type_id]);

        // 4️⃣ เพิ่มข้อมูลรูปภาพลงในตาราง images
        if (move_uploaded_file($picture_tmp_name, $picture_path)) {
            $sql_picture = "INSERT INTO pictureanimals (animal_id, file_animals, details, filepicture_animals) 
                            VALUES (?, ?, ?, ?)";
            $stmt_picture = $pdo->prepare($sql_picture);
            $stmt_picture->execute([$animal_id, $new_picture_name, null, $picture_path]);
        }
        

        // ✅ บันทึกข้อมูลทั้งหมด
        $pdo->commit();

        // แจ้งเตือนการสำเร็จและย้อนกลับ
        echo "<script>alert('เพิ่มข้อมูลสัตว์แรกเกิดสำเร็จ'); window.location='Manage_newanimal.php';</script>";
        exit;

    } catch (Exception $e) {
        // ❌ ถ้ามีข้อผิดพลาดให้ย้อนกลับ
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logo.png">
    <title>เพิ่มข้อมูลสัตว์แรกเกิด</title>
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
    <div class="content">
        <div class="form-container"> 
            <h2>เพิ่มข้อมูลสัตว์แรกเกิด</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-control">
                    <label for="animal_name">ชื่อสัตว์</label>
                    <input type="text" class="form-control" id="animal_name" name="animal_name" required>
                </div>
                <div class="mb-3">
                <label for="gender" class="form-label">เพศ</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="">-- เลือกเพศ --</option>
                    <option value="male">เพศผู้</option>
                    <option value="female">เพศเมีย</option>
                </select>
                <div class="form-control">
                    <label for="details_animal">รายละเอียดสัตว์</label>
                    <textarea class="form-control" id="details_animal" name="details_animal" rows="4" required></textarea>
                </div>
                <div class="form-control">
                    <label for="birthday_animal">วันที่เกิด</label>
                    <input type="date" class="form-control" id="birthday_animal" name="birthday_animal" required>
                </div>
                <div class="form-control">
                    <label for="weight">น้ำหนัก</label>
                    <input type="text" class="form-control" id="weight" name="weight" required>
                </div>
                <div class="form-control">
                    <label for="animalsiz">ขนาดสัตว์</label>
                    <input type="text" class="form-control" id="animalsiz" name="animalsiz" required>
                </div>
                <div class="form-control">
                    <label for="breeding_id">หมายเลขการผสมพันธุ์</label>
                    <input type="text" class="form-control" id="breeding_id" name="breeding_id" required>
                </div>
                <div class="form-control">
                    <label for="cage_id">หมายเลขกรง</label>
                    <input type="text" class="form-control" id="cage_id" name="cage_id" required>
                </div>
                <div class="form-control">
                    <label for="color">สี</label>
                    <input type="text" class="form-control" id="color" name="color" required>
                </div>
                <div class="form-control">
                    <label for="chip">หมายเลขชิป</label>
                    <input type="text" class="form-control" id="chip" name="chip" placeholder="กรุณากรอกหมายเลขชิป (ถ้ามี)">
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

                <!-- สำหรับการอัปโหลดหลายไฟล์ -->
                <div class="form-control">
                    <label for="picture_animal">เลือกรูปภาพสัตว์ (หลายไฟล์ได้)</label>
                    <input type="file" class="form-control" id="picture_animal" name="picture_animal[]" multiple>
                </div>

                <button type="submit" class="btn-custom">บันทึกข้อมูล</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
