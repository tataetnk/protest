<?php
require_once '../connect.php';

// ตรวจสอบว่ามีการส่ง delete_id มา
if (isset($_POST['delete_id'])) {
    $cage_id = $_POST['delete_id'];

    // ลบข้อมูลกรงเลี้ยงจากฐานข้อมูล
    $sql = "DELETE FROM animal WHERE animal_id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$cage_id])) {
        echo "ลบข้อมูลสัตว์สำเร็จ";
    } else {
        echo "เกิดข้อผิดพลาดในการลบข้อมูลสัตว์";
    }
} else {
    echo "ไม่มีข้อมูลสัตว์ที่ต้องการลบ";
}
?>
