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
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    switch ($error) {
        case 1:
            $errormessage = "E-mail ou senha incorretos";
            break;
        case 2:
            $errormessage = "Usuário ainda não foi ativado";
            break;
        default:
            $errormessage = "Erro desconhecido, por favor tente novamente (".$error.")";
            break;
    }
}
if (isset($_GET['warning'])) {
    $warning = $_GET['warning'];
    switch ($warning) {
        case 1:
            $warningmessage = "Conta criada. Por favor espere um administrador ativá-la";
            break;
        default:
            $warningmessage = "Aviso desconhecido, por favor tente novamente (".$warning.")";
            break;
    }
}

$cache_sufix = '?'.time();

?>

<!DOCTYPE HTML>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="/pogo/resources/css/w3.css">
        <link rel="stylesheet" type="text/css" href="/pogo/resources/css/theme.css<?php echo $cache_sufix; ?>"><!-- ?random=@Environment.TickCount -->
        <script src="/pogo/resources/js/jquery-3.3.1.min.js"></script>
        <title>Nats Invents - Login</title>
    </head>
    <body>
        <?php include($pogo_path."/resources/php_components/error_top_container.php"); ?>
        <?php include($pogo_path."/resources/php_components/warning_top_container.php"); ?>
        <div class="w3-display-container w3-col w3-half w3-hide-small theme-bg" style="height: 100vh;">
            <div class="w3-display-middle" style="width: 30vw; height: 30vw;">
                <img src="/pogo/resources/images/Logo.png" style="width: 100%; height: 100%;"/>
            </div>
        </div>
        <div class="w3-display-container w3-col w3-half" style="padding: 0; height: 100vh;">
            <div class="w3-display-middle w3-mobile">
                <form action="/pogo/php_posts/post_login.php" method="post">
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
                        <div class="w3-col w3-half" style="padding-right: 5px;">
                            <input type="submit" class="w3-button button-all button-main" value="ENTRAR" style="width: 100%"/>
                        </div>
                        <div class="w3-col w3-half" style="padding-left: 5px;">
                            <input type="submit" class="w3-button button-all button-secondary" value="REGISTRAR-SE" formaction="./register.php" style="width: 100%"/>
                        </div>
                    </div>
                </form>
            </div>

<!--            <div class="theme-bg-light" style="height: 30px; width: 100%; position: fixed; bottom: 0;">-->
<!--                <p></p>-->
<!--            </div>-->
        </div>
    </body>

    <script>

        $(document).ready(function() {
            <?php include($pogo_path."/resources/php_components/on_doc_ready_vanish.php"); ?>
        });
    </script>
</html>