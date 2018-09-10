<?php

if(!isset($server_var)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/server_var.php");
    $server_var = true;
}

if(!isset($check_login)) {
    include($pogo_path . "/php_posts/check_login.php");
    $check_login = true;
}

if (isset($_SESSION['start_path'])) {
    $location = "Location:" . $_SESSION['start_path'];
    unset($_SESSION['start_path']);
    header($location);
    exit();
}

$cache_sufix = '?'.time();

$admin = ($_SESSION['priority'] === 999);

?>


<!DOCTYPE HTML>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/w3.css">
    <link rel="stylesheet" type="text/css" href="/pogo/resources/css/theme.css<?php echo $cache_sufix ?>"><!-- ?random=@Environment.TickCount -->
    <script src="/pogo/resources/js/jquery-3.3.1.min.js"></script>
    <title>Nats Invents - Início</title>
</head>
<body>
    <?php include($pogo_path."/resources/php_components/main_top_header.php"); ?>

    <div class="w3-container w3-padding-16">
        <div>
            <button class="button-all button-tertiary"><a href="/pogo/views/shinylist.php">Lista de Shinies</a></button>
        </div>

    <?php if($admin === true) { ?>
        <div>
            <button class="button-all button-tertiary"><a href="/pogo/views_admin/shinylistadmin.php">Administrar Shinies Disponíveis</a></button>
        </div>
    <?php } ?>
    </div>

    <?php include($pogo_path."/resources/php_components/main_bottom_footer.php"); ?>
</body>

<script>

    $(document).ready(function() {

    });
</script>
</html>
