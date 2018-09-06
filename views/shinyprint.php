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
            operation: 'get_shinies_by_dex_evo',
            get_user_list: 'true'
        })
        .done(function(data) {
            console.log(data);
            if(data['status'] == 1) {
                var html = '';
                for(let row of data['shinies']) {
                    if(row['shinyimageurl'] == '') {
                        row['shinyimageurl'] = 'https://cdn3.iconfinder.com/data/icons/modifiers-add-on-1/48/v-17-512.png';
                    }
                    let marked = '';
                    if(row['marked'] === '1') {
                        marked = ' marked';
                    }
                    html += '<div class="w3-col s1 w3-center shiny-pokemon'+marked+'" data-id="'+row['id']+'">' +
                        '       <img src="/pogo/resources/images/pokemon/shiny/' + row['id'] + '.png" width="100%" onerror="this.src=\'/pogo/resources/images/pokemon/shiny/missing.png\';"/>' +
                        '</div>';
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

        // html2canvas(document.querySelector("#shiny_list")).then(canvas => {
        //     document.body.appendChild(canvas);
        //     //
        //     // $('canvas').prop('id', 'mycanvas');
        //     //
        //     // var canvas = document.getElementById("mycanvas");
        //
        //     // var link = document.getElementById('link');
        //     // link.setAttribute('download', 'MintyPaper.png');
        //     // link.setAttribute('href', canvas.toDataURL("image/png").replace("image/png", "image/octet-stream"));
        //     // link.click();
        // });

        html2canvas([ document.getElementById('shiny_list') ], {
            // "logging": true, //Enable log (use Web Console for get Errors and Warnings)
            "onrendered": function (canvas) {
                // var img = new Image();
                // img.onload = function() {
                //     img.onload = null;
                //     document.body.appendChild(img);
                // };
                // img.onerror = function() {
                //     img.onerror = null;
                //     if(window.console.log) {
                //         window.console.log("Not loaded image from canvas.toDataURL");
                //     } else {
                //         alert("Not loaded image from canvas.toDataURL");
                //     }
                // };
                // img.src = canvas.toDataURL("image/png");

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
