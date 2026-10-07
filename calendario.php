<?php

session_start();

if (!isset($_SESSION["usuario"])) {

    header("Location: index.php");
    exit();

}

$titulo = "Calendario | Sistema de Kardex";

include "includes/header.php";
include "includes/menu.php";

?>

<main class="main-content">

    <div class="page-header">

        <div>

            <h2>
                Calendario
            </h2>

            <p>
                Incapacidades y días económicos
            </p>

        </div>

    </div>


    <section class="calendar-container">

        <div class="calendar-header">

            <button
                type="button"
                class="calendar-button"
                onclick="mesAnterior()"
            >
                ←
            </button>

            <h3 id="mesActual">
                Septiembre 2026
            </h3>

            <button
                type="button"
                class="calendar-button"
                onclick="mesSiguiente()"
            >
                →
            </button>

        </div>


        <div class="calendar-week">

            <div>Lun</div>
            <div>Mar</div>
            <div>Mié</div>
            <div>Jue</div>
            <div>Vie</div>
            <div>Sáb</div>
            <div>Dom</div>

        </div>


        <div class="calendar-days" id="calendario">

            <!-- JavaScript generará los días -->

        </div>

    </section>


    <section class="info-section">

        <h3>
            Incidencias
        </h3>

        <div class="empty-state">

            <div>
                📅
            </div>

            <p>
                No hay incapacidades o días económicos registrados.
            </p>

        </div>

    </section>

</main>

<?php

include "includes/footer.php";

?>