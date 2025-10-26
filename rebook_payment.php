<?php
session_start();
include("dbconnect.php");

// ตรวจสอบการล็อกอินและสิทธิ์
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$course_id = intval($_GET['course_id'] ?? 0); 
$message = '';
$course = null;
$rebook_price = 0;

if ($course_id > 0) {
    // ดึงข้อมูลคอร์สและราคาซ้ำ
    $query = "SELECT tc.subject, tc.rebook_price, b.id as booking_id
              FROM tutor_courses tc
              JOIN bookings b ON tc.id = b.tutor_id
              WHERE tc.id = $course_id AND b.user_id = $user_id AND b.status = 'paid' LIMIT 1";
    
    $result = mysqli_query($conn, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $course = $row;
        $rebook_price = $course['rebook_price'] ?? 200.00; // ใช้ค่า 200 เป็นค่า Default ถ้าไม่มีใน DB
        $booking_id = $course['booking_id'];
    } else {
        $message = '<div class="alert alert-danger">ไม่พบคอร์สที่ต้องชำระเงินซ้ำหรือคุณไม่มีสิทธิ์.</div>';
    }
} else {
    header("Location: student_dashboard.php");
    exit();
}


// Logic สำหรับการยืนยันการชำระเงินซ้ำ (จำลอง)
if (isset($_POST['confirm_rebook_payment']) && $rebook_price > 0) {
    
    // อัปเดตสถานะการจอง: เพิ่ม paid_sessions ขึ้น 1 
    $update_sql = "UPDATE bookings SET paid_sessions = paid_sessions + 1 
                   WHERE user_id = $user_id AND tutor_id = $course_id AND id = $booking_id AND status = 'paid'";
                       
    if (mysqli_query($conn, $update_sql)) {
        // สำเร็จ! ตั้งค่า Session และ redirect กลับไปที่ course_room.php
        $_SESSION['rebook_success'] = "✅ ชำระค่าเข้าเรียนครั้งถัดไป " . number_format($rebook_price, 2) . " บาท สำเร็จแล้ว! คุณสามารถเข้าห้องเรียนได้ทันที.";
        header("Location: course_room.php?course_id=" . $course_id); 
        exit();
    } else {
        $message = '<div class="alert alert-danger">เกิดข้อผิดพลาดในการบันทึกการชำระเงิน: ' . mysqli_error($conn) . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ชำระค่าเข้าเรียนซ้ำ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f4f6fa; }
        .container { max-width: 600px; }
        .summary-card { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 30px; margin-top: 50px; }
        .total-row { border-top: 2px solid #eee; padding-top: 15px; margin-top: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="summary-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>💰 ชำระค่าเข้าเรียนซ้ำ</h2>
        </div>

        <?= $message; ?>

        <?php if ($course): ?>
            <h4 class="mb-3">รายการ: <?= htmlspecialchars($course['subject']); ?></h4>
            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div><strong>ค่าเข้าเรียนครั้งถัดไป</strong></div>
                    <span class="fw-bold text-danger"><?= number_format($rebook_price, 2); ?> บาท</span>
                </li>
            </ul>

            <div class="total-row d-flex justify-content-between align-items-center">
                <h4 class="mb-0">รวมราคาทั้งสิ้น</h4>
                <h3 class="mb-0 text-success fw-bold"><?= number_format($rebook_price, 2); ?> บาท</h3>
            </div>
            
            <hr class="my-4">

            <div class="alert alert-info" role="alert">
                เมื่อกดยืนยันการชำระเงิน คุณจะสามารถเข้าสู่ห้องเรียน **<?= htmlspecialchars($course['subject']); ?>** ได้ทันที.
            </div>

            <form method="post">
                <button type="submit" name="confirm_rebook_payment" class="btn btn-success btn-lg w-100">
                    ยืนยันและชำระเงิน (<?= number_format($rebook_price, 2); ?> บาท)
                </button>
                <a href="course_room.php?course_id=<?= $course_id; ?>" class="btn btn-outline-secondary w-100 mt-2">ยกเลิก</a>
            </form>

        <?php endif; ?>
    </div>
</div>

</body>
</html>