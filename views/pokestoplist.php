<?php

if(!isset($server_var)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/server_var.php");
    $server_var = true;
}

if(!isset($check_login)) {
    include($pogo_path . "/php_posts/check_login.php");
    $check_login = true;
}

$user = isset($_SESSION['email']);
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
    $awesomplete = false;
    $iconSelect = false;
    $moment = false;
    $html2canvas = false;
    include($pogo_path."/resources/php_components/import_js_css.php");
    ?>
    <title>Pokestop List</title>
</head>
<body>
    <?php include($pogo_path."/resources/php_components/main_top_header.php"); ?>

    <div>
        <div class="w3-container w3-padding theme-text">
            <h2>Lista de pokestops e ginásios</h2>
        </div>

        <div class="w3-container w3-padding">
            <?php if($user) { ?>
                <button class="w3-button button-all button-main full-width" onclick="document.getElementById('new_pokestop_gym_modal').style.display='block'">
            <?php } else { ?>
                <button class="w3-button button-all button-main full-width" onclick="window.location.replace('/pogo/views/login.php')">
            <?php } ?>
            <i class="fas fa-plus"></i> SUGERIR NOVO</button>
        </div>

        <div id="pokestop_list" class="w3-container w3-padding-16"></div>
    </div>

    <?php
    if($user) {
        include($pogo_path."/views/new_pokestop_gym_modal.php");
    }
    include($pogo_path."/views/confirm_modal.php");
    ?>

    <?php include($pogo_path."/resources/php_components/main_bottom_footer.php"); ?>
</body>

<script>

    $(document).ready(function() {
        $('#link_pokestoplist').addClass('focus-bg');

        $.post( "/pogo/php_posts/post_pokestop.php", {
            operation: 'list_pokestops_by_name'
        })
        .done(function(data) {
            console.log(data);
            if(data['status'] == 1) {
                var html = '';
                for(let row of data['data']) {
                    html += getPokestopElement(row);
                }
                $('#pokestop_list').append(html);
            }
            else {
                toastr['error']('Ocorreu um erro: ' + data['message'] + ' (' + data['status'] + ')');
            }
        })
        .fail(function() {
            toastr['error']('Erro desconhecido');
        });
    });

    function getPokestopElement(el) {
        let html = '<div class="w3-container">' +
            '<div class="w3-col w3-center icon-fix-width"><i class="fas fa-map-pin"></i></div>' +
            '<div class="w3-rest">' + el + '</div>' +
            '</div>' +
            '<hr>';
        return html;
    }
</script>
</html>
