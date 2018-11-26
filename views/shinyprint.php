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
    <?php
    $w3css = true;
    $theme = true;
    $jquery = true;
    $fontAwesome = true;
    $toastr = true;
    $html2canvas = true;
    include($pogo_path."/resources/php_components/import_js_css.php");
    ?>
    <title>Shiny Print</title>
    <style>
        body {
            width: 1280px !important;
            background-color: #01040f;
        }

        @media only screen and (min-width:600px ) {

        }
        @media only screen and (max-width:599px ) {
            #shiny_list {
                padding-left: 8px;
                padding-right: 8px;
            }
        }

        #shiny_list {
            background-color: #01040f;
            background-image: url('/pogo/resources/images/looped.png');
            padding: 0;
        }

        .print {
            width: 100%;
            padding: 3vh;
            font-size: 5vh;
            line-height: 5vh;
            height: auto;
        }
        
        .marked {
            border: 5px limegreen solid;
            border-radius: 10px;
        }

        .shiny_div {
            display: inline-block;
            margin-left: 10px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="w3-container w3-padding-16">
        <button id="download_button" class="w3-button button-all button-main print" disabled><i class="fas fa-file-download" style="margin-right: 3vh;"></i>FAZER DOWNLOAD</button>
        <a id="link" class="w3-hide"></a>
    </div>

    <div id="shiny_list" class="w3-container w3-center">
        <img src="/pogo/resources/images/PrintListHeader.png" width="100%"/>
    </div>
</body>

<script>
    var imagesToLoadList;
    var imagesToLoadIndex = 0;
    const amountOfFamiliesToLoadAtOnce = 4;
    const maxRepeatReload = 3;
    var link = null;

    $(document).ready(function() {

        $.post( "/pogo/php_posts/post_pokemons.php", {
            operation: 'get_shinies_by_dex_evo_group_families'
        })
        .done(function(data) {
            if(data['status'] == 1) {
                imagesToLoadList = data['data'];

                var html = '';
                let i = 0;
                for(let family of data['data']) {
                    html += '<div class="w3-center shiny_div">';

                    let j = 0;
                    for(let row of family) {
                        let marginLeft = '';
                        if(j > 0) {
                            marginLeft = 'style="margin-left: -40px;"';
                        }
                        html += '<img id="poke' + row['id'] + '" width="100px" class="shiny_inner" ' + marginLeft + ' onload="checkAllLoaded(' + i + ', ' + j + ');" onerror="reloadPoke(' + i + ', ' + j + ');"/>';

                        if(row['marked'] == 1) {
                            let rand = Math.floor((Math.random() * 4) + 1);
                            html += '<img src="/pogo/resources/images/svg/circle' + rand + '.svg" width="100px" style="margin-left: -100px;" onerror="this.src=\'/pogo/resources/images/pokemon/shiny/missing.png\';"/>';
                        }

                        j++;
                    }
                    html += '</div>';
                    i++;
                }

                $('#shiny_list').append(html);
                loadNextPokes();
            }
            else {
                toastr['error']('Ocorreu um erro: ' + data['message'] + ' (' + data['status'] + ')');
            }
        })
        .fail(function() {
            alert( "error" );
        });
    });

    $('.print').on('click', function() {
        html2canvas(document.querySelector("#shiny_list")).then(canvas => {
            link = document.getElementById('link');

            canvas.toBlob(function(blob) {
                link.href = window.URL.createObjectURL(blob);
                link.download = "shiny_list.png";
                var linkText = document.createTextNode(canvas.width + "px");
                link.appendChild(linkText);

                link.click();
            }, "image/png", 0.7);
        });
    });

    //In this case, the imagesToLoadIndex should count which family is current being loaded
    function loadNextPokes() {
        if(imagesToLoadIndex >= imagesToLoadList.length) {
            $('#download_button').prop('disabled', false);
            return;
        }

        for(let i = imagesToLoadIndex; i < imagesToLoadIndex + amountOfFamiliesToLoadAtOnce && i < imagesToLoadList.length; i++) {
            for (let j = 0; j < imagesToLoadList[i].length; j++) {
                let pokeNumber = imagesToLoadList[i][j]['id'];

                let src = '/pogo/resources/images/pokemon/shiny/' + pokeNumber + '.png';

                let element = document.getElementById('poke' + pokeNumber);
                element.src = src;
            }
        }
    }

    function checkAllLoaded(familyIndex, newLoadedIndex) {
        imagesToLoadList[familyIndex][newLoadedIndex]['loaded'] = true;

        for(let i = imagesToLoadIndex; i < imagesToLoadIndex + amountOfFamiliesToLoadAtOnce && i < imagesToLoadList.length; i++) {
            for (let j = 0; j < imagesToLoadList[i].length; j++) {
                if (!imagesToLoadList[i][j]['loaded']) {
                    return false;
                }
            }
        }

        imagesToLoadIndex += amountOfFamiliesToLoadAtOnce;
        loadNextPokes();
        return true;
    }

    function reloadPoke(familyIndex, index) {
        let src;
        let indexedElement = imagesToLoadList[familyIndex][index];
        let pokeNumber = indexedElement['id'];
        if(typeof indexedElement['repeat'] === 'undefined') {
            imagesToLoadList[familyIndex][index]['repeat'] = 0;
        }
        if(indexedElement['repeat'] == maxRepeatReload) {
            src = '/pogo/resources/images/pokemon/shiny/missing.png';
        }
        else {
            (imagesToLoadList[familyIndex][index]['repeat'])++;

            src = '/pogo/resources/images/pokemon/shiny/' + pokeNumber + '.png';
        }

        let element = document.getElementById('poke' + pokeNumber);
        element.src = src;
    }
</script>
</html>
