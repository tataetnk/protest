<?php
session_start();
require_once '../connect.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$staff_id = $_GET['staff_id'] ?? ''; // รับค่า staff_id จาก URL

// ตรวจสอบว่าได้ส่ง staff_id มา
if (empty($staff_id)) {
    echo "<script>alert('ไม่พบพนักงานนี้'); window.location='Manage personnel.php';</script>";
    exit;
}

// ลบข้อมูลพนักงานจากฐานข้อมูล
$sql = "DELETE FROM personnel WHERE staff_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$staff_id]);

// แจ้งเตือนการลบสำเร็จและย้อนกลับ
echo "<script>alert('ลบข้อมูลพนักงานสำเร็จ'); window.location='Manage personnel.php';</script>";
exit;
?>
