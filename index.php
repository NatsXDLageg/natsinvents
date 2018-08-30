<?php

if(!isset($server_var)) {
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/server_var.php");
    $server_var = true;
}

if(!isset($check_login)) {
    include($_SERVER['POGO_ROOT'] . "/php_posts/check_login.php");
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
    <script src="/pogo/resources/js/jquery-3.3.1.min.js"></script>
    <title>Nats Invents - Início</title>
</head>
<body>
    <?php include($_SERVER['POGO_ROOT']."/resources/php_components/error_top_container.php"); ?>
    <?php include($_SERVER['POGO_ROOT']."/resources/php_components/main_top_header.php"); ?>

    <div class="w3-container">
        <a href="/pogo"><h1>PoGo</h1></a>
    </div>

    <?php include($_SERVER['POGO_ROOT']."/resources/php_components/main_bottom_footer.php"); ?>
</body>

<script>

    $(document).ready(function() {
        var errorDivSelector = $('#errorDiv');
        if(errorDivSelector.length > 0) {
            setTimeout(function() {
                errorDivSelector.remove();
            }, 5000);
        }
    });
</script>
</html>
