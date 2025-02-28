<?php
require_once '../connect.php';

// ตรวจสอบว่ามีการส่ง delete_id มา
if (isset($_POST['delete_id'])) {
    $animal_type_id = $_POST['delete_id'];

    // ลบข้อมูลกรงเลี้ยงจากฐานข้อมูล
    $sql = "DELETE FROM animal_type WHERE animal_type_id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$animal_type_id])) {
        echo "ลบข้อมูลประเภทสัตว์สำเร็จ";
    } else {
        echo "เกิดข้อผิดพลาดในการลบข้อมูลประเภทสัตว์";
    }
} else {
    echo "ไม่มีข้อมูลประเภทสัตว์ที่ต้องการลบ";
}
?>
