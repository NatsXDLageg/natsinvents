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

$user = isset($_SESSION['email']);
$admin = isset($_SESSION['priority']) && ($_SESSION['priority'] === 999);

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
    $awesomplete = true;
    $iconSelect = false;
    $moment = true;
    $html2canvas = false;
    include($pogo_path."/resources/php_components/import_js_css.php");
    ?>
    <title>Nats Invents - Início</title>

    <style>
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
                    <button class="w3-button button-all button-main full-width" onclick="document.getElementById('new_research_modal').style.display='block'"><i class="fas fa-plus"></i> INFORMAR MISSÃO</button>
                <?php } else { ?>
                    <button class="w3-button button-all button-main full-width" onclick="window.location.replace('/pogo/views/login.php')"><i class="fas fa-plus"></i> INFORMAR MISSÃO</button>
                <?php } ?>
                <div id="loading_reasearch" class="w3-container w3-padding w3-center">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <button id="load_more_researches" class="w3-button button-all button-secondary full-width">CARREGAR MAIS</button>
            </div>
            <div id="message_div" class="w3-col w3-half w3-mobile">

            </div>
        </div>
    <?php
        if($user) {
            include($pogo_path."/views/new_research_modal.php");
        }
        include($pogo_path."/views/confirm_modal.php");
    ?>

    <?php include($pogo_path."/resources/php_components/main_bottom_footer.php"); ?>
</body>

<script>
    var awesomplete;
    var research_index = 0;

    $(document).ready(function() {
        $('#link_index').addClass('focus-bg');
        moment.locale('pt-br');

        <?php if($user) { ?>
            var pokestop_name_input = document.getElementById("pokestop_name");
            awesomplete = new Awesomplete(pokestop_name_input);
            awesomplete.list = [];

            $.post( "/pogo/php_posts/post_pokestop.php", {
                operation: 'list_pokestops_by_name'
            })
            .done(function(data) {
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

        loadResearches();
    });

    $('#load_more_researches').on('click', function() {
        loadResearches();
    });

    function loadResearches() {
        $('#loading_reasearch').show();
        $('#load_more_researches').hide();
        $.post( "/pogo/php_posts/post_research.php", {
            operation: 'list_researches_by_priority',
            index: research_index
        })
        .done(function(data) {
            console.log(data);
            if(data['status'] == 1) {
                let loaded = parseInt(data['data']['loaded']);
                if(loaded == 0) {
                    $('#load_more_researches').remove();
                }
                var html = '';
                for (let research of data['data']['research']) {

                    html += getResearchElement(research);
                }

                $('#loading_reasearch').before(html);
                research_index += loaded;
                <?php if($user) { ?>
                    bindDeleteResearchButtonAction();
                <?php } ?>
            }
            else {
                toastr['error']('Ocorreu um erro: ' + data['message'] + ' (' + data['status'] + ')');
            }
            $('#loading_reasearch').hide();
            $('#load_more_researches').show();
        });
    }

    function getResearchElement(research) {
        let faded = 'theme-text-secondary';

        let researchMoment = moment(research['dia'], "YYYY-MM-DD").format('L');
        let nowMoment = moment().format('L');

        let day = 'Hoje';
        if(researchMoment !== nowMoment) {
            faded = 'theme-text-secondary-faded';
            day = researchMoment;
        }

        let html =
        '<div class="w3-container w3-row w3-display-container ' + faded + ' research-container" data-id="' + research['id'] + '">';

        if(research['removable'] == 1) {
            html +=
                '<span class="w3-button button-all button-tertiary w3-display-topright research-delete-button"><i class="fas fa-times"></i></span>';
        }
        html +=
            '<div class="w3-col w3-center icon-fix-width"><i class="fas fa-tasks"></i></div>' +
            '<div class="w3-rest"><strong>' + research['missao'] + '</strong></div>' +

            '<div class="w3-col w3-center icon-fix-width"><i class="fas fa-map-pin"></i></div>' +
            '<div class="w3-rest">' + research['pokestop'] + '</div>' +

            '<div class="w3-col w3-center icon-fix-width"><i class="fas fa-clock"></i></div>' +
            '<div class="w3-rest">' + day + '</div>' +
            '</div>' +
            '<hr>';
        return html;
    }
</script>
</html>
