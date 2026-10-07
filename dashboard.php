<?php

session_start();

require_once "conexion.php";

/*
|
| Verificar sesión
|
*/

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
}

$usuario = $_SESSION["usuario"];

/*
|
| Obtener información completa del empleado
|
*/

$sql = "
    SELECT
        e.id_empleado,
        e.nombre,
        e.apellido_paterno,
        e.apellido_materno,
        e.curp,
        e.rfc,
        e.correo,
        e.telefono,
        e.fecha_nacimiento,
        e.fecha_ingreso,
        e.fecha_salida,
        e.horario,

        p.nombre AS puesto,
        a.nombre AS area,
        es.nombre AS estatus,
        pl.nombre AS plaza,
        n.nombre AS nivel

    FROM usuarios u

    INNER JOIN empleados e
        ON u.id_empleado = e.id_empleado

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

    WHERE u.usuario = ?
      AND u.activo = 1

    LIMIT 1
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();

$resultado = $stmt->get_result();
$empleado = $resultado->fetch_assoc();

$stmt->close();

/*
|
| Si no se encuentra el empleado
|
*/

if (!$empleado) {
    session_destroy();

    header("Location: index.php");
    exit();
}


$nombreCompleto = trim(
    $empleado["nombre"] . " " .
    $empleado["apellido_paterno"] . " " .
    $empleado["apellido_materno"]
);



$titulo = "Dashboard";

require_once "includes/header.php";
include "includes/menu.php";

?>

<main class="main-content">

    <!-- ENCABEZADO -->

    <div class="page-header">

        <div>

            <h2>
                Panel principal
            </h2>

            <p>
                Información de tu cuenta y datos laborales
            </p>

        </div>

    </div>


    <!-- BIENVENIDA -->

    <section class="welcome-card">

        <div>

            <h2>
                ¡Bienvenido, <?= htmlspecialchars($empleado["nombre"]) ?>!
            </h2>

            <p>
                Consulta la información registrada en tu Kardex.
            </p>

        </div>

    </section>


    <!-- INFORMACIÓN PRINCIPAL -->

    <section class="dashboard-grid">


        <!-- NOMBRE -->

        <div class="dashboard-card">

            <div class="card-icon">
                👤
            </div>

            <div>

                <span class="card-label">
                    Nombre completo
                </span>

                <strong>
                    <?= htmlspecialchars($nombreCompleto) ?>
                </strong>

            </div>

        </div>


        <!-- PUESTO -->

        <div class="dashboard-card">

            <div class="card-icon">
                💼
            </div>

            <div>

                <span class="card-label">
                    Puesto
                </span>

                <strong>
                    <?= htmlspecialchars($empleado["puesto"] ?? "Sin asignar") ?>
                </strong>

            </div>

        </div>


        <!-- ÁREA -->

        <div class="dashboard-card">

            <div class="card-icon">
                🏢
            </div>

            <div>

                <span class="card-label">
                    Área
                </span>

                <strong>
                    <?= htmlspecialchars($empleado["area"] ?? "Sin asignar") ?>
                </strong>

            </div>

        </div>


        <!-- ESTATUS -->

        <div class="dashboard-card">

            <div class="card-icon">
                📌
            </div>

            <div>

                <span class="card-label">
                    Estatus
                </span>

                <strong>
                    <?= htmlspecialchars($empleado["estatus"] ?? "Sin asignar") ?>
                </strong>

            </div>

        </div>


        <!-- PLAZA -->

        <div class="dashboard-card">

            <div class="card-icon">
                🪪
            </div>

            <div>

                <span class="card-label">
                    Plaza
                </span>

                <strong>
                    <?= htmlspecialchars($empleado["plaza"] ?? "Sin asignar") ?>
                </strong>

            </div>

        </div>


        <!-- NIVEL -->

        <div class="dashboard-card">

            <div class="card-icon">
                📊
            </div>

            <div>

                <span class="card-label">
                    Nivel
                </span>

                <strong>
                    <?= htmlspecialchars($empleado["nivel"] ?? "Sin asignar") ?>
                </strong>

            </div>

        </div>


    </section>


    <!-- DATOS PERSONALES -->

    

        <div class="section-header">

            <div>

                <h3>
                    Datos personales
                </h3>

                <p>
                    Información personal registrada
                </p>

            </div>

        </div>


        <div class="info-grid">


            <!-- CURP -->

            <div class="info-item">

                <span>
                    CURP
                </span>

                <strong>
                    <?= htmlspecialchars($empleado["curp"] ?? "No registrado") ?>
                </strong>

            </div>


            <!-- RFC -->

            <div class="info-item">

                <span>
                    RFC
                </span>

                <strong>
                    <?= htmlspecialchars($empleado["rfc"] ?? "No registrado") ?>
                </strong>

            </div>


            <!-- CORREO -->

            <div class="info-item">

                <span>
                    Correo electrónico
                </span>

                <strong>
                    <?= htmlspecialchars($empleado["correo"] ?? "No registrado") ?>
                </strong>

            </div>


            <!-- TELÉFONO -->

            <div class="info-item">

                <span>
                    Teléfono
                </span>

                <strong>
                    <?= htmlspecialchars($empleado["telefono"] ?? "No registrado") ?>
                </strong>

            </div>


            <!-- FECHA NACIMIENTO -->

            <div class="info-item">

                <span>
                    Fecha de nacimiento
                </span>

                <strong>

                    <?php

                    if (!empty($empleado["fecha_nacimiento"])) {

                        echo date(
                            "d/m/Y",
                            strtotime($empleado["fecha_nacimiento"])
                        );

                    } else {

                        echo "No registrada";

                    }

                    ?>

                </strong>

            </div>


            <!-- FECHA INGRESO -->

            <div class="info-item">

                <span>
                    Fecha de ingreso
                </span>

                <strong>

                    <?php

                    if (!empty($empleado["fecha_ingreso"])) {

                        echo date(
                            "d/m/Y",
                            strtotime($empleado["fecha_ingreso"])
                        );

                    } else {

                        echo "No registrada";

                    }

                    ?>

                </strong>

            </div>


            <!-- HORARIO -->

            <div class="info-item">

                <span>
                    Horario
                </span>

                <strong>
                    <?= htmlspecialchars($empleado["horario"] ?? "No registrado") ?>
                </strong>

            </div>


            <!-- FECHA DE SALIDA -->

            <div class="info-item">

                <span>
                    Fecha de salida
                </span>

                <strong>

                    <?php

                    if (!empty($empleado["fecha_salida"])) {

                        echo date(
                            "d/m/Y",
                            strtotime($empleado["fecha_salida"])
                        );

                    } else {

                        echo "No registrada";

                    }

                    ?>

                </strong>

            </div>


        </div>

    


    <!-- ACCIONES -->

    <section class="dashboard-actions">

        <a href="kardex.php" class="action-button1">
            📋 Ver mi Kardex
        </a>

        <a href="editar_perfil.php" class="action-button1">
            ✏️ Editar mis datos
        </a>

    </section>


</main>

<?php

require_once "includes/footer.php";

?>
