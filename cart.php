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

// --- ส่วนที่ 1: แก้ไข SQL Query เพื่อให้ข้อมูลแสดงผล ---
// เราจะดึงชื่อผู้สอนจาก t.name โดยตรง และตัดการ JOIN ที่มีปัญหา (JOIN users u ON t.name = u.name) ออก
$query = "SELECT 
            b.id AS booking_id, 
            t.subject, 
            t.price,
            t.name AS tutor_name, /* *** ดึงชื่อผู้สอนจากคอลัมน์ name ของตารางคอร์ส (tutor_courses.name) โดยตรง *** */
            b.start_time,
            b.status
          FROM bookings b 
          JOIN tutor_courses t ON b.tutor_id = t.id 
          /* ลบ JOIN users u ออก เพื่อแก้ปัญหาการเชื่อมโยงด้วยชื่อ */
          WHERE b.user_id = '$user_id' AND b.status = 'pending'";

$result = mysqli_query($conn, $query);

// จัดเก็บข้อมูลในอาร์เรย์และคำนวณราคารวม (ใช้ใน PHP เพื่อหา Total Price ตั้งต้น)
$cart_items = [];
$total_price_php = 0; 
if ($result) {
    while($row = mysqli_fetch_assoc($result)){
        $cart_items[] = $row;
        $total_price_php += $row['price'];
    }
}
// --- จบส่วนแก้ไข Logic PHP/SQL ---
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตะกร้าสินค้า - LearnHub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f4f6fa;
        }
        /* Navbar ที่ย้ายไปด้านขวาแล้ว */
        .navbar {
            background-color: #4a65a9;
            color: white;
        }
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        .btn-outline-light:hover {
            background-color: #ffffff33;
        }
        .table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .table thead {
            background-color: #4a65a9;
            color: white;
        }
        .badge {
            font-size: 0.9rem;
        }
        .total-box {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: right;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg px-4 py-3">
    <a class="navbar-brand" href="#">🛒 ตะกร้าสินค้าของฉัน</a>
    <div class="ms-auto">
        <a href="student_dashboard.php" class="btn btn-outline-light">กลับสู่หน้าหลัก</a>
    </div>
</nav>

<div class="container my-5">
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
                    <td><a href="delete_from_cart.php?booking_id=<?= $row['booking_id'] ?>" class="btn btn-danger btn-sm">ลบ</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </form>

        <div class="total-box mt-4">
            <h5>รวมราคาที่เลือก: <span id="total" class="text-success fw-bold">0.00</span> บาท</h5>
            <a id="checkoutBtn" href="#" class="btn btn-primary btn-lg mt-3 disabled">ดำเนินการชำระเงิน</a>
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
        let selectedBookingIds = []; // สร้าง Array เพื่อเก็บ ID การจองที่ถูกเลือก

        checkboxes.forEach(chk => {
            if (chk.checked) {
                total += parseFloat(chk.value);
                checkedCount++;
                
                // ดึง booking_id จาก URL ของปุ่ม "ลบ" ที่อยู่ในแถวเดียวกัน
                const deleteLink = chk.closest('tr').querySelector('.btn-danger');
                if (deleteLink) {
                    // แยกเอาเฉพาะค่า booking_id ออกมา (เช่น 123)
                    const urlParams = new URLSearchParams(new URL(deleteLink.href).search);
                    const bookingId = urlParams.get('booking_id');
                    if (bookingId) {
                        selectedBookingIds.push(bookingId);
                    }
                }
            }
        });

        totalDisplay.textContent = total.toFixed(2);
        
        // ถ้าไม่มีรายการที่เลือก ให้ปิดใช้งานปุ่มชำระเงิน
        checkoutBtn.classList.toggle('disabled', checkedCount === 0);
        
        // *** ส่วนสำคัญ: อัปเดตลิงก์ชำระเงิน ***
        if (checkedCount > 0) {
            // สร้าง URL ที่ส่ง ID ทั้งหมดไปยัง checkout.php
            checkoutBtn.href = 'checkout.php?booking_ids=' + selectedBookingIds.join(',');
        } else {
            checkoutBtn.href = '#';
        }
    }

    // Event Listeners
    checkboxes.forEach(chk => chk.addEventListener('change', calculateTotal));
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(chk => chk.checked = this.checked);
        calculateTotal();
    });

    // เรียกใช้ครั้งแรกเมื่อโหลดหน้า (เพื่อให้ราคารวมเริ่มต้นถูกต้อง)
    calculateTotal(); 
</script>

</body>
</html>