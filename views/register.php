<?php

if(!isset($server_var)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/server_var.php");
    $server_var = true;
}

$email = "";
if (isset($_POST['email'])) {
    $email = $_POST['email'];
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
        <link rel="stylesheet" type="text/css" href="/pogo/resources/css/iconselect.css" >
        <link rel="stylesheet" type="text/css" href="/pogo/resources/css/toastr.min.css">
        <script type="text/javascript" src="/pogo/resources/js/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="/pogo/resources/js/iconselect.js"></script>
        <script type="text/javascript" src="/pogo/resources/js/iscroll.js"></script>
        <script type="text/javascript" src="/pogo/resources/js/toastr.min.js"></script>

        <title>Nats Invents - Registrar</title>
    </head>
    <body>
        <div class="w3-display-container w3-col w3-half" style="padding: 0; height: 100vh;">
            <div class="w3-display-middle w3-mobile">
                <div class="w3-container w3-padding">
                    <h2 class="theme-text">Registre-se</h2>
                </div>
                <div class="w3-container w3-padding">
                    <label for="name">Nome</label>
                    <input type="text" id="name" name="name" placeholder="Nome" class="w3-input input-center" maxlength="50" required/>
                </div>
                <div class="w3-container w3-padding">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="ex@ex.com" value="<?php echo $email ?>" class="w3-input input-center" maxlength="40" required/>
                </div>
                <div class="w3-container w3-padding">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" class="w3-input input-center" maxlength="50" required/>
                </div>
                <div class="w3-container w3-padding">
                    <label for="team">Time</label>
                    <input type="hidden" id="team" name="team"/>
                    <div id="team_select"></div>
                </div>
                <div class="w3-container w3-padding">
                    <label for="level">Nível</label>
                    <input type="number" id="level" name="level" value="1" class="w3-input input-center" min="1" max="40" required/>
                </div>
                <div class="w3-container w3-padding">
                    <input type="button" id="submit" class="w3-button theme-bg button-main" style="width: 100%" value="CONFIRMAR"/>
                </div>
            </div>
        </div>
        <div class="w3-display-container w3-col w3-half w3-hide-small theme-bg" style="height: 100vh;">
            <div class="w3-display-middle" style="width: 30vw; height: 30vw;">
                <img src="/pogo/resources/images/Logo.png" style="width: 100%; height: 100%;"/>
            </div>
        </div>

    </body>

    <script>
        var iconSelect;
        IconSelect.COMPONENT_ICON_FILE_PATH = "/pogo/resources/images/icon-select/arrow.png";

        $('#submit').on('click', function() {

            if($('#name').val() == "") {
                toastr['warning']("Por favor informe o nome");
                return;
            }
            if($('#email').val() == "") {
                toastr['warning']("Por favor informe o email");
                return;
            }
            if($('#password').val() == "") {
                toastr['warning']("Por favor informe uma senha");
                return;
            }
            $(this).prop('disabled', true);
            $('#team').val(iconSelect.getSelectedValue());
            $('#level').val(clamp($('#level').val(), 1, 40));

            $.post( "/pogo/php_posts/post_register.php", {
                username: $('#name').val().trim(),
                password: $('#password').val().trim(),
                email: $('#email').val().trim(),
                team: $('#team').val(),
                level: $('#level').val()
            })
            .done(function(data) {
                if (data['status'] == 1) {
                    toastr['warning'](data['message']);

                    setTimeout(function() {
                        window.location.replace('login.php');
                    }, 5000);
                }
                else {
                    console.log(data);
                    toastr['error'](data['message']);
                }
            })
            .fail(function() {
                toastr['error']('Erro desconhecido');
                $('#submit').prop('disabled', false);
            });
        });

        $(document).ready(function() {

            let sessionEmail = sessionStorage.getItem("email");
            if(sessionEmail !== null && sessionEmail !== undefined) {
                $('#email').val(sessionEmail);
                sessionStorage.removeItem('email');
            }

            iconSelect = new IconSelect("team_select");

            var icons = [];
            icons.push({'iconFilePath':'/pogo/resources/images/Mystic.png', 'iconValue':'mystic'});
            icons.push({'iconFilePath':'/pogo/resources/images/Instinct.png', 'iconValue':'instinct'});
            icons.push({'iconFilePath':'/pogo/resources/images/Valor.png', 'iconValue':'valor'});

            iconSelect.refresh(icons);
        });

        function clamp(num, min, max) {
            return num <= min ? min : num >= max ? max : num;
        }
    </script>
</html>