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

                $query = "Select p.id, p.nome, p.shinyimageurl, COALESCE(uhp.has_shiny_too, 0) as 'marked' from pokemon p left join usuario_has_pokemon uhp on uhp.id_pokemon = p.id where p.hasshiny = 1 order by pokedexevo";
            }
            else {
                $query = "Select id, nome, shinyimageurl, 0 as 'marked' from pokemon where hasshiny = 1 order by pokedexevo";
            }
            $statement = $mysqli->prepare($query);
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
            $apply_to_family = isset($_POST['evoshiny']) && $_POST['evoshiny'] == 'on';
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

                            // Will search for all members of family
                            // Then update all fetched elements

                            $evolution_family = array();
                            $evolution_family[] = $pokemon_id;

                            if($apply_to_family) {

                                // Search for current pokemon
                                $statement = $mysqli->prepare("Select id from pokemon where id = ? and evolve is not null");
                                $statement->bind_param('i', $pokemon_id);
                                $result = $statement->execute();

                                if (!$result) {
                                    // SQL error
                                    header("Location:/pogo/views_admin/shinylistadmin.php?error=3");
                                    exit();
                                }

                                $result = $statement->get_result();
                                if ($result->num_rows == 1) {
                                    // If the pokemon has evolution information, so it has a pre-evolution
                                    $evolution_family[] = $pokemon_id - 1;

                                    // Do the same one more time
                                    $current_id = $pokemon_id - 1;

                                    $statement = $mysqli->prepare("Select id from pokemon where id = ? and evolve is not null");
                                    $statement->bind_param('i', $current_id);
                                    $result = $statement->execute();

                                    if (!$result) {
                                        // SQL error
                                        header("Location:/pogo/views_admin/shinylistadmin.php?error=3");
                                        exit();
                                    }

                                    $result = $statement->get_result();
                                    if ($result->num_rows == 1) {
                                        // If the pokemon has evolution information, so it has a pre-evolution
                                        $evolution_family[] = $current_id - 1;
                                    }
                                }

                                // Search for evolution
                                $current_id = $pokemon_id + 1;
                                $statement = $mysqli->prepare("Select id from pokemon where id = ? and evolve is not null");
                                $statement->bind_param('i', $current_id);
                                $result = $statement->execute();

                                if (!$result) {
                                    // SQL error
                                    header("Location:/pogo/views_admin/shinylistadmin.php?error=3");
                                    exit();
                                }

                                $result = $statement->get_result();
                                if ($result->num_rows == 1) {
                                    // The evolution must be considered
                                    $evolution_family[] = $current_id;

                                    // Do the same one more time
                                    $current_id = $pokemon_id + 2;
                                    $statement = $mysqli->prepare("Select id from pokemon where id = ? and evolve is not null");
                                    $statement->bind_param('i', $current_id);
                                    $result = $statement->execute();

                                    if (!$result) {
                                        // SQL error
                                        header("Location:/pogo/views_admin/shinylistadmin.php?error=3");
                                        exit();
                                    }

                                    $result = $statement->get_result();
                                    if ($result->num_rows == 1) {
                                        // The evolution must be considered
                                        $evolution_family[] = $current_id;
                                    }
                                }
                            }

                            $mysqli->autocommit(false);

                            // Update pokemon
                            foreach ($evolution_family as $el) {
                                $statement = $mysqli->prepare("Update pokemon set hasshiny = 1 where id = ?");
                                $statement->bind_param('i', $el);
                                $result = $statement->execute();

                                if(!$result) {
                                    // SQL error
                                    header("Location:/pogo/views_admin/shinylistadmin.php?error=3");
                                    $mysqli->rollback();
                                    exit();
                                }
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
                echo json_encode(array('status' => 0));
                exit();
            }

            if(isset($_POST['list'])) {
                $list = $_POST['list'];
            }
            else {
                echo json_encode(array('status' => 0));
                exit();
            }

            //It worked to get this list as an array!


            echo json_encode(array('status' => 1, 'var' => print_r($list, TRUE)));
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


