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

$statement = $mysqli->prepare("Select senha, status, prioridade from usuario where email = ?");
$statement->bind_param('s', $email);
$result = $statement->execute();

if($result) {
    $result = $statement->get_result();

    if($result->num_rows == 1) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $pass = $row["senha"];

        if ($pass == md5($userpwd)) {

            if ($row["status"] == '1') {

                $_SESSION['email'] = $email;
                $_SESSION['priority'] = intval($row["prioridade"]);

                if (isset($_SESSION['start_path'])) {
                    $location = "Location:" . $_SESSION['start_path'];
                    unset($_SESSION['start_path']);
                    header($location);
                }
                else {
                    header("Location:/pogo");
                }
            }
            else {
                header("Location:/pogo/views/login.php?error=2");
            }
        }
    }
    else {
        header("Location:/pogo/views/login.php?error=1");
    }
}
else {
    header("Location:/pogo/views/login.php?error=900");
}
