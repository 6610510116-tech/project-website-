<?php
  session_start();
  include_once 'dbconnect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">	
	<link rel="stylesheet" href="style_learnmore.css">

  <title>เรียนรู้เพิ่มเติม | LearnHub</title>
</head>
<body>
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
  <div class="content-box">
    <h1>เรียนรู้เพิ่มเติมเกี่ยวกับระบบ</h1>
    <p>ระบบนี้ถูกออกแบบมาเพื่อให้ผู้ใช้สามารถลงทะเบียน เข้าสู่ระบบ และจัดการข้อมูลต่าง ๆ ได้อย่างสะดวก รวดเร็ว และปลอดภัย</p>
    <p>คุณสามารถดูรายละเอียดของการทำงาน ระบบติวเตอร์ และข้อมูลผู้ใช้ได้ในส่วนนี้</p>
    <a href="index.php" class="back">← กลับหน้าหลัก</a>
  </div>
</body>
</html>
