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
	<link rel="stylesheet" href="style.css">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">	
	<style>
		:root {
			--4a65a9: #4a65a9;
			--d3dbee: #d3dbee;
			--6b96b9: #6b96b9;
			--b7dee0: #b7dee0;
		}
		
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
			font-family: 'Kanit', sans-serif;
		}
		
		.container {
			max-width: 1320px;
			margin: auto;
			padding: 0 20px;
		}
		
		/* Header Section */
		header {
			height: 800px;
			background: linear-gradient(to bottom, var(--d3dbee), #ffffff);
			position: relative;
			text-align: center;
		}
		
		/* -------- NAVIGATION -------- */
		nav {
			width: 100%;
		}
		
		.nav-wrapper {
			position: relative;
			height: 200px;
			display: flex;
			justify-content: space-between;
			align-items: center;
			top: 25px;
		}
		
		.logo h1 {
			font-size: 55px;
			color: var(--4a65a9);
		}
		
		.logo a {
			text-decoration: none;
		}
		.logo i {
			display: inline;
		}
		
		.menu {
			display: flex;
			list-style: none;
		}
		
		.menu li {
			margin-left: 40px;
		}
		
		.menu a {
			text-decoration: none;
			color: var(--4a65a9);
			font-size: 18px;
			transition: 0.3s;
		}
		
		.menu a:hover {
			color: var(--6b96b9);
		}
		
		/* -------- TITLE SECTION -------- */
		.discount-title {
			display: flex;
			justify-content: center;
			align-items: center;
			height: calc(100% - 200px); /* ให้ข้อความอยู่กลางหลัง header */
		}
		
		.discount-title-wrapper {
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			text-align: center;
			width: 100%;
		}
		
		.discount-title-box {
			max-width: 1000px;
			text-align: center;
			padding: 0 20px;
		}
		
		/* ✅ ตรงนี้คือส่วนเพิ่มขนาดฟอนต์ */
		.discount-title-box h1 {
			font-size: 80px;  /* ปรับขนาดฟอนต์ตรงนี้ได้เลย */
			line-height: 1.2;
			font-weight: 700;
			color: var(--4a65a9);
		}
		
		.discount-title-box p {
			font-size: 22px;
			margin-top: 20px;
			color: #333;
			line-height: 1.6;
		}
		
		.discount-btn,
		.logintutor-btn {
			display: inline-block;
			margin-top: 25px;
			margin-right: 10px;
			padding: 10px 25px;
			background-color: var(--4a65a9);
			color: #fff;
			text-decoration: none;
    		border-radius: 6px;
    		transition: 0.3s;
		}

		.discount-btn:hover,
		.logintutor-btn:hover {
			background-color: var(--6b96b9);
		}
	</style>
</head>
<body>
	<header>
		<nav>
			<div class="container">
				<div class="nav-wrapper">
					<i class="fa-solid fa-book-open-reader fa-flip-horizontal fa-2xl" style="color: #285171;"></i>
					<div class="logo">
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
						<a href="#" class="logintutor-btn">ลงทะเบียน</a>
					</div>
				</div>
			</div>
		</div>
	</header>
	<script src="script.js"></script>
</body>
</html>
