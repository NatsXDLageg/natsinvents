<?php

if(!isset($server_var)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/server_var.php");
    $server_var = true;
}

//if(!isset($check_login)) {
//    include($pogo_path . "/php_posts/check_login.php");
//    $check_login = true;
//}
session_start();

if (isset($_SESSION['start_path'])) {
    $location = "Location:" . $_SESSION['start_path'];
    unset($_SESSION['start_path']);
    header($location);
    exit();
}

$cache_sufix = '?'.time();

$user = isset($_SESSION['email']);
$admin = isset($_SESSION['priority']) && ($_SESSION['priority'] === 999);

?>


<!DOCTYPE HTML>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/w3.css">
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/awesomplete.css">
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/theme.css<?php echo $cache_sufix ?>"><!-- ?random=@Environment.TickCount -->
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/toastr.min.css">
    <script type="text/javascript" src="/pogo/resources/js/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="/pogo/resources/js/toastr.min.js"></script>
    <script type="text/javascript" src="/pogo/resources/js/awesomplete.min.js"></script>
    <title>Nats Invents - Início</title>

    <style>
        .full-width {
            width: 100%;
        }
        .icon-fix-width {
            width: 32px;
        }
        .research-container {
            margin-top: 16px;
        }
        textarea {
            resize: vertical;
        }
    </style>
</head>
<body>
    <?php include($pogo_path."/resources/php_components/main_top_header.php"); ?>

        <div id="user_div" class="w3-container w3-padding-16 theme-text">
            <div id="research_div" class="w3-col w3-half w3-mobile">
                <h2>Missões</h2>
                <?php if($user) { ?>
                    <button class="w3-button button-all button-main full-width" onclick="document.getElementById('new_research_modal').style.display='block'"><i class="fas fa-plus"></i> Informar Missão</button>
                <?php } ?>
            </div>
            <div id="message_div" class="w3-col w3-half w3-mobile">

            </div>
        </div>
    <?php
        if($user) {
            include($pogo_path."/views/new_research_modal.php");
        }
    ?>

    <?php include($pogo_path."/resources/php_components/main_bottom_footer.php"); ?>
</body>

<script>
    var awesomplete;

    $(document).ready(function() {
        <?php if($user) { ?>
            var pokestop_name_input = document.getElementById("pokestop_name");
            awesomplete = new Awesomplete(pokestop_name_input);
            awesomplete.list = [];

            $.post( "/pogo/php_posts/post_pokestop.php", {
                operation: 'list_pokestops_by_name'
            })
            .done(function(data) {
                console.log(data);
                if(data['status'] == 1) {
                    awesomplete.list = data['data'];
                }
                else {
                    toastr['error']('Ocorreu um erro: ' + data['message'] + ' (' + data['status'] + ')');
                }
            });

            var input = document.getElementById("pokestop_name");

            input.addEventListener("keyup", function(event) {
                if (event.keyCode === 13) {
                    $('#research_confirm').trigger('click');
                }
            });

            input = document.getElementById("research");

            input.addEventListener("keyup", function(event) {
                if (event.keyCode === 13) {
                    $('#research_confirm').trigger('click');
                }
            });

            input = document.getElementById("reward");

            input.addEventListener("keyup", function(event) {
                if (event.keyCode === 13) {
                    $('#research_confirm').trigger('click');
                }
            });
        <?php } ?>

        $.post( "/pogo/php_posts/post_research.php", {
            operation: 'list_researches_by_priority'
        })
        .done(function(data) {
            if(data['status'] == 1) {
                var html = '';
                for (let research of data['data']) {

                    html += getResearchElement(research);
                }

                $('#research_div').append(html);
                <?php if($user) { ?>
                   bindDeleteResearchButtonAction();
                <?php } ?>
            }
            else {
                toastr['error']('Ocorreu um erro: ' + data['message'] + ' (' + data['status'] + ')');
            }
        });
    });

    function getResearchElement(research) {
        let html =
        '<div class="w3-container w3-row w3-display-container theme-text-secondary research-container" data-id="' + research['id'] + '">';

        if(research['editable'] == 1) {
            html +=
                '<span class="w3-button button-all button-tertiary w3-display-topright research-delete-button"><i class="fas fa-times"></i></span>';
        }
        html +=
            '<div class="w3-col w3-center icon-fix-width"><i class="fas fa-tasks"></i></div>' +
            '<div class="w3-rest"><strong>' + research['missao'] + '</strong></div>' +

            '<div class="w3-col w3-center icon-fix-width"><i class="fas fa-map-pin"></i></div>' +
            '<div class="w3-rest">' + research['pokestop'] + '</div>' +

            '<div class="w3-col w3-center icon-fix-width"><i class="fas fa-clock"></i></div>' +
            '<div class="w3-rest">' + minutesToTimePast(research['diferenca_tempo']) + '</div>' +
            '</div>' +
            '<hr>';
        return html;
    }

    function minutesToTimePast(minutes) { //Portuguese only
        let minutesAgo = ' minutos atrás';
        let hoursAgo = ' horas atrás';
        let daysAgo = ' dias atrás';
        try {
            minutes = parseInt(minutes);
        }
        catch(e) {
            return '';
        }
        if(minutes < 0) {
            return '';
        }
        else if(minutes < 10) {
            return 'Poucos' + minutesAgo;
        }
        else if(minutes < 60) {
            return minutes + minutesAgo;
        }
        else {
            let hours = Math.floor(minutes / 60);
            if(hours < 24) {
                return hours + hoursAgo;
            }
            else {
                let days = Math.floor(hours / 24);
                return days + daysAgo;
            }
        }
    }
</script>
</html>
