<?php
    session_start();
    include_once 'dbconnect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">	
    <link rel="stylesheet" href="style_login.css">
    <title>LERNHUB Login Page</title>
</head>
<body>
    <nav>
        <div class="nav-wrapper">
            <div class="logo">
                <i class="fa-solid fa-book-open-reader fa-flip-horizontal fa-2xl" style="color: #285171;"></i>
                <a href="index.php"><h1>LearnHub</h1></a>
            </div>
        </div>
    </nav>
<div class="container" id="container">
    <div class="form-container sign in">
        <form>
            
            <h2>เข้าสู่ระบบสำหรับนักเรียน</h2>
            <div class="social-icons">
                <a href="#" class="icon"><i class="fa-brands fa-facebook"></i></a>
                <a href="#" class="icon"><i class="fa-brands fa-google"></i></a>
            </div>
            <input type="email" placeholder="Email" name="txtemail" required_class="form-control">
            <input type="password" placeholder="Password" name="txtpassword" required_class="form-control">
            <div class="link-row">
                <a href="#">ลืมรหัสผ่าน</a>
                <span>||</span>
                <a href="register.php">สมัครสมาชิก</a>
            </div>
            <button>Login</button>
            <a href="#">เข้าสู่ระบบสำหรับติวเตอร์</a>
        </form>
    </div>
    <div class="image-container">
        <img src="https://cdn.pixabay.com/photo/2015/12/15/06/42/merry-christmas-1093758_1280.jpg" alt="login image" style="width:100%; height:100%; object-fit: cover; right:50%;">
    </div>
</div>
    <script>
        const container = document.getElementById('container');
        const loginbtn = document.getElementById('loginbtn');
    </script>
</body>
</html>
