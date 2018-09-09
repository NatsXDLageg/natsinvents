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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                $pokemons = array();
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $pokemons[] = array_map("utf8_encode", $row);
                }

                echo json_encode(array('status' => 1, 'pokemons' => $pokemons));
            }
            else {
                // SQL error
                echo json_encode(array('status' => 0));
            }
            break;

        //     ___ ___ _____   ___ _  _ ___ _  _ ___ ___ ___   _____   __  ___  _____  __  _____   _____
        //    / __| __|_   _| / __| || |_ _| \| |_ _| __/ __| | _ ) \ / / |   \| __\ \/ / | __\ \ / / _ \
        //   | (_ | _|  | |   \__ \ __ || || .` || || _|\__ \ | _ \\ V /  | |) | _| >  <  | _| \ V / (_) |
        //    \___|___| |_|   |___/_||_|___|_|\_|___|___|___/ |___/ |_|   |___/|___/_/\_\ |___| \_/ \___/
        //
        case 'get_shinies_by_dex_evo':
            if(isset($_POST['get_user_list'])) {
                if(!isset($_SESSION['email'])) {
                    echo json_encode(array('status' => 0));
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

                $query = "
                    Select p.id, p.nome, COALESCE(uhp.has_shiny_too, 0) as 'marked', p.pokedexevo                     
                    from pokemon p
                        left join usuario_has_pokemon uhp on uhp.id_pokemon = p.id and uhp.id_usuario = ?
                    where
                        p.hasshiny = 1
                    order by pokedexevo";
                $statement = $mysqli->prepare($query);
                $statement->bind_param('i', $userid);
            }
            else {
                $query = "Select id, nome, 0 as 'marked' from pokemon where hasshiny = 1 order by pokedexevo";
                $statement = $mysqli->prepare($query);
            }
            $result = $statement->execute();

            if($result) {
                $result = $statement->get_result();
                $pokemons = array();
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $pokemons[] = array_map("utf8_encode", $row);
                }

                echo json_encode(array('status' => 1, 'shinies' => $pokemons));
            }
            else {
                // SQL error
                echo json_encode(array('status' => 0));
            }
            break;


        //     ___ ___ _____   ___ _  _ ___ _  _ ___ ___ ___   _____   __  ___  _____  __  _____   _____     ___ ___  ___  _   _ ___   ___ _   __  __ ___ _    ___ ___ ___
        //    / __| __|_   _| / __| || |_ _| \| |_ _| __/ __| | _ ) \ / / |   \| __\ \/ / | __\ \ / / _ \   / __| _ \/ _ \| | | | _ \ | __/_\ |  \/  |_ _| |  |_ _| __/ __|
        //   | (_ | _|  | |   \__ \ __ || || .` || || _|\__ \ | _ \\ V /  | |) | _| >  <  | _| \ V / (_) | | (_ |   / (_) | |_| |  _/ | _/ _ \| |\/| || || |__ | || _|\__ \
        //    \___|___| |_|   |___/_||_|___|_|\_|___|___|___/ |___/ |_|   |___/|___/_/\_\ |___| \_/ \___/   \___|_|_\\___/ \___/|_|   |_/_/ \_\_|  |_|___|____|___|___|___/
        //
        case 'get_shinies_by_dex_evo_group_families':

            if(!isset($_SESSION['email'])) {
                echo json_encode(array('status' => 0));
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

            $query = "
                    Select p.id, p.nome, COALESCE(uhp.has_shiny_too, 0) as 'marked', p.pokedexevo                     
                    from pokemon p
                        left join usuario_has_pokemon uhp on uhp.id_pokemon = p.id and uhp.id_usuario = ?
                    where
                        p.hasshiny = 1
                        and p.evolve is null
                    order by pokedexevo";
            $statement = $mysqli->prepare($query);
            $statement->bind_param('i', $userId);
            $result = $statement->execute();

            if($result) {
                $result = $statement->get_result();
                $data = array();
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $family = array();
                    $family[] = array_map("utf8_encode", $row);
                    $dexNum = intval($row['pokedexevo']) + 1;
                    $elementFound = true;
                    while ($elementFound) {
                        $query = "
                            Select
                                p.id,
                                p.pokedexevo,
                                p.nome,
                                COALESCE(uhp.has_shiny_too, 0) as marked
                            from
                                pokemon p
                                left join usuario_has_pokemon uhp on uhp.id_pokemon = p.id and uhp.id_usuario = ?
                            where
                                p.evolve is not null
                                and p.hasshiny = 1
                                and p.pokedexevo = ?
                        ";
                        $statement = $mysqli->prepare($query);
                        $statement->bind_param('ii', $userId, $dexNum);
                        $result2 = $statement->execute();

                        if(!$result2) {
                            echo json_encode(array('status' => 0));
                            exit();
                        }

                        $result2 = $statement->get_result();
                        if($result2->num_rows == 0) {
                            $elementFound = false;
                        }

                        while($row2 = $result2->fetch_array(MYSQLI_ASSOC)) {
                            $family[] = array_map("utf8_encode", $row2);
                        }

                        $dexNum = $dexNum + 1;
                    }

                    $data[] = $family;
                }

                echo json_encode(array('status' => 1, 'data' => $data));
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
            if(isset($_POST['pokemon'])) {
                $pokemon_id = $_POST['pokemon'];
                $pokemon_id = intval($pokemon_id);
            }
            else {
                header("Location:/pogo/views_admin/shinylistadmin.php?error=1");
                exit();
            }
            $link = isset($_POST['link']) ? $_POST['link'] : null;
            if(isset($_POST['password'])) {
                $userpwd = $_POST['password'];
            }
            else {
                header("Location:/pogo/views_admin/shinylistadmin.php?error=2");
                exit();
            }

            $statement = $mysqli->prepare("Select senha, prioridade from usuario where email = ?");
            $statement->bind_param('s', $_SESSION['email']);
            $result = $statement->execute();

            if($result) {
                $result = $statement->get_result();
                if($result->num_rows == 1) {
                    $row = $row = $result->fetch_array(MYSQLI_ASSOC);

                    if($row['senha'] == md5($userpwd)) {
                        if($row['prioridade'] == '999') {
                            // All right for user

                            $statement = $mysqli->prepare("Update pokemon set hasshiny = 1 where id = ?");
                            $statement->bind_param('i', $pokemon_id);
                            $result = $statement->execute();

                            if(!$result) {
                                // SQL error
                                header("Location:/pogo/views_admin/shinylistadmin.php?error=3");
                                $mysqli->rollback();
                                exit();
                            }

                            // Success
                            $mysqli->commit();
                            header("Location:/pogo/views_admin/shinylistadmin.php?success=1");
                        }
                        else {
                            // Not admin
                            header("Location:/pogo/views/shinylist.php?error=6");
                        }
                    }
                    else {
                        // Wrong password
                        header("Location:/pogo/views_admin/shinylistadmin.php?error=5");
                    }
                }
                else {
                    // No row fetch
                    header("Location:/pogo/views_admin/shinylistadmin.php?error=4");
                }
            }
            else {
                // SQL error
                header("Location:/pogo/views_admin/shinylistadmin.php?error=3");
            }
            break;

        //    _   _ ___ ___   _ _____ ___   ___ _  _ ___ _  _ ___ ___ ___   _   _ ___ ___ ___
        //   | | | | _ \   \ /_\_   _| __| / __| || |_ _| \| |_ _| __/ __| | | | / __| __| _ \
        //   | |_| |  _/ |) / _ \| | | _|  \__ \ __ || || .` || || _|\__ \ | |_| \__ \ _||   /
        //    \___/|_| |___/_/ \_\_| |___| |___/_||_|___|_|\_|___|___|___/  \___/|___/___|_|_\
        //
        case 'update_shinies_user':
            if(isset($_SESSION['email'])) {
                $usermail = $_SESSION['email'];
            }
            else {
                echo json_encode(array('status' => 0, 'message' => 'Missing param: email'));
                exit();
            }

            if(isset($_POST['listhas'])) {
                $listhas = $_POST['listhas'];
            }
            else {
                $listhas = array();
            }

            if(isset($_POST['listhasnt'])) {
                $listhasnt = $_POST['listhasnt'];
            }
            else {
                $listhasnt = array();
            }

            $mysqli->autocommit(false);

            //List of pokemon the user has as shiny
            $statementSelect = $mysqli->prepare("Select id_usuario from usuario_has_pokemon uhp left join usuario u on uhp.id_usuario = u.id where uhp.id_pokemon = ? and u.email = ?");
            $statementInsert = $mysqli->prepare("Insert into usuario_has_pokemon(id_usuario, id_pokemon, has_shiny_too) select u.id, ?, ? from usuario u where u.email = ?");
            $statementUpdate = $mysqli->prepare("Update usuario_has_pokemon set has_shiny_too = ? where id_usuario = ? and id_pokemon = ?");
            foreach ($listhas as $el) {
                $el = intval($el);
                $statementSelect->bind_param('is', $el, $_SESSION['email']);
                $result = $statementSelect->execute();
                if(!$result) {
                    echo json_encode(array('status' => 0, 'message' => 'Error in select query'));
                    $mysqli->rollback();
                    exit();
                }

                $value = 1;
                $result = $statementSelect->get_result();
                if($result->num_rows == 0) {
                    // Insert
                    $statementInsert->bind_param('iis', $el, $value, $_SESSION['email']);
                    $result = $statementInsert->execute();
                    if(!$result) {
                        echo json_encode(array('status' => 0, 'message' => 'Error in insert query'));
                        $mysqli->rollback();
                        exit();
                    }
                }
                else {
                    // Update
                    $row = $result->fetch_assoc();
                    $userId = intval($row['id_usuario']);
                    $statementUpdate->bind_param('iii', $value, $userId, $el);
                    $result = $statementUpdate->execute();
                    if(!$result) {
                        echo json_encode(array('status' => 0, 'message' => 'Error in insert query'));
                        $mysqli->rollback();
                        exit();
                    }
                }
            }

            foreach ($listhasnt as $el) {
                $el = intval($el);
                $statementSelect->bind_param('is', $el, $_SESSION['email']);
                $result = $statementSelect->execute();
                if(!$result) {
                    echo json_encode(array('status' => 0, 'message' => 'Error in select query'));
                    $mysqli->rollback();
                    exit();
                }

                $value = 0;
                $result = $statementSelect->get_result();
                if($result->num_rows == 0) {
                    // Insert
                    $statementInsert->bind_param('iis', $el, $value, $_SESSION['email']);
                    $result = $statementInsert->execute();
                    if(!$result) {
                        echo json_encode(array('status' => 0, 'message' => 'Error in insert query'));
                        $mysqli->rollback();
                        exit();
                    }
                }
                else {
                    // Update
                    $row = $result->fetch_assoc();
                    $userId = intval($row['id_usuario']);
                    $statementUpdate->bind_param('iii', $value, $userId, $el);
                    $result = $statementUpdate->execute();
                    if(!$result) {
                        echo json_encode(array('status' => 0, 'message' => 'Error in insert query'));
                        $mysqli->rollback();
                        exit();
                    }
                }
            }

            $mysqli->commit();
            echo json_encode(array('status' => 1));
            break;
        default:
            header("Location:/pogo/views/error.php");
            break;
    }
}
else if($_SERVER['REQUEST_METHOD'] === 'GET') {
    $operation = $_GET['operation'];

    header('Content-Type: application/json');
    switch ($operation) {


        //    ___ ___ __  __  _____   _____   ___ _  _ ___ _  ___   __
        //   | _ \ __|  \/  |/ _ \ \ / / __| / __| || |_ _| \| \ \ / /
        //   |   / _|| |\/| | (_) \ V /| _|  \__ \ __ || || .` |\ V /
        //   |_|_\___|_|  |_|\___/ \_/ |___| |___/_||_|___|_|\_| |_|
        //
        case 'remove_shiny':
            if(isset($_GET['pokemon'])) {
                $pokemon_id = $_GET['pokemon'];
                $pokemon_id = intval($pokemon_id);
            }
            else {
                header("Location:/pogo/views_admin/shinylistadmin.php?error=1");
                exit();
            }

            $statement = $mysqli->prepare("Update pokemon set hasshiny = 0 where id = ?");
            $statement->bind_param('i', $pokemon_id);
            $result = $statement->execute();

            if($result) {
                header("Location:/pogo/views_admin/shinylistadmin.php?success=1");
            }
            else {
                // SQL error
                header("Location:/pogo/views_admin/shinylistadmin.php?error=3");
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


