<?php

if(!isset($server_var)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/server_var.php");
    $server_var = true;
}

if(!isset($check_login)) {
    include($pogo_path . "/php_posts/check_login.php");
    $check_login = true;
}

?>


<!DOCTYPE HTML>

<html>
<head>
    <meta name="viewport" content="width=device-width, user-scalable=no">
    <?php
    $w3css = true;
    $theme = true;
    $jquery = true;
    $fontAwesome = true;
    $toastr = true;
    include($pogo_path."/resources/php_components/import_js_css.php");
    ?>
    <title>Shiny List</title>
    <style>

        @media only screen and (min-width:600px ) {

        }
        @media only screen and (max-width:599px ) {
            #shiny_list {
                padding-left: 8px;
                padding-right: 8px;
            }
        }

        .marked {
            /*border: 5px #0ABF58 solid;*/
            border: 5px #CCAD0A solid;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <?php include($pogo_path."/resources/php_components/main_top_header.php"); ?>

    <div>
        <div class="w3-container w3-padding theme-text">
            <h2>Lista de shinies</h2>

            <p>Clique ou toque sobre os pokemons para selecionar os shinies que você possui.</p>
            <p>Clique em "Salvar" após terminar a seleção.</p>
            <p>Clique em "Visualizar" para gerar uma imagem que poderá ser salva para compartilhar.</p>
        </div>
        <div class="w3-container w3-padding-16">
            <button class="w3-button button-all button-main update-list"><i class="fas fa-save"></i>SALVAR</button>
            <a href="shinyprint.php"><button class="w3-button button-all button-secondary"><i class="fas fa-eye"></i>VISUALIZAR</button></a>
        </div>

        <div id="shiny_list" class="w3-container"></div>

        <div class="w3-container w3-padding-16">
            <button class="w3-button button-all button-main update-list"><i class="fas fa-save"></i>SALVAR</button>
            <a href="shinyprint.php"><button class="w3-button button-all button-secondary"><i class="fas fa-eye"></i>VISUALIZAR</button></a>
        </div>
    </div>

    <?php include($pogo_path."/resources/php_components/main_bottom_footer.php"); ?>
</body>

<script>
    var imagesToLoadList;
    var imagesToLoadIndex = 0;
    const amountToLoadAtOnce = 10;
    const maxRepeatReload = 2;

    $(document).ready(function() {
        $('#link_shinylist').addClass('focus-bg');

        $.post( "/pogo/php_posts/post_pokemons.php", {
            operation: 'get_shinies_by_dex_evo',
            get_user_list: 'true'
        })
        .done(function(data) {
            if(data['status'] == 1) {
                imagesToLoadList = data['shinies'];
                console.log(imagesToLoadList);

                var html = '';
                for(let row of data['shinies']) {
                    let marked = '';
                    if(row['marked'] == 1) {
                        marked = ' marked';
                    }
                    html += '<div id="poke' + row['id'] + '" class="w3-col s4 m3 l2 w3-center shiny-pokemon'+marked+'" data-id="'+row['id']+'"></div>';
                }

                $('#shiny_list').append(html);

                $('#shiny_list .shiny-pokemon').on('click', function() {
                    if($(this).hasClass('marked')) {
                        $(this).removeClass('marked');
                    }
                    else {
                        $(this).addClass('marked');
                    }
                });

                loadNextPokes();
            }
            else {
                toastr['error'](data['message']);
            }
        })
        .fail(function() {
            toastr['error']('Erro desconhecido');
        });
    });

    $('.update-list').on('click', function() {
        var has_list = new Array();
        let fetch = $('#shiny_list .shiny-pokemon.marked');

        for (let i = 0; i < fetch.length; i++) {
            has_list.push(fetch.eq(i).attr('data-id'));
        }

        var has_not_list = new Array();
        fetch = $('#shiny_list .shiny-pokemon:not(.marked)');

        for (let i = 0; i < fetch.length; i++) {
            has_not_list.push(fetch.eq(i).attr('data-id'));
        }

        $.post( "/pogo/php_posts/post_pokemons.php", {
            'operation': 'update_shinies_user',
            'listhas[]': has_list,
            'listhasnt[]': has_not_list
        })
        .done(function(data) {
            if(data['status'] == 1) {
                toastr['success']('Lista atualizada!');
            }
            else {
                toastr['error']('Ocorreu um erro: ' + data['message'] + ' (' + data['status'] + ')');
            }
        })
        .fail(function() {
            toastr['error']('Ocorreu um erro');
        });
    });

    function loadNextPokes() {

        if(imagesToLoadIndex >= imagesToLoadList.length) {
            return;
        }

        for(let i = imagesToLoadIndex; i < imagesToLoadIndex + amountToLoadAtOnce && i < imagesToLoadList.length; i++) {
            let pokeNumber = imagesToLoadList[i]['id'];

            let html = '<img class="img-shiny" src="/pogo/resources/images/pokemon/shiny/' + pokeNumber + '.png" width="100%" onload="checkAllLoaded(' + i + ');" onerror="reloadPoke(' + i + ');"/>' +
                '<p>' + imagesToLoadList[i]['nome'] + '</p>';

            $('#poke' + pokeNumber).append(html);
        }
    }

    function checkAllLoaded(newLoadedIndex) {
        imagesToLoadList[newLoadedIndex]['loaded'] = true;

        for(let i = imagesToLoadIndex; i < imagesToLoadIndex + amountToLoadAtOnce && i < imagesToLoadList.length; i++) {
            if(!imagesToLoadList[i]['loaded']) {
                return false;
            }
        }

        imagesToLoadIndex += amountToLoadAtOnce;
        loadNextPokes();
        return true;
    }
    
    function reloadPoke(index) {
        let html;
        let pokeNumber = imagesToLoadList[index]['id'];

        if(typeof imagesToLoadList[index]['repeat'] === 'undefined') {
            imagesToLoadList[index]['repeat'] = 0;
        }
        if(imagesToLoadList[index]['repeat'] == maxRepeatReload) {
            // let html = '<img class="img-shiny" src="/pogo/resources/images/pokemon/shiny/' + row['id'] + '.png" width="100%" onload="loadNextPokes(false);" onerror="this.src=\'/pogo/resources/images/pokemon/shiny/missing.png\';"/>';
            html = '<img class="img-shiny" src="/pogo/resources/images/pokemon/shiny/missing.png" width="100%" onload="checkAllLoaded(' + index + ');" onerror="reloadPoke(' + index + ');"/>' +
                '<p>' + imagesToLoadList[index]['nome'] + '</p>';
        }
        else {
            (imagesToLoadList[index]['repeat'])++;

            html = '<img class="img-shiny" src="/pogo/resources/images/pokemon/shiny/' + pokeNumber + '.png" width="100%" onload="checkAllLoaded(' + index + ');" onerror="reloadPoke(' + index + ');"/>' +
                '<p>' + imagesToLoadList[index]['nome'] + '</p>';
        }

        let selector = $('#poke' + pokeNumber);
        selector.empty();
        selector.append(html);
    }
</script>
</html>
