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
	<link rel="stylesheet" href="styleindex.css">
	<title>LearnHub</title>
</head>
<body>
	<header>
		<nav>
			<div class="container">
				<div class="nav-wrapper">
					<div class="logo">
						<i class="fa-solid fa-book-open-reader fa-flip-horizontal fa-2xl" style="color: #285171;"></i>
						<a href="index.php"><h1>LearnHub</h1></a>
					</div>
					<ul class="menu">
						<li><a href="index.php">หน้าหลัก</a></li>
						<li><a href="login.php">เข้าสู่ระบบ</a></li>
						<li><a href="register.php">ลงทะเบียน</a></li>
						<li><a href="admin_login.php">Admin</a></li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="discount-title">
			<div class="container">
				<div class="discount-title-wrapper">
					<div class="discount-title-box">
						<h1>ค้นหาติวเตอร์ที่ใช่ <br>
							เรียนรู้อย่างมีประสิทธิภาพ
						</h1>
						<p>แพลตฟอร์มจองติวเตอร์ออนไลน์ที่ช่วยให้คุณเชื่อมต่อกับติวเตอร์มืออาชีพ <br>
						เลือกเวลาได้ตามสะดวก และเรียนรู้ในวิชาที่คุณต้องการ
						</p>
						<a href="#" class="discount-btn">เรียนรู้เพิ่มเติม</a>
						<a href="register.php" class="logintutor-btn">ลงทะเบียน</a>
					</div>
				</div>
			</div>
		</div>
	</header>
	<script src="script.js"></script>
</body>
</html>
