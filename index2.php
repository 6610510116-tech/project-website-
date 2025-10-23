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
<?php
session_start();
include_once 'dbconnect.php';

// ตรวจสอบล็อกอิน
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// ดึง user info
$user_id = $_SESSION['user']['id'];

// เพิ่มติวเตอร์ลงตะกร้า (POST)
if(isset($_POST['add_to_cart'])) {
    $tutor_id = intval($_POST['tutor_id']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // ตรวจสอบเวลาซ้ำ
    $check = mysqli_query($conn, "SELECT * FROM bookings WHERE user_id=$user_id 
                                 AND ((start_time BETWEEN '$start_time' AND '$end_time') 
                                 OR (end_time BETWEEN '$start_time' AND '$end_time'))");

    if(mysqli_num_rows($check) > 0){
        $msg = "ไม่สามารถจองได้! เวลาซ้อนกับตารางเรียนเดิม";
    } else {
        mysqli_query($conn, "INSERT INTO bookings(user_id, tutor_id, start_time, end_time) 
                             VALUES($user_id, $tutor_id, '$start_time', '$end_time')");
        $msg = "เพิ่มติวเตอร์ลงตะกร้าเรียบร้อย";
    }
}

// ดึงรายการในตะกร้า (สถานะ pending)
$cart_query = mysqli_query($conn, "SELECT b.id AS booking_id, t.fullname, t.subject, b.start_time, b.end_time
                                   FROM bookings b 
                                   JOIN tutors t ON b.tutor_id = t.id 
                                   WHERE b.user_id=$user_id AND b.status='pending'");

?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>หน้าแรกหลังล็อกอิน | LearnHub</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family:'Kanit', sans-serif; background:#f7f7ff; margin:0; }
nav { display:flex; justify-content:space-between; align-items:center; padding:0 30px; height:70px; background:white; box-shadow:0 2px 8px rgba(0,0,0,0.1);}
nav .logo h1 {color:#4a65a9;}
nav .menu-icon {font-size:24px; cursor:pointer;}
.cart-icon {position:relative; cursor:pointer;}
.cart-icon::after {content:"<?php echo mysqli_num_rows($cart_query); ?>"; position:absolute; top:-8px; right:-10px; background:red; color:white; width:18px; height:18px; border-radius:50%; font-size:12px; text-align:center;}

.dropdown {position:fixed; top:70px; left:0; width:250px; height:100%; background:white; transform:translateX(-100%); transition:0.3s; z-index:999;}
.dropdown.active {transform:translateX(0);}
.dropdown ul {list-style:none; margin-top:20px; padding:0;}
.dropdown ul li {padding:15px 20px; border-bottom:1px solid #eee;}
.dropdown ul li a {text-decoration:none; color:#333; display:flex; gap:10px;}
.dropdown ul li a:hover {color:#6b96b9;}

.container {max-width:1200px; margin:100px auto 50px; padding:0 20px;}
h2 {color:#4a65a9; margin-bottom:20px;}
table {width:100%; border-collapse:collapse;}
table th, table td {border:1px solid #ccc; padding:10px; text-align:center;}
table th {background:#4a65a9; color:white;}
button {padding:8px 15px; border:none; border-radius:6px; cursor:pointer; background:#4a65a9; color:white;}
button:hover {background:#6b96b9;}
.msg {margin-bottom:20px; color:red;}
</style>
</head>
<body>

<nav>
    <div class="menu-icon" id="menuToggle"><i class="fa-solid fa-bars"></i></div>
    <div class="logo"><h1>LearnHub</h1></div>
    <div class="cart-icon" id="cartBtn"><i class="fa-solid fa-cart-shopping"></i></div>
</nav>

<div class="dropdown" id="dropdownMenu">
    <ul>
        <li><a href="#"><i class="fa-solid fa-user"></i> ตัวฉัน</a></li>
        <li><a href="#"><i class="fa-solid fa-gear"></i> การตั้งค่าระบบ</a></li>
        <li><a href="#"><i class="fa-solid fa-book"></i> ขั้นตอนการจอง</a></li>
        <li><a href="#"><i class="fa-solid fa-clock-rotate-left"></i> ประวัติการจอง</a></li>
        <li><a href="#"><i class="fa-solid fa-bell"></i> การแจ้งเตือน</a></li>
        <li><a href="#"><i class="fa-solid fa-credit-card"></i> Payment</a></li>
        <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> ออกจากระบบ</a></li>
    </ul>
</div>

<div class="container">
    <h2>ตะกร้าการเรียน</h2>

    <?php if(isset($msg)) echo "<p class='msg'>$msg</p>"; ?>

    <table>
        <thead>
            <tr>
                <th>ติวเตอร์</th>
                <th>วิชา</th>
                <th>เริ่ม</th>
                <th>สิ้นสุด</th>
                <th>ชำระเงิน</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($cart_query)) { ?>
            <tr>
                <td><?php echo $row['fullname']; ?></td>
                <td><?php echo $row['subject']; ?></td>
                <td><?php echo $row['start_time']; ?></td>
                <td><?php echo $row['end_time']; ?></td>
                <td>
                    <form method="post" action="payment.php">
                        <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                        <button type="submit">ชำระเงิน</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
const menuToggle = document.getElementById('menuToggle');
const dropdownMenu = document.getElementById('dropdownMenu');
menuToggle.addEventListener('click', ()=> dropdownMenu.classList.toggle('active'));
</script>

</body>
</html>
