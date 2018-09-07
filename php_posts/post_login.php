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

if(!isset($_POST['password']) || !isset($_POST['email'])) {
    die(json_encode(array('status' => 0, 'message' => 'Parâmetro incorreto', 'debug' => $_POST)));
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

                die(json_encode(array('status' => 1, 'message' => 'Sucesso')));
            }
            else {
                die(json_encode(array('status' => 0, 'message' => 'Não foi possível fazer login')));
            }
        }
    }
    else {
        die(json_encode(array('status' => 0, 'message' => 'Usuário ou senha incorreto')));
    }
}
else {
    die(json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error)));
}
