<?php
    ob_start(); //ป้องกัน header ส่งไม่ไป
    session_start();
    include_once 'dbconnect.php';

    $error_message = '' ;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // รับข้อมูลจากฟอร์ม
        $email = $_POST['txtemail'];
        $password = $_POST['txtpassword'];

        // ตรวจสอบ role user
        if (isset($_POST['student'])) {
            $role = 'student';
        } elseif (isset($_POST['tutor'])) {
            $role = 'tutor';
        } else {
            $role = null;
        }

        if ($role) {
            $query = "SELECT * FROM users WHERE email = ? AND role = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $email, $role);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Setting Session
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name']; 
                    $_SESSION['user_role'] = strtolower($user['role']);
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['login_message'] = "Welcome, " . $user['name'];
                    //Check role
                    if ($_SESSION['user_role'] == 'student') {
                        header("Location: student_dashboard.php");
                        exit;
                    } elseif ($_SESSION['user_role'] == 'tutor') {
                        header("Location: tutor_dashboard.php");
                        exit; }
                } else {
                    $error_message = "อีเมลหรือรหัสผ่านไม่ถูกต้อง" ;
                }
            } else {
                $error_message = "ไม่พบบัญชีนี้";
            }
        }
    }
    ob_end_flush();
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
    <link rel="stylesheet" href="style_login.css">
    <title>LERNHUB Login Page</title>
</head>
<body>
    <nav>
        <div class="nav-wrapper">
            <div class="logo">
                <i class="fa-solid fa-book-open-reader fa-flip-horizontal fa-2xl" style="color: #285171;"></i>
                <a href="index.php"><h1>LearnHub</h1></a>
            </div>
        </div>
    </nav>
<div class="container" id="container">
    <div class="form-container sign-in student">
        <form method="post" action="">
           <!--เข้าระบบสำหรับนักเรียน อยู่ทางซ้าย-->
            <h2>เข้าสู่ระบบสำหรับนักเรียน</h2>
            <div class="social-icons">
                <a href="#" class="icon"><i class="fa-brands fa-facebook"></i></a>
                <a href="#" class="icon"><i class="fa-brands fa-google"></i></a>
            </div>
            <input type="email" placeholder="Email" name="txtemail" required class="form-control">
            <input type="password" placeholder="Password" name="txtpassword" required class="form-control">
            <div class="link-row">
                <a href="#">ลืมรหัสผ่าน</a>
                <span>||</span>
                <a href="register.php">สมัครสมาชิก</a>
            </div>
            <button type="submit" name="student">Login</button>
            <a href="#" class="switch-to-tutor">เข้าสู่ระบบสำหรับติวเตอร์</a>
        </form>
    </div>

    <!--เข้าระบบสำหรับติวเตอร์ อยู่ทางขวา-->
    <div class="form-container sign-in tutor">
        <form method="post" action="">
            <h2>เข้าสู่ระบบสำหรับติวเตอร์</h2>
            <div class="social-icons">
                <a href="#" class="icon"><i class="fa-brands fa-facebook"></i></a>
                <a href="#" class="icon"><i class="fa-brands fa-google"></i></a>
            </div>
            <input type="email" placeholder="Email" name="txtemail" required class="form-control">
            <input type="password" placeholder="Password" name="txtpassword" required class="form-control">
            <div class="link-row">
                <a href="#">ลืมรหัสผ่าน</a>
                <span>||</span>
                <a href="register.php">สมัครสมาชิก</a>
            </div>
            <button type="submit" name="tutor">Login</button>
            <a href="#" class="switch-to-student">เข้าสู่ระบบสำหรับนักเรียน</a>
        </form>
    </div>

    <div class="image-container" id="image-container">
        <img id="login-image" src="https://cdn.pixabay.com/photo/2015/12/15/06/42/merry-christmas-1093758_1280.jpg" alt="login image" style="width:100%; height:100%; object-fit: cover;">
    </div>
</div>
    <script>
        const container = document.getElementById('container');
        const tutorlink = document.querySelector('.switch-to-tutor'); // ลิงก์สำหรับติวเตอร์
        const studentlink = document.querySelector('.switch-to-student'); // ลิงก์สำหรับนักเรียน
        const imageContainer = document.getElementById('image-container');
        const image = document.getElementById('login-image');

        //ลิ้งค์ภาพ
        const studentImage = 'https://cdn.pixabay.com/photo/2015/12/15/06/42/merry-christmas-1093758_1280.jpg';
        const tutorImage = 'https://media.istockphoto.com/id/1444142886/th/%E0%B8%A3%E0%B8%B9%E0%B8%9B%E0%B8%96%E0%B9%88%E0%B8%B2%E0%B8%A2/%E0%B8%84%E0%B8%A3%E0%B8%B9%E0%B9%81%E0%B8%A5%E0%B8%B0%E0%B8%99%E0%B8%B1%E0%B8%81%E0%B9%80%E0%B8%A3%E0%B8%B5%E0%B8%A2%E0%B8%99%E0%B9%83%E0%B8%AB%E0%B9%89%E0%B8%81%E0%B8%B1%E0%B8%99%E0%B8%AA%E0%B8%B9%E0%B8%87%E0%B8%AB%E0%B9%89%E0%B8%B2%E0%B9%83%E0%B8%99%E0%B8%AB%E0%B9%89%E0%B8%AD%E0%B8%87%E0%B8%AA%E0%B8%A1%E0%B8%B8%E0%B8%94.jpg?s=612x612&w=0&k=20&c=StVl5rHn7stb4wxaMdrpkWzmcKa6FveQfb2uwpm6HL8=';

        tutorlink.addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.add('active');

            // transition ภาพ
            imageContainer.addEventListener('transitionend', function handler() {
                image.src = tutorImage;
                imageContainer.removeEventListener('transitionend', handler);
            });
        });

        studentlink.addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.remove('active');

            // transition ภาพ
            imageContainer.addEventListener('transitionend', function handler() {
                image.src = studentImage;
                imageContainer.removeEventListener('transitionend', handler);
            });
        });
    </script>

    <div class="error-box">
        <?php if (!empty($error_message)) : ?>
            <p style="color:red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
