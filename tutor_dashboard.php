<?php
include('dbconnect.php');
session_start();

// ตรวจสอบสิทธิ์อาจารย์
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'tutor') {
    header("Location: login.php");
    exit();
}

$tutor_name = $_SESSION['username'] ?? 'Tutor';
$message = '';
$current_action = 'add';
$edit_course = [];

// ====================== ✅ จัดการ "ลบคอร์ส" ======================
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $check = mysqli_query($conn, "SELECT image FROM tutor_courses WHERE id=$delete_id AND name='$tutor_name'");
    if ($check && mysqli_num_rows($check) > 0) {
        $course = mysqli_fetch_assoc($check);
        $image_path = "picture/" . $course['image'];
        // ลบไฟล์รูปภาพออกจากเซิร์ฟเวอร์ (ยกเว้น default.jpg)
        if (file_exists($image_path) && $course['image'] != 'default.jpg') {
            unlink($image_path);
        }
        mysqli_query($conn, "DELETE FROM tutor_courses WHERE id=$delete_id AND name='$tutor_name'");
        header("Location: tutor_dashboard.php?deleted=success");
        exit();
    } else {
        $message = "<div class='error'>❌ ไม่พบคอร์สที่ต้องการลบ หรือคุณไม่มีสิทธิ์ลบคอร์สนี้</div>";
    }
}

// ====================== ✅ แก้ไขคอร์ส (ดึงข้อมูลมาแสดง) ======================
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $result = mysqli_query($conn, "SELECT * FROM tutor_courses WHERE id=$edit_id AND name='$tutor_name'");
    if ($result && mysqli_num_rows($result) > 0) {
        $edit_course = mysqli_fetch_assoc($result);
        $current_action = 'edit';
    } else {
        $message = "<div class='error'>❌ ไม่พบคอร์สที่ต้องการแก้ไข</div>";
    }
}

// ====================== ✅ เพิ่ม/อัปเดตคอร์ส (POST) ======================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $price = intval($_POST['price']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category = mysqli_real_escape_string($conn, $_POST['category'] ?? ''); // ใช้ ?? '' เพื่อป้องกัน error ถ้าไม่ได้เลือก
    $name = mysqli_real_escape_string($conn, $tutor_name);

    // *** ตรวจสอบ Validation (ตามที่คุณต้องการ) ***
    if ($category == '') {
        $message = "<div class='error'>❌ กรุณาเลือกหมวดหมู่คอร์ส</div>";
        // หากอยู่ในโหมดแก้ไข ต้องดึงข้อมูลเดิมกลับมาแสดง
        if(isset($_POST['update_course']) && isset($_POST['course_id'])){
             $current_action = 'edit';
             $edit_id = intval($_POST['course_id']);
             $result = mysqli_query($conn, "SELECT * FROM tutor_courses WHERE id=$edit_id AND name='$tutor_name'");
             if ($result && mysqli_num_rows($result) > 0) {
                $edit_course = mysqli_fetch_assoc($result);
             }
        }
    } else {
        // --- ส่วนการจัดการรูปภาพ ---
        $target_dir = "picture/";
        if (!is_dir($target_dir)) mkdir($target_dir);

        $image_name = '';
        if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES["course_image"]["name"], PATHINFO_EXTENSION));
            // อนุญาตเฉพาะ jpg, jpeg, png
            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $image_name = uniqid("img_", true) . "." . $ext;
                move_uploaded_file($_FILES["course_image"]["tmp_name"], $target_dir . $image_name);
            } else {
                 $message = "<div class='error'>❌ ไม่อนุญาตให้ใช้ไฟล์นามสกุลนี้ กรุณาใช้ .jpg, .jpeg, หรือ .png</div>";
                 goto end_post_logic; // ข้ามส่วน INSERT/UPDATE
            }
        }

        // --- เพิ่มคอร์สใหม่ ---
        if (isset($_POST['add_course'])) {
            $final_img = $image_name != '' ? $image_name : 'default.jpg';
            $sql = "INSERT INTO tutor_courses (name, subject, price, time, description, image, category)
                    VALUES ('$name', '$subject', $price, '$time', '$description', '$final_img', '$category')";
            if (mysqli_query($conn, $sql)) {
                header("Location: tutor_dashboard.php?added=success");
                exit();
            } else {
                $message = "<div class='error'>เกิดข้อผิดพลาดในการเพิ่มคอร์ส: " . mysqli_error($conn) . "</div>";
            }
        }

        // --- อัปเดตคอร์สที่มีอยู่ ---
        if (isset($_POST['update_course'])) {
            $id = intval($_POST['course_id']);
            $query = "UPDATE tutor_courses SET subject='$subject', price=$price, time='$time', description='$description', category='$category'";
            
            if ($image_name != '') {
                // *** เพิ่มโค้ดลบรูปเก่าหากมีรูปใหม่ถูกอัปโหลด ***
                $old_img_result = mysqli_query($conn, "SELECT image FROM tutor_courses WHERE id=$id");
                if ($old_img_result && $old_img = mysqli_fetch_assoc($old_img_result)) {
                    $old_image_name = $old_img['image'];
                    $old_image_path = $target_dir . $old_image_name;
                    if (file_exists($old_image_path) && $old_image_name != 'default.jpg') {
                        unlink($old_image_path);
                    }
                }
                $query .= ", image='$image_name'"; // เพิ่มรูปใหม่ในฐานข้อมูล
            }
            
            $query .= " WHERE id=$id AND name='$tutor_name'";
            
            if (mysqli_query($conn, $query)) {
                header("Location: tutor_dashboard.php?updated=success");
                exit();
            } else {
                $message = "<div class='error'>เกิดข้อผิดพลาดในการอัปเดตคอร์ส: " . mysqli_error($conn) . "</div>";
            }
        }
    }
}
end_post_logic: // Label สำหรับ goto ในกรณีมีข้อผิดพลาดจากการอัปโหลดไฟล์

// ====================== ✅ ดึงข้อมูลคอร์สของอาจารย์ ======================
$all_courses = [];
$sql = "SELECT * FROM tutor_courses WHERE name = '$tutor_name' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $all_courses[] = $row;
}

// ====================== ✅ แจ้งเตือนสถานะ ======================
if (isset($_GET['added'])) $message = "<div class='success'>✅ เพิ่มคอร์สเรียบร้อยแล้ว!</div>";
if (isset($_GET['updated'])) $message = "<div class='success'>✅ อัปเดตคอร์สเรียบร้อย!</div>";
if (isset($_GET['deleted'])) $message = "<div class='success'>🗑️ ลบคอร์สเรียบร้อย!</div>";
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Tutor Dashboard | LearnHub</title>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
body { font-family: 'Kanit', sans-serif; background: linear-gradient(to bottom, #d3dbee, #ffffff); margin: 0; padding: 0; }
header { background-color: #4a69bd; color: white; padding: 15px 40px; font-size: 22px; font-weight: bold; box-shadow: 0 3px 8px rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; }
.header-left { display: flex; gap: 15px; align-items: center; }
.logout-btn { background-color: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 8px; font-size: 16px; cursor: pointer; text-decoration: none; transition: 0.3s; }
.logout-btn:hover { background-color: #c0392b; }
.container { max-width: 1000px; background-color: white; border-radius: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); margin: 60px auto; padding: 40px; }
h2 { text-align: center; color: #4a69bd; margin-bottom: 25px; }
label { font-weight: bold; display: block; margin-top: 15px; color: #333; }
input[type="text"], input[type="number"], input[type="file"], textarea, select { width: 100%; padding: 10px; margin-top: 8px; border: 1px solid #ccc; border-radius: 10px; font-size: 15px; resize: none; box-sizing: border-box; }
textarea { height: 100px; }
button[type="submit"] { background-color: #4a69bd; color: white; border: none; padding: 12px 20px; border-radius: 15px; font-size: 16px; margin-top: 20px; cursor: pointer; transition: 0.3s; width: 100%; }
button[type="submit"]:hover { background-color: #3b539b; }
.success { background-color: #dff0d8; color: #3c763d; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 20px; }
.error { background-color: #f2dede; color: #a94442; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 20px; }
.course-management { margin-top: 50px; }
.course-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.course-table th, .course-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
.course-table th { background-color: #f2f2f2; }
.action-btns a { margin-right: 5px; text-decoration: none; padding: 5px 10px; border-radius: 5px; }
.edit-btn { background-color: #3498db; color: white; }
.delete-btn { background-color: #e74c3c; color: white; }
.course-img { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; }
.toggle-btn { background-color: #5cb85c; color: white; border: none; padding: 10px; border-radius: 10px; cursor: pointer; width: 100%; margin-top: 20px; font-size: 16px; transition: background-color 0.3s; }
.toggle-btn:hover { background-color: #4cae4c; }
.hidden-content { display: none; }
</style>
</head>

<body>
<header>
    <div class="header-left">
        <span>💻 Tutor Dashboard | ยินดีต้อนรับ, <?= htmlspecialchars($tutor_name); ?></span>
    </div>
    <a href="logout.php" class="logout-btn">ออกจากระบบ</a>
</header>

<div class="container">
    <h2><?= ($current_action == 'edit') ? "✏️ แก้ไขคอร์สสอน: " . htmlspecialchars($edit_course['subject'] ?? 'ไม่พบชื่อวิชา') : "📘 สร้างคอร์สสอนใหม่"; ?></h2>
    <?= $message; ?>

    <form method="POST" enctype="multipart/form-data">
        <?php if ($current_action == 'edit'): ?>
            <input type="hidden" name="course_id" value="<?= $edit_course['id'] ?? ''; ?>">
            <input type="hidden" name="update_course" value="1">
        <?php else: ?>
            <input type="hidden" name="add_course" value="1">
        <?php endif; ?>

        <label>หมวดหมู่คอร์ส:</label>
        <select name="category" required>
            <option value="" disabled <?php if (!isset($edit_course['category'])) echo 'selected'; ?>>--- เลือกหมวดหมู่ ---</option>
            <?php
                // หมวดหมู่ทั้งหมด
                $categories = ['ดนตรี', 'ศิลปะ', 'คณิตศาสตร์', 'วิทยาศาสตร์', 'คอมพิวเตอร์', 'สังคมศาสตร์', 'ภาษาต่างประเทศ', 'บริหารการจัดการ', 'ทำอาหาร', 'ออกกำลังกาย', 'กราฟฟิก', 'เสริมสวย'];
                $selected_cat = $edit_course['category'] ?? '';
                foreach ($categories as $cat) {
                    // ใช้ค่าจาก POST ถ้ามีการ submit ผิดพลาด
                    $display_selected = ($current_action == 'add' && isset($_POST['category']) && $_POST['category'] == $cat) ? 'selected' : '';
                    if ($current_action == 'edit' && $selected_cat == $cat) {
                        $display_selected = 'selected';
                    }
                    echo "<option value='{$cat}' {$display_selected}>{$cat}</option>";
                }
            ?>
        </select>

        <label>ชื่อวิชาที่สอน:</label>
        <input type="text" name="subject" value="<?= htmlspecialchars($_POST['subject'] ?? ($edit_course['subject'] ?? '')); ?>" required>

        <label>ราคาคอร์ส (บาท):</label>
        <input type="number" name="price" min="0" value="<?= htmlspecialchars($_POST['price'] ?? ($edit_course['price'] ?? '')); ?>" required>

        <label>เวลาสอน:</label>
        <input type="text" name="time" placeholder="เช่น ทุกวันจันทร์ 18:00-20:00" value="<?= htmlspecialchars($_POST['time'] ?? ($edit_course['time'] ?? '')); ?>" required>

        <label>คำอธิบายคอร์ส:</label>
        <textarea name="description" required><?= htmlspecialchars($_POST['description'] ?? ($edit_course['description'] ?? '')); ?></textarea>

        <label>รูปภาพคอร์ส:</label>
        <input type="file" name="course_image" accept=".jpg, .jpeg, .png" <?php if($current_action == 'add' && !isset($_POST['update_course'])) echo 'required'; ?>>
        
        <?php if ($current_action == 'edit' && isset($edit_course['image'])): ?>
            <p style="font-size:12px; color:#555;">รูปภาพปัจจุบัน: <img src="picture/<?= htmlspecialchars($edit_course['image']); ?>" style="width: 30px; height: 30px; vertical-align: middle; border-radius: 3px;" onerror="this.src='picture/default.jpg';" /> (เลือกไฟล์ใหม่เพื่อแทนที่)</p>
        <?php endif; ?>

        <button type="submit"><?= $current_action == 'edit' ? '💾 บันทึกการแก้ไข' : '✅ บันทึกคอร์ส'; ?></button>

        <?php if ($current_action == 'edit'): ?>
            <a href="tutor_dashboard.php" style="display:block; text-align:center; margin-top:15px; color:#4a69bd; text-decoration: none;">ยกเลิกการแก้ไข</a>
        <?php endif; ?>
    </form>

    <button class="toggle-btn" onclick="toggleCourseList()">
        <i class="fas fa-list"></i> จัดการคอร์สที่คุณสอน (<?= count($all_courses); ?> วิชา)
    </button>

    <div class="course-management hidden-content" id="courseList">
        <h3>📖 รายการคอร์สของคุณ</h3>
        <?php if (empty($all_courses)): ?>
            <div class="error" style="background-color: #fcf8e3; color: #8a6d3b;">ยังไม่มีคอร์สที่คุณเพิ่ม</div>
        <?php else: ?>
            <table class="course-table">
                <thead>
                    <tr>
                        <th>รูป</th>
                        <th>วิชา</th>
                        <th>หมวดหมู่</th>
                        <th>ราคา</th>
                        <th>เวลา</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_courses as $course): ?>
                    <tr>
                        <td><img src="picture/<?= htmlspecialchars($course['image']); ?>" class="course-img" onerror="this.src='picture/default.jpg';"></td>
                        <td><?= htmlspecialchars($course['subject']); ?></td>
                        <td><?= htmlspecialchars($course['category']); ?></td>
                        <td><?= number_format($course['price'], 0); ?> บาท</td>
                        <td><?= htmlspecialchars($course['time']); ?></td>
                        <td class="action-btns">
                            <a href="tutor_dashboard.php?edit_id=<?= $course['id']; ?>" class="edit-btn">แก้ไข</a>
                            <a href="tutor_dashboard.php?delete_id=<?= $course['id']; ?>" class="delete-btn" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบคอร์สนี้?');">ลบ</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleCourseList() {
    var list = document.getElementById('courseList');
    var button = document.querySelector('.toggle-btn');
    if (list.style.display === "block") {
        list.style.display = "none";
        button.innerHTML = '<i class="fas fa-list"></i> จัดการคอร์สที่คุณสอน (<?= count($all_courses); ?> วิชา)';
    } else {
        list.style.display = "block";
        button.innerHTML = 'ซ่อนรายการคอร์ส';
    }
}

// เมื่ออยู่ในโหมดแก้ไข ให้เปิดรายการคอร์สไว้
<?php if ($current_action == 'edit' || isset($_POST['update_course'])): ?>
document.addEventListener('DOMContentLoaded', () => {
    var list = document.getElementById('courseList');
    var button = document.querySelector('.toggle-btn');
    list.style.display = "block";
    button.innerHTML = 'ซ่อนรายการคอร์ส';
});
<?php endif; ?>
</script>
</body>
</html>
