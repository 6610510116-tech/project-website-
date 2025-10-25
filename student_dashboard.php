 <?php

session_start();

// ตรวจสอบว่ามีการเชื่อมต่อฐานข้อมูลหรือไม่ ถ้าไม่ใช่ ให้เปลี่ยนเป็นชื่อไฟล์ที่ถูกต้อง

include_once 'dbconnect.php';



// ตรวจสอบการล็อกอิน

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {

    header("Location: login.php");

    exit();

}



$user_id = $_SESSION['user_id'];

$message = ''; // สำหรับแสดงข้อความแจ้งเตือน



// --- ส่วนจัดการการเพิ่มลงตะกร้า (Add to Cart Logic) ---

if (isset($_POST['add_to_cart'])) {

    $tutor_id = intval($_POST['tutor_id']);

   

    if ($tutor_id > 0) {

        // ดึงข้อมูลเวลาจริงจาก Form (แม้ว่าจะเป็น N/A หรือคำอธิบายก็ตาม)

        $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);

        $end_time = mysqli_real_escape_string($conn, $_POST['end_time']); // อาจเป็น N/A

       

        // 1. ตรวจสอบว่าคอร์สนี้ถูกเพิ่มในตะกร้า (status='pending') ไปแล้วหรือไม่

        $check_sql = "SELECT * FROM bookings WHERE user_id=$user_id AND tutor_id=$tutor_id AND status='pending'";

        $check_result = mysqli_query($conn, $check_sql);



        if (mysqli_num_rows($check_result) > 0) {

            $message = '<script>alert("คอร์สนี้อยู่ในตะกร้าของคุณแล้ว!");</script>';

        } else {

            // 2. ถ้ายังไม่มี ให้เพิ่มลงในตาราง bookings

            // NOTE: หากตาราง bookings ของคุณมีคอลัมน์สำหรับเก็บตารางสอน (เช่น time_description) ควรแก้ไข SQL ให้สอดคล้อง

            $insert_sql = "INSERT INTO bookings (user_id, tutor_id, booking_date, start_time, end_time, status)

                            VALUES ($user_id, $tutor_id, CURDATE(), '$start_time', '$end_time', 'pending')";

           

            if (mysqli_query($conn, $insert_sql)) {

                header("Location: student_dashboard.php?added=success");

                exit();

            } else {

                $message = '<script>alert("เกิดข้อผิดพลาดในการเพิ่มลงตะกร้า: ' . mysqli_error($conn) . '");</script>';

            }

        }

    }

}

// --- จบส่วนจัดการการเพิ่มลงตะกร้า ---



// --- ส่วนดึงข้อมูล ---



// ดึงข้อมูลคอร์สทั้งหมดจาก tutor_courses

$courses_result = mysqli_query($conn, "SELECT * FROM tutor_courses ORDER BY id ASC LIMIT 12");



// ดึงข้อมูลหมวดหมู่ที่ไม่ซ้ำกัน

$category_sql = "SELECT DISTINCT category FROM tutor_courses WHERE category IS NOT NULL AND category != '' ORDER BY category ASC";

$category_result = mysqli_query($conn, $category_sql);



// นับจำนวนในตะกร้า

$count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE user_id=$user_id AND status='pending'");

$count_row = mysqli_fetch_assoc($count_result);

$cart_count = $count_row['total'];



// แสดงข้อความแจ้งเตือนจากการ Redirect

if (isset($_GET['added']) && $_GET['added'] == 'success') {

    $message = '<script>alert("เพิ่มคอร์สลงในตะกร้าเรียบร้อยแล้ว!");</script>';

}

?>



<!DOCTYPE html>

<html lang="th">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>LearnHub - Student Dashboard</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">

<style>

/* ... (CSS ส่วนเดิม) ... */

.cart-icon {
    position: relative;
    color: white;
    font-size: 22px;
    text-decoration: none;
    /* เพิ่ม margin-left เพื่อเว้นระยะห่างจากไอคอนโปรไฟล์ */
    margin-left: 20px; 
}

/* เพิ่ม CSS สำหรับไอคอนโปรไฟล์ */
.profile-icon {
    color: white;
    font-size: 22px;
    text-decoration: none;
    /* เว้นระยะห่างจากชื่อผู้ใช้ */
    margin-left: 20px; 
}
.profile-icon:hover {
    color: #f1f1f1;
}

body {

    font-family: 'Kanit', sans-serif;

    background: #f0f4f8;

    margin: 0;

}

nav {

    background: #4a65a9;

    color: white;

    display: flex;

    justify-content: space-between;

    align-items: center;

    padding: 15px 40px;

}

nav a {

    color: white;

    text-decoration: none;

    margin-left: 20px;

}

nav h1 {

    display: inline;

    margin-left: 10px;

    font-size: 22px;

}

.cart-icon {

    position: relative;

    color: white;

    font-size: 22px;

    text-decoration: none;

}

.cart-icon .badge {

    position: absolute;

    top: -8px;

    right: -10px;

    background: #ff4d4d;

    color: white;

    font-size: 12px;

    border-radius: 50%;

    padding: 3px 7px;

}

.container {

    width: 90%;

    margin: auto;

    padding: 30px 0;

}

h2 {

    color: #333;

}

.courses-grid {

    display: grid;

    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));

    gap: 25px;

    margin-top: 20px;

}

.course-card {

    background: white;

    border-radius: 16px;

    box-shadow: 0 4px 12px rgba(0,0,0,0.1);

    overflow: hidden;

    text-align: center;

    transition: transform 0.2s ease-in-out;

}

.course-card:hover {

    transform: translateY(-5px);

}

.course-card img {

    width: 100%;

    height: 230px;

    object-fit: cover;

    border-bottom: 2px solid #f1f1f1;

    border-radius: 0;

}

.course-info {

    padding: 15px;

}

.course-info h3 {

    margin: 8px 0 5px;

    font-size: 18px;

    color: #333;

}

.course-info p {

    margin: 4px 0;

    color: #666;

    font-size: 14px;

}

.categories {

    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 15px;

    margin-top: 50px;

}

/* เพิ่มสไตล์สำหรับลิงก์ในกล่องหมวดหมู่ */

.category-box {

    background: #4a65a9;

    color: white;

    padding: 20px;

    border-radius: 12px;

    text-align: center;

    font-weight: 600;

    cursor: pointer;

    transition: 0.3s;

    text-decoration: none; /* ลบขีดเส้นใต้ของลิงก์ */

}

.category-box:hover {

    background: #2e427f;

}

button {

    background: #4a65a9;

    color: white;

    border: none;

    padding: 8px 15px;

    border-radius: 8px;

    margin-top: 10px;

    cursor: pointer;

    transition: 0.3s;

}

button:hover {

    background: #2e427f;

}
/* ... (CSS ส่วนเดิม) ... */
.cart-icon {
    position: relative;
    color: white;
    font-size: 22px;
    text-decoration: none;
    margin-left: 20px; /* ย้าย cart-icon ให้มี margin-left ถ้ามี */
}

/* เพิ่ม CSS สำหรับไอคอนโปรไฟล์ */
.profile-icon {
    color: white;
    font-size: 22px;
    text-decoration: none;
    margin-left: 20px; /* ให้มีระยะห่างจากข้อความ "สวัสดี" */
}
.profile-icon:hover {
    color: #f1f1f1;
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

        <span>👋 สวัสดี, <?= htmlspecialchars($_SESSION['username'] ?? 'นักเรียน'); ?></span>
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

    <h2>📚 คอร์สแนะนำ</h2>



    <div class="courses-grid">

        <?php

        // ตรวจสอบผลลัพธ์ก่อนเริ่มลูป

        if (isset($courses_result) && mysqli_num_rows($courses_result) > 0) {

            while ($course = mysqli_fetch_assoc($courses_result)) {

        ?>

        <div class="course-card">

            <img src="picture/<?= htmlspecialchars($course['picture'] ?? 'default.jpg'); ?>"

                 alt="<?= htmlspecialchars($course['name'] ?? 'ไม่มีชื่อผู้สอน'); ?>">

            <div class="course-info">

                <h3><?= htmlspecialchars($course['subject'] ?? 'ไม่มีชื่อวิชา'); ?></h3>

                <p><strong>ผู้สอน:</strong> <?= htmlspecialchars($course['name'] ?? '-'); ?></p>

                <p><strong>เวลา:</strong> <?= htmlspecialchars($course['time'] ?? '-'); ?></p>

                <p><strong>ราคา:</strong> <?= number_format($course['price'] ?? 0, 0); ?> บาท</p>

               

                <form method="post">

                    <input type="hidden" name="tutor_id" value="<?= $course['id'] ?? 0; ?>">

                    <input type="hidden" name="start_time" value="<?= htmlspecialchars($course['time'] ?? 'N/A'); ?>">

                    <input type="hidden" name="end_time" value="N/A">

                    <button type="submit" name="add_to_cart">เพิ่มลงตะกร้า</button>

                </form>

            </div>

        </div>

        <?php

            }

        } else {

            echo "<p style='text-align:center; grid-column: 1 / -1;'>ยังไม่มีคอร์สสอนแนะนำในขณะนี้</p>";

        }

        ?>

    </div>



    <h2 style="margin-top:50px;">🎯 หมวดหมู่</h2>

    <div class="categories">

        <?php

        // แสดงหมวดหมู่ที่ดึงมาจากฐานข้อมูลจริง

        if ($category_result && mysqli_num_rows($category_result) > 0) {

            while ($cat = mysqli_fetch_assoc($category_result)) {

        ?>

        <a href="student_dashboard.php?category=<?= urlencode($cat['category']); ?>" class="category-box">

            <?= htmlspecialchars($cat['category']); ?>

        </a>

        <?php

            }

        } else {

            // โชว์หมวดหมู่ Hardcode หากดึงข้อมูลไม่ได้ (ทางเลือก)

        ?>

            <a href="#" class="category-box">ดนตรี</a>

            <a href="#" class="category-box">ศิลปะ</a>

            <a href="#" class="category-box">คณิตศาสตร์</a>

            <a href="#" class="category-box">วิทยาศาสตร์</a>

            <a href="#" class="category-box">คอมพิวเตอร์</a>

            <a href="#" class="category-box">สังคมศาสตร์</a>

            <a href="#" class="category-box">ภาษาต่างประเทศ</a>

            <a href="#" class="category-box">บริหารธุรกิจ</a>

        <?php } ?>

    </div>

</div>



</body>

</html>