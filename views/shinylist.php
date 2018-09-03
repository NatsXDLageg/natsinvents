<?php

if(!isset($server_var)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/server_var.php");
    $server_var = true;
}

if(!isset($check_login)) {
    include($pogo_path . "/php_posts/check_login.php");
    $check_login = true;
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
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/w3.css">
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/theme.css<?php echo $cache_sufix ?>"><!-- ?random=@Environment.TickCount -->
    <script src="/pogo/resources/js/jquery-3.3.1.min.js"></script>
    <title>Shiny List</title>
    <style>
        .marked {
            border: 5px limegreen solid;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <?php include($pogo_path."/resources/php_components/success_top_container.php"); ?>
    <?php include($pogo_path."/resources/php_components/error_top_container.php"); ?>
    <?php include($pogo_path."/resources/php_components/main_top_header.php"); ?>
    <div class="w3-container w3-padding-16">
        <button class="w3-button button-all button-main">Salvar</button>
    </div>

    <div id="shiny_list" class="w3-container">

    </div>

    <?php include($pogo_path."/resources/php_components/main_bottom_footer.php"); ?>
</body>

<script>

    $(document).ready(function() {
        <?php include($pogo_path."/resources/php_components/on_doc_ready_vanish.php"); ?>

        $.post( "/pogo/php_posts/post_pokemons.php", {
            operation: 'get_shinies_by_dex_evo',
            get_user_list: 'true'
        })
        .done(function(data) {
            if(data['status'] == 1) {
                var html = '';
                for(let row of data['shinies']) {
                    if(row['shinyimageurl'] == '') {
                        row['shinyimageurl'] = 'https://cdn3.iconfinder.com/data/icons/modifiers-add-on-1/48/v-17-512.png';
                    }
                    html += '<div class="w3-col s6 m4 l2 w3-center marked">' +
                        '       <img src="' + row['shinyimageurl'] + '" width="100%"/>' +
                        '       <p>' + row['nome'] + '</p>' +
                        '       <input type="checkbox" class="w3-check" style="margin-bottom: 20px;"/>' +
                        '</div>';
                }

                $('#shiny_list').append(html);
            }
        })
        .fail(function() {
            alert( "error" );
        });
    });
</script>
</html>
