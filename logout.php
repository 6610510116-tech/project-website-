<?php
		//16.clear sessions
		session_start();

		if(isset($_SESSION['user_id'])) {
			session_destroy();
			//unset session varibles
			unset($_SESSION['user_id']);
			unset($_SESSION['user_name']);
			header("Location: index.php");
			exit;
		} else {
			header("Location: index.php");
			exit;
		}
		
?>