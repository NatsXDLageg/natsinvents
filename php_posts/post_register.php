<?php

session_start();
include($_SERVER['DOCUMENT_ROOT']."/../common_files/php_connection.php");

if (!$_SERVER['REQUEST_METHOD'] === 'POST') {

    die("Método de requisição incorreto");
}

$name = $_POST['name'];
$userpwd = md5($_POST['password']);
$email = $_POST['email'];
$team = $_POST['team'];
$level = $_POST['level'];

$query="Select senha from usuario where email = '$email'";

$result = $mysqli->query($query) or die("couldn't execute the query");

if($result->num_rows == 1) {
    header("Location:../views/register.php?error=1");
    exit();
}
else
{
    if(!in_array($team, array("mystic", "instinct", "valor")) || $level < 1 || $level > 40) {
        header("Location:../views/register.php?error=2");
        exit();
    }

    $query="Insert into usuario (nome, senha, email, time, nivel) values ('$name', '$userpwd', '$email', '$team', $level)";

    $result = $mysqli->query($query) or die("couldn't execute the query");

    // Se nada dá errado
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