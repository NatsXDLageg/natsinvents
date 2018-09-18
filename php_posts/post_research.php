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

        //    _  _ _____      __  ___ ___ ___ ___   _   ___  ___ _  _
        //   | \| | __\ \    / / | _ \ __/ __| __| /_\ | _ \/ __| || |
        //   | .` | _| \ \/\/ /  |   / _|\__ \ _| / _ \|   / (__| __ |
        //   |_|\_|___| \_/\_/   |_|_\___|___/___/_/ \_\_|_\\___|_||_|
        //
        case 'new_research':
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

            if(!isset($_SESSION['email'])) {
                echo json_encode(array('status' => 0, 'message' => 'Não pode adicionar se não estiver loggado'));
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

            $statement = $mysqli->prepare("Insert into informe_missao (id_usuario, pokestop_gym, descricao, recompensa) VALUES (?, ?, ?, ?);");
            $statement->bind_param('isss', $userId, $pokestop_name, $research, $reward);
            $result = $statement->execute();

            if(!$result) {
                echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
                exit();
            }

            echo json_encode(array('status' => 1, 'message' => 'Sucesso! Missão registrada', 'data' => array('insert_id' => $statement->insert_id)));
            break;

        //    _    ___ ___ _____   ___ ___ ___ ___   _   ___  ___ _  _ ___ ___   _____   __  ___ ___ ___ ___  ___ ___ _______   __
        //   | |  |_ _/ __|_   _| | _ \ __/ __| __| /_\ | _ \/ __| || | __/ __| | _ ) \ / / | _ \ _ \_ _/ _ \| _ \_ _|_   _\ \ / /
        //   | |__ | |\__ \ | |   |   / _|\__ \ _| / _ \|   / (__| __ | _|\__ \ | _ \\ V /  |  _/   /| | (_) |   /| |  | |  \ V /
        //   |____|___|___/ |_|   |_|_\___|___/___/_/ \_\_|_\\___|_||_|___|___/ |___/ |_|   |_| |_|_\___\___/|_|_\___| |_|   |_|
        //
        case 'list_researches_by_priority':
            $index = isset($_POST['index']) ? $_POST['index'] : 0;
            $loaded = 10;

            $userMail = null;
            if(isset($_SESSION['email'])) {
                $userMail = $_SESSION['email'];
            }

            if(!$mysqli->query('SET time_zone = \'-3:00\'')) {
                echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $mysqli->error));
                exit();
            }
            $query = "
                Select
                    im.id,
                    im.pokestop_gym as pokestop,
                    CASE
                        WHEN im.descricao IS NULL THEN im.recompensa
                        WHEN im.recompensa IS NULL THEN im.descricao
                        ELSE CONCAT(im.descricao, ': ', im.recompensa)
                    END as missao,
                    (100.0 / (TIMESTAMPDIFF(SECOND, im.criado_em, NOW()) + 1)) * COALESCE(u.prioridade, 1) as prior,
                    DATE(im.criado_em) as dia,
                    u.nick_jogo as usuario,
                    CASE WHEN u.email = ? THEN 1 ELSE 0 END as removable
                from
                    informe_missao im
                    left join usuario u on u.id = im.id_usuario
                order by dia DESC, prior DESC
                limit ?, ?
            ";
            $statement = $mysqli->prepare($query);
            $statement->bind_param('sii', $userMail, $index, $loaded);
            $result = $statement->execute();

            if($result) {
                $result = $statement->get_result();
                $loaded = $result->num_rows;
                $researches = array();
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $researches[] = $row;
                }

                echo json_encode(array('status' => 1, 'data' =>  array('research' => $researches, 'loaded' => $loaded)));
            }
            else {
                echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
                exit();
            }
            break;

        //    ___  ___ _    ___ _____ ___   ___ ___ ___ ___   _   ___  ___ _  _
        //   |   \| __| |  | __|_   _| __| | _ \ __/ __| __| /_\ | _ \/ __| || |
        //   | |) | _|| |__| _|  | | | _|  |   / _|\__ \ _| / _ \|   / (__| __ |
        //   |___/|___|____|___| |_| |___| |_|_\___|___/___/_/ \_\_|_\\___|_||_|
        //
        case 'delete_research':
            if(!isset($_POST['research'])) {
                echo json_encode(array('status' => 0, 'message' => 'Parâmetro não encontrado: research'));
                exit();
            }
            $researchId = $_POST['research'];

            if(!isset($_SESSION['email'])) {
                echo json_encode(array('status' => 0, 'message' => 'Não tem permissão para essa ação!'));
                exit();
            }
            $userMail = $_SESSION['email'];

            $query = "
                Select
                    im.id
                from
                    informe_missao im
                    left join usuario u on u.id = im.id_usuario
                where
                    u.email = ?
                    and im.id = ?
            ";
            $statement = $mysqli->prepare($query);
            $statement->bind_param('si', $userMail, $researchId);
            $result = $statement->execute();

            if(!$result) {
                echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
                exit();
            }

            $result = $statement->get_result();
            if($result->num_rows == 0) {
                echo json_encode(array('status' => 0, 'message' => 'Não tem permissão para essa ação!'));
            }
            else {
                // User can remove the report

                $statement = $mysqli->prepare("delete from informe_missao where id = ?");
                $statement->bind_param('i', $researchId);
                $result = $statement->execute();

                if(!$result) {
                    echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
                }
                else {
                    echo json_encode(array('status' => 1, 'message' => 'Removido com sucesso'));
                }
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


