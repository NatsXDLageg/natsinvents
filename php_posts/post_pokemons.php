<?php

session_start();

if(!isset($php_connection)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/php_connection.php");
    $php_connection = true;
}

if (!$_SERVER['REQUEST_METHOD'] === 'POST') {

    die("Método de requisição incorreto");
}

// Remove after
if(!isset($mysqli)) {
    $mysqli = new mysqli();
}

$operation = $_POST['operation'];

header('Content-Type: application/json');
switch ($operation) {

    //     ___ ___ _____   ___  ___  _  _____ __  __  ___  _  _ ___   _____   __  ___  _____  __
    //    / __| __|_   _| | _ \/ _ \| |/ / __|  \/  |/ _ \| \| / __| | _ ) \ / / |   \| __\ \/ /
    //   | (_ | _|  | |   |  _/ (_) | ' <| _|| |\/| | (_) | .` \__ \ | _ \\ V /  | |) | _| >  <
    //    \___|___| |_|   |_|  \___/|_|\_\___|_|  |_|\___/|_|\_|___/ |___/ |_|   |___/|___/_/\_\
    //
    case 'get_pokemons_by_dex':
        break;

    //     ___ ___ _____   ___  ___  _  _____ __  __  ___  _  _ ___   _____   __  _  _   _   __  __ ___
    //    / __| __|_   _| | _ \/ _ \| |/ / __|  \/  |/ _ \| \| / __| | _ ) \ / / | \| | /_\ |  \/  | __|
    //   | (_ | _|  | |   |  _/ (_) | ' <| _|| |\/| | (_) | .` \__ \ | _ \\ V /  | .` |/ _ \| |\/| | _|
    //    \___|___| |_|   |_|  \___/|_|\_\___|_|  |_|\___/|_|\_|___/ |___/ |_|   |_|\_/_/ \_\_|  |_|___|
    //
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
                // Couldn't get resultset
                echo json_encode(array('status' => -1));
            }
        }
        else {
            // SQL error
            echo json_encode(array('status' => 0));
        }
        break;

    //    ___ ___ _____   _  _ _____      __  ___ _  _ ___ _  ___   __
    //   / __| __|_   _| | \| | __\ \    / / / __| || |_ _| \| \ \ / /
    //   \__ \ _|  | |   | .` | _| \ \/\/ /  \__ \ __ || || .` |\ V /
    //   |___/___| |_|   |_|\_|___| \_/\_/   |___/_||_|___|_|\_| |_|
    //
    case 'set_new_shiny':
        $pokemon_id = isset($_POST['pokemon']) ? $_POST['pokemon'] : header("Location:/pogo/views_admin/shinylistadmin.php?error=1");
        $pokemon_id = intval($pokemon_id);
        $apply_to_family = isset($_POST['evoshiny']) && $_POST['evoshiny'] == 'on';
        $userpwd = isset($_POST['password']) ? $_POST['password'] : header("Location:/pogo/views_admin/shinylistadmin.php?error=2");

        $statement = $mysqli->prepare("Select senha, prioridade from usuario where email = ?");
        $statement->bind_param('s', $_SESSION['email']);
        $result = $statement->execute();

        if($result) {
            $result = $statement->get_result();
            if($result) {
                if ($result->num_rows == 1) {
                    $row = $row = $result->fetch_array(MYSQLI_ASSOC);

                    if($row['senha'] == md5($userpwd)) {
                        if($row['prioridade'] != '999') {
                            // All right for user

                            // I DONT KNOW HOW TO DO THIS

                            // Update pokemon (then search for evolutions)
                            $statement = $mysqli->prepare("Update pokemon set hasshiny = 1 where id = ?");
                            $statement->bind_param('i', $pokemon_id);
                            $result = $statement->execute();

                            if($result) {
                                // Updated. Now will search for evolutions
                                $statement = $mysqli->prepare("Select id from pokemon where ");
                                $statement->bind_param('i', $pokemon_id);
                                $result = $statement->execute();

                            }
                            else {
                                // SQL error

                            }
                        }
                        else {
                            // Not admin

                        }
                    }
                    else {
                        // Wrong password

                    }
                }
                else {
                    // No row fetch

                }
            }
            else {
                // Couldn't get resultset

            }
        }
        else {
            // SQL error

        }

        break;
    default:
        echo json_encode(array('status' => 0));
        break;
}