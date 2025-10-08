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
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&family=Sansation:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
	<title>Home||LearnHub</title>
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
			font-family: 'Nonto Sans Thai', sans-serif;
			font-family: 'Sansation', sans-serif;
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

			nav {
				.nav-wrapper {
					position : relative;
					height: 200px;
					display: flex;
					justify-content: space-between;
					top: 25px;

					.logo h1 {
						font-weight: bold;
						font-size: 50px;
						color: var(--4a65a9);
					}

					.menu {
						display: flex;
						list-style: none;

						li {
							margin-left: 40px;

							a {
								text-decoration: none;
								color: var(--4a65a9);
								font-size: 18px;
								transition: 0.3s;

								&:hover {
									color: var(--6b96b9);
								}
							}
						}
					}
				}

			}
		}

	</style>
</head>
<body>
	<header>
		<nav>
			<div class="container">
				<div class="nav-wrapper">
					<div class="logo">
						<a href="index.php"><h1>LearnHub</h1></a>
					</div>
					<ul class="menu">
						<li><a href="index.php">หน้าหลัก</a></li>
						<li><a href="login.php">เข้าสู่ระบบ</a></li>
						<li><a href="register.php">สมัครสมาชิก</a></li>
						<li><a href="admin_login.php">Admin</a></li>
					</ul>
					<div class="hamburger">
						<i class="fas fa-bars	"></i>
					</div>
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
						เลือกเวลาได้ตามสะดวก และเรียนรู็ในวิชาที่คุณต้องการ
						</p>
						<a href="#" class="discount-btn">เรียนรู้เพิ่มเติม</a>
						<a href="#" class="logintutor-btn">สมัครเป็นติวเตอร์</a>
					</div>
				</div>
			</div>
		</div>
	</header>
	<script src="script.js"></script>
</body>
</html>
