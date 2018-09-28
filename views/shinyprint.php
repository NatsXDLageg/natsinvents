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
        <button class="w3-button button-all button-main print"><i class="fas fa-file-download" style="margin-right: 3vh;"></i>FAZER DOWNLOAD</button>
        <a id="link" class="w3-hide"></a>
    </div>

    <div id="shiny_list" class="w3-container w3-center">
        <img src="/pogo/resources/images/PrintListHeader.png" width="100%"/>
    </div>
</body>

<script>

    $(document).ready(function() {

        $.post( "/pogo/php_posts/post_pokemons.php", {
            operation: 'get_shinies_by_dex_evo_group_families'
        })
        .done(function(data) {
            if(data['status'] == 1) {
                var html = '';
                for(let family of data['data']) {

                    html += '<div class="w3-center shiny_div">';

                    let index = 0;
                    for(let row of family) {
                        let marginLeft = '';
                        if(index > 0) {
                            marginLeft = 'margin-left: -40px;';
                        }
                        html += '<img src="/pogo/resources/images/pokemon/shiny/' + row['id'] + '.png" width="100px" class="shiny_inner" style="' + marginLeft + '" onerror="this.src=\'/pogo/resources/images/pokemon/shiny/missing.png\';"/>';

                        if(row['marked'] == 1) {
                            let rand = Math.floor((Math.random() * 4) + 1);
                            html += '<img src="/pogo/resources/images/svg/circle' + rand + '.svg" width="100px" style="margin-left: -100px;" onerror="this.src=\'/pogo/resources/images/pokemon/shiny/missing.png\';"/>';
                        }

                        index++;
                    }
                    html += '</div>';
                }

                $('#shiny_list').append(html);
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

        html2canvas([ document.getElementById('shiny_list') ], {
            "onrendered": function (canvas) {
                console.log(canvas);
                var link = document.getElementById('link');
                link.setAttribute('download', 'shiny_list.png');
                link.setAttribute('href', canvas.toDataURL("image/png").replace("image/png", "image/octet-stream"));
                link.click();
            }
        });
    });
</script>
</html>
