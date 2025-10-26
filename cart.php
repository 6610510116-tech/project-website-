<?php
session_start();
// ตรวจสอบว่ามีการเชื่อมต่อฐานข้อมูลหรือไม่
include("dbconnect.php");

// ตรวจสอบการล็อกอิน
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$message = ''; // เพิ่มตัวแปร message สำหรับแจ้งเตือน

// --- ส่วนที่ 1: SQL Query เพื่อดึงข้อมูลในตะกร้า ---
// ตรวจสอบว่าตาราง bookings ใช้ tutor_id เพื่อเชื่อมโยงกับ tutor_courses
$query = "SELECT 
            b.id AS booking_id, 
            t.subject, 
            t.price,
            t.name AS tutor_name, /* ดึงชื่อผู้สอนจากคอลัมน์ name ของตารางคอร์ส */
            COALESCE(b.start_time, 'ไม่ได้กำหนด') AS start_time, /* ใช้ COALESCE เพื่อจัดการค่า NULL ใน start_time */
            b.status
          FROM bookings b 
          JOIN tutor_courses t ON b.tutor_id = t.id 
          WHERE b.user_id = '$user_id' AND b.status = 'pending'
          ORDER BY b.id DESC";

$result = mysqli_query($conn, $query);

// จัดเก็บข้อมูลในอาร์เรย์และคำนวณราคารวม
$cart_items = [];
$total_price_php = 0; 
if ($result) {
    while($row = mysqli_fetch_assoc($result)){
        $cart_items[] = $row;
        $total_price_php += $row['price'];
    }
} else {
    // แสดงข้อผิดพลาดหากการดึงข้อมูลล้มเหลว
    $message = '<div class="alert alert-danger text-center">❌ เกิดข้อผิดพลาดในการดึงข้อมูลตะกร้า: ' . mysqli_error($conn) . '</div>';
}
// --- จบส่วนแก้ไข Logic PHP/SQL ---
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตะกร้าสินค้า - LearnHub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f4f6fa; }
        .navbar { background-color: #4a65a9; color: white; }
        .navbar-brand { font-weight: bold; color: white !important; }
        .btn-outline-light:hover { background-color: #ffffff33; }
        .table { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table thead { background-color: #4a65a9; color: white; }
        .badge { font-size: 0.9rem; }
        .total-box { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 20px; text-align: right; }
        .btn-danger { background-color: #dc3545; border-color: #dc3545; }
        .btn-danger:hover { background-color: #c82333; border-color: #bd2130; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg px-4 py-3">
    <a class="navbar-brand" href="#">🛒 ตะกร้าสินค้าของฉัน</a>
    <div class="ms-auto">
        <a href="student_dashboard.php" class="btn btn-outline-light"><i class="fa-solid fa-arrow-left"></i> กลับสู่หน้าหลัก</a>
    </div>
</nav>

<div class="container my-5">
    <?= $message; ?> 
    
    <?php if (count($cart_items) > 0): ?>
        <form id="cartForm">
        <table class="table table-hover align-middle text-center">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>#</th>
                    <th>ชื่อคอร์ส</th>
                    <th>ผู้สอน</th>
                    <th>เวลาเรียน</th>
                    <th>ราคา (บาท)</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                foreach($cart_items as $row): 
                ?>
                <tr>
                    <td><input type="checkbox" name="selected[]" value="<?= $row['price'] ?>"></td>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td><?= htmlspecialchars($row['tutor_name']) ?></td>
                    <td><?= htmlspecialchars($row['start_time']) ?></td>
                    <td><?= number_format($row['price'], 2) ?></td>
                    <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($row['status']) ?></span></td>
                    <td><a href="delete_from_cart.php?booking_id=<?= $row['booking_id'] ?>" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash-can"></i> ลบ</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </form>

        <div class="total-box mt-4">
            <h5>รวมราคาที่เลือก: <span id="total" class="text-success fw-bold">0.00</span> บาท</h5>
            <a id="checkoutBtn" href="#" class="btn btn-primary btn-lg mt-3 disabled"><i class="fa-solid fa-money-bill-wave"></i> ดำเนินการชำระเงิน</a>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <h4>🛒 ตะกร้าสินค้าว่างเปล่า</h4>
            <p>กรุณากลับไปที่ <a href="student_dashboard.php" class="alert-link">หน้าหลัก</a> เพื่อเพิ่มคอร์ส</p>
        </div>
    <?php endif; ?>
</div>

<script>
    const checkboxes = document.querySelectorAll('input[name="selected[]"]');
    const selectAll = document.getElementById('selectAll');
    const totalDisplay = document.getElementById('total');
    const checkoutBtn = document.getElementById('checkoutBtn');

    function calculateTotal() {
        let total = 0;
        let checkedCount = 0;
        let selectedBookingIds = [];

        checkboxes.forEach(chk => {
            if (chk.checked) {
                total += parseFloat(chk.value);
                checkedCount++;
                
                const deleteLink = chk.closest('tr').querySelector('.btn-danger');
                if (deleteLink) {
                    const urlParams = new URLSearchParams(new URL(deleteLink.href).search);
                    const bookingId = urlParams.get('booking_id');
                    if (bookingId) {
                        selectedBookingIds.push(bookingId);
                    }
                }
            }
        });

        totalDisplay.textContent = total.toFixed(2);
        
        checkoutBtn.classList.toggle('disabled', checkedCount === 0);
        
        if (checkedCount > 0) {
            checkoutBtn.href = 'checkout.php?booking_ids=' + selectedBookingIds.join(',');
        } else {
            checkoutBtn.href = '#';
        }
    }

    checkboxes.forEach(chk => chk.addEventListener('change', calculateTotal));
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(chk => chk.checked = this.checked);
        calculateTotal();
    });

    calculateTotal(); 
</script>

</body>
</html>
