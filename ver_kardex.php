
<?php

session_start();

require_once "conexion.php";

if (!isset($_SESSION["usuario"])) {

    header("Location: index.php");
    exit();

}

$idEmpleado = isset($_GET["id"])
    ? (int)$_GET["id"]
    : 0;

if ($idEmpleado <= 0) {

    die("Empleado no válido.");

}



$sql = "

SELECT

    e.*,

    p.nombre AS puesto,

    a.nombre AS area,

    es.nombre AS estatus,

    pl.nombre AS plaza,

    n.nombre AS nivel

FROM empleados e

LEFT JOIN puestos p
    ON e.id_puesto = p.id_puesto

LEFT JOIN areas a
    ON e.id_area = a.id_area

LEFT JOIN estatus es
    ON e.id_estatus = es.id_estatus

LEFT JOIN plazas pl
    ON e.id_plaza = pl.id_plaza

LEFT JOIN niveles n
    ON e.id_nivel = n.id_nivel

WHERE e.id_empleado = ?

LIMIT 1

";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "i",
    $idEmpleado
);

$stmt->execute();

$resultado =
    $stmt->get_result();

$empleado =
    $resultado->fetch_assoc();

$stmt->close();

if (!$empleado) {

    die("Empleado no encontrado.");

}



$sqlIncidencias = "

SELECT

    fecha,
    tipo_dia,
    descripcion

FROM calendario_empleados

WHERE id_empleado = ?

ORDER BY fecha DESC

";

$stmtInc =
    $conexion->prepare(
        $sqlIncidencias
    );

$stmtInc->bind_param(
    "i",
    $idEmpleado
);

$stmtInc->execute();

$incidencias =
    $stmtInc->get_result();


$sqlJustificantes = "

SELECT *

FROM justificantes

WHERE id_empleado = ?

ORDER BY fecha_registro DESC

";

$stmtJust =
    $conexion->prepare(
        $sqlJustificantes
    );

$stmtJust->bind_param(
    "i",
    $idEmpleado
);

$stmtJust->execute();

$justificantes =
    $stmtJust->get_result();

$titulo = "Kardex de empleado";

include "includes/header.php";
include "includes/menu.php";

?>

<main class="main-content">

    <div class="page-header">

        <div>

            <h2>
                Kardex de Empleado
            </h2>

            <p>
                Información completa del empleado
            </p>

        </div>

    </div>

    <section class="dashboard-grid">

        <div class="dashboard-card">

            <div>

                <span class="card-label">
                    Nombre
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $empleado["nombre"] . " " .
                        $empleado["apellido_paterno"] . " " .
                        $empleado["apellido_materno"]
                    ) ?>

                </strong>

            </div>

        </div>

        <div class="dashboard-card">

            <div>

                <span class="card-label">
                    CURP
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $empleado["curp"]
                    ) ?>

                </strong>

            </div>

        </div>

        <div class="dashboard-card">

            <div>

                <span class="card-label">
                    RFC
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $empleado["rfc"]
                    ) ?>

                </strong>

            </div>

        </div>

        <div class="dashboard-card">

            <div>

                <span class="card-label">
                    Correo
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $empleado["correo"]
                    ) ?>

                </strong>

            </div>

        </div>

    </section>

    <section class="personal-table-container">

        <h3>
            Información laboral
        </h3>

        <table class="personal-table">

            <tbody>

                <tr>
                    <th>Puesto</th>
                    <td><?= htmlspecialchars($empleado["puesto"] ?? "") ?></td>
                </tr>

                <tr>
                    <th>Área</th>
                    <td><?= htmlspecialchars($empleado["area"] ?? "") ?></td>
                </tr>

                <tr>
                    <th>Estatus</th>
                    <td><?= htmlspecialchars($empleado["estatus"] ?? "") ?></td>
                </tr>

                <tr>
                    <th>Plaza</th>
                    <td><?= htmlspecialchars($empleado["plaza"] ?? "") ?></td>
                </tr>

                <tr>
                    <th>Nivel</th>
                    <td><?= htmlspecialchars($empleado["nivel"] ?? "") ?></td>
                </tr>

                <tr>
                    <th>Fecha ingreso</th>
                    <td><?= htmlspecialchars($empleado["fecha_ingreso"] ?? "") ?></td>
                </tr>

            </tbody>

        </table>

    </section>

    <section class="personal-table-container">

        <h3>
            Historial de incidencias
        </h3>

        <table class="personal-table">

            <thead>

                <tr>

                    <th>Fecha</th>

                    <th>Tipo</th>

                    <th>Descripción</th>

                </tr>

            </thead>

            <tbody>

                <?php while($inc = $incidencias->fetch_assoc()): ?>

                <tr>

                    <td><?= htmlspecialchars($inc["fecha"]) ?></td>

                    <td><?= htmlspecialchars($inc["tipo_dia"]) ?></td>

                    <td><?= htmlspecialchars($inc["descripcion"]) ?></td>

                </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </section>

    <section class="personal-table-container">

        <h3>
            Justificantes
        </h3>

        <table class="personal-table">

            <thead>

                <tr>

                    <th>Tipo</th>

                    <th>Origen</th>

                    <th>Periodo</th>

                    <th>Archivo</th>

                </tr>

            </thead>

        <tbody>

<?php while($just = $justificantes->fetch_assoc()): ?>

<tr>

    <td>
        <?= htmlspecialchars($just["tipo_justificante"]) ?>
    </td>

    <td>
        <?= htmlspecialchars($just["origen"]) ?>
    </td>

    <td>

        <?= htmlspecialchars($just["fecha_inicio"]) ?>

        al

        <?= htmlspecialchars($just["fecha_fin"]) ?>

    </td>

    <td>

        <a
            href="<?= htmlspecialchars($just["archivo"]) ?>"
            target="_blank"
            class="btn-ver-kardex"
        >
            📄 Ver archivo
        </a>

    </td>

</tr>

<?php endwhile; ?>

</tbody>
        </table>

    </section>

</main>

<?php

include "includes/footer.php";

?>
