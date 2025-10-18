<?php
session_start();
include_once 'dbconnect.php';

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
            $query = "INSERT INTO users(username, email, password) VALUES('$username', '$email', '$hashed')";
            if (mysqli_query($conn, $query)) {
                $_SESSION['success_student'] = "สมัครนักเรียนสำเร็จ! โปรดเข้าสู่ระบบ";
            } else {
                $error_student = "เกิดข้อผิดพลาดในการบันทึกข้อมูล!";
            }
        }
    }
}

// ลงทะเบียนผู้สอน
if (isset($_POST['signup_tutor'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['txtfullname']);
    $email = mysqli_real_escape_string($conn, $_POST['txtemail']);
    $password = mysqli_real_escape_string($conn, $_POST['txtpassword']);
    $confirm = mysqli_real_escape_string($conn, $_POST['txtconfirm']);

    if ($password !== $confirm) {
        $error_tutor = "รหัสผ่านไม่ตรงกัน!";
    } else {
        $check_email = mysqli_query($conn, "SELECT email FROM tutors WHERE email='$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $error_tutor = "อีเมลนี้ถูกใช้แล้ว!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO tutors(fullname, email, password) VALUES('$fullname', '$email', '$hashed')";
            if (mysqli_query($conn, $query)) {
                $_SESSION['success_tutor'] = "สมัครติวเตอร์สำเร็จ! โปรดเข้าสู่ระบบ";
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
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Kanit', sans-serif; }

.container {
    background: white;
    border-radius: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    width: 800px;
    min-height: 480px;
    overflow: hidden;
    position: relative;
    margin-top: 40px; /*  ขยับกล่องลงเล็กน้อย */
}


        body {
            background: linear-gradient(to bottom, #d3dbee, #ffffff);
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; height: 100vh;
        }

        nav {
            width: 100%; background: rgba(255,255,255,0.9);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: sticky; top: 0; z-index: 1000;
            margin-top: -150px;
        }

        .nav-wrapper { height: 100px; display: flex; justify-content: space-between; align-items: center; padding: 0 40px; }
        .logo { display: flex; align-items: center; gap: 10px; margin-left: 77px; }
        .logo h1 { font-size: 55px; color: #4a65a9; }

        .container {
            background: white;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            width: 800px;
            min-height: 480px;
            overflow: hidden;
            position: relative;
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

        /* การเลื่อนแบบ slide */
        .student-container { left: 0; }
        .tutor-container { left: 100%; }
        .container.active .student-container { transform: translateX(-100%); }
        .container.active .tutor-container { transform: translateX(-100%); }

        form input {
            background: #eee; border: none;
            margin: 8px 0; padding: 12px 15px;
            border-radius: 8px; width: 100%;
        }

        form button {
            background: #6b96b9; color: white; border: none;
            border-radius: 8px; padding: 10px 45px; cursor: pointer;
            margin-top: 10px; font-weight: 600; text-transform: uppercase;
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
        .toggle-container h2 { margin-bottom: 20px; }
        .toggle-container button {
            background: white; color: #4a65a9;
            border: none; border-radius: 8px;
            padding: 10px 45px; cursor: pointer;
            font-weight: 600; text-transform: uppercase;
        }
    </style>
</head>
<body>
<nav>
    <div class="nav-wrapper">
        <div class="logo">
            <i class="fa-solid fa-book-open-reader fa-flip-horizontal fa-2xl" style="color:#285171;"></i>
            <a href="index.php"><h1>LearnHub</h1></a>
        </div>
    </div>
</nav>

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
            <a href="tutor_login.php">กลับหน้าเข้าสู่ระบบ</a>
        </form>
    </div>

    <!-- toggle -->
    <div class="toggle-container">
        <div id="togglePanel">
            <h2>คุณเป็นใคร?</h2>
            <p>เลือกเพื่อสลับหน้าระหว่าง นักเรียน และ ผู้สอน</p>
            <button id="toggleBtn">สมัครผู้สอน</button>
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
