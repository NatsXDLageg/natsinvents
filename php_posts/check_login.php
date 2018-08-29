<?php

session_start();
// include("../common_files/php_connection.php");

if(!isset($_SESSION['email']))
{
    $_SESSION['start_path'] = $_SERVER['PHP_SELF'];
    header("location:/views/login.php");
    exit();
}