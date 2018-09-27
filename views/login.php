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
        <title>Nats Invents - Login</title>
    </head>
    <body>
        <div class="w3-display-container w3-col w3-half w3-hide-small theme-bg" style="height: 100vh;">
            <div class="w3-display-middle" style="width: 30vw; height: 30vw;">
                <a href="/pogo/index.php"><img src="/pogo/resources/images/Logo.png" style="width: 100%; height: 100%;"/></a>
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
                    <div class="w3-container w3-padding">
                        <label for="remember">Mantenha-me conectado: </label>
                        <input type="checkbox" id="remember" name="remember" class="w3-check"/>
                    </div>
                    <br/>
                    <div class="w3-container">
                        <input type="button" id="submit" class="w3-button button-all button-main full-width" value="ENTRAR"/>
                        <input type="button" id="register" class="w3-button button-all button-tertiary" value="REGISTRAR-SE" style="margin-top: 16px;"/>
                    </div>
                </form>
            </div>
        </div>
    </body>

    <script>

        $('#submit').on('click', function() {

            if($('input#email').val() == "") {
                toastr['warning']("Por favor informe o email");
                return;
            }
            if($('input#password').val() == "") {
                toastr['warning']("Por favor informe uma senha");
                return;
            }
            $.post("/pogo/php_posts/post_login.php", {
                password: $('input#password').val().trim(),
                email: $('input#email').val().trim(),
                rememberme: $('input#remember').prop('checked')
            })
            .done(function (data) {
                if (data['status'] == 1) {
                    window.location.href = '/pogo/';
                }
                else {
                    console.log(data);
                    toastr['error'](data['message']);
                }
            });
        });

        $('#register').on('click', function() {
            sessionStorage.email = encodeURI($('input[name=email').val().trim());
            window.location.href = '/pogo/views/register.php';
        });

        $(document).ready(function() {
            var input = document.getElementById("email");

            input.addEventListener("keyup", function(event) {
                if (event.keyCode === 13) {
                    $('#submit').trigger('click');
                }
            });

            input = document.getElementById("password");

            input.addEventListener("keyup", function(event) {
                if (event.keyCode === 13) {
                    $('#submit').trigger('click');
                }
            });
        });
    </script>
</html>