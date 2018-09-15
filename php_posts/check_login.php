<?php

function fetchTokenByUserName($mysqli, $user, $token) {
    //Must check all session elements in database and if one of them is not the one being searched here,
    // its date of creation must be checked, and if it overpass a N value, it must be removed from table
    // Or not, it will be needed to implement a routine where all the expired elements of session table
    // will be deleted

    $statement = $mysqli->prepare("Select token, timestamp from session where id_usuario = ?");
    $statement->bind_param('i', $user);
    $result = $statement->execute();

    if(!$result) {
        return false;
    }

    $result = $statement->get_result();
    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $usertoken = $row['token'];
        if (hash_equals($usertoken, $token)) {
            return true;
        }
    }

    return false;
}

function logUserIn($mysqli, $user) {
    $statement = $mysqli->prepare("Select email, prioridade from usuario where id = ?");
    $statement->bind_param('i', $user);
    $result = $statement->execute();

    if(!$result) {
        header("location:/pogo/views/login.php");
    }

    $result = $statement->get_result();
    $row = $result->fetch_array(MYSQLI_ASSOC);

    $_SESSION['email'] = $row['email'];
    $_SESSION['priority'] = intval($row['prioridade']);
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

if(!isset($_SESSION['email']))
{
    $cookie = isset($_COOKIE['rememberme']) ? $_COOKIE['rememberme'] : null;
    if ($cookie) {

        list ($user, $token, $mac) = explode(':', $cookie);
        if (!hash_equals(hash_hmac('sha256', $user . ':' . $token, $secret_key), $mac)) {
            $logged = false;
        }
        else if(fetchTokenByUserName($mysqli, $user, $token)) {
            logUserIn($mysqli, $user);
            $logged = true;
        }
        else {
            setcookie('rememberme', '');
            $logged = false;
        }
    }
}
else {
    $logged = true;
}

if(!$logged) {
    $_SESSION['start_path'] = $_SERVER['PHP_SELF'];
    header("location:/pogo/views/login.php");
    exit();
}