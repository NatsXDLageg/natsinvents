<?php
    include($_SERVER['DOCUMENT_ROOT']."/php_posts/check_login.php");
    include($_SERVER['DOCUMENT_ROOT']."/../common_files/php_connection.php");

    $cache_sufix = '?'.time();

    $current_time = date('Y-m-d');

    $query="Select DATE_FORMAT(criado_em, '%Y-%m-%d %H:%i:%s') AS 'data', DATEDIFF(NOW(), criado_em) AS 'diff' from mensagem_feed";
    //Pensar em um jeito de calcular uma pontuação para ver se a mensagem aparece no topo do feed (Talvez considerar só se foi colocada no dia e depois desempatar pela prioridade do usuário?)

    $result = $mysqli->query($query) or die("couldn't execute the query");
?>


<!DOCTYPE HTML>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="/resources/css/w3.css">
    <link rel="stylesheet" type="text/css" href="/resources/css/theme.css<?php echo $cache_sufix; ?>"><!-- ?random=@Environment.TickCount -->
    <script src="/resources/js/jquery-3.3.1.min.js"></script>
    <title>Nats Invents - Início</title>
</head>
<body>
    <?php include($_SERVER['DOCUMENT_ROOT']."/resources/php_components/error_top_container.php"); ?>
    <?php include($_SERVER['DOCUMENT_ROOT']."/resources/php_components/main_top_header.php");

    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
        echo var_dump($row);
    }

    include($_SERVER['DOCUMENT_ROOT']."/resources/php_components/main_bottom_footer.php"); ?>
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