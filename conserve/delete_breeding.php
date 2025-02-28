<?php
require_once '../connect.php';

// ตรวจสอบว่ามีการส่ง delete_id มา
if (isset($_POST['delete_id'])) {
    $cage_id = $_POST['delete_id'];

    // ลบข้อมูลกรงเลี้ยงจากฐานข้อมูล
    $sql = "DELETE FROM breeding WHERE breeding_id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$cage_id])) {
        echo "ลบข้อมูลการผสมพันธุ์สำเร็จ";
    } else {
        echo "เกิดข้อผิดพลาดในการลบข้อมูลผสมพันธุ์";
    }
} else {
    echo "ไม่มีข้อมูลผสมพันธุ์ที่ต้องการลบ";
}
?>
