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
<style>
/* -------------------- CSS: style_admin.css -------------------- */
body {
    font-family: 'Kanit', sans-serif;
    background-color: #f4f7f6;
    margin: 0;
    padding: 0;
    color: #333;
}

nav {
    background-color: #364d79;
    color: white;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

nav h2 {
    margin: 0;
    font-weight: 700;
}

.btn-logout {
    background-color: #e74c3c;
    color: white;
    padding: 8px 15px;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s;
}

.btn-logout:hover {
    background-color: #c0392b;
}

.dashboard-content {
    padding: 20px 40px;
}

/* หัวข้อตาราง */
.h-tutor, .h-student {
    margin-top: 30px;
    border-bottom: 3px solid;
    padding-bottom: 5px;
    font-weight: 600;
}

.h-tutor { border-color: #3498db; color: #3498db; }
.h-student { border-color: #2ecc71; color: #2ecc71; }

/* ตาราง */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    background-color: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    border-radius: 8px;
    overflow: hidden;
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    font-size: 14px;
}

th {
    background-color: #ecf0f1;
    color: #555;
    font-weight: 600;
    text-transform: uppercase;
}

tr:hover {
    background-color: #f9f9f9;
}

/* ปุ่มจัดการ */
.btn {
    padding: 6px 10px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 13px;
    margin-right: 5px;
    display: inline-block;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-edit {
    background-color: #f39c12;
    color: white;
}
.btn-edit:hover { background-color: #e67e22; }

.btn-delete {
    background-color: #e74c3c;
    color: white;
}
.btn-delete:hover { background-color: #c0392b; }

/* -------------------- ฟอร์มแก้ไข (Modal-like) -------------------- */
.edit-form-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.edit-container {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    width: 90%;
    max-width: 500px;
}

.edit-container h2 {
    margin-top: 0;
    color: #364d79;
    border-bottom: 2px solid #364d79;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"],
.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

.btn-update {
    background-color: #3498db;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
    width: 100%;
    margin-top: 10px;
    font-weight: 600;
}

.btn-update:hover {
    background-color: #2980b9;
}

.btn-cancel {
    display: block;
    text-align: center;
    margin-top: 15px;
    color: #e74c3c;
    text-decoration: none;
    font-weight: 600;
}
</style>
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
