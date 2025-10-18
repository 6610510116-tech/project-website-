<?php
  session_start();
  include_once 'dbconnect.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เรียนรู้เพิ่มเติม | LearnHub</title>
  <style>
    body {
      font-family: "Prompt", sans-serif;
      background-color: #f7f7ff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      flex-direction: column;
      margin: 0;
    }
    .content-box {
      background: white;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      width: 60%;
      text-align: center;
    }
    a.back {
      display: inline-block;
      margin-top: 20px;
      color: #6c63ff;
      text-decoration: none;
      font-weight: bold;
    }
    a.back:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="content-box">
    <h1>เรียนรู้เพิ่มเติมเกี่ยวกับระบบ</h1>
    <p>ระบบนี้ถูกออกแบบมาเพื่อให้ผู้ใช้สามารถลงทะเบียน เข้าสู่ระบบ และจัดการข้อมูลต่าง ๆ ได้อย่างสะดวก รวดเร็ว และปลอดภัย</p>
    <p>คุณสามารถดูรายละเอียดของการทำงาน ระบบติวเตอร์ และข้อมูลผู้ใช้ได้ในส่วนนี้</p>
    <a href="index.php" class="back">← กลับหน้าหลัก</a>
  </div>
</body>
</html>
