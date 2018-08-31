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
    <script src="/pogo/resources/js/jquery-3.3.1.min.js"></script>
    <title>Shiny List</title>
</head>
<body>
    <?php include($pogo_path."/resources/php_components/error_top_container.php"); ?>
    <?php include($pogo_path."/resources/php_components/main_top_header.php"); ?>

    <div class="w3-container w3-padding-16">
<!--        <form action="/pogo/views/shinylist.php">-->
<!--            <input type="button" class="button-all button-tertiary" value="Shiny List"/>-->
<!--        </form>-->
    </div>

    <?php include($pogo_path."/resources/php_components/main_bottom_footer.php"); ?>
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
