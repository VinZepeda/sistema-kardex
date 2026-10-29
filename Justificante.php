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

<main class="main-content">

    <div class="page-header">

        <div>

            <h2>
                Presentar justificante
            </h2>

            <p>
                Registra una incapacidad médica
                y adjunta el documento correspondiente.
            </p>

        </div>

    </div>

    <section class="form-card">

        

            <div class="form-group">

                <label>
                    Tipo de justificante
                </label>

                <select
                    name="tipo_justificante"
                    required
                >

                    <option value="">
                        Selecciona una opción
                    </option>

                    <option value="Incapacidad">
                        Incapacidad
                    </option>

                </select>

            </div>

            <div class="form-group">

                <label>
                    Tipo de incapacidad
                </label>

                <select
                    name="tipo_incapacidad"
                    required
                >

                    <option value="">
                        Selecciona una opción
                    </option>

                    <option value="Temporal">
                        Temporal
                    </option>

                    <option value="Permanente parcial">
                        Permanente parcial
                    </option>

                    <option value="Permanente total">
                        Permanente total
                    </option>

                </select>

            </div>

            <div class="form-group">

                <label>
                    Origen
                </label>

                <select
                    name="origen"
                    required
                >

                    <option value="">
                        Selecciona una opción
                    </option>

                    <option value="Riesgo de trabajo">
                        Riesgo de trabajo
                    </option>

                    <option value="Enfermedad general">
                        Enfermedad general
                    </option>

                    <option value="Maternidad">
                        Maternidad
                    </option>

                </select>

            </div>

            <div class="form-group">

                <label>
                    Fecha de inicio
                </label>

                <input
                    type="date"
                    name="fecha_inicio"
                    required
                >

            </div>

            <div class="form-group">

                <label>
                    Fecha de fin
                </label>

                <input
                    type="date"
                    name="fecha_fin"
                    required
                >

            </div>

            <div class="form-group">

                <label>
                    Archivo justificante
                </label>

                <input
                    type="file"
                    name="archivo"
                    accept=".pdf,.jpg,.jpeg,.png"
                    required
                >

            </div>

            <button
                type="submit"
                class="btn-primary"
            >

                📄 Presentar justificante

            </button>

        </form>

    </section>

</main>

<?php

include "includes/footer.php";

?>