<?php
    $email = "";
    $errormessage = "";
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
    }
    if (isset($_GET['error'])) {
        $error = $_GET['error'];
        switch ($error) {
            case 1:
                $errormessage = "Email já cadastrado. Tente outro";
                break;
            case 2:
                $errormessage = "Não tente burlar o sistema";
                break;
            default:
                $errormessage = "Erro desconhecido, por favor tente novamente (".$error.")";
                break;
        }
    }

    $cache_sufix = '?'.time();
?>

<!DOCTYPE HTML>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="/resources/css/w3.css">
        <link rel="stylesheet" type="text/css" href="/resources/css/theme.css<?php echo $cache_sufix; ?>"><!-- ?random=@Environment.TickCount -->
        <link rel="stylesheet" type="text/css" href="/resources/css/iconselect.css" >
        <script src="/resources/js/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="/resources/js/iconselect.js"></script>
        <script type="text/javascript" src="/resources/js/iscroll.js"></script>

        <title>Nats Invents - Registrar</title>
    </head>
    <body>
        <?php include("../resources/php_components/error_top_container.php"); ?>
        <div class="w3-display-container w3-col w3-half" style="padding: 0; height: 100vh;">
            <div class="w3-display-middle w3-mobile">
                <form id="form" action="/php_posts/post_register.php" method="post">
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
                        <input type="hidden" name="team"/>
                        <div id="team_select"></div>
                    </div>
                    <div class="w3-container w3-padding">
                        <label for="level">Nível</label>
                        <input type="number" id="level" name="level" value="1" class="w3-input input-center" min="1" max="40" required/>
                    </div>
                    <div class="w3-container w3-padding">
                        <input type="submit" class="w3-button theme-bg button-main" style="width: 100%" value="CONFIRMAR"/>
                    </div>
                </form>
            </div>
        </div>
        <div class="w3-display-container w3-col w3-half w3-hide-small theme-bg" style="height: 100vh;">
            <div class="w3-display-middle" style="width: 30vw; height: 30vw;">
                <img src="/resources/images/Logo.png" style="width: 100%; height: 100%;"/>
            </div>
        </div>

    </body>

    <script>
        var iconSelect;
        IconSelect.COMPONENT_ICON_FILE_PATH = "/resources/images/icon-select/arrow.png";

        $('#form').submit(function() {

            var re = new RegExp("^[a-zA-Z0-9_]+$");
            if(!re.test($('input[name=username').val())) {
                alert("Nome de usuário somente pode conter caracteres alfanuméricos ou underscore ( _ )");
                return false;
            }
            if($('input[name=password').val() == "") {
                alert("Por favor informe uma senha");
                return false;
            }
            $('input[name=team').val(iconSelect.getSelectedValue());
            $('input#level').val(clamp($('input#level').val(), 1, 40));
            return true; // return false to cancel form action
        });

        $(document).ready(function() {
            var errorDivSelector = $('#errorDiv');
            if(errorDivSelector.length > 0) {
                setTimeout(function() {
                    errorDivSelector.remove();
                }, 5000);
            }

            //selectedText.value = iconSelect.getSelectedValue();

            iconSelect = new IconSelect("team_select");

            var icons = [];
            icons.push({'iconFilePath':'/resources/images/Mystic.png', 'iconValue':'mystic'});
            icons.push({'iconFilePath':'/resources/images/Instinct.png', 'iconValue':'instinct'});
            icons.push({'iconFilePath':'/resources/images/Valor.png', 'iconValue':'valor'});

            iconSelect.refresh(icons);
        });

        function clamp(num, min, max) {
            return num <= min ? min : num >= max ? max : num;
        }
    </script>
</html>