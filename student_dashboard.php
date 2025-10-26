<?php
session_start();
include_once 'dbconnect.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'นักเรียน'; 
$message = ''; 

// --- ส่วนเพิ่มคอร์สลงตะกร้า (แก้ไขให้ใช้ tutor_id และจัดการ Redirect) ---
if (isset($_POST['add_to_cart'])) {
    $course_id = intval($_POST['course_id']); // $course_id คือ ID ของคอร์ส (tutor_courses.id)
    
    if ($course_id > 0) {
        // ดึงข้อมูลเวลาจากคอร์ส
        $course_data_sql = "SELECT time FROM tutor_courses WHERE id = $course_id";
        $course_data_result = mysqli_query($conn, $course_data_sql);
        $course_data = mysqli_fetch_assoc($course_data_result);

        // ดึงค่า time มาใช้ หรือใช้ 'N/A' ถ้าไม่มี
        $start_time = mysqli_real_escape_string($conn, $course_data['time'] ?? 'N/A');
        $end_time = 'N/A';
        
        // 1. ตรวจสอบว่าคอร์สนี้อยู่ในตะกร้าแล้วหรือยัง (ใช้ tutor_id ในตาราง bookings)
        $check_sql = "SELECT id FROM bookings WHERE user_id=$user_id AND tutor_id=$course_id AND status='pending'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
            // คอร์สอยู่ในตะกร้าแล้ว
            header("Location: student_dashboard.php?added=already_exists");
            exit();
        } else {
            // 2. เพิ่มลงในตะกร้า (ใช้ tutor_id)
            $insert_sql = "INSERT INTO bookings (user_id, tutor_id, booking_date, start_time, end_time, status)
                           VALUES ($user_id, $course_id, CURDATE(), '$start_time', '$end_time', 'pending')";
            
            if (mysqli_query($conn, $insert_sql)) {
                // เพิ่มสำเร็จ
                header("Location: student_dashboard.php?added=success");
                exit();
            } else {
                // เพิ่มไม่สำเร็จ
                $error_msg = urlencode("เกิดข้อผิดพลาดในการเพิ่มลงตะกร้า: " . mysqli_error($conn));
                header("Location: student_dashboard.php?added=error&details={$error_msg}");
                exit();
            }
        }
    }
}
// --- จบส่วนเพิ่มคอร์สลงตะกร้า ---

// --- ส่วนจัดการข้อความแจ้งเตือน (ต้องอยู่ก่อน <body>) ---
if (isset($_GET['added'])) {
    $status = $_GET['added'];
    if ($status == 'success') {
        $message = '<script>alert("✅ เพิ่มคอร์สลงในตะกร้าเรียบร้อยแล้ว!");</script>';
    } elseif ($status == 'already_exists') {
        $message = '<script>alert("⚠️ คอร์สนี้อยู่ในตะกร้าของคุณแล้ว!");</script>';
    } elseif ($status == 'error') {
        $details = htmlspecialchars($_GET['details'] ?? 'ไม่ทราบสาเหตุ');
        $message = '<script>alert("❌ การเพิ่มลงตะกร้าล้มเหลว: ' . $details . '");</script>';
    }
}
// --- จบส่วนจัดการข้อความแจ้งเตือน ---


// --- ส่วนค้นหาวิชา, ดึงคอร์สแนะนำ, ดึงหมวดหมู่ (เหมือนเดิม) ---
$search_query = '';
$current_category = '';
$courses_result = null;

if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $search_query = mysqli_real_escape_string($conn, trim($_GET['search']));

    $category_search_sql = "SELECT DISTINCT category FROM tutor_courses WHERE subject LIKE '%$search_query%' LIMIT 1";
    $category_search_result = mysqli_query($conn, $category_search_sql);
    
    if ($category_search_result && mysqli_num_rows($category_search_result) > 0) {
        $category_row = mysqli_fetch_assoc($category_search_result);
        $current_category = mysqli_real_escape_string($conn, $category_row['category']);
    }
    
    if ($current_category) {
        $courses_result = mysqli_query($conn, 
            "SELECT id, subject, price, time, name, picture FROM tutor_courses WHERE category = '$current_category' ORDER BY subject ASC");
    } else {
        $courses_result = mysqli_query($conn, 
            "SELECT id, subject, price, time, name, picture FROM tutor_courses WHERE subject LIKE '%$search_query%' ORDER BY id DESC");
    }

} else {
    $courses_result = mysqli_query($conn, "SELECT id, subject, price, time, name, picture FROM tutor_courses WHERE is_featured = 1 ORDER BY id DESC LIMIT 5");
}

$category_sql = "SELECT DISTINCT category FROM tutor_courses WHERE category IS NOT NULL AND category != '' ORDER BY category ASC";
$category_result = mysqli_query($conn, $category_sql);

$categories = [];
if ($category_result && mysqli_num_rows($category_result) > 0) {
    while ($cat = mysqli_fetch_assoc($category_result)) {
        $categories[] = trim($cat['category']);
    }
}

$fallback_cats = ['ดนตรี', 'ศิลปะ', 'คณิตศาสตร์', 'วิทยาศาสตร์', 'คอมพิวเตอร์', 'ภาษาต่างประเทศ', 'สังคมศาสตร์', 'บริหารการจัดการ', 'ทำอาหาร', 'ออกกำลังกาย', 'กราฟฟิก', 'เสริมสวย'];
$categories = array_unique(array_merge($categories, $fallback_cats));

$count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE user_id=$user_id AND status='pending'");
$count_row = mysqli_fetch_assoc($count_result);
$cart_count = $count_row['total'] ?? 0;

// --- ฟังก์ชันไอคอนหมวดหมู่ (เหมือนเดิม) ---
function getCategoryIcon($category) {
    if (strpos($category, 'คณิตศาสตร์') !== false) return 'fa-calculator';
    elseif (strpos($category, 'วิทยาศาสตร์') !== false) return 'fa-flask';
    elseif (strpos($category, 'ภาษา') !== false) return 'fa-globe-asia';
    elseif (strpos($category, 'ดนตรี') !== false) return 'fa-music';
    elseif (strpos($category, 'ศิลปะ') !== false) return 'fa-palette';
    elseif (strpos($category, 'คอมพิวเตอร์') !== false) return 'fa-laptop-code';
    elseif (strpos($category, 'ทำอาหาร') !== false) return 'fa-utensils';
    elseif (strpos($category, 'ออกกำลังกาย') !== false) return 'fa-dumbbell';
    elseif (strpos($category, 'กราฟฟิก') !== false) return 'fa-vector-square';
    elseif (strpos($category, 'เสริมสวย') !== false) return 'fa-spa';
    elseif (strpos($category, 'สังคมศาสตร์') !== false) return 'fa-users';
    elseif (strpos($category, 'บริหารการจัดการ') !== false) return 'fa-chart-line';
    elseif (strpos($category, 'ธุรกิจ') !== false || strpos($category, 'บริหาร') !== false) return 'fa-briefcase';
    else return 'fa-book-open';
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LearnHub - Student Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
/* CSS เหมือนเดิม */
body { font-family: 'Kanit', sans-serif; background: #f0f4f8; margin: 0; }
nav { background: #4a65a9; color: white; display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
nav a { color: white; text-decoration: none; margin-left: 20px; transition: color 0.2s; }
nav a:hover { color: #f1f1f1; }
nav h1 { display: inline; margin-left: 10px; font-size: 24px; font-weight: 700; }
.right { display: flex; align-items: center; }
.cart-icon { position: relative; font-size: 22px; }
.cart-icon .badge {
    position: absolute; top: -8px; right: -10px;
    background: #ff4d4d; color: white; font-size: 12px;
    border-radius: 50%; padding: 3px 7px; line-height: 1;
}
.profile-icon { font-size: 22px; }
.container { width: 95%; max-width: 1400px; margin: auto; padding: 30px 0; }
h2 { color: #333; font-weight: 600; margin-bottom: 25px; padding-left: 10px; border-left: 5px solid #4a65a9; }
.search-bar { text-align: center; margin: 25px auto 50px; }
.search-bar form { display: inline-flex; background: white; border-radius: 50px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); overflow: hidden; }
.search-bar input { border: none; padding: 12px 20px; font-size: 16px; width: 300px; outline: none; }
.search-bar button { background: #4a65a9; border: none; color: white; padding: 12px 20px; cursor: pointer; transition: 0.3s; }
.search-bar button:hover { background: #2e427f; }
.courses-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px; margin-bottom: 60px; }
.course-card { background: white; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden; text-align: center; transition: transform 0.2s ease-in-out; display: flex; flex-direction: column; height: 100%; }
.course-card:hover { transform: translateY(-5px); }
.course-card img { width: 100%; height: 180px; object-fit: cover; }
.course-info { padding: 15px; flex-grow: 1; display: flex; flex-direction: column; align-items: center; }
.course-info h3 { margin: 5px 0 5px; font-size: 18px; color: #4a65a9; font-weight: 700; }
.course-info p { margin: 4px 0; color: #666; font-size: 14px; }
.course-info button { margin-top: auto; background: #5cb85c; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; color: white; transition: 0.3s; }
.course-info button:hover { background: #4cae4c; }
.categories-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
.category-box-link { text-decoration: none; }
.category-box { background: #4a65a9; color: white; padding: 25px 15px; border-radius: 12px; text-align: center; font-weight: 600; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 8px rgba(0,0,0,0.1); display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 120px; }
.category-box:hover { background: #2e427f; transform: scale(1.03); }
.category-icon-main { font-size: 2.5rem; margin-bottom: 10px; }
.category-title { font-size: 1.1rem; color: white; font-weight: 600; }
</style>
</head>

<body>
<?= $message; ?>

<nav>
    <div class="left">
        <i class="fa-solid fa-book-open-reader fa-xl"></i>
        <h1>LearnHub</h1>
    </div>
    <div class="right">
        <span>👋 สวัสดี, <?= htmlspecialchars($username); ?></span>
        <a href="student_profile.php" class="profile-icon" title="แก้ไขข้อมูลส่วนตัว">
            <i class="fa-solid fa-user"></i>
        </a>
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

    <div class="search-bar">
        <form method="get" action="student_dashboard.php">
            <input type="text" name="search" placeholder="ค้นหาวิชา เช่น คณิตศาสตร์, ภาษาอังกฤษ..." value="<?= htmlspecialchars($search_query); ?>">
            <button type="submit"><i class="fa-solid fa-search"></i> ค้นหา</button>
        </form>
    </div>

    <?php 
        $display_title = "คอร์สเปิดใหม่แนะนำ";
        if ($search_query) {
            $display_title = "ผลการค้นหาสำหรับ: " . htmlspecialchars($search_query);
            if ($current_category) {
                 $display_title .= " (รวมคอร์สในหมวดหมู่ " . htmlspecialchars($current_category) . ")";
            }
        }
    ?>
    <h2>✨ <?= $display_title; ?></h2>

    <div class="courses-grid">
        <?php if ($courses_result && mysqli_num_rows($courses_result) > 0): ?>
            <?php while ($course = mysqli_fetch_assoc($courses_result)): ?>
                <div class="course-card">
                    <img src="picture/<?= htmlspecialchars($course['picture'] ?? 'default.png'); ?>"
                             alt="<?= htmlspecialchars($course['subject'] ?? 'วิชา'); ?>"
                             onerror="this.onerror=null;this.src='picture/default.png';">
                    <div class="course-info">
                        <h3><?= htmlspecialchars($course['subject'] ?? 'ไม่มีชื่อวิชา'); ?></h3>
                        <p><strong>ผู้สอน:</strong> <?= htmlspecialchars($course['name'] ?? '-'); ?></p>
                        <p><strong>เวลา:</strong> <?= htmlspecialchars($course['time'] ?? '-'); ?></p>
                        <p><strong>ราคา:</strong>
                            <span style="font-weight:700; color:#ff4d4d;">
                                <?= number_format($course['price'] ?? 0, 0); ?> บาท
                            </span>
                        </p>
                        <form method="post">
                            <input type="hidden" name="course_id" value="<?= $course['id'] ?? 0; ?>"> 
                            <button type="submit" name="add_to_cart">
                                <i class="fas fa-cart-plus"></i> เพิ่มลงตะกร้า
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; grid-column: 1 / -1;">ไม่พบคอร์สที่ตรงกับคำค้น</p>
        <?php endif; ?>
    </div>

    <h2>🎯 ค้นหาตามหมวดหมู่</h2>
    
    <div class="categories-grid">
        <?php foreach ($categories as $category_name): 
            $icon = getCategoryIcon($category_name);
        ?>
        <a href="category_courses.php?category=<?= urlencode($category_name); ?>" class="category-box-link">
            <div class="category-box">
                <i class="fas <?= $icon; ?> category-icon-main"></i>
                <span class="category-title"><?= htmlspecialchars($category_name); ?></span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

</div>
</body>
</html>
