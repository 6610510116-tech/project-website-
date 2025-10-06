<?php
    session_start();
    include_once 'dbconnect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&family=Sansation:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Noto Sans Thai', sans-serif;
            font-family: 'Sansation', serif;
        }
        body {
            background-color: #d3dbee;
            background: linear-gradient(to bottom, #d3dbee, #ffffff);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            height: 100vh;
        }
        
        .container {
            background-color: #ffffff;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.35);
            position: relative;
            overflow: hidden;
            width: 768px;
            max-width: 100%;
            min-height: 480px;
        }

        .container p{
            font-size: 14px;
            font-weight: 100;
            line-height: 20px;
            letter-spacing: 0.3px;
            margin: 20px 0;
        }

        .container span{
            font-size: 12px;
        }

        .container a{
            color: #4a65a9;
            font-size: 13px;
            text-decoration: none;
            margin: 15px 0 10px;
        }

        .container button{
            background-color: #6b96b9;
            color: #ffffff;
            font-size: 12px;
            padding: 10px 45px;
            border: 1px solid transparent;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 10px;
            cursor: pointer;
        }

        .container form{
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
        }

        .container input{
            background-color: #eee;
            border: none;
            margin: 8px 0;
            padding: 12px 15px;
            font-size: 13px;
            border-radius: 8px;
            width: 100%;
            outline: none;
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .social-icons{
            margin: 10px 0;
        }

        .social-icons a{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 3px;
        }
        
        .toggle-container{
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            z-index: 1000;
            transition: all 0.6s ease-in-out;
        }

        .container .sign.in {
            left: 0;
            width: 50%;
            z-index: 2;
        }

        .image-container {
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 15;
        }
    </style>

    <title>LERNHUB Login Page</title>
</head>
<body>
<div class="container" id="container">
    <div class="form-container sign in">
        <form>
            <h1>เข้าสู่ระบบ</h1>
            <div class="social-icons">
                <a href="#" class="icon"><i class="fa-brands fa-facebook"></i></a>
                <a href="#" class="icon"><i class="fa-brands fa-google"></i></a>
            </div>
            <input type="email" placeholder="Email" name="txtemail" required_class="form-control">
            <input type="password" placeholder="Password" name="txtpassword" required_class="form-control">
            <div class="link-row">
                <a href="#">ลืมรหัสผ่าน</a>
                <span>||</span>
                <a href="#">สมัครสมาชิก</a>
            </div>
            <button>Login</button>
        </form>
    </div>
    <div class="image-container">
        <img src="https://cdn.pixabay.com/photo/2015/12/15/06/42/merry-christmas-1093758_1280.jpg" alt="login image" style="width:100%; height:100%; object-fit: cover; right:50%;">
    </div>
</div>
    <script src="scriptlogin.js"></script>
</body>
</html>
