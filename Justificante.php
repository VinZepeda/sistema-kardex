<?php

session_start();

require_once "conexion.php";

if (!isset($_SESSION["usuario"])) {

    header("Location: index.php");
    exit();
}

$titulo = "Justificantes";

require_once "includes/header.php";
include "includes/menu.php";

?>

<main class="main-content-justificante">

    <div class="page-header-justificante">
        <div>
            <h2>Presentar justificante</h2>
            <p>Registra una incapacidad médica y adjunta el documento correspondiente.</p>
        </div>
    </div>

    <section class="form-card-justificante">
        <form method="POST" enctype="multipart/form-data" class="form-justificante">

            <div class="form-grid-justificante">

                <div class="form-group-justificante">
                    <label for="tipo_justificante">Tipo de justificante</label>
                    <select id="tipo_justificante" name="tipo_justificante" required>
                        <option value="">Selecciona una opción</option>
                        <option value="Incapacidad">Incapacidad</option>
                    </select>
                </div>

                <div class="form-group-justificante">
                    <label for="tipo_incapacidad">Tipo de incapacidad</label>
                    <select id="tipo_incapacidad" name="tipo_incapacidad" required>
                        <option value="">Selecciona una opción</option>
                        <option value="Temporal">Temporal</option>
                        <option value="Permanente parcial">Permanente parcial</option>
                        <option value="Permanente total">Permanente total</option>
                    </select>
                </div>

                <div class="form-group-justificante">
                    <label for="origen">Origen</label>
                    <select id="origen" name="origen" required>
                        <option value="">Selecciona una opción</option>
                        <option value="Riesgo de trabajo">Riesgo de trabajo</option>
                        <option value="Enfermedad general">Enfermedad general</option>
                        <option value="Maternidad">Maternidad</option>
                    </select>
                </div>

                <div class="form-group-justificante">
                    <label for="fecha_inicio">Fecha de inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                </div>

                <div class="form-group-justificante">
                    <label for="fecha_fin">Fecha de fin</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" required>
                </div>

                <div class="form-group-justificante form-group-full">
                    <label for="archivo">Archivo justificante (PDF, JPG, PNG)</label>
                    <input type="file" id="archivo" name="archivo" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>

            </div>

            <div class="form-actions-justificante">
                <button type="submit" class="btn-primary-justificante">
                    📄 Presentar justificante
                </button>
            </div>

        </form>
    </section>

</main>

<?php

include "includes/footer.php";

?>