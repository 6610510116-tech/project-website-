<?php
session_start();
include_once 'dbconnect.php';

if (isset($_POST['signup'])) {
    $username = mysqli_real_escape_string($conn, $_POST['txtusername']);
    $email = mysqli_real_escape_string($conn, $_POST['txtemail']);
    $password = mysqli_real_escape_string($conn, $_POST['txtpassword']);
    $confirm = mysqli_real_escape_string($conn, $_POST['txtconfirm']);

    if ($password !== $confirm) {
        $error = "รหัสผ่านไม่ตรงกัน!";
    } else {
        $check_email = mysqli_query($conn, "SELECT email FROM users WHERE email='$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $error = "อีเมลนี้ถูกใช้แล้ว!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users(username, email, password) VALUES('$username', '$email', '$hashed')";
            if (mysqli_query($conn, $query)) {
                $_SESSION['success'] = "สมัครสมาชิกสำเร็จ! โปรดเข้าสู่ระบบ";
                header("Location: login.php");
                exit();
            } else {
                $error = "เกิดข้อผิดพลาดในการบันทึกข้อมูล!";
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
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {box-sizing: border-box; margin: 0; padding: 0; font-family: 'Kanit', sans-serif;}
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
        .nav-wrapper {height: 100px; display: flex; justify-content: space-between; align-items: center; padding: 0 40px;}
        .logo {display: flex; align-items: center; gap: 10px; margin-left: 77px;}
        .logo h1 {font-size: 55px; color: #4a65a9;}
        .container {
            background: white; border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            width: 768px; min-height: 480px;
            display: flex; overflow: hidden; margin-top: 100px;
        }
        form {
            width: 50%; padding: 50px;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
        }
        h1 {color: #393f86; margin-bottom: 20px;}
        input {
            background: #eee; border: none; margin: 8px 0; padding: 12px 15px;
            border-radius: 8px; width: 100%;
        }
        button {
            background: #6b96b9; color: white; border: none;
            border-radius: 8px; padding: 10px 45px; cursor: pointer;
            margin-top: 10px; font-weight: 600; text-transform: uppercase;
        }
        a {color: #4a65a9; text-decoration: none; font-size: 13px; margin-top: 15px;}
        .image-container {width: 50%;}
        .image-container img {width: 100%; height: 100%; object-fit: cover;}
        .error {color: red; margin: 10px 0;}
        .success {color: green; margin: 10px 0;}
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

<div class="container">
    <form method="post" action="">
        <h1>ลงทะเบียนผู้ใช้ใหม่</h1>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if(isset($_SESSION['success'])) { echo "<p class='success'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); } ?>

        <input type="text" name="txtusername" placeholder="ชื่อผู้ใช้" required>
        <input type="email" name="txtemail" placeholder="อีเมล" required>
        <input type="password" name="txtpassword" placeholder="รหัสผ่าน" required>
        <input type="password" name="txtconfirm" placeholder="ยืนยันรหัสผ่าน" required>

        <button type="submit" name="signup">ลงทะเบียน</button>
        <a href="login.php">กลับไปหน้าเข้าสู่ระบบ</a>
    </form>

    <div class="image-container">
        <img src="https://mademindday.com/wp-content/uploads/2020/04/studying-online-YDRP5K3-scaled.jpg" width="400">

    </div>
</div>
</body>
</html>
