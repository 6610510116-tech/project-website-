<?php
session_start();
// ตรวจสอบเส้นทางไฟล์ dbconnect.php ให้ถูกต้องตามโครงสร้างโฟลเดอร์ของคุณ
include_once 'dbconnect.php';

// ตรวจสอบสิทธิ์แอดมิน
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    // ผู้ใช้ที่ไม่มีสิทธิ์จะถูกส่งไปหน้า Admin Login
    header("Location: admin_login.php"); 
    exit();
}

// ------------------------------------------------------------------
// 1. ฟังก์ชัน DELETE (ลบ)
// ------------------------------------------------------------------
if (isset($_GET['delete_id'])) {
    $user_id_to_delete = mysqli_real_escape_string($conn, $_GET['delete_id']);

    // ป้องกันไม่ให้แอดมินลบตัวเอง
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id_to_delete) {
        echo "<script>alert('🚫 ไม่สามารถลบบัญชีแอดมินที่กำลังใช้งานอยู่ได้'); window.location.href='admin.php';</script>";
        exit();
    }

    $delete_query = "DELETE FROM users WHERE id = '$user_id_to_delete'";
    
    if (mysqli_query($conn, $delete_query)) {
        $message = "✅ ลบผู้ใช้ ID: $user_id_to_delete สำเร็จแล้ว";
    } else {
        $message = "❌ เกิดข้อผิดพลาดในการลบข้อมูล: " . mysqli_error($conn);
    }
    // Redirect เพื่อล้างพารามิเตอร์ GET (delete_id) และป้องกันการลบซ้ำ
    header("Location: admin.php?msg=" . urlencode($message)); 
    exit();
}

// ------------------------------------------------------------------
// 2. ฟังก์ชัน EDIT (แก้ไข) - เตรียมข้อมูลหากมี ID ถูกส่งมา (แสดงฟอร์ม)
// ------------------------------------------------------------------
$editing_user = null;
if (isset($_GET['edit_id'])) {
    $edit_id = mysqli_real_escape_string($conn, $_GET['edit_id']);
    $edit_result = mysqli_query($conn, "SELECT id, username, email, role FROM users WHERE id='$edit_id' LIMIT 1");
    $editing_user = mysqli_fetch_assoc($edit_result);
    if (!$editing_user) {
        // ถ้าไม่พบผู้ใช้ ให้กลับไปหน้า Admin
        header("Location: admin.php"); 
        exit();
    }
}

// ------------------------------------------------------------------
// 3. ฟังก์ชัน EDIT (บันทึกข้อมูลที่แก้ไข)
// ------------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_user') {
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_role = mysqli_real_escape_string($conn, $_POST['role']);
    $new_password = mysqli_real_escape_string($conn, $_POST['password']);

    $update_fields = "username='$new_username', email='$new_email', role='$new_role'";
    
    if (!empty($new_password)) {
        // **การใช้งานจริง: ควรเข้ารหัสรหัสผ่านด้วย password_hash()**
        $update_fields .= ", password='$new_password'"; 
    }

    $update_query = "UPDATE users SET $update_fields WHERE id='$user_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $message = "✅ แก้ไขข้อมูลผู้ใช้ ID: $user_id สำเร็จแล้ว";
    } else {
        $message = "❌ เกิดข้อผิดพลาดในการแก้ไขข้อมูล: " . mysqli_error($conn);
    }
    header("Location: admin.php?msg=" . urlencode($message));
    exit();
}

// ------------------------------------------------------------------
// ดึงข้อมูลผู้ใช้ทั้งหมดเพื่อแสดงตาราง
// ------------------------------------------------------------------
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");

// แสดงข้อความแจ้งเตือน (ถ้ามี)
if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
    echo "<script>alert('$message');</script>";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Kanit', sans-serif;
    background: #eef3fb;
    margin: 0;
    padding: 0;
}
nav {
    background: #4a65a9;
    color: white;
    padding: 15px 50px;
    display: flex; justify-content: space-between; align-items: center;
}
/* CSS สำหรับฟอร์มแก้ไข */
.edit-form-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px 0; 
}
.edit-container {
    width: 90%;
    max-width: 550px;
    padding: 30px;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    border-left: 5px solid #6b96b9; /* เปลี่ยนสีเส้นขอบเพื่อให้เข้ากับปุ่ม Edit */
}
.edit-container h2 {
    color: #6b96b9;
    margin-top: 0;
    text-align: center;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}
.form-group { margin-bottom: 15px; }
.form-group label { display: block; font-weight: 600; margin-bottom: 5px; color: #333; }
.form-group input, .form-group select { 
    width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; 
}
.btn-update { 
    background: #6b96b9; color: white; padding: 10px 20px; border: none; border-radius: 5px; 
    cursor: pointer; font-size: 16px; margin-top: 10px; width: 100%; 
}
.btn-update:hover { background: #5a7f9a; }
.btn-cancel { 
    display: block; text-align: center; margin-top: 15px; color: #d9534f; 
    text-decoration: none; font-size: 14px; 
}
/* CSS เดิมสำหรับตาราง */
table {
    width: 90%;
    margin: 40px auto;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
th, td {
    padding: 15px;
    text-align: center;
}
th {
    background: #4a65a9;
    color: white;
}
tr:nth-child(even) {
    background: #f5f7fa;
}
a.btn {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    margin: 0 3px;
}
.btn-edit {background: #6b96b9; color: white;}
.btn-delete {background: #d9534f; color: white;}
.btn-logout {background: #f7c948; color: black;}
</style>
</head>
<body>

<nav>
    <h2>Admin Dashboard</h2>
    <a href="logout.php" class="btn btn-logout">ออกจากระบบ</a>
</nav>

<?php if ($editing_user): ?>
<div class="edit-form-wrapper">
    <div class="edit-container">
        <h2>แก้ไขข้อมูลผู้ใช้ ID: <?= $editing_user['id']; ?></h2>
        
        <form method="post" action="admin.php">
            <input type="hidden" name="action" value="update_user">
            <input type="hidden" name="user_id" value="<?= $editing_user['id']; ?>">
            
            <div class="form-group">
                <label for="username">ชื่อผู้ใช้:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($editing_user['username']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">อีเมล:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($editing_user['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="role">บทบาท:</label>
                <select id="role" name="role" required>
                    <option value="admin" <?= ($editing_user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="tutor" <?= ($editing_user['role'] == 'tutor') ? 'selected' : ''; ?>>Tutor (ติวเตอร์)</option>
                    <option value="student" <?= ($editing_user['role'] == 'student') ? 'selected' : ''; ?>>Student (นักเรียน)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="password">รหัสผ่านใหม่ (ว่างไว้ถ้าไม่ต้องการเปลี่ยน):</label>
                <input type="password" id="password" name="password" placeholder="ป้อนรหัสผ่านใหม่">
            </div>

            <button type="submit" class="btn-update">บันทึกการแก้ไข</button>
        </form>
        
        <a href="admin.php" class="btn-cancel">ยกเลิก/ปิดฟอร์ม</a>
    </div>
</div>
<?php endif; ?>

<table>
    <tr>
        <th>ID</th>
        <th>ชื่อผู้ใช้</th>
        <th>อีเมล</th>
        <th>บทบาท</th>
        <th>วันที่สมัคร</th>
        <th>จัดการ</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?= $row['id']; ?></td>
        <td><?= htmlspecialchars($row['name']); ?></td>
        <td><?= htmlspecialchars($row['email']); ?></td>
        <td><?= $row['role']; ?></td>
        <td><?= $row['created_at']; ?></td>
        <td>
            <a href="admin.php?edit_id=<?= $row['id']; ?>" class="btn btn-edit">แก้ไข</a>
            <a href="admin.php?delete_id=<?= $row['id']; ?>" class="btn btn-delete" 
               onclick="return confirm('แน่ใจหรือไม่ว่าจะลบผู้ใช้นี้?');">ลบ</a>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
