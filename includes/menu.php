<?php

$paginaActual = basename($_SERVER["PHP_SELF"]);

?>

<aside class="sidebar" id="sidebar">

    <div class="sidebar-header">

        <div class="sidebar-logo">
            📋
        </div>

        <div>
            <h2>Kardex</h2>
            <p>Personal</p>
        </div>

    </div>

    <nav class="sidebar-menu">

        <a
            href="dashboard.php"
            class="<?= $paginaActual === 'dashboard.php' ? 'active' : '' ?>"
        >
            <span>🏠</span>
            <span>Inicio</span>
        </a>

        <a
            href="kardex.php"
            class="<?= $paginaActual === 'kardex.php' ? 'active' : '' ?>"
        >
            <span>👤</span>
            <span>Mi Kardex</span>
        </a>

        <a
            href="calendario.php"
            class="<?= $paginaActual === 'calendario.php' ? 'active' : '' ?>"
        >
            <span>📅</span>
            <span>Calendario</span>
        </a>

        <a
            href="personal.php"
            class="<?= $paginaActual === 'personal.php' ? 'active' : '' ?>"
        >
            <span>👥</span>
            <span>Personal</span>
        </a>

    </nav>

    <div class="sidebar-footer">

        <a href="logout.php" class="logout-button">

            <span>🚪</span>

            <span>
                Cerrar sesión
            </span>

        </a>

    </div>

</aside>