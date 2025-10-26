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
						<a href="learnmore.php" class="discount-btn">เรียนรู้เพิ่มเติม</a>
						<a href="register.php" class="logintutor-btn">ลงทะเบียน</a>
					</div>
				</div>
			</div>
		</div>
	</header>
	<!-- SUBJECT TABS -->
<div class="container subject-tabs">
    <div class="tab-menu">
        <div class="tab-item active" data-tab="math">
            <i class="fa-solid fa-calculator"></i>
            <span>คณิตศาสตร์</span>
        </div>
        <div class="tab-item" data-tab="language">
            <i class="fa-solid fa-language"></i>
            <span>ภาษาต่างประเทศ</span>
        </div>
        <div class="tab-item" data-tab="science">
            <i class="fa-solid fa-atom"></i>
            <span>วิทยาศาสตร์</span>
        </div>
        <div class="tab-item" data-tab="art">
            <i class="fa-solid fa-palette"></i>
            <span>ศิลปะ</span>
        </div>
        <div class="tab-item" data-tab="program">
            <i class="fa-solid fa-code"></i>
            <span>โปรแกรม</span>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-panel active" data-tab="math">
			<div class="subject-item"><img src="https://cdn.pixabay.com/photo/2023/01/10/03/57/digits-7708860_1280.jpg"><p>พื้นฐาน</p></div>
            <div class="subject-item"><img src="https://cdn.pixabay.com/photo/2020/10/11/17/20/icon-5646465_1280.jpg"><p>สถิติ</p></div>
            <div class="subject-item"><img src="https://cdn.pixabay.com/photo/2022/09/27/05/34/approximation-7482129_1280.jpg"><p>แคลคูลัส</p></div>
        </div>
        <div class="tab-panel" data-tab="language">
            <div class="subject-item"><img src="https://cdn.pixabay.com/photo/2017/01/31/08/23/british-2023201_1280.png"><p>ภาษาอังกฤษ</p></div>
            <div class="subject-item"><img src="https://cdn.pixabay.com/photo/2016/11/07/14/03/japan-1805865_1280.jpg"><p>ภาษาญี่ปุ่น</p></div>
			<div class="subject-item"><img src="https://cdn.pixabay.com/photo/2024/02/07/14/04/ai-generated-8559123_1280.jpg"><p>ภาษาจีน</p></div>
			<div class="subject-item"><img src="https://cdn.pixabay.com/photo/2014/04/15/16/53/reichstag-324982_1280.jpg"><p>ภาษาเยอรมัน</p></div>
			<div class="subject-item"><img src="https://cdn.pixabay.com/photo/2021/11/04/21/59/episkopeio-6769484_1280.jpg"><p>ภาษารัสเซีย</p></div>
        </div>
        <div class="tab-panel" data-tab="science">
            <div class="subject-item"><img src="https://cdn.pixabay.com/photo/2022/01/14/07/50/physics-6936704_1280.jpg"><p>ฟิสิกส์</p></div>
            <div class="subject-item"><img src="https://cdn.pixabay.com/photo/2018/07/15/10/44/dna-3539309_1280.jpg"><p>เคมี</p></div>
            <div class="subject-item"><img src="https://cdn.pixabay.com/photo/2020/03/18/07/25/monkey-4943051_1280.jpg"><p>ชีวภาพ</p></div>
        </div>
        <div class="tab-panel" data-tab="art">
            <div class="subject-item"><img src="https://cdn.pixabay.com/photo/2023/07/14/18/23/color-pencils-8127500_1280.jpg"><p>วาดภาพ</p></div>
            <div class="subject-item"><img src="https://cdn.pixabay.com/photo/2022/11/28/18/58/piano-7622920_1280.jpg"><p>ดนตรี</p></div>
            <div class="subject-item"><img src="https://cdn.pixabay.com/photo/2014/10/31/17/41/dancing-dave-minion-510835_1280.jpg"><p>อนิเมชัน</p></div>
            <div class="subject-item"><img src="https://cdn.pixabay.com/photo/2017/11/08/22/28/camera-2931883_1280.jpg"><p>ถ่ายรูป</p></div>
        </div>
        <div class="tab-panel" data-tab="program">
            <div class="subject-item"><img src="https://cdn.pixabay.com/photo/2016/07/13/08/48/mobile-phone-1513945_1280.jpg"><p>Python</p></div>
            <div class="subject-item"><img src="https://cdn.pixabay.com/photo/2020/09/27/13/15/data-5606639_1280.jpg"><p>HTML/CSS</p></div>
            <div class="subject-item"><img src="https://cdn.pixabay.com/photo/2020/04/25/15/38/codes-5091352_1280.png"><p>JavaScipt</p></div>
            <div class="subject-item"><img src="https://cdn.pixabay.com/photo/2024/10/02/14/52/hack-9091214_1280.png"><p>C#</p></div>
        </div>
    </div>
</div>

<script>
// --- Tabs JS ---
const tabs = document.querySelectorAll('.tab-item');
const panels = document.querySelectorAll('.tab-panel');

tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        panels.forEach(p => p.classList.remove('active'));
        document.querySelector(`.tab-panel[data-tab="${tab.dataset.tab}"]`).classList.add('active');
    });
});
</script>

</body>
</html>
