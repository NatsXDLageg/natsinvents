<?php

if(!isset($server_var)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/server_var.php");
    $server_var = true;
}

if(!isset($check_login_admin)) {
    include($pogo_path . "/php_posts/check_login_admin.php");
    $check_login_admin = true;
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

        <div class="w3-container w3-padding">
            <label for="pkmn_list">Pokemon: </label>
            <select id="pkmn_list" name="pokemon" class="w3-select" disabled>
                <option value="none">Nenhum</option>
            </select>
        </div>
        <div class="w3-container w3-padding">
            <label for="link">Link com imagem do pokemon shiny: </label>
            <input type="text" id="link" name="link" class="w3-input" placeholder="google.com/img.png" maxlength="300"/>
        </div>
        <br/>

        <div class="w3-container w3-padding">
            <label for="password">Senha de confirmação do usuário: </label>
            <input type="password" id="password" name="password" class="w3-input input-center" maxlength="50" required/>
        </div>

        <input type="hidden" name="operation" value="set_new_shiny"/>

        <div class="w3-container w3-padding">
            <input type="button" id="submit" class="w3-button theme-bg button-main" value="CONFIRMAR"/>
        </div>

        <div id="shiny_list" class="w3-container w3-padding-16">

        </div>
    </div>

    <?php include($pogo_path."/resources/php_components/main_bottom_footer.php"); ?>
</body>

<script>

    $(document).ready(function() {


        //    _    ___   _   ___    ___ ___ _    ___ ___ _____   _    ___ ___ _____
        //   | |  / _ \ /_\ |   \  / __| __| |  | __/ __|_   _| | |  |_ _/ __|_   _|
        //   | |_| (_) / _ \| |) | \__ \ _|| |__| _| (__  | |   | |__ | |\__ \ | |
        //   |____\___/_/ \_\___/  |___/___|____|___\___| |_|   |____|___|___/ |_|
        //
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

        //    _    ___   _   ___    ___  ___ _____ _____ ___  __  __   _    ___ ___ _____
        //   | |  / _ \ /_\ |   \  | _ )/ _ \_   _|_   _/ _ \|  \/  | | |  |_ _/ __|_   _|
        //   | |_| (_) / _ \| |) | | _ \ (_) || |   | || (_) | |\/| | | |__ | |\__ \ | |
        //   |____\___/_/ \_\___/  |___/\___/ |_|   |_| \___/|_|  |_| |____|___|___/ |_|
        //
        $.post( "/pogo/php_posts/post_pokemons.php", {
            operation: 'get_shinies_by_dex_evo'
        })
        .done(function(data) {
            if(data['status'] == 1) {
                var html = '';
                for(let row of data['shinies']) {
                    html += '<div class="w3-col s6 m4 l2 w3-center close_button_parent">' +
                        '       <button class="button-all button-tertiary close_button" data-id="' + row['id'] + '">&times</button>' +
                        '       <img src="/pogo/resources/images/pokemon/shiny/' + row['id'] + '.png" width="100%" onerror="this.src=\'/pogo/resources/images/pokemon/shiny/missing.png\';"/>' +
                        '       <p>' + row['nome'] + '</p>' +
                        '</div>';
                }

                $('#shiny_list').append(html);

                //    ___ ___    _   ___ ___   ___ _   _ _____ _____ ___  _  _     _   ___ _____ ___ ___  _  _
                //   | __| _ \  /_\ / __| __| | _ ) | | |_   _|_   _/ _ \| \| |   /_\ / __|_   _|_ _/ _ \| \| |
                //   | _||   / / _ \\__ \ _|  | _ \ |_| | | |   | || (_) | .` |  / _ \ (__  | |  | | (_) | .` |
                //   |___|_|_\/_/ \_\___/___| |___/\___/  |_|   |_| \___/|_|\_| /_/ \_\___| |_| |___\___/|_|\_|
                //
                $('.close_button').on('click', function() {
                    var parent_element = $(this).closest('div.close_button_parent');
                    $.post("/pogo/php_posts/post_pokemons.php", {
                        operation: 'remove_shiny',
                        pokemon: $(this).attr('data-id')
                    })
                    .done(function (data) {
                        console.log(data);
                        if (data['status'] == 1) {
                            toastr['success'](data['message']);
                            parent_element.remove();
                        }
                        else {
                            console.log(data);
                            toastr['error'](data['message']);
                        }
                    });
                });
            }
        })
        .fail(function() {
            alert( 'error' );
        });

        var input = document.getElementById("link");

        input.addEventListener("keyup", function(event) {
            if (event.keyCode === 13) {
                $('#submit').trigger('click');
            }
        });

        input = document.getElementById("password");

        input.addEventListener("keyup", function(event) {
            if (event.keyCode === 13) {
                $('#submit').trigger('click');
            }
        });
    });

    //    ___ _   _ ___ __  __ ___ _____     _   ___ _____ ___ ___  _  _
    //   / __| | | | _ )  \/  |_ _|_   _|   /_\ / __|_   _|_ _/ _ \| \| |
    //   \__ \ |_| | _ \ |\/| || |  | |    / _ \ (__  | |  | | (_) | .` |
    //   |___/\___/|___/_|  |_|___| |_|   /_/ \_\___| |_| |___\___/|_|\_|
    //
    $('#submit').on('click', function() {
        if($('#pkmn_list').val() == 'none') {
            toastr['warning']('Escolha um pokemon');
            return;
        }
        var link = $('#link').val().trim();
        if(link === '') {
            link = null;
        }
        $.post("/pogo/php_posts/post_pokemons.php", {
            operation: 'set_new_shiny',
            password: $('input#password').val(),
            pokemon: $('#pkmn_list').val(),
            link: link
        })
        .done(function (data) {
            console.log(data);
            if (data['status'] == 1) {
                toastr['success'](data['message']);
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            }
            else {
                console.log(data);
                toastr['error'](data['message']);
            }
        });
    });
</script>
</html>
