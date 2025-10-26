<?php
include('dbconnect.php');
session_start();

// ตรวจสอบสิทธิ์อาจารย์ (Tutor)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'tutor') {
    header("Location: login.php");
    exit();
}

$tutor_id = $_SESSION['user_id'];
$tutor_name = $_SESSION['username'] ?? 'Tutor';
$message = '';
$payment_data = [];

// ====================== 1. ดึงข้อมูลปัจจุบันของอาจารย์ ======================
// สมมติว่าข้อมูลการชำระเงินถูกเก็บไว้ในตาราง users
$sql_fetch = "SELECT bank_name, account_number, account_name, qr_code FROM users WHERE id = $tutor_id";
$result_fetch = mysqli_query($conn, $sql_fetch);

if ($result_fetch && mysqli_num_rows($result_fetch) > 0) {
    $payment_data = mysqli_fetch_assoc($result_fetch);
}

// ====================== 2. จัดการฟอร์มบันทึกข้อมูล (POST) ======================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_payment_info'])) {
    
    // รับค่าจากฟอร์มและป้องกัน SQL Injection
    $bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);
    $account_number = mysqli_real_escape_string($conn, $_POST['account_number']);
    $account_name = mysqli_real_escape_string($conn, $_POST['account_name']);
    
    $update_fields = "bank_name = '$bank_name', account_number = '$account_number', account_name = '$account_name'";
    $qr_code_update = "";
    
    // --- ส่วนการจัดการ QR Code (ไฟล์รูปภาพ) ---
    if (isset($_FILES['qr_code']) && $_FILES['qr_code']['error'] == 0) {
        $target_dir = "profile_pics/"; // ใช้โฟลเดอร์เดียวกันกับรูปโปรไฟล์
        if (!is_dir($target_dir)) mkdir($target_dir);

        $ext = strtolower(pathinfo($_FILES["qr_code"]["name"], PATHINFO_EXTENSION));
        // อนุญาตเฉพาะ jpg, jpeg, png
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $qr_file_name = "qr_" . $tutor_id . "_" . time() . "." . $ext;
            $target_file = $target_dir . $qr_file_name;

            if (move_uploaded_file($_FILES["qr_code"]["tmp_name"], $target_file)) {
                $qr_code_update = ", qr_code = '$qr_file_name'";
                
                // ลบ QR Code เก่า (ถ้ามี)
                $old_qr = $payment_data['qr_code'] ?? null;
                if (!empty($old_qr) && file_exists($target_dir . $old_qr)) {
                    unlink($target_dir . $old_qr);
                }
            } else {
                 $message = "<div class='error'>❌ เกิดข้อผิดพลาดในการอัปโหลดไฟล์ QR Code</div>";
            }
        } else {
             $message = "<div class='error'>❌ ไม่อนุญาตให้ใช้ไฟล์นามสกุลนี้ กรุณาใช้ .jpg, .jpeg, หรือ .png สำหรับ QR Code</div>";
        }
    }
    
    // อัปเดตข้อมูลในตาราง users
    if (empty($message)) {
        $sql_update = "UPDATE users SET $update_fields $qr_code_update WHERE id = $tutor_id";
        
        if (mysqli_query($conn, $sql_update)) {
            $message = "<div class='success'>✅ บันทึกข้อมูลการรับเงินเรียบร้อยแล้ว!</div>";
            // ดึงข้อมูลใหม่มาแสดงทันที
            $result_fetch = mysqli_query($conn, "SELECT bank_name, account_number, account_name, qr_code FROM users WHERE id = $tutor_id");
            if ($result_fetch) $payment_data = mysqli_fetch_assoc($result_fetch);
        } else {
            $message = "<div class='error'>เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ระบบรับเงิน | Tutor</title>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
body { font-family: 'Kanit', sans-serif; background: linear-gradient(to bottom, #d3dbee, #ffffff); margin: 0; padding: 0; }
header { background-color: #4a69bd; color: white; padding: 15px 40px; font-size: 22px; font-weight: bold; box-shadow: 0 3px 8px rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; }
.header-right { display: flex; gap: 15px; align-items: center; } 
.action-btn { background-color: #5cb85c; color: white; border: none; padding: 8px 15px; border-radius: 8px; font-size: 16px; cursor: pointer; text-decoration: none; transition: 0.3s; }
.action-btn:hover { background-color: #4cae4c; }
.logout-btn { background-color: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 8px; font-size: 16px; cursor: pointer; text-decoration: none; transition: 0.3s; }
.logout-btn:hover { background-color: #c0392b; }
.container { max-width: 700px; background-color: white; border-radius: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); margin: 60px auto; padding: 40px; }
h2 { text-align: center; color: #4a69bd; margin-bottom: 25px; }
label { font-weight: bold; display: block; margin-top: 15px; color: #333; }
input[type="text"], input[type="file"] { width: 100%; padding: 10px; margin-top: 8px; border: 1px solid #ccc; border-radius: 10px; font-size: 15px; box-sizing: border-box; }
button[type="submit"] { background-color: #4a69bd; color: white; border: none; padding: 12px 20px; border-radius: 15px; font-size: 16px; margin-top: 20px; cursor: pointer; transition: 0.3s; width: 100%; }
button[type="submit"]:hover { background-color: #3b539b; }
.success { background-color: #dff0d8; color: #3c763d; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 20px; }
.error { background-color: #f2dede; color: #a94442; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 20px; }
.qr-preview { margin-top: 10px; text-align: center; }
.qr-preview img { max-width: 150px; border: 1px solid #ddd; border-radius: 5px; }
</style>
</head>

<body>
<header>
    <a href="tutor_dashboard.php" style="color: white; text-decoration: none;"><i class="fas fa-arrow-left"></i> กลับสู่หน้าจัดการคอร์ส</a>
    <div class="header-right">
        <span style="font-size: 16px;">ยินดีต้อนรับ, <?= htmlspecialchars($tutor_name); ?></span>
        <a href="logout.php" class="logout-btn">ออกจากระบบ</a>
    </div>
</header>

<div class="container">
    <h2>💸 ตั้งค่าระบบรับเงินของคุณ</h2>
    <?= $message; ?>
    
    <div class="alert" style="background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 8px;">
        <i class="fas fa-info-circle"></i> ข้อมูลที่คุณกรอกจะถูกนำไปแสดงให้นักเรียนใช้ในการชำระเงินเมื่อพวกเขาจองคอร์สของคุณ
    </div>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="save_payment_info" value="1">
        
        <label>ชื่อธนาคาร:</label>
        <input type="text" name="bank_name" value="<?= htmlspecialchars($payment_data['bank_name'] ?? ''); ?>" required>
        
        <label>เลขที่บัญชี:</label>
        <input type="text" name="account_number" value="<?= htmlspecialchars($payment_data['account_number'] ?? ''); ?>" required pattern="[0-9\-]+" title="กรุณาใส่เฉพาะตัวเลขหรือเครื่องหมายขีด (-)">
        
        <label>ชื่อบัญชี:</label>
        <input type="text" name="account_name" value="<?= htmlspecialchars($payment_data['account_name'] ?? ''); ?>" required>

        <label>อัปโหลด QR Code (สำหรับโอนเงิน):</label>
        <input type="file" name="qr_code" accept=".jpg, .jpeg, .png">

        <div class="qr-preview">
            <?php if (!empty($payment_data['qr_code'])): ?>
                <p style="font-size:14px; margin-bottom:5px;">QR Code ปัจจุบัน:</p>
                <img src="profile_pics/<?= htmlspecialchars($payment_data['qr_code']); ?>" 
                     alt="QR Code" 
                     onerror="this.style.display='none'; document.querySelector('.qr-preview p').innerText='ไม่พบไฟล์ QR Code ปัจจุบัน';">
            <?php else: ?>
                <p style="font-size:14px; color:#999;">ยังไม่มี QR Code (กรุณาอัปโหลด)</p>
            <?php endif; ?>
        </div>

        <button type="submit">💾 บันทึกข้อมูลการรับเงิน</button>
    </form>
</div>

</body>
</html>