<?php
		//4.check login info from users table

		//start session
		session_start();

		if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != "") {
			header("Location: index.php");
			exit;
		}

		include_once 'dbconnect.php';

		if (isset($_POST ['btnlogin'])) {
			//4.1 get form data เพิ่มฟังก์ชัน mysql_real_escape_string เพื่อเป็นการป้องกันจากการถูกโจมตี
			//prevent sql injection using mysqli_real_escape_string() function
			$email = mysqli_real_escape_string($conn, $_POST['txtemail']);
			$password = mysqli_real_escape_string($conn, $_POST['txtpassword']);

			$sql_txt = "SELECT * FROM users WHERE email='$email' and password='" . md5($password) ."'";
			$result = mysqli_query($conn, $sql_txt);

			if($row = mysqli_fetch_array($result)) {
				//login success
				$_SESSION['user_id'] = $row['id'];
				$_SESSION['user_name'] = $row['name'];
				//redirect to index.php || header เป็นคำสั่งที่เราจะใช้ให้เป็นอีกหน้านึง	
				header("Location: index.php");
				exit;					
			} else {
				//login failed
				$error_message = "Incorrect Email or Password!!!";
			}
		}

?>

<!DOCTYPE html>
<html>
<head>
	<title>PHP Login</title>
	<meta content="width=device-width, initial-scale=1.0" name="viewport" >
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
</head>
<body>

<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
		<!-- add header -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="index.php">PHP Simple CRUD</a>
		</div>
		<!-- menu items -->
		<div class="collapse navbar-collapse" id="navbar1">
			<ul class="nav navbar-nav navbar-right">
				<li class="active"><a href="login.php">Login</a></li>
				<li><a href="register.php">Sign Up</a></li>
				<li><a href="admin_login.php">Admin</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="container">
	<div class="row">
		<div class="col-md-4 col-md-offset-4 well">
			<form role="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="loginform">
				<fieldset>
					<legend>Login</legend>

					<div class="form-group">
						<label for="name">Email</label>
						<input type="text" name="txtemail" placeholder="Your Email" required class="form-control" />
					</div>

					<div class="form-group">
						<label for="name">Password</label>
						<input type="password" name="txtpassword" placeholder="Your Password" required class="form-control" />
					</div>

					<div class="form-group">
						<input type="submit" name="btnlogin" value="Login" class="btn btn-primary" />
					</div>
				</fieldset>
			</form>
			<!--5.display message -->
			<?php
				if(isset($error_message)) {
					echo '<div class="alert alert-danger">';
					echo $error_message;
					echo '</div>';
				}
			?>

		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-4 text-center">
		New User? <a href="register.php">Sign Up Here</a>
		</div>
	</div>
</div>
</body>
</html>