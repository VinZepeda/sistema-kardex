<?php
$paginaActual = basename($_SERVER["PHP_SELF"]);
?>

<div class="contenedor-ver-perfil">

    <nav class="desplegable-menu">

        <a href="Perfil_personal.php"
            class="<?= $paginaActual == 'Perfil_personal.php' ? 'active' : '' ?>">
            Perfil
        </a>

        <a href="Ajustes.php"
            class="<?= $paginaActual == 'Ajustes.php' ? 'active' : '' ?>">
            Ajustes
        </a>

        <a href="logout.php" class="logout-button">
            Cerrar sesión
        </a>

    </nav>

</div>