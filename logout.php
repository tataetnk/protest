<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logo.png">
    <title>กำลังออกจากระบบ...</title>
    <script>
        function logout() {
            // สามารถเพิ่มการล้างข้อมูล session หรือ token ที่นี่ถ้าจำเป็น
            window.location.href = '../Protest/homepang.html';
        }
        window.onload = logout;
    </script>
</head>
<body>
    <p>กำลังออกจากระบบ... กรุณารอสักครู่</p>
</body>
</html>