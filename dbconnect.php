<?php
    $host = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name = "simpledb";
    $conn = mysqli_connect($host, $db_username, $db_password, $dbname) or die("Could not connect to database" . mysqli_error ($conn));
?>