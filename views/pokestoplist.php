<?php

if(!isset($server_var)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/server_var.php");
    $server_var = true;
}

if(!isset($check_login)) {
    include($pogo_path . "/php_posts/check_login.php");
    $check_login = true;
}

$cache_sufix = '?'.time();

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
            <h2>Lista de pokestops</h2>
        </div>

        <div id="pokestop_list" class="w3-container w3-padding-16"></div>
    </div>

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
                    html += '<div class="w3-container">' +
                                '<div class="w3-col w3-center icon-fix-width"><i class="fas fa-map-pin"></i></div>' +
                                '<div class="w3-rest">' + row + '</div>' +
                            '</div>' +
                            '<hr>';
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
</script>
</html>
