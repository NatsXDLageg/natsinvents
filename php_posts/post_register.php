<?php

session_start();

if(!isset($php_connection)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/php_connection.php");
    $php_connection = true;
}

// Remove after
if(!isset($mysqli)) {
    $mysqli = new mysqli();
}

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    die(json_encode(array('status' => 0, 'message' => 'Método incorreto de requisição')));
}

if(!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['email']) || !isset($_POST['team']) || !isset($_POST['level'])) {
    die(json_encode(array('status' => 0, 'message' => 'Parâmetro incorreto', 'debug' => $_POST)));
}

$name = utf8_decode($_POST['username']);
$userpwd = md5($_POST['password']);
$email = utf8_decode($_POST['email']);
$team = $_POST['team'];
$level = $_POST['level'];

$statement = $mysqli->prepare("Select senha from usuario where email = ?");
$statement->bind_param('s', $email);
$result = $statement->execute();

if($result) {
    $result = $statement->get_result();

    if ($result->num_rows == 1) {
        die(json_encode(array('status' => 0, 'message' => 'E-mail já está sendo utilizado')));
    }
    else {
        if (!in_array($team, array("mystic", "instinct", "valor")) || $level < 1 || $level > 40) {
            die(json_encode(array('status' => 0, 'message' => 'Time ou nível com valor inválido')));
        }

        $statement = $mysqli->prepare("Insert into usuario (nome, senha, email, time, nivel) values (?, ?, ?, ?, ?)");

        $statement->bind_param('ssssi', $name, $userpwd, $email, $team, $level);
        $result = $statement->execute();

        if ($result) {
            die(json_encode(array('status' => 1, 'message' => 'Conta criada. Por favor espere um administrador ativá-la')));
        }
        else {
            die(json_encode(array('status' => 0, 'message' => 'Não foi possível registrar usuário')));
        }
    }
}
else {
    die(json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error)));
}