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



$mensaje = "";
$tipoMensaje = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $apellidoPaterno = trim($_POST["apellido_paterno"] ?? "");
    $apellidoMaterno = trim($_POST["apellido_materno"] ?? "");

    $curp = strtoupper(trim($_POST["curp"] ?? ""));
    $rfc = strtoupper(trim($_POST["rfc"] ?? ""));

    $correo = trim($_POST["correo"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");

    $fechaNacimiento = $_POST["fecha_nacimiento"] ?? "";

    $horario = trim($_POST["horario"] ?? "");


    if ($nombre === "" || $apellidoPaterno === "") {

        $mensaje = "El nombre y apellido paterno son obligatorios.";
        $tipoMensaje = "error";

    } elseif (
        $correo !== "" &&
        !filter_var($correo, FILTER_VALIDATE_EMAIL)
    ) {

        $mensaje = "El correo electrónico no es válido.";
        $tipoMensaje = "error";

    } elseif (
        $curp !== "" &&
        strlen($curp) !== 18
    ) {

        $mensaje = "La CURP debe tener 18 caracteres.";
        $tipoMensaje = "error";

    } elseif (
        $rfc !== "" &&
        strlen($rfc) !== 12 &&
        strlen($rfc) !== 13
    ) {

        $mensaje = "El RFC debe tener 12 o 13 caracteres.";
        $tipoMensaje = "error";

    } else {



        $sqlDuplicados = "
            SELECT id_empleado
            FROM empleados
            WHERE id_empleado <> ?

            AND (
                (curp IS NOT NULL AND curp <> '' AND curp = ?)

                OR

                (rfc IS NOT NULL AND rfc <> '' AND rfc = ?)

                OR

                (correo IS NOT NULL AND correo <> '' AND correo = ?)
            )

            LIMIT 1
        ";

        $stmtDuplicados = $conexion->prepare($sqlDuplicados);

        $stmtDuplicados->bind_param(
            "isss",
            $idEmpleado,
            $curp,
            $rfc,
            $correo
        );

        $stmtDuplicados->execute();

        $resultadoDuplicados = $stmtDuplicados->get_result();


        if ($resultadoDuplicados->num_rows > 0) {

            $mensaje =
                "La CURP, RFC o correo electrónico ya pertenece a otro empleado.";

            $tipoMensaje = "error";

            $stmtDuplicados->close();

        } else {

            $stmtDuplicados->close();



            $sql = "
                UPDATE empleados

                SET
                    nombre = ?,
                    apellido_paterno = ?,
                    apellido_materno = ?,
                    curp = ?,
                    rfc = ?,
                    correo = ?,
                    telefono = ?,
                    fecha_nacimiento = NULLIF(?, ''),
                    horario = ?

                WHERE id_empleado = ?
            ";

            $stmt = $conexion->prepare($sql);

            $stmt->bind_param(
                "sssssssssi",
                $nombre,
                $apellidoPaterno,
                $apellidoMaterno,
                $curp,
                $rfc,
                $correo,
                $telefono,
                $fechaNacimiento,
                $horario,
                $idEmpleado
            );


            if ($stmt->execute()) {

                $mensaje =
                    "Tus datos se actualizaron correctamente.";

                $tipoMensaje = "exito";

            } else {

                $mensaje =
                    "Ocurrió un error al actualizar tus datos.";

                $tipoMensaje = "error";
            }

            $stmt->close();
        }
    }
}



$sqlEmpleado = "

    SELECT

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
        pl.nombre AS plaza,
        n.nombre AS nivel,
        es.nombre AS estatus

    FROM empleados e

    LEFT JOIN puestos p
        ON e.id_puesto = p.id_puesto

    LEFT JOIN areas a
        ON e.id_area = a.id_area

    LEFT JOIN plazas pl
        ON e.id_plaza = pl.id_plaza

    LEFT JOIN niveles n
        ON e.id_nivel = n.id_nivel

    LEFT JOIN estatus es
        ON e.id_estatus = es.id_estatus

    WHERE e.id_empleado = ?

    LIMIT 1
";


$stmtEmpleado = $conexion->prepare($sqlEmpleado);

$stmtEmpleado->bind_param(
    "i",
    $idEmpleado
);

$sqlEmpleado = "SELECT id_empleado, nombre FROM empleados Where id_empleado = ? ";


$stmtEmpleado->execute();
$resultadoEmpleado = $stmtEmpleado->get_result();
$empleado = $resultadoEmpleado->fetch_assoc();
$stmtEmpleado->close();




if (!$empleado) {

    echo "No se encontró la información del empleado.";

    exit();
}


$titulo = "Editar mis datos";

require_once "includes/header.php";
include "includes/menu.php";


?>


<main class="main-content">


 

    <div class="page-header">

        <div>

            <h2>
                Editar mis datos
            </h2>

            <p>
                Actualiza tu información personal.
            </p>

        </div>

    </div>


    <?php if ($mensaje !== ""): ?>

        <div
            class="<?= $tipoMensaje === "exito"
                ? "mensaje-exito"
                : "mensaje-error"
            ?>"
        >

            <?= htmlspecialchars(
                $mensaje,
                ENT_QUOTES,
                "UTF-8"
            ) ?>

        </div>

    <?php endif; ?>


    <section class="content-card">


        <div class="section-header">

            <div>

                <h3>
                    Información personal
                </h3>

                <p>
                    Actualiza los datos que correspondan a tu información personal.
                </p>

            </div>

        </div>



        <form
            method="POST"
            action="editar_perfil.php"
            class="edit-form"
        >


            <!-- NOMBRE -->

            <div class="form-group">

                <label for="nombre">
                    Nombre *
                </label>

                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="<?= htmlspecialchars(
                        $empleado["nombre"] ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                    required
                >

            </div>



            <!-- APELLIDO PATERNO -->

            <div class="form-group">

                <label for="apellido_paterno">
                    Apellido paterno *
                </label>

                <input
                    type="text"
                    id="apellido_paterno"
                    name="apellido_paterno"
                    value="<?= htmlspecialchars(
                        $empleado["apellido_paterno"] ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                    required
                >

            </div>



            <!-- APELLIDO MATERNO -->

            <div class="form-group">

                <label for="apellido_materno">
                    Apellido materno
                </label>

                <input
                    type="text"
                    id="apellido_materno"
                    name="apellido_materno"
                    value="<?= htmlspecialchars(
                        $empleado["apellido_materno"] ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

            </div>



            <!-- CURP -->

            <div class="form-group">

                <label for="curp">
                    CURP
                </label>

                <input
                    type="text"
                    id="curp"
                    name="curp"
                    maxlength="18"
                    placeholder="Ingresa tu CURP"
                    value="<?= htmlspecialchars(
                        $empleado["curp"] ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

            </div>



            <!-- RFC -->

            <div class="form-group">

                <label for="rfc">
                    RFC
                </label>

                <input
                    type="text"
                    id="rfc"
                    name="rfc"
                    maxlength="13"
                    placeholder="Ingresa tu RFC"
                    value="<?= htmlspecialchars(
                        $empleado["rfc"] ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

            </div>



            <!-- CORREO -->

            <div class="form-group">

                <label for="correo">
                    Correo electrónico
                </label>

                <input
                    type="email"
                    id="correo"
                    name="correo"
                    placeholder="correo@ejemplo.com"
                    value="<?= htmlspecialchars(
                        $empleado["correo"] ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

            </div>



            <!-- TELÉFONO -->

            <div class="form-group">

                <label for="telefono">
                    Teléfono
                </label>

                <input
                    type="text"
                    id="telefono"
                    name="telefono"
                    maxlength="20"
                    placeholder="Ingresa tu teléfono"
                    value="<?= htmlspecialchars(
                        $empleado["telefono"] ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

            </div>



            <!-- FECHA DE NACIMIENTO -->

            <div class="form-group">

                <label for="fecha_nacimiento">
                    Fecha de nacimiento
                </label>

                <input
                    type="date"
                    id="fecha_nacimiento"
                    name="fecha_nacimiento"
                    value="<?= htmlspecialchars(
                        $empleado["fecha_nacimiento"] ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

            </div>



            <!-- HORARIO -->

            <div class="form-group">

                <label for="horario">
                    Horario
                </label>

                <input
                    type="text"
                    id="horario"
                    name="horario"
                    placeholder="Ej. 08:00 - 16:00"
                    value="<?= htmlspecialchars(
                        $empleado["horario"] ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

            </div>



            <div class="edit-section-title">

                <h3>
                    Información laboral
                </h3>

                <p>
                    Estos datos son administrados por el personal autorizado.
                </p>

            </div>



            <div class="readonly-grid">


                <!-- PUESTO -->

                <div class="readonly-item">

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

                <div class="readonly-item">

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

                <div class="readonly-item">

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

                <div class="readonly-item">

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

                <div class="readonly-item">

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


            <div class="form-actions">


                <a
                    href="dashboard.php"
                    class="action-button-secondary"
                >
                    ← Cancelar
                </a>


                <button
                    type="submit"
                    class="action-button2"
                >
                    💾 Guardar cambios
                </button>


            </div>


        </form>


    </section>


</main>


<?php

require_once "includes/footer.php";

?>


