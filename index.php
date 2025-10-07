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

    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&family=Sansation:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="style.css">

	<title>Home||LearnHub</title>
</head>
<body>
	<header>
		<nav>
			<div class="container">
				<div class="nav-wrapper">
					<div class="icon">
						<a href="#"><span class="material-symbols-outlined">book_ribbon</span></a>
						<a href="index.php"><h2>LearnHub</h2></a>
					</div>
					<ul class="menu">
						<li><a href="index.php">หน้าหลัก</a></li>
						<li><a href="login.php">เข้าสู่ระบบ</a></li>
						<li><a href="register.php">สมัครสมาชิก</a></li>
						<li><a href="admin_login.php">Admin</a></li>
					</ul>
				</div>
			</div>
		</nav>
	</header>
	<script src="script.js"></script>
</body>
</html>
