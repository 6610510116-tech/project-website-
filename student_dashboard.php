<?php
session_start();
include_once 'dbconnect.php'; 

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // ใช้สำหรับตะกร้าสินค้า
$username = $_SESSION['user_name'] ?? 'นักเรียน'; 
$message = ''; 
$category_name = '';
$courses_by_category = [];

// ตรวจสอบว่ามีการส่งค่า category มาหรือไม่
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category_name = mysqli_real_escape_string($conn, $_GET['category']);

    // ดึงคอร์สทั้งหมดในหมวดหมู่นั้น
    // ดึงคอลัมน์ทั้งหมด รวมถึง 'picture'
    $sql = "SELECT id, subject, price, time, name, picture FROM tutor_courses WHERE category = '$category_name' ORDER BY subject ASC";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $courses_by_category[] = $row;
        }
    } else {
        $message = "<div class='error'>เกิดข้อผิดพลาดในการดึงข้อมูล: " . mysqli_error($conn) . "</div>";
    }

} else {
    // ถ้าไม่มีการระบุหมวดหมู่
    $category_name = "ไม่พบหมวดหมู่";
}

// นับจำนวนในตะกร้า (เผื่อไว้ใช้งาน)
$count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE user_id=$user_id AND status='pending'");
$count_row = mysqli_fetch_assoc($count_result);
$cart_count = $count_row['total'] ?? 0; // เพิ่ม ?? 0 เพื่อจัดการกรณีที่ไม่มีผลลัพธ์
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คอร์ส: <?= htmlspecialchars($category_name); ?> | LearnHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* CSS เหมือนเดิม */
        body { font-family: 'Kanit', sans-serif; background: #f0f4f8; margin: 0; }
        nav { background: #4a65a9; color: white; display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        nav a { color: white; text-decoration: none; margin-left: 20px; transition: color 0.2s; }
        .container { width: 95%; max-width: 1400px; margin: auto; padding: 30px 0; }
        h2 { color: #4a65a9; font-weight: 600; margin-bottom: 25px; padding-left: 10px; border-left: 5px solid #4a65a9; }
        .courses-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px; margin-bottom: 60px; }
        .course-card { background: white; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden; text-align: center; transition: transform 0.2s ease-in-out; display: flex; flex-direction: column; height: 100%; }
        .course-card img { width: 100%; height: 180px; object-fit: cover; }
        .course-info { padding: 15px; flex-grow: 1; display: flex; flex-direction: column; align-items: center; }
        .course-info h3 { margin: 5px 0 5px; font-size: 18px; color: #4a65a9; font-weight: 700; }
        .course-info p { margin: 4px 0; color: #666; font-size: 14px; }
        .course-info button { margin-top: auto; background: #5cb85c; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; color: white; transition: 0.3s; }
        .course-info button:hover { background: #4cae4c; }
        .error { background-color: #f2dede; color: #a94442; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 20px; }
    </style>
</head>

<body>

<nav>
    <div class="left">
        <a href="student_dashboard.php" style="margin-left:0;"><i class="fa-solid fa-arrow-left fa-xl"></i></a>
        <h1 style="display:inline-block;">คอร์สหมวดหมู่: <?= htmlspecialchars($category_name); ?></h1>
    </div>
    <div class="right">
        <a href="#"><span>👋 สวัสดี, <?= htmlspecialchars($username); ?></span>
        <a href="cart.php" class="cart-icon">
            <i class="fa-solid fa-cart-shopping"></i>
            <?php if($cart_count > 0): ?>
            <span class="badge"><?= $cart_count ?></span>
            <?php endif; ?>
        </a>
        <a href="logout.php">ออกจากระบบ</a>
    </div>
</nav>

<div class="container">
    <?= $message; ?>

    <div class="courses-grid">
        <?php
        if (!empty($courses_by_category)) {
            foreach ($courses_by_category as $course) {
        ?>
        <div class="course-card">
            <img src="picture/<?= htmlspecialchars($course['picture'] ?? 'default.jpg'); ?>"
                 alt="<?= htmlspecialchars($course['subject'] ?? 'วิชา'); ?>"
                 onerror="this.onerror=null;this.src='picture/default.jpg';"
            >
            <div class="course-info">
                <h3><?= htmlspecialchars($course['subject'] ?? 'ไม่มีชื่อวิชา'); ?></h3>
                <p><strong>ผู้สอน:</strong> <?= htmlspecialchars($course['name'] ?? '-'); ?></p>
                <p><strong>เวลา:</strong> <?= htmlspecialchars($course['time'] ?? '-'); ?></p>
                <p><strong>ราคา:</strong> <span style="font-weight:700; color:#ff4d4d;"><?= number_format($course['price'] ?? 0, 0); ?> บาท</span></p>
                
                <form method="post" action="student_dashboard.php">
                    <input type="hidden" name="add_to_cart" value="1">
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?? 0; ?>"> 
                    <button type="submit">
                        <i class="fas fa-cart-plus"></i> เพิ่มลงตะกร้า
                    </button>
                </form>
            </div>
        </div>
        <?php
            }
        } else {
            echo "<p style='text-align:center; grid-column: 1 / -1;'>ไม่พบคอร์สในหมวดหมู่ <strong>" . htmlspecialchars($category_name) . "</strong> ในขณะนี้</p>";
        }
        ?>
    </div>
</div>

</body>
</html> 
