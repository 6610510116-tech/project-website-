<?php
  session_start();
  include_once 'dbconnect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">	
	<link rel="stylesheet" href="style_learnmore.css">

  <title>เรียนรู้เพิ่มเติม | LearnHub</title>
</head>
<body>
  	<nav>
			<div class="container">
				<div class="nav-wrapper">
					<div class="logo">
						<i class="fa-solid fa-book-open-reader fa-flip-horizontal fa-2xl" style="color: #285171;"></i>
						<a href="index.php"><h1>LearnHub</h1></a>
					</div>
					<ul class="menu">
						<li><a href="index.php">หน้าหลัก</a></li>
						<li><a href="login.php">เข้าสู่ระบบ</a></li>
						<li><a href="register.php">ลงทะเบียน</a></li>
						<li><a href="admin_login.php">Admin</a></li>
					</ul>
				</div>
			</div>
		</nav>
  <div class="content-box">
    <h1>เรียนรู้เพิ่มเติมเกี่ยวกับระบบ</h1>
    <p>ระบบนี้ถูกออกแบบมาเพื่อให้ผู้ใช้สามารถลงทะเบียน เข้าสู่ระบบ และจัดการข้อมูลต่าง ๆ ได้อย่างสะดวก รวดเร็ว และปลอดภัย</p>
    <p>คุณสามารถดูรายละเอียดของการทำงาน ระบบติวเตอร์ และข้อมูลผู้ใช้ได้ในส่วนนี้</p>
	    <!-- ส่วนวิธีจอง -->
    <div class="booking-section">
      <button id="toggleBooking" class="booking-btn">ดูวิธีจอง</button>

      <div id="booking-details" class="booking-details" style="display: none;">
        <h3>ขั้นตอนการจองอาจารย์เพื่อเรียนบนแพลตฟอร์ม LearnHub</h3>
        <ol>
          <li><strong>ลงทะเบียนเข้าสู่ระบบ:</strong> ผู้ใช้งานเริ่มต้นด้วยการสมัครสมาชิกหรือล็อกอินเข้าสู่ระบบ เพื่อยืนยันตัวตนก่อนใช้งานแพลตฟอร์ม</li>
          <li><strong>ตั้งค่าข้อมูลส่วนตัว:</strong> หลังเข้าสู่ระบบ ผู้เรียนสามารถเข้าไปยังหน้าโปรไฟล์ (Profile) เพื่อกรอกหรือแก้ไขข้อมูลส่วนตัว เช่น ชื่อ เบอร์โทร ความสนใจ หรือระดับการเรียน เพื่อให้ระบบแนะนำติวเตอร์ที่เหมาะสม</li>
          <li><strong>เลือกหัวข้อหรือรายวิชาที่ต้องการเรียน:</strong> ไปที่หน้าค้นหาหรือหมวดหมู่รายวิชา จากนั้นเลือกหัวข้อที่สนใจ พร้อมดูรายละเอียดของอาจารย์ เช่น ประสบการณ์ รีวิว หรือค่าเรียน</li>
          <li><strong>เพิ่มคอร์สที่สนใจลงในตะกร้า:</strong> เมื่อเจอคอร์สที่ต้องการเรียน สามารถกด “เพิ่มในตะกร้า” เพื่อเก็บไว้ตัดสินใจภายหลังได้</li>
          <li><strong>ตรวจสอบตะกร้าการเรียน:</strong> เมื่อเลือกครบแล้ว ให้เข้าไปที่หน้า ตะกร้า (Cart) เพื่อตรวจสอบรายวิชาและอาจารย์ที่เลือกไว้</li>
          <li><strong>ยืนยันและดำเนินการชำระเงิน:</strong>
            <ul>
              <li>ติ๊กเลือกคอร์สที่ต้องการเรียนจริง</li>
              <li>กด “ดำเนินการชำระเงิน”</li>
              <li>กรอกข้อมูลครบและกด “ชำระเงิน” ระบบจะบันทึกคำสั่งจองเรียนเรียบร้อย</li>
            </ul>
          </li>
          <li><strong>เข้าหน้าคอร์สพร้อมเรียน:</strong> หลังชำระเงินสำเร็จ ระบบจะแสดงคอร์สในหน้า “คอร์สของฉัน” สามารถกด “เข้าห้องเรียน” เพื่อเริ่มเรียนกับอาจารย์ได้ทันที</li>
          <li><strong>ยกเลิกคอร์สเรียน (ถ้าต้องการหยุดเรียน):</strong> หากผู้เรียนไม่ต้องการเรียนต่อ สามารถกดยกเลิกคอร์สได้ผ่านหน้า “คอร์สของฉัน” และระบบ</li>
        </ol>
      </div>
    </div>
  </div>

  <script>
    const toggleBtn = document.getElementById('toggleBooking');
    const bookingDetails = document.getElementById('booking-details');

    toggleBtn.addEventListener('click', () => {
      if (bookingDetails.style.display === 'none') {
        bookingDetails.style.display = 'block';
        toggleBtn.textContent = 'ซ่อนวิธีจอง';
      } else {
        bookingDetails.style.display = 'none';
        toggleBtn.textContent = 'ดูวิธีจอง';
      }
    });
  </script>
</body>
</html>
