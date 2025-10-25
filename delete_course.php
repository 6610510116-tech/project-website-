<?php
include('dbconnect.php');
session_start();

// ตรวจสอบการเข้าถึง
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'tutor') {
    header("Location: login.php");
    exit();
}

$tutor_name = $_SESSION['username'] ?? '';
$course_id = intval($_GET['course_id'] ?? 0);

if ($course_id > 0) {
    // โค้ดสำหรับลบคอร์ส (ต้องแน่ใจว่าเป็นคอร์สของอาจารย์คนนี้จริง ๆ)
    $sql = "DELETE FROM tutor_courses WHERE id = $course_id AND name = '$tutor_name'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: tutor_dashboard.php?deleted=success");
        exit();
    } else {
        // หากล้มเหลวในการลบ
        header("Location: tutor_dashboard.php?error=delete_failed");
        exit();
    }
} else {
    // หากไม่ระบุ ID
    header("Location: tutor_dashboard.php");
    exit();
}
?>