<?php

function storeTokenForUser($mysqli, $user, $token) {

// Remove after
if(!isset($mysqli)) {
    $mysqli = new mysqli();
}

    $statement = $mysqli->prepare("Insert into session (id_usuario, token) values (?, ?);");
    $statement->bind_param('is', $user, $token);
    $result = $statement->execute();

    if(!$result) {
        die(json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error)));
    }
}

session_start();

if(!isset($php_connection)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/php_connection.php");
    $php_connection = true;
}
if(!isset($server_var)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/server_var.php");
    $server_var = true;
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
$rememberme = $_POST['rememberme'] === 'true';

$statement = $mysqli->prepare("Select id, senha, status, prioridade from usuario where email = ?");
$statement->bind_param('s', $email);
$result = $statement->execute();

if($result) {
    $result = $statement->get_result();

    if($result->num_rows == 1) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $pass = $row["senha"];

        if ($pass == md5($userpwd)) {

            if ($row["status"] == '1') {

                if($rememberme) {
                    $user = $row['id'];

                    $token = bin2hex(openssl_random_pseudo_bytes(256)); // generate a token, should be 128 - 256 bit
                    storeTokenForUser($mysqli, $user, $token);
                    $cookie = $user . ':' . $token;
                    $mac = hash_hmac('sha256', $cookie, $secret_key);
                    $cookie .= ':' . $mac;
                    setcookie('rememberme', $cookie, time()+60*60*24*365, '/');
                }

                $_SESSION['email'] = $email;
                $_SESSION['priority'] = intval($row["prioridade"]);

                die(json_encode(array('status' => 1, 'message' => 'Sucesso')));
            }
            else {
                die(json_encode(array('status' => 0, 'message' => 'Não foi possível fazer login')));
            }
        }
        else {
            die(json_encode(array('status' => 0, 'message' => 'Usuário ou senha incorreto')));
        }
    }
    else {
        die(json_encode(array('status' => 0, 'message' => 'Usuário ou senha incorreto')));
    }
}
else {
    die(json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error)));
}
