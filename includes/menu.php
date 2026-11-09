<?php

$paginaActual = basename($_SERVER["PHP_SELF"]);

?>


<aside class="sidebar" id="sidebar">


    <div class="sidebar-header">
        
        <div class="sidebar-logo"> 
            <h2>Kardex</h2>
            <p>Personal</p>
        </div>

    </div>

    <nav class="sidebar-menu">

        <a
            href="dashboard.php"
            class="<?= $paginaActual === 'dashboard.php' ? 'active' : '' ?>"
        >
            <span>Inicio</span>
        </a>

        <a
            href="kardex.php"
            class="<?= $paginaActual === 'kardex.php' ? 'active' : '' ?>"
        >
            <span>Mi Kardex</span>
        </a>

        <a
            href="Justificante.php"
            class="<?= $paginaActual === 'Justificante.php' ? 'active' : '' ?>"
        >
            <span>Justificante</span>
        </a>

        <a
            href="personal.php"
            class="<?= $paginaActual === 'personal.php' ? 'active' : '' ?>"
        >
            <span>Personal</span>
        </a>


    </nav>

    <div class="sidebar-footer">

    
        <div class="logo-perfil">
            
            <a href="perfil_lateral.php" class="ver-perfil-button">
                <img src="img/logo.png" alt="Abiri Perfil" style="width:50px; height:50px;" >
            
            </a>
           
        </div>

    </div>


</aside>