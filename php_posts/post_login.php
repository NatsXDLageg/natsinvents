<?php

session_start();

if(!isset($php_connection)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/php_connection.php");
    $php_connection = true;
}

if (!$_SERVER['REQUEST_METHOD'] === 'POST') {

    die("Método de requisição incorreto");
}

$email = $_POST['email'];
$userpwd = $_POST['password'];

$query="Select senha, status from usuario where email = '$email'";

$result = $mysqli->query($query) or die("couldn't execute the query");

if($result->num_rows == 1) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $pass = $row["senha"];

    if($pass == md5($userpwd)) {

        if($row["status"] == '1') {

            $_SESSION['email'] = $email;

            if(isset($_SESSION['start_path'])) {
                $location = "Location:".$_SESSION['start_path'];
                unset($_SESSION['start_path']);
                header($location);
            }
            else {
                header("Location:/pogo");
            }
            exit();
        }
        else {
            header("Location:/pogo/views/login.php?error=2");
            exit();
        }
    }
}

header("Location:/pogo/views/login.php?error=1");
exit();