<?php

session_start();
// include("../common_files/php_connection.php");

if(!isset($_SESSION['email']) || !isset($_SESSION['priority']) || $_SESSION['priority'] !== 999)
{
    $_SESSION['start_path'] = $_SERVER['PHP_SELF'];
    header("location:/pogo/views/login.php");
    exit();
}
else {
    $logged = true;
}