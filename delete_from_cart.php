<?php
session_start();
include("dbconnect.php"); // ตรวจสอบเส้นทางไฟล์ให้ถูกต้อง

// 1. ตรวจสอบการล็อกอินและสิทธิ์
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// 2. ตรวจสอบว่ามีการส่ง booking_id มาหรือไม่
if (isset($_GET['booking_id'])) {
    $booking_id = mysqli_real_escape_string($conn, $_GET['booking_id']);

    // 3. สร้างคำสั่ง DELETE
    // ต้องตรวจสอบ user_id เพื่อป้องกันไม่ให้ผู้ใช้ลบรายการของคนอื่น (Security Check)
    $delete_query = "DELETE FROM bookings 
                     WHERE id = '$booking_id' 
                     AND user_id = '$user_id' 
                     AND status = 'pending'";
    
    if (mysqli_query($conn, $delete_query)) {
        // ลบสำเร็จ
        $message = "✅ ลบคอร์สออกจากตะกร้าสำเร็จแล้ว";
    } else {
        // ล้มเหลว
        $message = "❌ เกิดข้อผิดพลาดในการลบ: " . mysqli_error($conn);
    }
} else {
    // ไม่มีการส่ง ID มา
    $message = "🚫 ไม่พบรายการที่ต้องการลบ";
}

// 4. Redirect กลับไปหน้าตะกร้าสินค้าพร้อมข้อความแจ้งเตือน
header("Location: cart.php?msg=" . urlencode($message));
exit();

// หมายเหตุ: คุณจะต้องเพิ่ม Logic ใน cart.php เพื่อแสดง $_GET['msg'] ด้วย
?>