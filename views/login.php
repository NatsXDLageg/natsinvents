<?php

if(!isset($server_var)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/server_var.php");
    $server_var = true;
}
session_start();

if(isset($_SESSION['email']))
{
    header("location:../index.php");
    exit();
}

$cache_sufix = '?'.time();

?>

<!DOCTYPE HTML>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, user-scalable=no">
        <link rel="stylesheet" type="text/css" href="/pogo/resources/css/w3.css">
        <link rel="stylesheet" type="text/css" href="/pogo/resources/css/theme.css<?php echo $cache_sufix; ?>"><!-- ?random=@Environment.TickCount -->
        <link rel="stylesheet" type="text/css" href="/pogo/resources/css/toastr.min.css">
        <script type="text/javascript" src="/pogo/resources/js/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="/pogo/resources/js/toastr.min.js"></script>
        <title>Nats Invents - Login</title>
        <style>

            @media only screen and (min-width:600px ) {
                #login_button_div {
                    padding-right: 5px;
                }
                #register_button_div {
                    padding-left: 5px;
                }
            }
            @media only screen and (max-width:599px ) {
                #login_button_div {
                    padding-bottom: 5px;
                }
                #register_button_div {
                    padding-top: 5px;
                }
            }
        </style>
    </head>
    <body>
        <div class="w3-display-container w3-col w3-half w3-hide-small theme-bg" style="height: 100vh;">
            <div class="w3-display-middle" style="width: 30vw; height: 30vw;">
                <img src="/pogo/resources/images/Logo.png" style="width: 100%; height: 100%;"/>
            </div>
        </div>
        <div class="w3-display-container w3-col w3-half" style="padding: 0; height: 100vh;">
            <div class="w3-display-middle w3-mobile">
                <form action="./register.php" method="post">
                    <div class="w3-container w3-padding">
                        <h2 class="theme-text">Login</h2>
                    </div>
                    <div class="w3-container w3-padding">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" placeholder="ex@ex.com" class="w3-input input-center" maxlength="40"/>
                    </div>
                    <div class="w3-container w3-padding">
                        <label for="password">Senha</label>
                        <input type="password" id="password" name="password" class="w3-input input-center" maxlength="50"/>
                    </div>
                    <div class="w3-row w3-padding">
                        <div id="login_button_div" class="w3-col w3-half">
                            <input type="button" id="submit" class="w3-button button-all button-main" value="ENTRAR" style="width: 100%"/>
                        </div>
                        <div id="register_button_div" class="w3-col w3-half">
                            <input type="submit" class="w3-button button-all button-secondary" value="REGISTRAR-SE" style="width: 100%"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </body>

    <script>

        $('#submit').on('click', function() {

            if($('input[name=email').val() == "") {
                toastr['warning']("Por favor informe o email");
                return;
            }
            if($('input[name=password').val() == "") {
                toastr['warning']("Por favor informe uma senha");
                return;
            }
            $.post("/pogo/php_posts/post_login.php", {
                password: $('input[name=password').val().trim(),
                email: $('input[name=email').val().trim()
            })
            .done(function (data) {
                if (data['status'] == 1) {
                    window.location.replace('/pogo/');
                }
                else {
                    console.log(data);
                    toastr['error'](data['message']);
                }
            });
        });

        $(document).ready(function() {

        });
    </script>
</html>