<?php
session_start();
include_once 'dbconnect.php';

// ตรวจสอบสิทธิ์แอดมิน
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลผู้ใช้ทั้งหมด
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");
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
    <td><?= htmlspecialchars($row['username']); ?></td>
    <td><?= htmlspecialchars($row['email']); ?></td>
    <td><?= $row['role']; ?></td>
    <td><?= $row['created_at']; ?></td>
    <td>
      <a href="edit_user.php?id=<?= $row['id']; ?>" class="btn btn-edit">แก้ไข</a>
      <a href="delete_user.php?id=<?= $row['id']; ?>" class="btn btn-delete" onclick="return confirm('แน่ใจหรือไม่ว่าจะลบผู้ใช้นี้?');">ลบ</a>
    </td>
  </tr>
  <?php } ?>
</table>

</body>
</html>
