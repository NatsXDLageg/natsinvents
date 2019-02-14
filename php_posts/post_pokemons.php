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
            $sql = "Select
                        id,
                        CASE
                          WHEN alternateformname IS NOT NULL THEN 
                            CONCAT(nome, ' (', alternateformname, ')')
                          ELSE
                            nome
                        END as nome
                    from pokemon
                    order by nome";

            $statement = $mysqli->prepare($sql);
            $result = $statement->execute();

            if($result) {
                $result = $statement->get_result();
                $pokemons = array();
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $pokemons[] = $row;
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
                    echo json_encode(array('status' => 0, 'message' => 'Parâmetro não encontrado: email'));
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
                    Select
                        p.id,
                        CASE
                          WHEN p.alternateformname IS NOT NULL THEN 
                            CONCAT(p.nome, ' (', p.alternateformname, ')')
                          ELSE
                            p.nome
                        END as nome,
                        COALESCE(uhp.has_shiny_too, 0) as 'marked',
                        p.pokedexevo                   
                    from pokemon p
                        left join usuario_has_pokemon uhp on uhp.id_pokemon = p.id and uhp.id_usuario = ?
                    where
                        p.hasshiny = 1
                    order by p.pokedexevo, p.pokedex, p.id";
                $statement = $mysqli->prepare($query);
                $statement->bind_param('i', $userId);
            }
            else {
                $query = "Select
                            id,
                            CASE
                              WHEN alternateformname IS NOT NULL THEN 
                                CONCAT(nome, ' (', alternateformname, ')')
                              ELSE
                                nome
                            END as nome,
                            0 as 'marked'
                        from pokemon
                        where hasshiny = 1
                        order by pokedexevo, pokedex, id";
                $statement = $mysqli->prepare($query);
            }
            $result = $statement->execute();

            if($result) {
                $result = $statement->get_result();
                $pokemons = array();
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $pokemons[] = $row;
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
                echo json_encode(array('status' => 0, 'message' => 'Parâmetro não encontrado: email'));
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
                    Select p.id, p.nome, COALESCE(uhp.has_shiny_too, 0) as 'marked', p.pokedexevo, p.alternateformname                     
                    from pokemon p
                        left join usuario_has_pokemon uhp on uhp.id_pokemon = p.id and uhp.id_usuario = ?
                    where
                        p.hasshiny = 1
                        and p.evolve is null
                    order by p.pokedexevo, p.pokedex, p.id";
            $statement = $mysqli->prepare($query);
            $statement->bind_param('i', $userId);
            $result = $statement->execute();

            if($result) {
                $result = $statement->get_result();
                $data = array();
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $family = array();
                    $family[] = $row;

                    $alternateformname = $row['alternateformname'];
                    $firstOfFamilyWithAlternateForm = ($alternateformname !== null);

                    $dexNum = intval($row['pokedexevo']) + 1;
                    $elementFound = true;
                    while ($elementFound) {
                        if($firstOfFamilyWithAlternateForm) {
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
                                    and p.isevolutionuniquetoalternateform = 1
                                    and p.pokedexevo = ?
                                    and p.alternateformname = ?
                            ";

                            $statement = $mysqli->prepare($query);
                            $statement->bind_param('iis', $userId, $dexNum, $alternateformname);
                        }
                        else {
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
                                    and p.isevolutionuniquetoalternateform = 0
                                    and p.pokedexevo = ?
                            ";

                            $statement = $mysqli->prepare($query);
                            $statement->bind_param('ii', $userId, $dexNum);
                        }
                        $result2 = $statement->execute();

                        if(!$result2) {
                            echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
                            exit();
                        }

                        $result2 = $statement->get_result();
                        if($result2->num_rows == 0) {
                            $elementFound = false;
                        }

                        while($row2 = $result2->fetch_array(MYSQLI_ASSOC)) {
                            $family[] = $row2;
                        }

                        $dexNum = $dexNum + 1;
                    }

                    $data[] = $family;
                }

                echo json_encode(array('status' => 1, 'data' => $data));
            }
            else {
                // SQL error
                echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
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
                echo json_encode(array('status' => 0, 'message' => 'Parâmetro não encontrado: pokemon'));
                exit();
            }
            $link = isset($_POST['link']) ? $_POST['link'] : null;
            if(isset($_POST['password'])) {
                $userpwd = $_POST['password'];
            }
            else {
                echo json_encode(array('status' => 0, 'message' => 'Parâmetro não encontrado: password'));
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
                                echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
                                $mysqli->rollback();
                                exit();
                            }

                            $copy = 'não';
                            if($link) {
                                if(copy($link, $pogo_path.'/resources/images/pokemon/shiny/'.$pokemon_id.'.png')) {
                                    $copy = 'sim';
                                }
                                else {
                                    $copy = 'erro';
                                }
                            }

                            // Success
                            $mysqli->commit();
                            echo json_encode(array('status' => 1, 'message' => 'Novo shiny adicionado', 'debug' => 'copy: '.$copy));
                        }
                        else {
                            // Not admin
                            echo json_encode(array('status' => 0, 'message' => 'Não é usuário admin'));
                        }
                    }
                    else {
                        // Wrong password
                        echo json_encode(array('status' => 0, 'message' => 'Senha incorreta'));
                    }
                }
                else {
                    // No row fetch
                    echo json_encode(array('status' => 0, 'message' => 'Não foi possível recuperar resultados'));
                }
            }
            else {
                // SQL error
                echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
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
                echo json_encode(array('status' => 0, 'message' => 'Parâmetro não encontrado: email'));
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
                    echo json_encode(array('status' => 0, 'message' => 'Error in select query', 'debug' => $statement->error));
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
                        echo json_encode(array('status' => 0, 'message' => 'Error in insert query', 'debug' => $statement->error));
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
                        echo json_encode(array('status' => 0, 'message' => 'Error in insert query', 'debug' => $statement->error));
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
                    echo json_encode(array('status' => 0, 'message' => 'Error in select query', 'debug' => $statement->error));
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
                        echo json_encode(array('status' => 0, 'message' => 'Error in insert query', 'debug' => $statement->error));
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
                        echo json_encode(array('status' => 0, 'message' => 'Error in insert query', 'debug' => $statement->error));
                        $mysqli->rollback();
                        exit();
                    }
                }
            }

            $mysqli->commit();
            echo json_encode(array('status' => 1));
            break;

        //    ___ ___ __  __  _____   _____   ___ _  _ ___ _  ___   __
        //   | _ \ __|  \/  |/ _ \ \ / / __| / __| || |_ _| \| \ \ / /
        //   |   / _|| |\/| | (_) \ V /| _|  \__ \ __ || || .` |\ V /
        //   |_|_\___|_|  |_|\___/ \_/ |___| |___/_||_|___|_|\_| |_|
        //
        case 'remove_shiny':
            if(isset($_POST['pokemon'])) {
                $pokemon_id = $_POST['pokemon'];
                $pokemon_id = intval($pokemon_id);
            }
            else {
                echo json_encode(array('status' => 0, 'message' => 'Parâmetro não encontrado: pokemon'));
                exit();
            }

            $statement = $mysqli->prepare("Update pokemon set hasshiny = 0 where id = ?");
            $statement->bind_param('i', $pokemon_id);
            $result = $statement->execute();

            if($result) {
                echo json_encode(array('status' => 1, 'message' => 'Sucesso. Atualizado'));
            }
            else {
                // SQL error
                echo json_encode(array('status' => 0, 'message' => 'Erro de SQL', 'debug' => $statement->error));
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


