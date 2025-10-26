<?php
session_start();
// ตรวจสอบเส้นทางไฟล์ dbconnect.php ให้ถูกต้อง
include_once 'dbconnect.php'; 

// ตรวจสอบสิทธิ์แอดมิน (สมมติว่า admin_login.php ทำการตั้งค่า $_SESSION['user_id'] และอาจจะ $_SESSION['role']='admin' ด้วย)
if (!isset($_SESSION['user_id'])) {
    // ผู้ใช้ที่ไม่มีสิทธิ์จะถูกส่งไปหน้า Admin Login
    header("Location: admin_login.php"); 
    exit();
}

// 1. ฟังก์ชัน DELETE (ลบ)
if (isset($_GET['delete_id'])) {
    $user_id_to_delete = mysqli_real_escape_string($conn, $_GET['delete_id']);

    // ป้องกันไม่ให้แอดมินลบตัวเอง
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id_to_delete) {
        $message = '🚫 ไม่สามารถลบบัญชีแอดมินที่กำลังใช้งานอยู่ได้';
    } else {
        // **ข้อควรระวัง:** ในระบบจริง ควรลบข้อมูลที่เกี่ยวข้องในตารางอื่น ๆ ก่อน (เช่น bookings, tutor_courses)
        $delete_query = "DELETE FROM users WHERE id = '$user_id_to_delete'";
        
        if (mysqli_query($conn, $delete_query)) {
            $message = "✅ ลบผู้ใช้ ID: $user_id_to_delete สำเร็จแล้ว";
        } else {
            $message = "❌ เกิดข้อผิดพลาดในการลบข้อมูล: " . mysqli_error($conn);
        }
    }
    // Redirect เพื่อล้างพารามิเตอร์ GET (delete_id) และป้องกันการลบซ้ำ
    header("Location: admin.php?msg=" . urlencode($message)); 
    exit();
}

// 2. ฟังก์ชัน EDIT (แก้ไข) - เตรียมข้อมูลหากมี ID ถูกส่งมา (แสดงฟอร์ม)
$editing_user = null;
if (isset($_GET['edit_id'])) {
    $edit_id = mysqli_real_escape_string($conn, $_GET['edit_id']);
    // ดึงข้อมูลที่จำเป็น
    $edit_result = mysqli_query($conn, "SELECT id, name, email, role FROM users WHERE id='$edit_id' LIMIT 1");
    $editing_user = mysqli_fetch_assoc($edit_result);
    if (!$editing_user) {
        // ถ้าไม่พบผู้ใช้ ให้กลับไปหน้า Admin
        header("Location: admin.php"); 
        exit();
    }
}

// 3. ฟังก์ชัน EDIT (บันทึกข้อมูลที่แก้ไข)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_user') {
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $new_name = mysqli_real_escape_string($conn, $_POST['name']); 
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_role = mysqli_real_escape_string($conn, $_POST['role']);
    $new_password = mysqli_real_escape_string($conn, $_POST['password']);

    // เริ่มต้นสร้างคำสั่ง UPDATE
    $update_fields = "name='$new_name', email='$new_email', role='$new_role'";
    
    if (!empty($new_password)) {
        // **การใช้งานจริง: ควรเข้ารหัสรหัสผ่านด้วย password_hash() เพื่อความปลอดภัยสูงสุด**
        // ตัวอย่างการเข้ารหัส: $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        // $update_fields .= ", password='$hashed_password'"; 
        $update_fields .= ", password='$new_password'"; // ใช้ตามโค้ดต้นฉบับ
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

// ดึงข้อมูลผู้ใช้แยกตามบทบาท
$tutor_result = mysqli_query($conn, "SELECT id, name, email, role, created_at FROM users WHERE role='tutor' ORDER BY id ASC");
$student_result = mysqli_query($conn, "SELECT id, name, email, role, created_at FROM users WHERE role='student' ORDER BY id ASC");

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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel = "stylesheet" href = "style_admin.css">
</head>
<body>

<nav>
    <h2>Admin Dashboard</h2>
    <a href="logout.php" class="btn btn-logout">ออกจากระบบ <i class="fa-solid fa-right-from-bracket"></i></a>
</nav>

<?php if ($editing_user): ?>
<div class="edit-form-wrapper">
    <div class="edit-container">
        <h2>แก้ไขข้อมูลผู้ใช้ ID: <?= $editing_user['id']; ?></h2>
        
        <form method="post" action="admin.php">
            <input type="hidden" name="action" value="update_user">
            <input type="hidden" name="user_id" value="<?= $editing_user['id']; ?>">
            
            <div class="form-group">
                <label for="name">ชื่อผู้ใช้:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($editing_user['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">อีเมล:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($editing_user['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="role">บทบาท:</label>
                <select id="role" name="role" required>
                    <option value="tutor" <?= ($editing_user['role'] == 'tutor') ? 'selected' : ''; ?>>Tutor (ติวเตอร์)</option>
                    <option value="student" <?= ($editing_user['role'] == 'student') ? 'selected' : ''; ?>>Student (นักเรียน)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="password">รหัสผ่านใหม่ (ว่างไว้ถ้าไม่ต้องการเปลี่ยน):</label>
                <input type="password" id="password" name="password" placeholder="ป้อนรหัสผ่านใหม่">
            </div>

            <button type="submit" class="btn-update">
                <i class="fa-solid fa-save"></i> บันทึกการแก้ไข
            </button>
        </form>
        
        <a href="admin.php" class="btn-cancel">
            <i class="fa-solid fa-xmark"></i> ยกเลิก/ปิดฟอร์ม
        </a>
    </div>
</div>
<?php endif; ?>

<div class="dashboard-content">

    <h3 class="h-tutor">🧑‍🏫 รายชื่ออาจารย์ (Tutor)</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ชื่อผู้ใช้</th>
                <th>อีเมล</th>
                <th>บทบาท</th>
                <th>วันที่สมัคร</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (mysqli_num_rows($tutor_result) > 0) {
                while ($row = mysqli_fetch_assoc($tutor_result)) { 
            ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td> 
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><span style="color:#3498db; font-weight:600;"><?= $row['role']; ?></span></td>
                <td><?= $row['created_at']; ?></td>
                <td>
                    <a href="admin.php?edit_id=<?= $row['id']; ?>" class="btn btn-edit"><i class="fa-solid fa-pen-to-square"></i> แก้ไข</a>
                    <a href="admin.php?delete_id=<?= $row['id']; ?>" class="btn btn-delete" 
                       onclick="return confirm('แน่ใจหรือไม่ว่าจะลบผู้ใช้ ID: <?= $row['id']; ?> (<?= htmlspecialchars($row['name']); ?>) นี้?');"><i class="fa-solid fa-trash-can"></i> ลบ</a>
                </td>
            </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;'>ยังไม่มีผู้ใช้บทบาท Tutor ในระบบ</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <h3 class="h-student">🎒 รายชื่อนักเรียน (Student)</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ชื่อผู้ใช้</th>
                <th>อีเมล</th>
                <th>บทบาท</th>
                <th>วันที่สมัคร</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (mysqli_num_rows($student_result) > 0) {
                while ($row = mysqli_fetch_assoc($student_result)) { 
            ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td> 
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><span style="color:#2ecc71; font-weight:600;"><?= $row['role']; ?></span></td>
                <td><?= $row['created_at']; ?></td>
                <td>
                    <a href="admin.php?edit_id=<?= $row['id']; ?>" class="btn btn-edit"><i class="fa-solid fa-pen-to-square"></i> แก้ไข</a>
                    <a href="admin.php?delete_id=<?= $row['id']; ?>" class="btn btn-delete" 
                       onclick="return confirm('แน่ใจหรือไม่ว่าจะลบผู้ใช้ ID: <?= $row['id']; ?> (<?= htmlspecialchars($row['name']); ?>) นี้?');"><i class="fa-solid fa-trash-can"></i> ลบ</a>
                </td>
            </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;'>ยังไม่มีผู้ใช้บทบาท Student ในระบบ</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
