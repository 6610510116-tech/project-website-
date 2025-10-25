<?php
session_start();
include("dbconnect.php");

// ตรวจสอบการล็อกอิน
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$message = '';
$checkout_items = [];
$total_amount = 0;

// 1. รับ ID การจองที่ถูกเลือกจาก cart.php
$booking_ids_string = $_GET['booking_ids'] ?? '';

// ตรวจสอบว่ามีการเลือกรายการมาหรือไม่
if (empty($booking_ids_string)) {
    header("Location: cart.php");
    exit();
}

// แปลง string ของ ID ให้เป็น array และเตรียมสำหรับ SQL
$booking_ids_array = array_map('intval', explode(',', $booking_ids_string));
$booking_ids_sql = implode(',', $booking_ids_array);


// 2. ดึงข้อมูลรายการที่ต้องชำระเงิน
// โค้ด SQL คล้ายกับ cart.php แต่กรองเฉพาะ ID ที่เลือกมาเท่านั้น
$query = "SELECT 
            b.id AS booking_id, 
            t.subject, 
            t.price,
            t.name AS tutor_name,
            b.start_time
          FROM bookings b 
          JOIN tutor_courses t ON b.tutor_id = t.id 
          WHERE b.user_id = '$user_id' AND b.status = 'pending' 
          AND b.id IN ($booking_ids_sql)";

$result = mysqli_query($conn, $query);

if ($result) {
    while($row = mysqli_fetch_assoc($result)){
        $checkout_items[] = $row;
        $total_amount += $row['price'];
    }
}

// 3. Logic สำหรับการยืนยันการชำระเงิน (จำลอง)
if (isset($_POST['confirm_payment'])) {
    if ($total_amount > 0 && !empty($booking_ids_sql)) {
        // อัปเดตสถานะการจองจาก 'pending' เป็น 'booked'
        $update_sql = "UPDATE bookings SET status = 'booked', payment_date = NOW() 
                       WHERE user_id = '$user_id' AND id IN ($booking_ids_sql)";
                       
        if (mysqli_query($conn, $update_sql)) {
            // สำเร็จ! ให้ไปที่หน้าประวัติการจองหรือ Dashboard พร้อมข้อความสำเร็จ
            $_SESSION['payment_success'] = "การชำระเงินเสร็จสมบูรณ์! คอร์สของคุณพร้อมเรียนแล้ว.";
            header("Location: student_dashboard.php"); 
            exit();
        } else {
            $message = '<div class="alert alert-danger">เกิดข้อผิดพลาดในการบันทึกการชำระเงิน: ' . mysqli_error($conn) . '</div>';
        }
    } else {
        $message = '<div class="alert alert-warning">ไม่พบรายการที่ต้องชำระเงิน.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ยืนยันการชำระเงิน</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f4f6fa;
        }
        .container {
            max-width: 800px;
        }
        .summary-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 30px;
            margin-top: 30px;
        }
        .total-row {
            border-top: 2px solid #eee;
            padding-top: 15px;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>💰 ยืนยันการชำระเงิน</h2>
        <a href="cart.php" class="btn btn-outline-secondary">กลับไปตะกร้า</a>
    </div>

    <?= $message; ?>

    <?php if (!empty($checkout_items)): ?>
        
        <div class="summary-card">
            <h4>รายการที่ต้องชำระ</h4>
            <ul class="list-group list-group-flush mb-4">
                <?php foreach($checkout_items as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($item['subject']); ?></strong>
                            <small class="text-muted d-block">ผู้สอน: <?= htmlspecialchars($item['tutor_name']); ?></small>
                        </div>
                        <span class="fw-bold"><?= number_format($item['price'], 2); ?> บาท</span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="total-row d-flex justify-content-between align-items-center">
                <h4 class="mb-0">รวมราคาทั้งสิ้น</h4>
                <h3 class="mb-0 text-success fw-bold"><?= number_format($total_amount, 2); ?> บาท</h3>
            </div>
            
            <hr class="my-4">

            <h4>วิธีการชำระเงิน</h4>
            <div class="alert alert-info" role="alert">
                ระบบนี้เป็นระบบจำลองการชำระเงิน. เมื่อกดยืนยัน รายการจะถูกเปลี่ยนสถานะเป็น "ชำระเงินแล้ว".
            </div>

            <form method="post">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="payment_method" id="bankTransfer" value="Bank" checked required>
                    <label class="form-check-label" for="bankTransfer">
                        โอนเงินผ่านธนาคาร
                    </label>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="radio" name="payment_method" id="creditCard" value="Card" required>
                    <label class="form-check-label" for="creditCard">
                        บัตรเครดิต / เดบิต
                    </label>
                </div>

                <button type="submit" name="confirm_payment" class="btn btn-success btn-lg w-100">
                    ยืนยันและชำระเงิน (<?= number_format($total_amount, 2); ?> บาท)
                </button>
            </form>
        </div>

    <?php else: ?>
        <div class="alert alert-warning text-center">
            ไม่พบรายการที่ต้องชำระเงิน. <a href="cart.php" class="alert-link">กลับไปที่ตะกร้าสินค้า</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>