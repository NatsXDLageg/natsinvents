<?php

if(!isset($check_login)) {
    include($_SERVER['DOCUMENT_ROOT']."/pogo/php_posts/check_login.php");
    $check_login = true;
}
$logged = false;

if(!isset($_SESSION['priority']) || $_SESSION['priority'] !== 999)
{
    $_SESSION['start_path'] = $_SERVER['PHP_SELF'];
    header("location:/pogo/views/login.php");
    exit();
}
else {
    $logged = true;
}