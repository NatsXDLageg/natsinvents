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

        //    _  _ _____      __  _    ___ _  _ _  __
        //   | \| | __\ \    / / | |  |_ _| \| | |/ /
        //   | .` | _| \ \/\/ /  | |__ | || .` | ' <
        //   |_|\_|___| \_/\_/   |____|___|_|\_|_|\_\
        //
        case 'new_link':
            if(!isset($_POST['image_url']) && !isset($_POST['url'])) {
                echo json_encode(array('status' => 0, 'message' => 'Parâmetro não encontrado: image_url ou url'));
                exit();
            }

            $title = ($_POST['title'] !== '') ? $_POST['title'] : null;
            $image_url = ($_POST['image_url'] !== '') ? $_POST['image_url'] : null;
            $url = ($_POST['url'] !== '') ? $_POST['url'] : null;

            if(!isset($_SESSION['email'])) {
                echo json_encode(array('status' => 0, 'message' => 'Não tem permissão para essa ação!'));
                exit();
            }
            $userMail = $_SESSION['email'];

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

            $statement = $mysqli->prepare("Insert into link_interessante (titulo, image_url, url, id_usuario) values (?, ?, ?, ?);");
            if(!$statement) {
                echo json_encode(array('status' => 0, 'message' => 'Erro de SQL no prepare', 'debug' => $mysqli->error));
                exit();
            }
            $statement->bind_param('sssi', $title, $image_url, $url, $userId);
            $result = $statement->execute();

            if(!$result) {
                echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
                exit();
            }

            echo json_encode(array('status' => 1, 'message' => 'Sucesso! Link registrado', 'data' => array('insert_id' => $statement->insert_id)));
            break;

        //    _    ___ ___ _____   _    ___ _  _ _  _____   _____   __  _____ ___ __  __ ___
        //   | |  |_ _/ __|_   _| | |  |_ _| \| | |/ / __| | _ ) \ / / |_   _|_ _|  \/  | __|
        //   | |__ | |\__ \ | |   | |__ | || .` | ' <\__ \ | _ \\ V /    | |  | || |\/| | _|
        //   |____|___|___/ |_|   |____|___|_|\_|_|\_\___/ |___/ |_|     |_| |___|_|  |_|___|
        //
        case 'list_links_by_time':
            $query = "
                Select
                    id,
                    titulo,
                    image_url,
                    url,
                    criado_em
                from
                    link_interessante
                order by criado_em DESC
            ";
            $statement = $mysqli->prepare($query);
            $result = $statement->execute();

            if($result) {
                $result = $statement->get_result();
                $links = array();
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $links[] = $row;
                }

                echo json_encode(array('status' => 1, 'data' => $links));
            }
            else {
                echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
                exit();
            }
            break;
    }
}
else {
    die("Método de requisição incorreto");
}
