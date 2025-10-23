<?php
session_start();
include_once 'dbconnect.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลอาจารย์จากฐานข้อมูล (ตัวอย่าง)
$courses_result = mysqli_query($conn, "SELECT * FROM courses ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styleindex.css">
<title>LearnHub - Dashboard</title>
<style>
body { font-family: 'Kanit', sans-serif; margin:0; padding:0; background:#f0f4f8; }
header nav { background:#4a65a9; color:white; padding:15px 50px; display:flex; justify-content:space-between; align-items:center; }
header nav a { color:white; text-decoration:none; margin-left:15px; }
.container { width:90%; margin:auto; padding:20px 0; }
.courses-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:20px; }
.course-card { background:white; border-radius:10px; overflow:hidden; box-shadow:0 3px 10px rgba(0,0,0,0.1); text-align:center; padding-bottom:15px; }
.course-card img { width:100%; height:180px; object-fit:cover; }
.course-card h3 { margin:10px 0 5px; font-size:18px; color:#333; }
.course-card p { margin:3px 0; color:#555; font-size:14px; }
.categories { display:grid; grid-template-columns: repeat(4, 1fr); gap:15px; margin-top:40px; }
.category-box { background:#4a65a9; color:white; padding:20px; border-radius:10px; text-align:center; font-weight:600; cursor:pointer; transition:0.3s; }
.category-box:hover { background:#2e427f; }
</style>
</head>
<body>

<header>
    <nav>
        <div class="logo">
            <i class="fa-solid fa-book-open-reader fa-2xl"></i>
            <a href="index2.php"><h1>LearnHub</h1></a>
        </div>
        <div>
            <span>สวัสดี, <?= htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php">ออกจากระบบ</a>
        </div>
    </nav>
</header>

<div class="container">
    <h2>คอร์สแนะนำ</h2>
    <div class="courses-grid">
        <?php while ($course = mysqli_fetch_assoc($courses_result)) { ?>
        <div class="course-card">
            <img src="uploads/<?= htmlspecialchars($course['image']); ?>" alt="<?= htmlspecialchars($course['name']); ?>">
            <h3><?= htmlspecialchars($course['teacher_name']); ?></h3>
            <p>วิชา: <?= htmlspecialchars($course['subject']); ?></p>
            <p>ราคา: <?= number_format($course['price'], 0); ?> บาท</p>
        </div>
        <?php } ?>
    </div>

    <h2 style="margin-top:50px;">หมวดหมู่</h2>
    <div class="categories">
        <div class="category-box">ดนตรี</div>
        <div class="category-box">ศิลปะ</div>
        <div class="category-box">คณิตศาสตร์</div>
        <div class="category-box">วิทยาศาสตร์</div>
        <div class="category-box">คอมพิวเตอร์</div>
        <div class="category-box">สังคมศาสตร์</div>
        <div class="category-box">ภาษาต่างประเทศ</div>
        <div class="category-box">บริหารธุรกิจ</div>
    </div>
</div>

</body>
</html>
