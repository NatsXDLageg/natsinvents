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
        default:
            $errormessage = "Erro desconhecido, por favor tente novamente (".$error.")";
            break;
    }
}

$cache_sufix = '?'.time();

?>


<!DOCTYPE HTML>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/w3.css">
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/theme.css<?php echo $cache_sufix ?>"><!-- ?random=@Environment.TickCount -->
    <script src="/pogo/resources/js/jquery-3.3.1.min.js"></script>
    <title>Shiny List - Admin</title>
</head>
<body>
    <?php include($pogo_path."/resources/php_components/error_top_container.php"); ?>
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

            <div class="w3-container w3-padding">
                <input type="checkbox" id="check" name="evoshiny" class="w3-check"/>
                <label for="check"> Evoluções também possuem forma shiny? </label>
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
    </div>

    <?php include($pogo_path."/resources/php_components/main_bottom_footer.php"); ?>
</body>

<script>

    $(document).ready(function() {
        <?php include($pogo_path."/resources/php_components/on_doc_ready_vanish.php"); ?>

        $.post( "/pogo/php_posts/post_pokemons.php", {
            operation: 'get_pokemons_by_name'
        })
        .done(function(data) {
            console.log(data);
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
    });

    $('#form').submit(function() {
        console.log($(this).serialize());
        return false;
        if($('#pkmn_list').val() == 'none') {
            alert("Por favor escolha um pokemon");
            return false;
        }
    });
</script>
</html>
