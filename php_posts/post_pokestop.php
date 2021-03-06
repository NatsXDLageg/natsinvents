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
            if(!isset($_POST['aliases'])) {
                echo json_encode(array('status' => 0, 'message' => 'Parâmetro não encontrado: aliases'));
                exit();
            }
            if(!isset($_POST['type'])) {
                echo json_encode(array('status' => 0, 'message' => 'Parâmetro não encontrado: type'));
                exit();
            }
            $pokestop_name = $_POST['pokestop_name'];
            $aliases = ($_POST['aliases'] !== '') ? $_POST['aliases'] : null;
            $type = $_POST['type'];
            if ($type !== 'p' && $type !== 'g') {
                echo json_encode(array('status' => 0, 'message' => 'Tipo não reconhecido. Deve ser p ou g '));
                exit();
            }

            if(!isset($_SESSION['email'])) {
                echo json_encode(array('status' => 0, 'message' => 'Parâmetro não encontrado: email'));
                exit();
            }
            $userMail = $_SESSION['email'];

            $status = 0;
            if (isset($_SESSION['priority']) && $_SESSION['priority'] === 999) {
                $status = 1;
            }

            $statement = $mysqli->prepare("Select id from usuario where email = ?");
            $statement->bind_param('s', $userMail);
            $result = $statement->execute();

            if(!$result) {
                echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
                exit();
            }

            $result = $statement->get_result();
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $userId = $row['id'];

            $statement = $mysqli->prepare("Insert into pokestop_gym (nome, apelidos, tipo, criador_id, status) values (?, ?, ?, ?, ?)");
            $statement->bind_param('sssii', $pokestop_name, $aliases, $type, $userId, $status);
            $result = $statement->execute();

            if(!$result) {
                echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
                exit();
            }

            echo json_encode(array('status' => 1, 'message' => 'Sucesso. Daqui a algum tempo o pokestop/ginásio poderá aparecer na listagem'));
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
                    pg.tipo = 'p'
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

        //    _    ___ ___ _____   ___  ___  _  _____ ___ _____ ___  ___  ___     _   _  _ ___     _____   ____  __ ___   _____   __  _  _   _   __  __ ___
        //   | |  |_ _/ __|_   _| | _ \/ _ \| |/ / __/ __|_   _/ _ \| _ \/ __|   /_\ | \| |   \   / __\ \ / /  \/  / __| | _ ) \ / / | \| | /_\ |  \/  | __|
        //   | |__ | |\__ \ | |   |  _/ (_) | ' <| _|\__ \ | || (_) |  _/\__ \  / _ \| .` | |) | | (_ |\ V /| |\/| \__ \ | _ \\ V /  | .` |/ _ \| |\/| | _|
        //   |____|___|___/ |_|   |_|  \___/|_|\_\___|___/ |_| \___/|_|  |___/ /_/ \_\_|\_|___/   \___| |_| |_|  |_|___/ |___/ |_|   |_|\_/_/ \_\_|  |_|___|
        //
        case 'list_pokestops_and_gyms_by_name':
            $query = "
                Select
                    CONCAT(
                        pg.nome,
                        COALESCE(CONCAT(' (', pg.apelidos, ')'), '')
                    ) as nome,
                    pg.tipo
                from
                    pokestop_gym pg
                where
                    pg.status = 1
                order by pg.nome ASC
            ";
            $statement = $mysqli->prepare($query);
            $result = $statement->execute();

            if($result) {
                $result = $statement->get_result();
                $pokestops = array();
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $pokestops[] = $row;
                }

                echo json_encode(array('status' => 1, 'data' => array('pokestops' => $pokestops)));
            }
            else {
                echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
                exit();
            }
            break;

        //    _    ___ ___ _____   ___  ___  _  _____ ___ _____ ___  ___  ___   _____   __  ___ _____ ___ ___ _____ ___ _  _
        //   | |  |_ _/ __|_   _| | _ \/ _ \| |/ / __/ __|_   _/ _ \| _ \/ __| | _ ) \ / / / __|_   _| _ \ __|_   _/ __| || |
        //   | |__ | |\__ \ | |   |  _/ (_) | ' <| _|\__ \ | || (_) |  _/\__ \ | _ \\ V /  \__ \ | | |   / _|  | || (__| __ |
        //   |____|___|___/ |_|   |_|  \___/|_|\_\___|___/ |_| \___/|_|  |___/ |___/ |_|   |___/ |_| |_|_\___| |_| \___|_||_|
        //
        case 'list_pokestops_by_stretch':
            if(!isset($_POST['stretch'])) {
                echo json_encode(array('status' => 0, 'message' => 'Parâmetro não encontrado: stretch'));
                exit();
            }
            $resultsRemaining = 20;
            if(isset($_POST['totalResults'])) {
                $resultsRemaining = intval($_POST['totalResults']);
            }

            $pokestops = array();
            $queryByName = "
                Select
                    CONCAT(
                        pg.nome,
                        COALESCE(CONCAT(' (', pg.apelidos, ')'), '')
                    ) as nome
                from
                    pokestop_gym pg
                where
                    pg.tipo = 'p'
                    and pg.status = 1
                    and pg.nome like ?
                order by pg.nome ASC
                limit ?
            ";
            $queryByNickname = "
                Select
                    CONCAT(
                        pg.nome,
                        COALESCE(CONCAT(' (', pg.apelidos, ')'), '')
                    ) as nome
                from
                    pokestop_gym pg
                where
                    pg.tipo = 'p'
                    and pg.status = 1
                    and pg.apelidos like ?
                order by pg.apelidos ASC
                limit ?
            ";

            // Search first for pokestops with $stretch in its names
            $stretch = '%'.$_POST['stretch'].'%';

            $statement = $mysqli->prepare($queryByName);
            $statement->bind_param('si', $stretch, $resultsRemaining);
            $result = $statement->execute();

            if($result) {
                $result = $statement->get_result();
                $resultsRemaining -= $result->num_rows;

                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $pokestops[] = $row['nome'];
                }
            }
            else {
                echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
                exit();
            }

            if($resultsRemaining > 0) {
                // Search for pokestops with $stretch in its nicknames

                $statement = $mysqli->prepare($queryByNickname);
                $statement->bind_param('si', $stretch, $resultsRemaining);
                $result = $statement->execute();

                if ($result) {
                    $result = $statement->get_result();
                    $resultsRemaining -= $result->num_rows;

                    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                        $pokestops[] = $row['nome'];
                    }
                }
                else {
                    echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
                    exit();
                }
            }

            echo json_encode(array('status' => 1, 'data' => $pokestops));
            break;
        default:
            header("Location:/pogo/views/error.php");
            break;
    }
}
else {
    die("Método de requisição incorreto");
}


