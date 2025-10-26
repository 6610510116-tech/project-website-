<?php
    session_start();
    include_once 'dbconnect.php';
?><?php
    session_start();
    include_once 'dbconnect.php';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LearnHub</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet"> 	
    
    <link rel="stylesheet" href="styleindex.css">

    <style>
        /* Container for better control */
        .container {
            width: 90%;
            max-width: 1300px;
            margin: 0 auto;
        }

        /* --- Category Boxes Section (กล่องข้อความหัวข้อ) --- */
        .category-section {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 40px 0;
            background-color: #f7f9fc;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 50px;
        }

        .category-card {
            flex: 1;
            max-width: 220px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            border-top: 5px solid #285171;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .category-card i {
            font-size: 2.5rem;
            color: #285171;
            margin-bottom: 10px;
        }

        .category-card h3 {
            font-family: 'Kanit', sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        /* --- Gallery Section (Swiper Slider) --- */
        .gallery-section {
            padding: 0 0 80px 0;
            text-align: center;
        }

        .gallery-section h2 {
            font-family: 'Kanit', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: #285171;
            margin-bottom: 30px;
        }

        .swiper {
            width: 90%;
            max-width: 1200px;
            height: 450px; /* กำหนดความสูงของ Gallery */
            margin: 0 auto;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .swiper-slide {
            text-align: center;
            font-size: 18px;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .swiper-slide img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Swiper Navigation/Pagination Colors */
        .swiper-button-next, .swiper-button-prev {
            color: white !important;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px 10px;
            border-radius: 50px;
            transition: background-color 0.3s;
        }
        .swiper-button-next:hover, .swiper-button-prev:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }
        .swiper-pagination-bullet-active {
            background: #285171 !important;
        }


        /* Responsive adjustments */
        @media (max-width: 992px) {
            .category-section {
                flex-wrap: wrap;
            }
            .category-card {
                flex: 1 1 40%; /* 2 คอลัมน์ */
                margin-bottom: 20px;
            }
        }
        @media (max-width: 600px) {
            .category-card {
                flex: 1 1 100%; /* 1 คอลัมน์ */
            }
            .swiper {
                height: 250px;
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

<section class="category-section">
    <div class="category-card" onclick="window.location.href='login.php?category=คณิตศาสตร์'">
        <i class="fa-solid fa-calculator"></i>
        <h3>คณิตศาสตร์</h3>
    </div>
    <div class="category-card" onclick="window.location.href='login.php?category=ภาษาต่างประเทศ'">
        <i class="fa-solid fa-language"></i>
        <h3>ภาษาต่างประเทศ</h3>
    </div>
    <div class="category-card" onclick="window.location.href='login.php?category=วิทยาศาสตร์'">
        <i class="fa-solid fa-flask"></i> <h3>วิทยาศาสตร์</h3>
    </div>
    <div class="category-card" onclick="window.location.href='login.php?category=ศิลปะ'">
        <i class="fa-solid fa-palette"></i>
        <h3>ศิลปะ</h3>
    </div>
    <div class="category-card" onclick="window.location.href='login.php?category=โปรแกรม'">
        <i class="fa-solid fa-code"></i>
        <h3>โปรแกรม</h3>
    </div>
</section>

<section class="gallery-section">
    <h2>ภาพบรรยากาศการเรียนรู้</h2>
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="https://www.aksorn.com/storage/upload/images/iStock-178422309.t5b470954.m600.x0939bf06.jpg" alt="บรรยากาศในห้องเรียน"></div>
            <div class="swiper-slide"><img src="https://www.gourmetandcuisine.com/Images/editor_upload/_editor20231024040159_original.jpg" alt="การติวเข้ม"></div>
            <div class="swiper-slide"><img src="https://img.global.news.samsung.com/th/wp-content/uploads/2022/04/SIC2022_Coding-Skills-3.jpg" alt="ลงมือทำจริง"></div>
            <div class="swiper-slide"><img src="https://mtimakeupschool.com/wp-content/uploads/2023/09/banner3-sub1-1024x681.jpg" alt="เพิ่มศักยภาพ"></div>
            <div class="swiper-slide"><img src="https://jeducation.com/main/wp-content/uploads/2016/12/Untitled-1-1.png" alt="พร้อมดูเเลอนาคตของคุณ"></div>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</section>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<script>
    // Initialize Swiper
    var swiper = new Swiper(".mySwiper", {
        loop: true, // เลื่อนวน
        spaceBetween: 30,
        centeredSlides: true,
        autoplay: {
            delay: 4000, // เปลี่ยนภาพทุก 4 วินาที
            disableOnInteraction: false,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        // Responsive breakpoints
        breakpoints: {
            // เมื่อหน้าจอ >= 640px
            640: {
                slidesPerView: 1,
                spaceBetween: 20,
            },
            // เมื่อหน้าจอ >= 768px
            768: {
                slidesPerView: 2, // แสดง 2 รูป
                spaceBetween: 30,
            },
            // เมื่อหน้าจอ >= 1024px
            1024: {
                slidesPerView: 3, // แสดง 3 รูป
                spaceBetween: 40,
            },
        }
    });
</script>

</body>
</html>

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
