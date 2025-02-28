<?php
require_once '../connect.php';

if (isset($_POST['delete_id'])) {
    $newanimal_id = $_POST['delete_id']; // ใช้ newanimal_id สำหรับการลบสัตว์แรกเกิด

    try {
        $pdo->beginTransaction(); // เริ่ม Transaction เพื่อความปลอดภัย

        // ดึง animal_id ที่เกี่ยวข้องกับ newanimal_id จากตาราง newborn_animals
        $sql_get_animal_id = "SELECT animal_id FROM newborn_animals WHERE newanimal_id = ?";
        $stmt_get_animal_id = $pdo->prepare($sql_get_animal_id);
        $stmt_get_animal_id->execute([$newanimal_id]);
        $animal = $stmt_get_animal_id->fetch();

        if ($animal) {
            // 1️⃣ ลบข้อมูลจาก newborn_animals ก่อน
            $sql_newborn = "DELETE FROM newborn_animals WHERE newanimal_id = ?";
            $stmt_newborn = $pdo->prepare($sql_newborn);
            $stmt_newborn->execute([$newanimal_id]);

            // 2️⃣ ตรวจสอบว่ามีสัตว์อื่น ๆ ที่ใช้ animal_id เดียวกันอยู่หรือไม่
            $sql_check_animal = "SELECT COUNT(*) FROM newborn_animals WHERE animal_id = ?";
            $stmt_check_animal = $pdo->prepare($sql_check_animal);
            $stmt_check_animal->execute([$animal['animal_id']]);
            $count = $stmt_check_animal->fetchColumn();

            // 3️⃣ ถ้าไม่มีสัตว์แรกเกิดที่เชื่อมโยงกับ animal_id แล้ว ค่อยลบ animal
            if ($count == 0) {
                $sql_animal = "DELETE FROM animal WHERE animal_id = ?";
                $stmt_animal = $pdo->prepare($sql_animal);
                $stmt_animal->execute([$animal['animal_id']]);
            }

            $pdo->commit(); // ยืนยันการลบทั้งหมด
            echo "ลบข้อมูลสัตว์แรกเกิดและสัตว์จากฐานข้อมูลสำเร็จ";
        } else {
            echo "ไม่พบข้อมูลสัตว์ที่ต้องการลบ";
        }
    } catch (Exception $e) {
        $pdo->rollBack(); // ยกเลิกการทำงานหากเกิดข้อผิดพลาด
        echo "เกิดข้อผิดพลาดในการลบข้อมูล: " . $e->getMessage();
    }
} else {
    echo "ไม่มีข้อมูลสัตว์แรกเกิดที่ต้องการลบ";
}
?>
