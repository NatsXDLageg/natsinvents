<?php
    if(!isset($user)) {
        $user = isset($_SESSION['email']);
    }
    if(!isset($admin)) {
        $admin = isset($_SESSION['priority']) && ($_SESSION['priority'] === 999);
    }

?>

<div id="sidebar" class="w3-sidebar w3-bar-block w3-border-right theme-text" style="display: none; width: 240px;">
    <button onclick="document.getElementById('sidebar').style.display='none'" class="w3-bar-item button-all button-main w3-xlarge w3-center" style="height: 48px; cursor: pointer; margin-bottom: 8px; font-size: 18px;"><i class="fas fa-times"></i>FECHAR</button>
    <a id="link_index" href="/pogo/index.php" class="w3-bar-item w3-button button-all button-tertiary">
        <div class="w3-col w3-center icon-fix-width"><i class="fas fa-tasks"></i></div>
        <div class="w3-rest">Missões</div>
    </a>
    <a id="link_pokestoplist" href="/pogo/views/pokestoplist.php" class="w3-bar-item w3-button button-all button-tertiary">
        <div class="w3-col w3-center icon-fix-width"><i class="fas fa-map-pin"></i></div>
        <div class="w3-rest">Pokestops e Gyms</div>
    </a>
    <?php if($user) { ?>
        <a id="link_shinylist" href="/pogo/views/shinylist.php" class="w3-bar-item w3-button button-all button-tertiary">
            <div class="w3-col w3-center icon-fix-width"><i class="fas fa-th"></i></div>
            <div class="w3-rest">Lista de Shinies</div>
        </a>
        <?php if($admin) { ?>
            <a id="link_shinylistadmin" href="/pogo/views_admin/shinylistadmin.php" class="w3-bar-item w3-button button-all button-tertiary">
                <div class="w3-col w3-center icon-fix-width"><i class="fas fa-user-edit"></i></div>
                <div class="w3-rest">Administrar Shinies</div>
            </a>
        <?php } ?>
    <?php } ?>
</div>

<div id="top-container" class="w3-container theme-bg">
    <button class="w3-button button-all button-tertiary text-white-force" style="padding: 8px;" onclick="document.getElementById('sidebar').style.display='block'"><i class="fas fa-bars" style="margin-right: 0;"></i></button>
    <a href="/pogo/index.php"><img src="/pogo/resources/images/Logo.png" style="width: 40px; height: 40px;"/></a>
    <?php if (isset($_SESSION['email'])) { ?>
        <a href="/pogo/views/logout.php" class="button-all button-main">
            <span style="float: right; font-size: 18px; margin-top: 11px; margin-right: 8px;"><i class="fas fa-sign-out-alt"></i>SAIR</span>
        </a>
    <?php } else { ?>
        <a href="/pogo/views/login.php" class="button-all button-main">
            <span style="float: right; font-size: 18px; margin-top: 11px; margin-right: 8px;"><i class="fas fa-sign-in-alt"></i>FAZER LOGIN</span>
        </a>
    <?php } ?>
</div>
