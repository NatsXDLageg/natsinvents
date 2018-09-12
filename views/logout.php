<?php

function eraseSession($mysqli, $user, $token) {
    //Must check all session elements in database and if one of them is not the one being searched here,
    // its date of creation must be checked, and if it overpass a N value, it must be removed from table
    // Or not, it will be needed to implement a routine where all the expired elements of session table
    // will be deleted

    $statement = $mysqli->prepare("Select id, token, timestamp from session where id_usuario = ?");
    $statement->bind_param('i', $user);
    $result = $statement->execute();

    if(!$result) {
        return false;
    }

    $result = $statement->get_result();
    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $usertoken = $row['token'];
        if (hash_equals($usertoken, $token)) {
            $id = $row['id'];
            $statement = $mysqli->prepare("Delete from session where id = ?");
            $statement->bind_param('i', $id);
            $result = $statement->execute();

            if(!$result) {
                return false;
            }
            return true;
        }
    }

    return false;
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

$cookie = isset($_COOKIE['rememberme']) ? $_COOKIE['rememberme'] : null;
if ($cookie) {
    list ($user, $token, $mac) = explode(':', $cookie);
    if (!hash_equals(hash_hmac('sha256', $user . ':' . $token, $secret_key), $mac)) {
        return false;
    }
    if (eraseSession($mysqli, $user, $token)) {
        setcookie('rememberme', '');
    }
}
unset($_SESSION['email']);
unset($_SESSION['priority']);

header("location:/pogo/index.php");
exit();