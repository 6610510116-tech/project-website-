<?php
session_start();
// ตรวจสอบว่าไฟล์ dbconnect.php อยู่ในระดับเดียวกันกับ admin_login.php หรือไม่
include_once 'dbconnect.php'; 

// ตรวจสอบเมื่อกดปุ่ม Login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ใช้ txtemail และ txtpassword ตามที่ฟอร์มส่งมา
    $email = mysqli_real_escape_string($conn, $_POST['txtemail']);
    $password = mysqli_real_escape_string($conn, $_POST['txtpassword']);
    
    // ไม่ต้องมี $selectedRole เพราะเราจะบังคับให้ role เป็น 'admin' เท่านั้น
    
    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
    $query = "SELECT id, email, password, role FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // **สำคัญ**: ควรใช้ password_verify() หากรหัสผ่านถูกเข้ารหัสด้วย password_hash()
        // สำหรับโค้ดนี้ ยังคงใช้การตรวจสอบรหัสผ่านแบบไม่เข้ารหัส (ไม่แนะนำสำหรับการใช้งานจริง!)
        if ($password === $row['password']) {

            // **จุดเน้น: ตรวจสอบเฉพาะบทบาท 'admin' เท่านั้น**
            if ($row['role'] == 'admin') {
                
                // ตั้งค่า session และส่งไปยังหน้า admin.php
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $row['role']; 
                
                header("Location: admin.php");
                exit();
            } else {
                // ถ้าล็อกอินสำเร็จแต่บทบาทไม่ใช่ admin 
                echo "<script>alert('❌ คุณไม่มีสิทธิ์เข้าถึงในฐานะผู้ดูแลระบบ');</script>";
            }
        } else {
            echo "<script>alert('❌ รหัสผ่านไม่ถูกต้อง');</script>";
        }
    } else {
        echo "<script>alert('❌ ไม่พบอีเมลนี้ในระบบ');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - LearnHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .admin-login-box { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 350px; text-align: center; }
        .admin-login-box h2 { color: #4a65a9; margin-bottom: 25px; }
        .admin-login-box input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .admin-login-box button { background: #4a65a9; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; width: 100%; }
        .admin-login-box button:hover { background: #3b5085; }
    </style>
</head>
<body>
    <div class="admin-login-box">
        <h2><i class="fa-solid fa-user-gear"></i> Admin Login</h2>
        
        <form method="post" action="admin_login.php">
            <input type="email" placeholder="Email" name="txtemail" required>
            <input type="password" placeholder="Password" name="txtpassword" required>
            <button type="submit">Login as Admin</button>
            <p style="margin-top: 15px;"><a href="index.php" style="color: #4a65a9; text-decoration: none;">กลับหน้าหลัก</a></p>
        </form>
    </div>
</body>
</html>