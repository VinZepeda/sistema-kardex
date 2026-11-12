
<?php

$paginaActual = basename($_SERVER["PHP_SELF"]);

?>

<header class="navbar">

    <!-- LOGOTIPO -->
    <div class="navbar-logo">
        <h2>Kardex</h2>
    </div>

    <!-- MENÚ HORIZONTAL -->
    <nav class="navbar-menu">

        <a
            href="dashboard.php"
            class="<?= $paginaActual === 'dashboard.php' ? 'active' : '' ?>"
        >
            Inicio
        </a>

        <a
            href="kardex.php"
            class="<?= $paginaActual === 'kardex.php' ? 'active' : '' ?>"
        >
            Mi Kardex
        </a>

        <a
            href="Justificante.php"
            class="<?= $paginaActual === 'Justificante.php' ? 'active' : '' ?>"
        >
            Justificantes
        </a>

        <a
            href="personal.php"
            class="<?= $paginaActual === 'personal.php' ? 'active' : '' ?>"
        >
            Personal
        </a>

    </nav>

    <!-- PERFIL -->
    <div class="navbar-profile">
<button
    type="button"
    class="profile-button"
    id="profileButton"
    aria-label="Abrir menú de perfil"
>
    <img
        src=""
        alt="Foto de perfil"
        class="profile-photo"
    >
</button>

        <div class="profile-dropdown" id="profileDropdown">

            <a href="perfil_lateral.php">
                Perfil
            </a>

            <a href="ajustes.php">
                Ajustes
            </a>

            <a href="logout.php" class="logout-option">
                Cerrar sesión
            </a>

        </div>

    </div>

</header>
