<?php

if(!isset($server_var)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/server_var.php");
    $server_var = true;
}

if(!isset($check_login_admin)) {
    include($pogo_path . "/php_posts/check_login_admin.php");
    $check_login_admin = true;
}

if (isset($_GET['error'])) {
    $error = $_GET['error'];
    switch ($error) {
        case 1:
            $errormessage = "Parâmetros (pokemon) incorreto";
            break;
        case 2:
            $errormessage = "Parâmetros (senha) incorreto";
            break;
        case 3:
            $errormessage = "Erro de SQL";
            break;
        case 4:
            $errormessage = "Pokemon não encontrado";
            break;
        case 5:
            $errormessage = "Senha incorreta";
            break;
        default:
            $errormessage = "Erro desconhecido, por favor tente novamente (".$error.")";
            break;
    }
}
if (isset($_GET['success'])) {
    $successmessage = "Sucesso!<br/>";
    $success = $_GET['success'];
    switch ($success) {
        case 1:
            $successmessage .= "Dados atualizados";
            break;
        default:
            break;
    }
}

$cache_sufix = '?'.time();

?>


<!DOCTYPE HTML>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/w3.css">
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/theme.css<?php echo $cache_sufix ?>"><!-- ?random=@Environment.TickCount -->
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/toastr.min.css">
    <script src="/pogo/resources/js/jquery-3.3.1.min.js"></script>
    <script src="/pogo/resources/js/toastr.min.js"></script>
    <title>Shiny List - Admin</title>
    <style>
        .close_button {
            position:absolute;
            left:5px;
            top:5px;
        }
        .close_button_parent {
            position:relative;
        }
    </style>
</head>
</head>
<body>
    <?php include($pogo_path."/resources/php_components/main_top_header.php"); ?>

    <div class="w3-container w3-padding-16">
        <h3>Adicionar Shiny:</h3>

        <form id="form" action="/pogo/php_posts/post_pokemons.php" method="post">
            <div class="w3-container w3-padding">
                <label for="pkmn_list">Pokemon: </label>
                <select id="pkmn_list" name="pokemon" class="w3-select" disabled>
                    <option value="none">Nenhum</option>
                </select>
            </div>
            <br/>

            <div class="w3-container w3-padding">
                <label for="password">Senha de confirmação do usuário: </label>
                <input type="password" id="password" name="password" class="w3-input input-center" maxlength="50" required/>
            </div>

            <input type="hidden" name="operation" value="set_new_shiny"/>

            <div class="w3-container w3-padding">
                <input type="submit" class="w3-button theme-bg button-main" value="CONFIRMAR"/>
            </div>
        </form>

        <div id="shiny_list" class="w3-container w3-padding-16">

        </div>
    </div>

    <?php include($pogo_path."/resources/php_components/main_bottom_footer.php"); ?>
</body>

<script>

    $(document).ready(function() {

        $.post( "/pogo/php_posts/post_pokemons.php", {
            operation: 'get_pokemons_by_name'
        })
        .done(function(data) {
            if(data['status'] == 1) {
                var html = '';
                for(let row of data['pokemons']) {
                    html += '<option value="' + row['id'] + '">' + row['nome'] + '</option>';
                }
                $('#pkmn_list').append(html).prop('disabled', false);
            }
        })
        .fail(function() {
            alert( "error" );
        });
        // .always(function() {
        //     alert( "finished" );
        // });


        $.post( "/pogo/php_posts/post_pokemons.php", {
            operation: 'get_shinies_by_dex_evo'
        })
        .done(function(data) {
            if(data['status'] == 1) {
                var html = '';
                for(let row of data['shinies']) {
                    html += '<div class="w3-col s6 m4 l2 w3-center close_button_parent">' +
                        '       <a href="/pogo/php_posts/post_pokemons.php?operation=remove_shiny&pokemon=' + row['id'] + '" class="close_button">' +
                        '           <button class="button-all button-tertiary">x</button>' +
                        '       </a>' +
                        '       <img src="/pogo/resources/images/pokemon/shiny/' + row['id'] + '.png" width="100%" onerror="this.src=\'/pogo/resources/images/pokemon/shiny/missing.png\';"/>' +
                        '       <p>' + row['nome'] + '</p>' +
                        '</div>';
                }

                $('#shiny_list').append(html);
            }
        })
        .fail(function() {
            alert( 'error' );
        });
    });

    $('#form').submit(function() {
        if($('#pkmn_list').val() == 'none') {
            toastr['warning']('Escolha um pokemon');
            return false;
        }
    });
</script>
</html>
