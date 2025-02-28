<?php
require 'db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าได้รับข้อมูลจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // ตรวจสอบผู้ใช้ในฐานข้อมูล
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // เข้าสู่ระบบสำเร็จ
        echo "<script>
                alert('ยินดีต้อนรับ " . htmlspecialchars($username) . "!');
                window.location.href = 'dashboard.php'; // เปลี่ยนเส้นทางหลังล็อกอิน
              </script>";
    } else {
        // ชื่อผู้ใช้หรือรหัสผ่านผิด
        echo "<script>
                alert('ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
                window.history.back(); // กลับไปยังหน้าล็อกอิน
              </script>";
    }
}
?>
