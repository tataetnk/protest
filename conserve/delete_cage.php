<?php
require_once '../connect.php';

// ตรวจสอบว่ามีการส่ง delete_id มา
if (isset($_POST['delete_id'])) {
    $cage_id = $_POST['delete_id'];

    // ลบข้อมูลกรงเลี้ยงจากฐานข้อมูล
    $sql = "DELETE FROM cage WHERE cage_id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$cage_id])) {
        echo "ลบข้อมูลกรงเลี้ยงสำเร็จ";
    } else {
        echo "เกิดข้อผิดพลาดในการลบข้อมูลกรงเลี้ยง";
    }
} else {
    echo "ไม่มีข้อมูลกรงเลี้ยงที่ต้องการลบ";
}
?>
