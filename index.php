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
    $moment = true;
    $leaflet = true;
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
        #map-crosshair {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 900;
            background-color: white;
            border-radius: 4px;
            border-color: rgba(0, 0, 0, 0.2);
            font-size: 22px;
            cursor: pointer;
            padding: 0;
            height: 34px;
            width: 34px;
        }
        #map-crosshair:hover {
            background-color: #f4f4f4;
        }
        #map-crosshair:disabled {
            background-color: #f4f4f4;
            color: #bbb;
        }
        #search_div {
            margin-top: 16px;
        }
        #search_input {
            display: inline-block;
            width: calc(100% - 36px) !important;
            vertical-align: top;
        }
        #search_icon_button {
            display: inline-block;
            height: 39px;
            width: 36px;
            vertical-align: top;
            padding: 8px !important;
            border-top-left-radius: 20px;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 20px;
        }
    </style>
</head>
<body>
    <?php include($pogo_path."/resources/php_components/main_top_header.php"); ?>

        <div id="user_div" class="w3-container w3-padding-16 theme-text">
            <div id="research_div" class="w3-col w3-half w3-mobile">
                <h2>Missões</h2>
                <?php if($user) { ?>
                    <div class="duo_button_div">
                        <div class="duo_button_left">
                            <button class="w3-button button-all button-secondary full-width" onclick="openResearchModal()"><i class="fas fa-plus"></i><span class="w3-hide-small"> INFORMAR MISSÃO</span></button>
                        </div>
                        <div class="duo_button_right">
                            <button class="w3-button button-all button-main full-width" onclick="openMapModal()"><i class="fas fa-map-marked-alt"></i> INFORMAR NO MAPA</button>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="duo_button_div">
                        <div class="duo_button_left">
                            <button class="w3-button button-all button-secondary full-width" onclick="window.location.href = '/pogo/views/login.php'"><i class="fas fa-plus"></i><span class="w3-hide-small"> INFORMAR MISSÃO</span></button>
                        </div>
                        <div class="duo_button_right">
                            <button class="w3-button button-all button-main full-width" onclick="window.location.href = '/pogo/views/login.php'"><i class="fas fa-map-marked-alt"></i> INFORMAR NO MAPA</button>
                        </div>
                    </div>
                <?php } ?>
                <div id="search_div">
                    <button id="search_icon_button" class="w3-button button-all button-secondary">
                        <i class="fas fa-search" style="margin: 0;"></i>
                    </button><input id="search_input" type="text" class="w3-input" placeholder="Pesquisar"/>
                </div>
                <div id="no_match_filter" class="w3-container theme-text-secondary" style="display: none;">
                    <p>Nenhuma missão encontrada para essa pesquisa</p>
                </div>
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
            include($pogo_path."/views/modals/new_research_modal.php");
            include($pogo_path."/views/modals/new_research_map_modal.php");
        }
        include($pogo_path."/views/modals/confirm_modal.php");
    ?>

    <?php include($pogo_path."/resources/php_components/main_bottom_footer.php"); ?>
</body>

<script>
    var research_index = 0;

    $(document).ready(function() {
        $('#link_index').addClass('focus-bg');
        moment.locale('pt-br');

        <?php if($user) { ?>
            researchModalOnLoad();
            researchMapModalOnLoad();
        <?php } ?>

        loadResearches();
    });

    $('#load_more_researches').on('click', function() {
        loadResearches();
    });

    $('#search_icon_button').on('click', function() {
        $('#search_input').focus();
    });
    $('#search_input').on('keyup', function() {
        filter($(this).val().trim().toLowerCase());
    });

    function filter(value) {
        let containers = $('.research-container');

        if(value === '') {
            containers.show();
            containers.next().show();
            return;
        }

        containers.hide();
        containers.next().hide();

        let searchables = $('.searchable');
        let matches = 0;
        for(let i = 0; i < searchables.length; i++) {
            let searchable = searchables.eq(i);

            if(searchable.text().toLowerCase().indexOf(value) >= 0) {
                let searchable_parent = searchable.closest('.research-container');

                searchable_parent.show();
                searchable_parent.next().show();
                matches++;
            }
        }

        if(matches === 0) {
            $('#no_match_filter').show();
        }
        else {
            $('#no_match_filter').hide();
        }
    }

    function loadResearches() {
        $('#loading_reasearch').show();
        $('#load_more_researches').hide();
        $.post( "/pogo/php_posts/post_research.php", {
            operation: 'list_researches_by_priority',
            index: research_index
        })
        .done(function(data) {
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

                $('#search_input').trigger('keyup');
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

        let user = 'Eu';

        // url com coordenadas: "http://maps.apple.com/?q=-15.623037,18.388672";
        let mapsUrl = "http://maps.apple.com/?q=";
        let mapsLabel = 'Abrir localização';

        let html =
        '<div class="w3-container w3-row w3-display-container ' + faded + ' research-container" data-id="' + research['id'] + '">';

        if(research['removable'] == 1) {
            html +=
                '<span class="w3-button button-all button-tertiary w3-display-topright research-delete-button"><i class="fas fa-times"></i></span>';
        }
        else {
            user = research['usuario'];
        }

        html +=
            '<div class="w3-col w3-center icon-fix-width"><i class="fas fa-tasks"></i></div>' +
            '<div class="w3-rest"><strong class="searchable">' + research['missao'] + '</strong></div>';

        if(typeof research['coordenadas'] !== 'undefined' && research['coordenadas'] !== null) {
            html +=
                '<div class="w3-col w3-center icon-fix-width"><i class="fas fa-map-pin"></i></div>' +
                '<div class="w3-rest"><a href="' + mapsUrl + research['coordenadas'] + '" target="_blank">' + mapsLabel + '</a></div>';
        }
        else {
            html +=
                '<div class="w3-col w3-center icon-fix-width"><i class="fas fa-map-pin"></i></div>' +
                '<div class="w3-rest searchable">' + research['pokestop'] + '</div>';
        }

        html +=
            '<div class="w3-col w3-center icon-fix-width"><i class="fas fa-clock"></i></div>' +
            '<div class="w3-rest searchable">' + day + '</div>' +

            '<div class="w3-col w3-center icon-fix-width"><i class="fas fa-user"></i></div>' +
            '<div class="w3-rest searchable">' + user + '</div>' +
            '</div>' +
            '<hr>';
        return html;
    }
</script>
</html>
