<?php
include('dbconnect.php');
session_start();

// *** 1. การตรวจสอบสิทธิ์การใช้งาน (Authentication) ***
// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วและมี Role เป็น 'tutor' (ปรับตามชื่อ Role ใน Session ของคุณ)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'tutor') {
    // หากไม่ใช่ tutor ให้เปลี่ยนเส้นทางไปหน้า login.php
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// ดึงชื่ออาจารย์จาก Session (สมมติว่าเก็บใน $_SESSION['username'])
$tutor_name = $_SESSION['username'] ?? 'Tutor Name'; 
$message = ''; // ตัวแปรสำหรับเก็บข้อความแจ้งเตือน

// --- 2. ส่วนจัดการการเพิ่มคอร์สและการอัปโหลดรูปภาพ ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_course'])) {
    
    // 2.1. ป้องกัน SQL Injection และเตรียมตัวแปร
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $price = intval($_POST['price']); 
    $time = mysqli_real_escape_string($conn, $_POST['time']); // ตารางเวลา/คำอธิบาย
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category = mysqli_real_escape_string($conn, $_POST['category']); // *** ค่าหมวดหมู่ใหม่ ***
    $name = mysqli_real_escape_string($conn, $tutor_name); // ใช้ชื่อที่ดึงมาจาก Session

    // 2.2. จัดการไฟล์รูปภาพ
    $image_name = 'default.jpg'; // ตั้งค่าเริ่มต้นเป็นรูปสำรอง
    $uploadOk = 1;
    
    if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] == 0) {
        $target_dir = "picture/"; 
        $original_file_name = basename($_FILES["course_image"]["name"]);
        $imageFileType = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));
        
        // สร้างชื่อไฟล์ที่ไม่ซ้ำกัน
        $image_name = uniqid("img_", true) . '.' . $imageFileType;
        $target_file = $target_dir . $image_name;
        
        // ตรวจสอบว่าเป็นรูปภาพจริงหรือไม่
        $check = getimagesize($_FILES["course_image"]["tmp_name"]);
        if($check === false) {
            $message .= "<div class='error'>❌ ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ!</div>";
            $uploadOk = 0;
        }

        // ตรวจสอบนามสกุลไฟล์
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $message .= "<div class='error'>❌ อนุญาตเฉพาะไฟล์ JPG, JPEG & PNG เท่านั้น!</div>";
            $uploadOk = 0;
        }

        // ลองย้ายไฟล์
        if ($uploadOk == 1) {
            if (!move_uploaded_file($_FILES["course_image"]["tmp_name"], $target_file)) {
                $message .= "<div class='error'>❌ เกิดข้อผิดพลาดในการอัปโหลด/ย้ายไฟล์! โปรดตรวจสอบสิทธิ์โฟลเดอร์ 'picture'</div>";
                $image_name = 'default.jpg'; // ใช้รูปภาพสำรองหากย้ายล้มเหลว
            }
        } else {
            $image_name = 'default.jpg'; // ใช้รูปภาพสำรองหากมีการตรวจสอบไม่ผ่าน
        }
    } 

    // 2.3. บันทึกข้อมูลลงฐานข้อมูล (INSERT)
    // *** แก้ไข SQL: เพิ่ม 'category' เข้าไปในรายการคอลัมน์และค่า VALUES ***
    $sql = "INSERT INTO tutor_courses (name, subject, price, time, description, image, category) 
             VALUES ('$name', '$subject', $price, '$time', '$description', '$image_name', '$category')";

    if (mysqli_query($conn, $sql)) {
        // ทำการ Redirect ตัวเองเพื่อป้องกันการส่งข้อมูลซ้ำ
        header("Location: tutor_dashboard.php?added=success&subject=" . urlencode($subject) . "&category=" . urlencode($category));
        exit();
    } else {
        $message = "<div class='error'>❌ เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . mysqli_error($conn) . "</div>";
    }
}

// 3. จัดการข้อความแจ้งเตือนหลังจากการ Redirect
if (isset($_GET['added']) && $_GET['added'] == 'success') {
    $subject_name = htmlspecialchars($_GET['subject'] ?? 'คอร์ส');
    $category_name = htmlspecialchars($_GET['category'] ?? '-');
    $message = "<div class='success'>✅ เพิ่มคอร์สสอน **{$subject_name}** ในหมวดหมู่ **{$category_name}** สำเร็จแล้ว!</div>";
}
// --------------------------------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Tutor Dashboard | LearnHub</title>
    <style>
        /* CSS เดิมของคุณ */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to bottom, #d3dbee, #ffffff);
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #4a69bd;
            color: white;
            padding: 15px 40px;
            font-size: 22px;
            font-weight: bold;
            text-align: left;
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
        }
        .container {
            width: 800px;
            background-color: white;
            border-radius: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            margin: 60px auto;
            padding: 40px;
        }
        h2 {
            text-align: center;
            color: #4a69bd;
            margin-bottom: 25px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            color: #333;
        }
        /* ใช้สไตล์นี้สำหรับ input/select/textarea ทั้งหมด */
        input[type="text"],
        input[type="number"],
        input[type="file"], 
        textarea,
        select { 
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 15px;
            resize: none;
            box-sizing: border-box; /* ทำให้ padding ไม่เพิ่มขนาดรวม */
        }
        textarea {
            height: 100px;
        }
        button {
            background-color: #4a69bd;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 15px;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
        }
        button:hover {
            background-color: #3b539b;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <header>LearnHub | ยินดีต้อนรับ, <?= htmlspecialchars($tutor_name); ?></header>

    <div class="container">
        <h2>📘 สร้างคอร์สสอนใหม่</h2>

        <?= $message; ?> 

        <form method="POST" action="" enctype="multipart/form-data">
            
            <input type="hidden" name="tutor_name" value="<?= htmlspecialchars($tutor_name); ?>"> 

            <label>หมวดหมู่คอร์ส:</label>
            <select name="category" required>
                <option value="" disabled selected>--- เลือกหมวดหมู่ ---</option>
                <option value="ดนตรี">ดนตรี</option>
                <option value="ศิลปะ">ศิลปะ</option>
                <option value="คณิตศาสตร์">คณิตศาสตร์</option>
                <option value="วิทยาศาสตร์">วิทยาศาสตร์</option>
                <option value="คอมพิวเตอร์">คอมพิวเตอร์</option>
                <option value="สังคมศาสตร์">สังคมศาสตร์</option>
                <option value="ภาษาต่างประเทศ">ภาษาต่างประเทศ</option>
                <option value="บริหารธุรกิจ">บริหารธุรกิจ</option>
                <option value="อื่นๆ">อื่นๆ</option>
            </select>

            <label>ชื่อวิชาที่สอน:</label>
            <input type="text" name="subject" required>

            <label>ราคาคอร์ส (บาท):</label>
            <input type="number" name="price" min="0" required>

            <label>เวลาสอน (รูปแบบคำอธิบาย):</label>
            <input type="text" name="time" placeholder="เช่น ทุกวันจันทร์ 18:00-20:00" required> 

            <label>คำอธิบายคอร์ส:</label>
            <textarea name="description" required></textarea>
            
            <label>รูปภาพคอร์ส (JPG/PNG):</label>
            <input type="file" name="course_image" accept=".jpg, .jpeg, .png" required>

            <button type="submit" name="add_course">บันทึกคอร์ส</button>
        </form>
    </div>

</body>
</html>