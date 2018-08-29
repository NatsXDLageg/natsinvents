<?php

session_start();
include($_SERVER['DOCUMENT_ROOT']."/../common_files/php_connection.php");

if (!$_SERVER['REQUEST_METHOD'] === 'POST') {

    die("Método de requisição incorreto");
}

$email = $_POST['email'];
$userpwd = $_POST['password'];

$query="Select senha from usuario where email = '$email'";

$result = $mysqli->query($query) or die("couldn't execute the query");

if($result->num_rows == 1) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $pass = $row["senha"];

    if($pass == md5($userpwd))
    {
        $_SESSION['email'] = $email;

        if(isset($_SESSION['start_path'])) {
            $location = "Location:".$_SESSION['start_path'];
            unset($_SESSION['start_path']);
            header($location);
        }
        else {
            header("Location:/index.php");
        }
        exit();
    }
}

header("Location:/views/login.php?error=1");
exit();