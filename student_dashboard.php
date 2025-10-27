<?php
session_start();
include_once 'dbconnect.php';

// ตรวจสอบการล็อกอิน
// เรายังคงดึงบทบาทมาตรวจสอบเพื่อแสดงลิงก์แอดมิน แม้ว่าหน้านี้หลักๆ จะสำหรับนักเรียน
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'นักเรียน'; 
$role = $_SESSION['role'] ?? 'student'; // <--- ดึงตัวแปร role เพื่อใช้งาน

// บังคับ Redirect หากไม่ใช่ Student หรือ Admin
if ($role != 'student' && $role != 'admin') {
    header("Location: login.php");
    exit();
}

$message = ''; 

// 🚀 Logic สำหรับการยกเลิกคอร์สที่ชำระเงินแล้ว 🚀
if (isset($_POST['cancel_course'])) {
    $booking_id_to_cancel = intval($_POST['booking_id']); 
    
    if ($booking_id_to_cancel > 0) {
        // อัปเดตสถานะเป็น 'cancelled' โดยไม่ต้องคืนเงิน
        $cancel_sql = "UPDATE bookings SET status = 'cancelled', cancellation_date = NOW() 
                       WHERE id = $booking_id_to_cancel AND user_id = $user_id AND status = 'paid'";
        
        if (mysqli_query($conn, $cancel_sql)) {
            $_SESSION['cancellation_success'] = " คอร์สถูกยกเลิกเรียบร้อยแล้ว. (ไม่มีการคืนเงินที่ชำระไป)";
        } else {
            $_SESSION['cancellation_error'] = " เกิดข้อผิดพลาดในการยกเลิก: " . mysqli_error($conn);
        }
        header("Location: student_dashboard.php");
        exit();
    }
}
// --------------------------------------------------------------------------

// --- ส่วนเพิ่มคอร์สลงตะกร้า ---
if (isset($_POST['add_to_cart'])) {
    $course_id = intval($_POST['course_id']); 
    
    if ($course_id > 0) {
        $course_data_sql = "SELECT time FROM tutor_courses WHERE id = $course_id";
        $course_data_result = mysqli_query($conn, $course_data_sql);
        $course_data = mysqli_fetch_assoc($course_data_result);

        $start_time = mysqli_real_escape_string($conn, $course_data['time'] ?? 'N/A');
        $end_time = 'NULL';
        
        $check_sql = "SELECT id FROM bookings WHERE user_id=$user_id AND tutor_id=$course_id AND status='pending'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
            header("Location: student_dashboard.php?added=already_exists");
            exit();
        } else {
            $insert_sql = "INSERT INTO bookings (user_id, tutor_id, booking_date, start_time, end_time, status)
                           VALUES ($user_id, $course_id, CURDATE(), '$start_time', $end_time, 'pending')";
            
            if (mysqli_query($conn, $insert_sql)) {
                header("Location: student_dashboard.php?added=success");
                exit();
            } else {
                $error_msg = urlencode("เกิดข้อผิดพลาดในการเพิ่มลงตะกร้า: " . mysqli_error($conn));
                header("Location: student_dashboard.php?added=error&details={$error_msg}");
                exit();
            }
        }
    }
}
// --- จบส่วนเพิ่มคอร์สลงตะกร้า ---

// --- ส่วนจัดการข้อความแจ้งเตือน (Query Strings) ---
if (isset($_GET['added'])) {
    $status = $_GET['added'];
    if ($status == 'success') {
        $message = '<script>alert(" เพิ่มคอร์สลงในตะกร้าเรียบร้อยแล้ว!");</script>';
    } elseif ($status == 'already_exists') {
        $message = '<script>alert(" คอร์สนี้อยู่ในตะกร้าของคุณแล้ว!");</script>';
    } elseif ($status == 'error') {
        $details = htmlspecialchars($_GET['details'] ?? 'ไม่ทราบสาเหตุ');
        $message = '<script>alert(" การเพิ่มลงตะกร้าล้มเหลว: ' . $details . '");</script>';
    }
}
// --- จบส่วนจัดการข้อความแจ้งเตือน (Query Strings) ---

// 🚀 จัดการข้อความแจ้งเตือนจาก Session (Payment & Cancellation) 🚀
if (isset($_SESSION['payment_success'])) {
    $message .= '<div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 30px; text-align: center; border: 1px solid #c3e6cb;">' . $_SESSION['payment_success'] . '</div>';
    unset($_SESSION['payment_success']);
} elseif (isset($_SESSION['cancellation_success'])) {
    $message .= '<div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 30px; text-align: center; border: 1px solid #f5c6cb;">' . $_SESSION['cancellation_success'] . '</div>';
    unset($_SESSION['cancellation_success']);
} elseif (isset($_SESSION['cancellation_error'])) {
    $message .= '<div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 30px; text-align: center; border: 1px solid #f5c6cb;">' . $_SESSION['cancellation_error'] . '</div>';
    unset($_SESSION['cancellation_error']);
}

// --- ส่วนค้นหาวิชาและดึงคอร์สแนะนำ ---
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
    // ดึงคอร์สแนะนำ (is_featured = 1) เป็น 5 คอร์ส
    $courses_result = mysqli_query($conn, "SELECT id, subject, price, time, name, picture FROM tutor_courses WHERE is_featured = 1 ORDER BY id DESC LIMIT 5");
}

// 🚀 ดึงคอร์สที่นักเรียนชำระเงินแล้ว (Paid Courses) 🚀
$paid_courses_result = mysqli_query($conn, 
    "SELECT b.id AS booking_id, t.id AS course_id, t.subject, t.price, t.name AS tutor_name, t.picture
     FROM bookings b
     JOIN tutor_courses t ON b.tutor_id = t.id
     WHERE b.user_id = $user_id AND b.status = 'paid' AND b.status != 'cancelled'
     ORDER BY b.payment_date DESC");


// --- ส่วนจัดการหมวดหมู่ (แก้ไขปัญหาซ้ำซ้อนและเพิ่มรายการที่หายไป) ---
// 1. ดึงหมวดหมู่จากตาราง categories ของ admin เป็นหลัก
$category_sql = "SELECT DISTINCT name AS category FROM categories ORDER BY name ASC";
$category_result = mysqli_query($conn, $category_sql);
$categories = []; 

// ดึงรายการหมวดหมู่จากฐานข้อมูล
if ($category_result && mysqli_num_rows($category_result) > 0) {
    while ($cat = mysqli_fetch_assoc($category_result)) {
        $categories[] = trim($cat['category']);
    }
} else {
    // 2. ถ้าไม่มีตาราง categories หรือไม่มีข้อมูล ให้ดึงจาก tutor_courses (แบบเดิม)
    $category_sql_fallback = "SELECT DISTINCT category FROM tutor_courses WHERE category IS NOT NULL AND category != '' ORDER BY category ASC";
    $category_result_fallback = mysqli_query($conn, $category_sql_fallback);
    if ($category_result_fallback) {
         while ($cat = mysqli_fetch_assoc($category_result_fallback)) {
            $categories[] = trim($cat['category']);
        }
    }
}

// 3. รายการสำรอง (เพิ่มรายการที่ขาดไปกลับมา)
$fallback_cats = [
    'คณิตศาสตร์', 
    'วิทยาศาสตร์', 
    'สังคมศาสตร์', 
    'คอมพิวเตอร์', 
    'ทำอาหาร', 
    'ศิลปะ',
    'เสริมสวย', 
    'ดนตรี', 
    'ภาษาต่างประเทศ', 
    'บริหารการจัดการ', 
    'ออกกำลังกาย', 
    'กราฟฟิก', 
    'การช่าง' // <--- "การช่าง" อยู่ที่นี่
];

// แก้ไข: ใช้ array_unique เพื่อให้แน่ใจว่าไม่ซ้ำกัน
$categories = array_unique(array_filter(array_merge($categories, $fallback_cats)));

$count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE user_id=$user_id AND status='pending'");
$count_row = mysqli_fetch_assoc($count_result);
$cart_count = $count_row['total'] ?? 0;

// --- ฟังก์ชันไอคอนหมวดหมู่ ---
function getCategoryIcon($category) {
    if (strpos($category, 'คณิตศาสตร์') !== false) return 'fa-calculator'; 
    elseif (strpos($category, 'วิทยาศาสตร์') !== false) return 'fa-flask';
    elseif (strpos($category, 'สังคมศาสตร์') !== false) return 'fa-user-friends';
    elseif (strpos($category, 'การช่าง') !== false) return 'fa-wrench'; // <--- ไอคอนสำหรับ "การช่าง"
    elseif (strpos($category, 'ภาษา') !== false) return 'fa-globe-asia';
    elseif (strpos($category, 'ดนตรี') !== false) return 'fa-music';
    elseif (strpos($category, 'ศิลปะ') !== false) return 'fa-palette';
    elseif (strpos($category, 'คอมพิวเตอร์') !== false) return 'fa-laptop-code';
    elseif (strpos($category, 'ทำอาหาร') !== false) return 'fa-utensils';
    elseif (strpos($category, 'ออกกำลังกาย') !== false) return 'fa-dumbbell';
    elseif (strpos($category, 'กราฟฟิก') !== false) return 'fa-vector-square';
    elseif (strpos($category, 'เสริมสวย') !== false) return 'fa-spa';
    elseif (strpos($category, 'บริหารการจัดการ') !== false || strpos($category, 'ธุรกิจ') !== false) return 'fa-briefcase';
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
/* CSS */
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
.courses-grid { 
    display: grid; 
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
    gap: 30px; 
    margin-bottom: 60px; 
}
.course-card { background: white; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden; text-align: center; transition: transform 0.2s ease-in-out; display: flex; flex-direction: column; height: 100%; }
.course-card:hover { transform: translateY(-5px); }
.course-card img { width: 100%; height: 180px; object-fit: cover; }
.course-info { padding: 15px; flex-grow: 1; display: flex; flex-direction: column; align-items: center; }
.course-info h3 { margin: 5px 0 5px; font-size: 18px; color: #4a65a9; font-weight: 700; }
.course-info p { margin: 4px 0; color: #666; font-size: 14px; }
/* ปุ่มตะกร้า */
.course-info button { margin-top: auto; background: #5cb85c; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; color: white; transition: 0.3s; }
.course-info button:hover { background: #4cae4c; }
/* ปุ่มรวม (กลุ่ม) */
.action-btn-group { width: 80%; margin-top: 10px; }
/* ปุ่มเข้าห้องเรียน */
.course-info .action-btn-group a button { background: #4a69bd; padding: 10px 15px; margin: 5px 0; width: 100%; }
.course-info .action-btn-group a button:hover { background: #3c518d; }
/* ปุ่มยกเลิก */
.course-info .action-btn-group button[name="cancel_course"] { background: #e74c3c; padding: 10px 15px; margin: 5px 0; width: 100%; font-size: 14px; }
.course-info .action-btn-group button[name="cancel_course"]:hover { background: #c0392b; }

.categories-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
.category-box-link { text-decoration: none; }
.category-box { background: #4a65a9; color: white; padding: 25px 15px; border-radius: 12px; text-align: center; font-weight: 600; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 8px rgba(0,0,0,0.1); display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 120px; }
.category-box:hover { background: #2e427f; transform: scale(1.03); }
.category-icon-main { font-size: 2.5rem; margin-bottom: 10px; }
.category-title { font-size: 1.1rem; color: white; font-weight: 600; }
/* CSS สำหรับ Admin Link */
.admin-link { 
    color: #ffeb3b !important; 
    font-weight: 700; 
    margin-right: 10px; 
}
.admin-link:hover {
    color: #ffd600 !important; 
}
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
        <?php if ($role === 'admin'): // ลิงก์ไปยังหน้าแอดมิน (admin.php) ?>
            <a href="admin.php" class="admin-link" title="หน้าจัดการระบบแอดมิน">
                <i class="fa-solid fa-screwdriver-wrench"></i> จัดการระบบ
            </a>
        <?php endif; ?>
        
        <span> สวัสดี, <?= htmlspecialchars($username); ?></span>
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
    
    <h2> คอร์สที่ฉันกำลังเรียน</h2>

    <div class="courses-grid">
        <?php if ($paid_courses_result && mysqli_num_rows($paid_courses_result) > 0): ?>
            <?php while ($course = mysqli_fetch_assoc($paid_courses_result)): ?>
                <div class="course-card">
                    <img src="picture/<?= htmlspecialchars($course['picture'] ?? 'default.pnj'); ?>" 
                            alt="<?= htmlspecialchars($course['subject'] ?? 'วิชา'); ?>"
                            onerror="this.onerror=null;this.src='picture/default.pnj';">
                    <div class="course-info">
                        <h3><?= htmlspecialchars($course['subject'] ?? 'ไม่มีชื่อวิชา'); ?></h3>
                        <p><strong>ผู้สอน:</strong> <?= htmlspecialchars($course['tutor_name'] ?? '-'); ?></p>
                        <p>สถานะ: <span style="color: green; font-weight: 700;">ชำระเงินแล้ว</span></p>
                        
                        <div class="action-btn-group">
                            <a href="course_room.php?course_id=<?= $course['course_id']; ?>" style="text-decoration: none;">
                                <button type="button">
                                    <i class="fas fa-door-open"></i> เข้าห้องเรียน
                                </button>
                            </a>
                            
                            <form method="post" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะยกเลิกคอร์สนี้? (คุณจะไม่ได้รับเงินคืน)');">
                                <input type="hidden" name="booking_id" value="<?= $course['booking_id']; ?>"> 
                                <button type="submit" name="cancel_course">
                                    <i class="fas fa-ban"></i> ยกเลิกคอร์ส
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; grid-column: 1 / -1;">คุณยังไม่มีคอร์สที่ชำระเงินแล้ว. ลองค้นหาและเพิ่มคอร์สในตะกร้า!</p>
        <?php endif; ?>
    </div>

    <hr style="margin: 50px 0; border-top: 1px solid #ccc;">

    <?php 
        $display_title = "คอร์สเปิดใหม่แนะนำ";
        if ($search_query) {
            $display_title = "ผลการค้นหาสำหรับ: " . htmlspecialchars($search_query);
            if ($current_category) {
                   $display_title .= " (รวมคอร์สในหมวดหมู่ " . htmlspecialchars($current_category) . ")";
            }
        }
    ?>
    <h2> <?= $display_title; ?></h2>

    <div class="courses-grid">
        <?php if ($courses_result && mysqli_num_rows($courses_result) > 0): ?>
            <?php while ($course = mysqli_fetch_assoc($courses_result)): ?>
                <div class="course-card">
                    <img src="picture/<?= htmlspecialchars($course['picture'] ?? 'default.pnj'); ?>" 
                            alt="<?= htmlspecialchars($course['subject'] ?? 'วิชา'); ?>"
                            onerror="this.onerror=null;this.src='picture/default.pnj';">
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

    <h2> ค้นหาตามหมวดหมู่</h2>
    
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
