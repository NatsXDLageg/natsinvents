<?php

session_start();

if(!isset($php_connection)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/php_connection.php");
    $php_connection = true;
}

if (!$_SERVER['REQUEST_METHOD'] === 'POST') {

    die("Método de requisição incorreto");
}

$name = $_POST['name'];
$userpwd = md5($_POST['password']);
$email = $_POST['email'];
$team = $_POST['team'];
$level = $_POST['level'];

$statement = $mysqli->prepare("Select senha from usuario where email = ?");
$statement->bind_param('s', $email);
$result = $statement->execute();

if($result) {
    $result = $statement->get_result();

    if ($result->num_rows == 1) {
        header("Location:/pogo/views/register.php?error=1");
    }
    else {
        if (!in_array($team, array("mystic", "instinct", "valor")) || $level < 1 || $level > 40) {
            header("Location:/pogo/views/register.php?error=2");
            exit();
        }

        $statement = $mysqli->prepare("Insert into usuario (nome, senha, email, time, nivel) values (?, ?, ?, ?, ?)");

        $statement->bind_param('ssssi', $name, $userpwd, $email, $team, $level);
        $result = $statement->execute();

        if ($result) {
            header("Location:/pogo/views/login.php?warning=1");
        }
        else {
            header("Location:/pogo/views/register.php?error=101");
        }
    }
}
else {
    header("Location:/pogo/views/register.php?error=900");
}