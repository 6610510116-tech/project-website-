<?php
		session_start();

		//include database connection
		include_once 'dbconnect.php';

		//Check if form is submitted
		if (isset($_POST['btnLogin'])) {
			//prevent sql injection using mysqli_real_escape_string() function
			$admin_email = mysqli_real_escape_string($conn, $_POST['txtAdmin_email']);
			$admin_password = mysqli_real_escape_string($conn, $_POST['txtAdmin_password']);

			if ($admin_email === 'admin@email.com' && $admin_password === '123456') {
				//login success
				//create session 
				$_SESSION['user_id'] = 0;
				$_SESSION['user_name'] ="Admin";
				header("Location: admin.php");
				exit;
			} else {
				//login failed
				$error_message = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
			}
		}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - LearnHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style_admin_login.css">
</head>
<body>
    <div class="admin-login-box">
        <h2><i class="fa-solid fa-user-gear"></i> Admin Login</h2>
        
        <form role="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="loginform">
            <input type="email" placeholder="Email" name="txtAdmin_email" required>
            <input type="password" placeholder="Password" name="txtAdmin_password" required>
            <button type="submit" name="btnLogin" value="Login" class="btn btn-primary">Login as Admin</button>
            <p style="margin-top: 15px;"><a href="index.php" style="color: #4a65a9; text-decoration: none;">กลับหน้าหลัก</a></p>
        </form>
		<?php if (isset($error_message)) { 
			echo "<div class='alert alert-danger'>" . $error_message . "</div>"; 
    		} 
		?>
    </div>
</body>
</html>
