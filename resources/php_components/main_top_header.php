<?php
    if(!isset($user)) {
        $user = isset($_SESSION['email']);
    }
    if(!isset($admin)) {
        $admin = isset($_SESSION['priority']) && ($_SESSION['priority'] === 999);
    }

?>

<div id="sidebar" class="w3-sidebar w3-bar-block w3-border-right theme-text" style="display:none;">
    <button onclick="document.getElementById('sidebar').style.display='none'" class="w3-bar-item button-all button-main w3-xlarge w3-center" style="height: 78px; cursor: pointer; margin-bottom: 8px;">Fechar &times;</button>
    <?php if($user) { ?>
        <a href="/pogo/views/shinylist.php" class="w3-bar-item w3-button button-all button-tertiary">
            <i class="fas fa-th"></i> Lista de Shinies
        </a>
<!--        <a href="#" class="w3-bar-item w3-button button-all button-tertiary">Link 3</a>-->
        <?php if($admin) { ?>
            <a href="/pogo/views_admin/shinylistadmin.php" class="w3-bar-item w3-button button-all button-tertiary">
                <i class="fas fa-user-edit"></i> Administrar Shinies
            </a>
        <?php } ?>
    <?php } ?>
</div>

<div class="w3-container theme-bg top-container">
    <button class="w3-button button-all button-tertiary text-white-force w3-xlarge" style="padding: 8px;" onclick="document.getElementById('sidebar').style.display='block'">☰</button>
    <a href="/pogo/index.php"><img src="/pogo/resources/images/Logo.png" style="width: 70px; height: 70px;"/></a>
    <?php if (isset($_SESSION['email'])) { ?>

        <a href="/pogo/views/logout.php"><span style="float: right; font-size: 18px; margin-top: 22px; margin-right: 16px;"><i class="fas fa-sign-out-alt"></i> SAIR</span></a>
    <?php } else { ?>
        <a href="/pogo/views/login.php"><span style="float: right; font-size: 18px; margin-top: 22px; margin-right: 16px;"><i class="fas fa-sign-in-alt"></i> FAZER LOGIN</span></a>
    <?php } ?>
</div>
