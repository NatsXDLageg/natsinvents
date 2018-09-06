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
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/w3.css">
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/theme.css<?php echo $cache_sufix ?>"><!-- ?random=@Environment.TickCount -->
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/toastr.min.css">
    <script src="/pogo/resources/js/jquery-3.3.1.min.js"></script>
    <script src="/pogo/resources/js/toastr.min.js"></script>
    <script src="/pogo/resources/js/html2canvas.js"></script>
    <title>Shiny Print</title>
    <style>
        body {
            width: 1280px !important;
            height: 1080px !important;
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
            background-color: white;
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
            position: relative;
            left: 0;
            top: 0;
            width: 207px;
        }
        .shiny_inner {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="w3-container w3-padding-16">
        <button class="w3-button button-all button-main print">Imprimir</button>
        <a id="link" class="w3-hide"></a>
    </div>

    <div id="shiny_list" class="w3-container"></div>
</body>

<script>

    $(document).ready(function() {

        $.post( "/pogo/php_posts/post_pokemons.php", {
            operation: 'get_shinies_by_dex_evo_group_families',
            get_user_list: 'true'
        })
        .done(function(data) {
            console.log(data);
            if(data['status'] == 1) {
                var html = '';
                for(let family of data['data']) {
                    let flength = family.length;
                    let outerWidth = (flength + 40) + 'px';
                    let innerWidth = '60px';
                    // if(flength == 1) {
                    //     colClass = 's1';
                    //     width_float = 100.0;
                    // }
                    // else if(flength > 1 && flength < 4) {
                    //     colClass = 's2';
                    //     width_float = 100.0 / 3;
                    // }
                    // else if(flength >= 4) {
                    //     colClass = 's4';
                    //     width_float = 100.0 / 4;
                    // }
                    // else {
                    //     continue;
                    // }

                    // let width = width_float + '%';

                    let marked = '';
                    // if(row['marked'] === '1') {
                    //     marked = ' marked';
                    // }
                    html += '<div class="w3-center'+marked+' shiny_div">';

                    let index = 0;
                    for(let row of family) {
                        let piece = 12.0;
                        let left = (- index) * piece;
                        left = left + '%';
                        html += '<img src="/pogo/resources/images/pokemon/shiny/' + row['id'] + '.png" class="shiny_inner" style="left: ' + left + ';" width="80px" onerror="this.src=\'/pogo/resources/images/pokemon/shiny/missing.png\';"/>';
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
