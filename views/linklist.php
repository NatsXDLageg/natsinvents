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
    include($pogo_path."/resources/php_components/import_js_css.php");
    ?>
    <title>Links Interessantes</title>
</head>
<body>
    <?php include($pogo_path."/resources/php_components/main_top_header.php"); ?>

    <div>
        <div class="w3-container w3-padding theme-text">
            <h2>Links Interessantes</h2>
        </div>

        <!--            TODO -->
<!--        <div class="w3-container w3-padding">-->
<!--            --><?php //if($user) { ?>
<!--                <button class="w3-button button-all button-main full-width" onclick="document.getElementById('new_link_modal').style.display='block'">-->
<!--            --><?php //} else { ?>
<!--                <button class="w3-button button-all button-main full-width" onclick="window.location.href = '/pogo/views/login.php'">-->
<!--            --><?php //} ?>
<!--            <i class="fas fa-plus"></i> ADICIONAR</button>-->
<!--        </div>-->

        <div id="link_list" class="w3-container w3-padding-16"></div>
    </div>

    <?php
    if($user) {
        include($pogo_path."/views/modals/new_link_modal.php");
    }
    include($pogo_path."/views/modals/confirm_modal.php");
    ?>

    <?php include($pogo_path."/resources/php_components/main_bottom_footer.php"); ?>
</body>

<script>

    $(document).ready(function() {
        $('#link_linklist').addClass('focus-bg');

        $.post( "/pogo/php_posts/post_links.php", {
            operation: 'list_links_by_time'
        })
        .done(function(data) {
            if(data['status'] == 1) {
                var html = '';
                for(let row of data['data']) {
                    html += getLinkElement(row);
                }
                $('#link_list').append(html);
            }
            else {
                toastr['error']('Ocorreu um erro: ' + data['message'] + ' (' + data['status'] + ')');
            }
        })
        .fail(function() {
            toastr['error']('Erro desconhecido');
        });
    });

    function getLinkElement(el) {

        let imageUrl = el['image_url'];
        let html;
        if(imageUrl !== null && imageUrl != '') {
            html = '<a href="' + el['url'] + '" target="_blank">' +
                    '<div class="full-width">' +
                        '<div class="w3-cell">' +
                            '<img src="' + imageUrl + '" class="link-img"/>' +
                        '</div>' +
                        '<div id="poke' + el['id'] + '" class="w3-container w3-cell link" data-id="' + el['id'] + '">' +
                            '<i class="w3-center icon-fix-width fas fa-link"></i>&nbsp;' +
                            el['titulo'] +
                        '</div>' +
                    '</div>' +
                '</a>' +
                '<hr>';
        }
        else {
            html = '<a href="' + el['url'] + '" target="_blank">' +
                    '<div class="w3-container">' +
                        '<i class="w3-center icon-fix-width fas fa-link"></i>&nbsp;' +
                        el['titulo'] +
                    '</div>' +
                '</a>' +
                '<hr>';
        }
        return html;
    }
</script>
</html>
