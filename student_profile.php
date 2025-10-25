<?php
session_start();
// ตรวจสอบว่ามีการเชื่อมต่อฐานข้อมูลหรือไม่
include_once 'dbconnect.php'; 

// ตรวจสอบการล็อกอิน และบทบาท (ต้องเป็นนักเรียนเท่านั้น)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// --- 1. ส่วนประมวลผลฟอร์ม (Save Logic) ---
if (isset($_POST['update_profile'])) {
    // รับค่าและป้องกัน SQL Injection
    $new_name = mysqli_real_escape_string($conn, $_POST['name']);
    $new_surname = mysqli_real_escape_string($conn, $_POST['surname']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $new_bio = mysqli_real_escape_string($conn, $_POST['bio']);
    
    // เตรียมคำสั่ง SQL เบื้องต้น
    $update_fields = "name = '$new_name', surname = '$new_surname', email = '$new_email', phone = '$new_phone', bio = '$new_bio'";
    $image_update = "";

    // --- ส่วนจัดการการอัปโหลดรูปภาพ ---
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "profile_pics/";
        
        // ตรวจสอบและสร้างโฟลเดอร์ profile_pics/ ถ้าไม่มี
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // ตั้งชื่อไฟล์ใหม่ให้ไม่ซ้ำกัน (user_id_timestamp.ext)
        $file_ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $new_file_name = $user_id . '_' . time() . '.' . $file_ext;
        $target_file = $target_dir . $new_file_name;

        // ตรวจสอบว่าเป็นรูปภาพจริงหรือไม่
        $check = getimagesize($_FILES['profile_image']['tmp_name']);
        if ($check !== false) {
            // ตรวจสอบขนาดไฟล์ (เช่น ไม่เกิน 5MB)
            if ($_FILES['profile_image']['size'] < 5000000) {
                // ย้ายไฟล์
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                    $image_update = ", image = '$new_file_name'";
                } else {
                    $message = '<script>alert("เกิดข้อผิดพลาดในการย้ายไฟล์รูปภาพ!");</script>';
                }
            } else {
                $message = '<script>alert("ขนาดไฟล์ใหญ่เกินไป! (สูงสุด 5MB)");</script>';
            }
        } else {
            $message = '<script>alert("ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ!");</script>';
        }
    }
    // --- จบส่วนจัดการการอัปโหลดรูปภาพ ---
    
    // อัพเดทข้อมูลในตาราง users 
    $update_sql = "UPDATE users SET 
        $update_fields
        $image_update
        WHERE id = $user_id";
        
    if (mysqli_query($conn, $update_sql)) {
        // อัพเดท Session หากมีการเปลี่ยนชื่อผู้ใช้
        $_SESSION['username'] = $new_name; 
        if (empty($message)) { // ถ้าไม่มี error จากการอัปโหลดรูป
            $message = '<script>alert("อัพเดทข้อมูลส่วนตัวเรียบร้อยแล้ว!");</script>';
        }
    } else {
        $message = '<script>alert("เกิดข้อผิดพลาดในการอัพเดทข้อมูล: ' . mysqli_error($conn) . '");</script>';
    }
}
// ---------------------------------------------

// --- 2. ดึงข้อมูลปัจจุบันของนักเรียน (Fetch Data) ---
// ดึงคอลัมน์ทั้งหมดที่จำเป็น (ต้องมีใน DB)
$user_sql = "SELECT username, name, surname, email, phone, bio, image FROM users WHERE id = $user_id"; 
$user_result = mysqli_query($conn, $user_sql);

// ถ้าหาข้อมูลไม่เจอ ให้ Redirect กลับ
if (mysqli_num_rows($user_result) == 0) {
    header("Location: student_dashboard.php");
    exit();
}
$user_data = mysqli_fetch_assoc($user_result);
// ----------------------------------------------------
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลส่วนตัว</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
   <style>
    /* CSS สำหรับจัดหน้าให้อยู่ตรงกลาง */
    html, body { 
        height: 100%; /* สำคัญ: กำหนดให้ HTML และ Body มีความสูง 100% */
        margin: 0;
        padding: 0;
    }
    body { 
        font-family: 'Kanit', sans-serif; 
        background: #f0f4f8; 
        display: flex; /* ใช้ Flexbox จัดกลาง */
        justify-content: center; /* จัดกลางแนวนอน */
        align-items: center; /* จัดกลางแนวตั้ง */
        min-height: 100vh; /* กำหนดความสูงให้เต็มหน้าจอ (Viewport Height) */
    }
    .container { 
        width: 90%; 
        max-width: 600px; /* จำกัดความกว้างของฟอร์ม */
        background: white; 
        padding: 30px; 
        border-radius: 10px; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
        /* ลบ margin: auto; ที่ซ้ำซ้อนออก */
    }
    h2 { color: #4a65a9; margin-bottom: 20px; text-align: center; }
    
    /* สไตล์ฟอร์ม */
    label { display: block; margin-top: 15px; font-weight: 600; color: #333; }
    input[type="text"], input[type="email"], textarea { 
        width: 95%; 
        padding: 10px; 
        margin-top: 5px; 
        border: 1px solid #ccc; 
        border-radius: 6px; 
        box-sizing: border-box;
    }
    /* สไตล์พิเศษสำหรับ input file */
    input[type="file"] {
         width: 95%;
         margin-top: 5px; 
         padding: 5px;
         border: none;
    }
    textarea { resize: vertical; height: 80px; }
    button { background: #4a65a9; color: white; border: none; padding: 10px 20px; border-radius: 8px; margin-top: 20px; cursor: pointer; transition: 0.3s; width: 100%; font-size: 16px; }
    button:hover { background: #2e427f; }
    
    /* รูปโปรไฟล์ */
    .profile-img-container { text-align: center; margin-bottom: 20px; }
    .profile-img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #4a65a9; }
    
    /* จัดตำแหน่งปุ่มกลับสู่หน้าหลักให้ลอยอยู่มุมขวาบนของ Container */
    .container a {
        position: absolute;
        top: 30px; /* เว้นระยะห่างจากขอบด้านบนของ container */
        right: 30px; /* เว้นระยะห่างจากขอบด้านขวาของ container */
        text-decoration: none; 
        color:#4a65a9;
    }
</style>
</head>
<body>
<?= $message; ?>
<div class="container">
    <a href="student_dashboard.php" style="float:right; text-decoration: none; color:#4a65a9;"><i class="fa-solid fa-arrow-left"></i> กลับสู่หน้าหลัก</a>
    <h2>แก้ไขข้อมูลส่วนตัว</h2>

    <div class="profile-img-container">
        <img src="profile_pics/<?= htmlspecialchars($user_data['image'] ?? 'default_student.png'); ?>" alt="Profile Picture" class="profile-img">
    </div>

    <form method="post" action="student_profile.php" enctype="multipart/form-data">
        
        <label for="profile_image">เปลี่ยนรูปโปรไฟล์:</label>
        <input type="file" id="profile_image" name="profile_image" accept="image/*">
        
        <label for="name">ชื่อ:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user_data['name'] ?? ''); ?>" required>

        <label for="surname">นามสกุล:</label>
        <input type="text" id="surname" name="surname" value="<?= htmlspecialchars($user_data['surname'] ?? ''); ?>" required>

        <label for="email">อีเมล:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data['email'] ?? ''); ?>" required>

        <label for="phone">เบอร์โทรศัพท์:</label>
        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user_data['phone'] ?? ''); ?>">

        <label for="bio">คำอธิบายตนเอง:</label>
        <textarea id="bio" name="bio"><?= htmlspecialchars($user_data['bio'] ?? ''); ?></textarea>
        
        <button type="submit" name="update_profile">บันทึกการแก้ไข</button>
    </form>
</div>
</body>
</html>