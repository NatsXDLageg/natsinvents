<?php

session_start();

if(!isset($php_connection)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/php_connection.php");
    $php_connection = true;
}

if (!$_SERVER['REQUEST_METHOD'] === 'POST') {

    die("Método de requisição incorreto");
}

$operation = $_POST['operation'];

header('Content-Type: application/json');
switch ($operation) {
    case 'get_pokemons_by_dex':
        break;
    case 'get_pokemons_by_name':
        $statement = $mysqli->prepare("Select id, nome from pokemon order by nome");
        $result = $statement->execute();

        if($result) {
            $result = $statement->get_result();
            if($result) {
                $pokemons = array();
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $pokemons[] = array_map("utf8_encode", $row);
                }

                echo json_encode(array('status' => 1, 'pokemons' => $pokemons));
            }
            else {
                echo json_encode(array('status' => -1));
            }
        }
        else {
            echo json_encode(array('status' => 0));
        }
        break;
    case 'set_new_shiny':
        echo json_encode(array('status' => 1, 'params' => $_POST));
        break;
}