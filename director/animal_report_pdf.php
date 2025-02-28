<?php
require_once('../tcpdf/tcpdf.php');
require_once('../connect.php');

// เริ่มต้น session และดึงชื่อผู้ใช้งาน
session_start();
$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown User'; // ดึงชื่อผู้ใช้งานจาก session

ini_set('memory_limit', '256M');


// สร้าง PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($user_name);
$pdf->SetTitle('รายงานข้อมูลสัตว์เข้าใหม่');
$pdf->SetHeaderData('', 0, 'รายงานข้อมูลสัตว์เข้าใหม่', 'สร้างโดย: ' . $user_name);
$pdf->setFooterData(array(0, 64, 128), array(0, 64, 128));
$pdf->setMargins(15, 27, 15);
$pdf->setHeaderFont(Array('freeserif', '', 10));
$pdf->setFooterFont(Array('freeserif', '', 8));
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();
$pdf->SetFont('freeserif', '', 12);

// ดึงข้อมูลจากฐานข้อมูล
$sql = "SELECT * FROM animal";
$stmt = $pdo->prepare($sql);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    // สร้างหัวข้อและตาราง
    $html = '
    <p style="text-align: center; font-size: 14px; font-weight: bold;">ข้อมูลสัตว์เข้าใหม่</p>
    <table border="1" cellpadding="5">
        <tr>
            <th width="10%">รหัสสัตว์</th>
            <th width="15%">ชื่อสัตว์</th>
            <th width="15%">เพศ</th>
            <th width="20%">วันที่เข้า</th>
            <th width="15%">รายละเอียด</th>
            <th width="20%">มาจาก</th>
        </tr>';
    
    // เติมข้อมูลจากฐานข้อมูลลงในตาราง
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $html .= '<tr>
                    <td>' . $row['animal_id'] . '</td>
                    <td>' . $row['animal_name'] . '</td>
                    <td>' . $row['gender'] . '</td>
                    <td>' . $row['entry_date'] . '</td>
                    <td>' . $row['nature'] . '</td>
                    <td>' . $row['source'] . '</td>
                  </tr>';
    }
    $html .= '</table>';
    
    // เขียน HTML ลงใน PDF
    $pdf->writeHTML($html, true, false, true, false, '');
} else {
    // หากไม่พบข้อมูล
    $pdf->Write(0, 'ไม่พบข้อมูลสัตว์เข้าใหม่');
}

// ปิดการเชื่อมต่อฐานข้อมูล
$pdo = null;

// ส่งออกไฟล์ PDF
$pdf->Output('new_animal_report.pdf', 'I');
?>
