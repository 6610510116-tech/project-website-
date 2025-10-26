<?php
session_start();
include_once 'dbconnect.php';

// ... ส่วนของ PHP Logic
if (isset($_POST['register'])) { // หรือชื่อปุ่ม Submit ของคุณ
    
    // 💡 โค้ดที่ต้องตรวจสอบ: Key ใน [ ] ต้องตรงกับ name="..." ใน HTML
    $name = mysqli_real_escape_string($conn, $_POST['name']); 
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // ... ส่วนของ INSERT INTO SQL ...
}


// ลงทะเบียนนักเรียน
if (isset($_POST['signup_student'])) {
    $username = mysqli_real_escape_string($conn, $_POST['txtusername']);
    $email = mysqli_real_escape_string($conn, $_POST['txtemail']);
    $password = mysqli_real_escape_string($conn, $_POST['txtpassword']);
    $confirm = mysqli_real_escape_string($conn, $_POST['txtconfirm']);

    if ($password !== $confirm) {
        $error_student = "รหัสผ่านไม่ตรงกัน!";
    } else {
        $check_email = mysqli_query($conn, "SELECT email FROM users WHERE email='$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $error_student = "อีเมลนี้ถูกใช้แล้ว!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
      $query = "INSERT INTO users(name, email, password, role) 
          VALUES('$username', '$email', '$hashed', 'student')";
if (mysqli_query($conn, $query)) {
    $_SESSION['success_student'] = "สมัครนักเรียนสำเร็จ! โปรดเข้าสู่ระบบ";
    header("Location: login.php");
    exit();
} else {
    $error_student = "เกิดข้อผิดพลาดในการบันทึกข้อมูล!";
}

        }
    }
}

// ลงทะเบียนผู้สอน
// ลงทะเบียนผู้สอน
if (isset($_POST['signup_tutor'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['txtfullname']);
    $email = mysqli_real_escape_string($conn, $_POST['txtemail']);
    $password = mysqli_real_escape_string($conn, $_POST['txtpassword']);
    $confirm = mysqli_real_escape_string($conn, $_POST['txtconfirm']);

    if ($password !== $confirm) {
        $error_tutor = "รหัสผ่านไม่ตรงกัน!";
    } else {
        // ✅ ตรวจสอบอีเมลใน users แทน tutors
        $check_email = mysqli_query($conn, "SELECT email FROM users WHERE email='$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $error_tutor = "อีเมลนี้ถูกใช้แล้ว!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            // ✅ บันทึกลงใน users พร้อมกำหนด role='tutor'
  $query = "INSERT INTO users(name, email, password, role) 
          VALUES('$fullname', '$email', '$hashed', 'tutor')";
if (mysqli_query($conn, $query)) {
    $_SESSION['success_tutor'] = "สมัครติวเตอร์สำเร็จ! โปรดเข้าสู่ระบบ";
    header("Location: login.php");
    exit();
} else {
    $error_tutor = "เกิดข้อผิดพลาดในการบันทึกข้อมูล!";
}
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Register - LearnHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">	
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Kanit', sans-serif; }

        body {
            background: linear-gradient(to bottom right, #e8eef9, #ffffff);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        nav {
            width: 100%;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 15px 0;
            display: flex;
            align-items: center;
            justify-content: flex-start; /* ✅ โลโก้อยู่ซ้าย */
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: 80px; /* ✅ ขยับห่างจากขอบซ้ายหน่อย */
        }

        .logo h1 {
            font-size: 40px;
            color: #4a65a9;
            font-family: 'Archivo Black', sans-serif;
        }

        /* กล่องลงทะเบียน */
        .wrapper {
            margin-top: 120px; /* ✅ เว้นให้พ้น navbar */
            display: flex;
            justify-content: center;
            align-items: center;
            flex: 1;
        }

        .container {
            background: white;
            border-radius: 30px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 800px;
            min-height: 480px;
            overflow: hidden;
            position: relative;
            transition: all 0.6s ease-in-out;
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            width: 50%;
            transition: all 0.6s ease-in-out;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0 50px;
            text-align: center;
        }

        .student-container { left: 0; }
        .tutor-container { left: 100%; }
        .container.active .student-container { transform: translateX(-100%); }
        .container.active .tutor-container { transform: translateX(-100%); }

        form input {
            background: #f1f1f1;
            border: none;
            margin: 8px 0;
            padding: 12px 15px;
            border-radius: 8px;
            width: 100%;
        }

        form button {
            background: #4a65a9;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 45px;
            cursor: pointer;
            margin-top: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        a {
            display: block;
            margin-top: 15px;
            color: #4a65a9;
            text-decoration: none;
        }

        a:hover { text-decoration: underline; }

        .error { color: red; margin-top: 10px; }
        .success { color: green; margin-top: 10px; }

        .toggle-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            background: #4a65a9;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            transition: all 0.6s ease-in-out;
        }

        .container.active .toggle-container { transform: translateX(-100%); }

        .toggle-container button {
            background: white;
            color: #4a65a9;
            border: none;
            border-radius: 8px;
            padding: 10px 45px;
            cursor: pointer;
            font-weight: 600;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<!-- Navbar ด้านบน -->
<nav>
    <div class="logo">
        <i class="fa-solid fa-book-open-reader fa-flip-horizontal fa-2xl" style="color:#285171;"></i>
        <a href="index.php" style="text-decoration:none;"><h1>LearnHub</h1></a>
    </div>
</nav>

<!-- กล่องลงทะเบียน -->
<div class="wrapper">
    <div class="container" id="container">
        <!-- ฟอร์มนักเรียน -->
        <div class="form-container student-container">
            <form method="post" action="">
                <h1>ลงทะเบียนนักเรียน</h1>
                <?php if(isset($error_student)) echo "<p class='error'>$error_student</p>"; ?>
                <?php if(isset($_SESSION['success_student'])) { echo "<p class='success'>".$_SESSION['success_student']."</p>"; unset($_SESSION['success_student']); } ?>

                <input type="text" name="txtusername" placeholder="ชื่อผู้ใช้" required>
                <input type="email" name="txtemail" placeholder="อีเมล" required>
                <input type="password" name="txtpassword" placeholder="รหัสผ่าน" required>
                <input type="password" name="txtconfirm" placeholder="ยืนยันรหัสผ่าน" required>
                <button type="submit" name="signup_student">ลงทะเบียนนักเรียน</button>
                <a href="login.php">กลับหน้าเข้าสู่ระบบ</a>
            </form>
        </div>

        <!-- ฟอร์มติวเตอร์ -->
        <div class="form-container tutor-container">
            <form method="post" action="">
                <h1>ลงทะเบียนติวเตอร์</h1>
                <?php if(isset($error_tutor)) echo "<p class='error'>$error_tutor</p>"; ?>
                <?php if(isset($_SESSION['success_tutor'])) { echo "<p class='success'>".$_SESSION['success_tutor']."</p>"; unset($_SESSION['success_tutor']); } ?>

                <input type="text" name="txtfullname" placeholder="ชื่อ-นามสกุล" required>
                <input type="email" name="txtemail" placeholder="อีเมล" required>
                <input type="password" name="txtpassword" placeholder="รหัสผ่าน" required>
                <input type="password" name="txtconfirm" placeholder="ยืนยันรหัสผ่าน" required>
                <button type="submit" name="signup_tutor">ลงทะเบียนติวเตอร์</button>
                <a href="login.php">กลับหน้าเข้าสู่ระบบ</a>
            </form>
        </div>

        <!-- toggle -->
        <div class="toggle-container">
            <div id="togglePanel">
                <h2>คุณเป็นใคร?</h2>
                <p>เลือกเพื่อสลับระหว่าง นักเรียน และ ผู้สอน</p>
                <button id="toggleBtn">สมัครผู้สอน</button>
            </div>
        </div>
    </div>
</div>

<script>
    const container = document.getElementById('container');
    const toggleBtn = document.getElementById('toggleBtn');
    let isTutor = false;

    toggleBtn.addEventListener('click', () => {
        isTutor = !isTutor;
        container.classList.toggle('active');
        toggleBtn.textContent = isTutor ? 'สมัครนักเรียน' : 'สมัครผู้สอน';
    });
</script>
</body>
</html>
