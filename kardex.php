<?php

session_start();

require_once "conexion.php";




if (!isset($_SESSION["usuario"])) {

    header("Location: index.php");
    exit();

}


$idEmpleado = $_SESSION["id_empleado"] ?? null;


if (!$idEmpleado) {

    header("Location: index.php");
    exit();

}


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


if (!$stmt) {

    die(
        "Error al preparar la consulta: "
        . $conexion->error
    );

}


$stmt->bind_param(
    "i",
    $idEmpleado
);


$stmt->execute();


$resultado = $stmt->get_result();


$empleado = $resultado->fetch_assoc();


$stmt->close();




if (!$empleado) {

    echo "
        <div style='padding:30px;font-family:Arial;'>
            No se encontró información del empleado.
        </div>
    ";

    exit();

}




$nombreCompleto = trim(

    $empleado["nombre"] . " " .

    ($empleado["apellido_paterno"] ?? "") . " " .

    ($empleado["apellido_materno"] ?? "")

);



function mostrarFechaKardex($fecha)
{

    if (empty($fecha)) {

        return "No registrada";

    }


    return date(
        "d/m/Y",
        strtotime($fecha)
    );

}



$titulo = "Mi Kardex";


require_once "includes/header.php";

?>


<main class="main-content">




    <div class="page-header">


        <div>

            <h2>
                Mi Kardex
            </h2>

            <p>
                Información personal y laboral registrada en el sistema.
            </p>

        </div>


        <a
            href="editar_perfil.php"
            class="action-button"
        >
            ✏️ Editar mis datos
        </a>


    </div>


    <section class="content-card">


        <div class="section-header">


            <div>

                <h3>
                    Datos personales
                </h3>

                <p>
                    Información personal del empleado.
                </p>

            </div>


        </div>



        <div class="info-grid">


            <!-- NOMBRE -->

            <div class="info-item">

                <span>
                    Nombre completo
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $nombreCompleto,
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </strong>

            </div>



            <!-- CURP -->

            <div class="info-item">

                <span>
                    CURP
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $empleado["curp"] ?? "No registrada",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </strong>

            </div>



            <!-- RFC -->

            <div class="info-item">

                <span>
                    RFC
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $empleado["rfc"] ?? "No registrado",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </strong>

            </div>



            <!-- CORREO -->

            <div class="info-item">

                <span>
                    Correo electrónico
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $empleado["correo"] ?? "No registrado",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </strong>

            </div>



            <!-- TELÉFONO -->

            <div class="info-item">

                <span>
                    Teléfono
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $empleado["telefono"] ?? "No registrado",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </strong>

            </div>



            <!-- FECHA DE NACIMIENTO -->

            <div class="info-item">

                <span>
                    Fecha de nacimiento
                </span>

                <strong>

                    <?= htmlspecialchars(
                        mostrarFechaKardex(
                            $empleado["fecha_nacimiento"]
                        ),
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </strong>

            </div>



            <!-- FECHA DE INGRESO -->

            <div class="info-item">

                <span>
                    Fecha de ingreso
                </span>

                <strong>

                    <?= htmlspecialchars(
                        mostrarFechaKardex(
                            $empleado["fecha_ingreso"]
                        ),
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </strong>

            </div>



            <!-- FECHA DE SALIDA -->

            <div class="info-item">

                <span>
                    Fecha de salida
                </span>

                <strong>

                    <?= htmlspecialchars(
                        mostrarFechaKardex(
                            $empleado["fecha_salida"]
                        ),
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </strong>

            </div>



            <!-- HORARIO -->

            <div class="info-item">

                <span>
                    Horario
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $empleado["horario"] ?? "No registrado",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </strong>

            </div>


        </div>


    </section>



    <section class="content-card">


        <div class="section-header">


            <div>

                <h3>
                    Información laboral
                </h3>

                <p>
                    Información asignada por la administración.
                </p>

            </div>


        </div>



        <div class="info-grid">


            <!-- PUESTO -->

            <div class="info-item">

                <span>
                    Puesto
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $empleado["puesto"] ?? "Sin asignar",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </strong>

            </div>



            <!-- ÁREA -->

            <div class="info-item">

                <span>
                    Área
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $empleado["area"] ?? "Sin asignar",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </strong>

            </div>



            <!-- PLAZA -->

            <div class="info-item">

                <span>
                    Plaza
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $empleado["plaza"] ?? "Sin asignar",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </strong>

            </div>



            <!-- NIVEL -->

            <div class="info-item">

                <span>
                    Nivel
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $empleado["nivel"] ?? "Sin asignar",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </strong>

            </div>



            <!-- ESTATUS -->

            <div class="info-item">

                <span>
                    Estatus
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $empleado["estatus"] ?? "Sin asignar",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </strong>

            </div>


        </div>


    </section>



    
    <section class="dashboard-actions">


        <a
            href="editar_perfil.php"
            class="action-button"
        >
            ✏️ Editar mis datos
        </a>


        <a
            href="dashboard.php"
            class="action-button secondary"
        >
            ← Regresar al inicio
        </a>


    </section>


</main>


<?php

require_once "includes/footer.php";

?>
