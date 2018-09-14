<?php

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $operation = $_POST['operation'];

    header('Content-Type: application/json');
    switch ($operation) {

        //    _  _ _____      __  ___  ___  _  _____ ___ _____ ___  ___
        //   | \| | __\ \    / / | _ \/ _ \| |/ / __/ __|_   _/ _ \| _ \
        //   | .` | _| \ \/\/ /  |  _/ (_) | ' <| _|\__ \ | || (_) |  _/
        //   |_|\_|___| \_/\_/   |_|  \___/|_|\_\___|___/ |_| \___/|_|
        //
        case 'new_pokestop':
            //TODO
            if(!isset($_POST['pokestop_name'])) {
                echo json_encode(array('status' => 0, 'message' => 'Parâmetro não encontrado: pokestop_name'));
                exit();
            }
            if(!isset($_POST['research'])) {
                echo json_encode(array('status' => 0, 'message' => 'Parâmetro não encontrado: research'));
                exit();
            }
            if(!isset($_POST['reward'])) {
                echo json_encode(array('status' => 0, 'message' => 'Parâmetro não encontrado: reward'));
                exit();
            }
            $pokestop_name = $_POST['pokestop_name'];
            $research = ($_POST['research'] !== '') ? $_POST['research'] : null;
            $reward = ($_POST['reward'] !== '') ? $_POST['reward'] : null;


            echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $_POST));
            break;

        //    _    ___ ___ _____   ___  ___  _  _____ ___ _____ ___  ___  ___   _____   __  _  _   _   __  __ ___
        //   | |  |_ _/ __|_   _| | _ \/ _ \| |/ / __/ __|_   _/ _ \| _ \/ __| | _ ) \ / / | \| | /_\ |  \/  | __|
        //   | |__ | |\__ \ | |   |  _/ (_) | ' <| _|\__ \ | || (_) |  _/\__ \ | _ \\ V /  | .` |/ _ \| |\/| | _|
        //   |____|___|___/ |_|   |_|  \___/|_|\_\___|___/ |_| \___/|_|  |___/ |___/ |_|   |_|\_/_/ \_\_|  |_|___|
        //
        case 'list_pokestops_by_name':
            $query = "
                Select
                    CONCAT(
                        pg.nome,
                        COALESCE(CONCAT(' (', pg.apelidos, ')'), '')
                    ) as nome
                from
                    pokestop_gym pg
                where
                    pg.tipo = 'P'
                    and pg.status = 1
                order by pg.nome ASC
            ";
            $statement = $mysqli->prepare($query);
            $result = $statement->execute();

            if($result) {
                $result = $statement->get_result();
                $pokestops = array();
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $pokestops[] = $row['nome'];
                }

                echo json_encode(array('status' => 1, 'data' => $pokestops));
            }
            else {
                echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
                exit();
            }
            break;
        default:
            header("Location:/pogo/views/error.php");
            break;
    }
}
else {
    die("Método de requisição incorreto");
}


