<?php

$paginaActual = basename($_SERVER["PHP_SELF"]);

?>

<div class="contenedor-ver-perfil">

    <aside class="desplegable" id="desplegable">

    <div class="desplagable-header">


    <nav class="delplegable-menu">

        <a
            href="Perfil_personal.php"
            class="<?= $paginaActual === 'Perfil_personal.php' ? 'active' : '' ?>"
        >
            <span>Perfil</span>
        </a>

        <a
            href="Ajustes.php"
            class="<?= $paginaActual === 'Ajustes.php' ? 'active' : '' ?>"
        >
            <span>Ajustes</span>
        </a>

        <a href="logout.php" class="logout-button">

            <span>
                Cerrar sesión
            </span>

        </a>

         </div>


    </nav>

</div>